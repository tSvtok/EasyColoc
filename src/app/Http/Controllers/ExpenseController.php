<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\Colocation;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function store(StoreExpenseRequest $request, Colocation $colocation): RedirectResponse
    {
        $colocation->expenses()->create([
            'title' => $request->validated('title'),
            'amount' => $request->validated('amount'),
            'expense_date' => $request->validated('expense_date'),
            'category_id' => $request->validated('category_id'),
            'paid_by' => $request->validated('paid_by'),
        ]);

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Dépense ajoutée avec succès.');
    }

    public function destroy(Request $request, Colocation $colocation, Expense $expense): RedirectResponse
    {
        // Only owner or the payer can delete
        $user = $request->user();
        if ((int) $expense->paid_by !== (int) $user->id && (int) $colocation->owner_id !== (int) $user->id) {
            abort(403, 'Vous ne pouvez pas supprimer cette dépense.');
        }

        $expense->delete();

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Dépense supprimée.');
    }
}
