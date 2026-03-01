<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Settlement;
use App\Services\BalanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function markPaid(Request $request, Colocation $colocation, Settlement $settlement): RedirectResponse
    {
        $user = $request->user();

        // Only the creditor (to_user) or owner can mark as paid
        if ((int) $settlement->to_user_id !== (int) $user->id && (int) $colocation->owner_id !== (int) $user->id) {
            abort(403, "Vous n'êtes pas autorisé à effectuer cette action.");
        }

        $settlement->update(['is_paid' => true]);

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Paiement enregistré.');
    }

    //Recalculate and store settlements for a colocation.
    public function recalculate(Request $request, Colocation $colocation): RedirectResponse
    {
        if ((int) $colocation->owner_id !== (int) $request->user()->id) {
            abort(403);
        }

        $balanceService = new BalanceService;
        $calculatedSettlements = $balanceService->calculateSettlements($colocation);

        // Remove old unpaid settlements
        $colocation->settlements()->where('is_paid', false)->delete();

        // Create new settlements
        foreach ($calculatedSettlements as $s) {
            if ($s['from'] && $s['to']) {
                Settlement::create([
                    'colocation_id' => $colocation->id,
                    'from_user_id' => $s['from']->id,
                    'to_user_id' => $s['to']->id,
                    'amount' => $s['amount'],
                    'is_paid' => false,
                ]);
            }
        }

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Remboursements recalculés.');
    }
}
