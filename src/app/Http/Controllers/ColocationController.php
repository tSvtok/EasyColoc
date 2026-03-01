<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreColocationRequest;
use App\Models\Colocation;
use App\Services\BalanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ColocationController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasActiveColocation()) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous avez déjà une colocation active.');
        }

        return view('colocations.create');
    }

    public function store(StoreColocationRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasActiveColocation()) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous avez déjà une colocation active.');
        }

        $colocation = Colocation::create([
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
            'owner_id' => $user->id,
        ]);

        // Add the owner as a member with role 'owner'
        $colocation->members()->attach($user->id, [
            'role' => 'owner',
            'joined_at' => now(),
        ]);

        // Create default categories
        $defaults = ['Loyer', 'Courses', 'Électricité', 'Internet', 'Ménage', 'Autre'];
        foreach ($defaults as $name) {
            $colocation->categories()->create(['name' => $name]);
        }

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Colocation créée avec succès !');
    }

    public function show(Request $request, Colocation $colocation): View
    {
        $user = $request->user();

        // Check user is a member
        $isMember = $colocation->activeMembers()->where('user_id', $user->id)->exists();
        if (! $isMember) {
            abort(403, 'Vous ne faites pas partie de cette colocation.');
        }

        $month = $request->query('month');

        $expensesQuery = $colocation->expenses()->with(['payer', 'category'])->orderByDesc('expense_date');
        if ($month) {
            $expensesQuery->whereMonth('expense_date', substr($month, 5, 2))
                ->whereYear('expense_date', substr($month, 0, 4));
        }
        $expenses = $expensesQuery->get();

        $members = $colocation->activeMembers()->get();
        $categories = $colocation->categories;

        // Calculate balances and settlements
        $balanceService = new BalanceService;
        $balances = $balanceService->calculateBalances($colocation);
        $settlements = $balanceService->calculateSettlements($colocation);

        // Get existing settlements (payments)
        $payments = $colocation->settlements()->with(['fromUser', 'toUser'])->get();

        $isOwner = (int) $colocation->owner_id === (int) $user->id;

        // Available months for filter (compatible with both PostgreSQL and SQLite)
        $driver = config('database.default');
        if ($driver === 'pgsql') {
            $availableMonths = $colocation->expenses()
                ->selectRaw("DISTINCT TO_CHAR(expense_date, 'YYYY-MM') as month")
                ->orderByDesc('month')
                ->pluck('month');
        } else {
            $availableMonths = $colocation->expenses()
                ->selectRaw("DISTINCT strftime('%Y-%m', expense_date) as month")
                ->orderByDesc('month')
                ->pluck('month');
        }

        return view('colocations.show', compact(
            'colocation',
            'expenses',
            'members',
            'categories',
            'balances',
            'settlements',
            'payments',
            'isOwner',
            'month',
            'availableMonths'
        ));
    }

    public function edit(Request $request, Colocation $colocation): View
    {
        if ((int) $colocation->owner_id !== (int) $request->user()->id) {
            abort(403);
        }

        return view('colocations.edit', compact('colocation'));
    }

    public function update(StoreColocationRequest $request, Colocation $colocation): RedirectResponse
    {
        if ((int) $colocation->owner_id !== (int) $request->user()->id) {
            abort(403);
        }

        $colocation->update($request->validated());

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Colocation mise à jour.');
    }

    public function cancel(Request $request, Colocation $colocation): RedirectResponse
    {
        $user = $request->user();

        if ((int) $colocation->owner_id !== (int) $user->id) {
            abort(403);
        }

        // Check for debts: if owner cancels with debt, reputation -1
        $balanceService = new BalanceService;
        $balances = $balanceService->calculateBalances($colocation);

        $colocation->update(['status' => 'cancelled']);

        // Mark all active members as left
        $colocation->activeMembers()->each(function ($member) use ($balances) {
            $balance = $balances[$member->id] ?? 0;
            // Reputation: -1 if has debt, +1 if no debt
            $member->increment('reputation', $balance < 0 ? -1 : 1);
            $member->colocations()->updateExistingPivot(
                $member->pivot->colocation_id,
                ['left_at' => now()]
            );
        });

        return redirect()->route('dashboard')
            ->with('success', 'Colocation annulée.');
    }

    public function leave(Request $request, Colocation $colocation): RedirectResponse
    {
        $user = $request->user();

        if ((int) $colocation->owner_id === (int) $user->id) {
            return redirect()->route('colocations.show', $colocation)
                ->with('error', 'Le propriétaire ne peut pas quitter la colocation. Annulez-la plutôt.');
        }

        $balanceService = new BalanceService;
        $balances = $balanceService->calculateBalances($colocation);
        $balance = $balances[$user->id] ?? 0;

        // Reputation update
        $user->increment('reputation', $balance < 0 ? -1 : 1);

        // If member leaves with debt, redistribute via internal adjustments
        if ($balance < 0) {
            // The debt is redistributed among remaining members (simplified: owner absorbs)
            $owner = $colocation->owner;
            // Create an expense adjustment: owner "paid" for the departing member's debt
            // This is handled by recalculating balances after member leaves
        }

        $colocation->members()->updateExistingPivot($user->id, ['left_at' => now()]);

        return redirect()->route('dashboard')
            ->with('success', 'Vous avez quitté la colocation.');
    }

    public function removeMember(Request $request, Colocation $colocation, int $userId): RedirectResponse
    {
        $currentUser = $request->user();

        if ((int) $colocation->owner_id !== (int) $currentUser->id) {
            abort(403);
        }

        if ($userId === (int) $colocation->owner_id) {
            return redirect()->route('colocations.show', $colocation)
                ->with('error', 'Vous ne pouvez pas vous retirer vous-même.');
        }

        $balanceService = new BalanceService;
        $balances = $balanceService->calculateBalances($colocation);
        $memberBalance = $balances[$userId] ?? 0;

        $member = \App\Models\User::findOrFail($userId);

        // If member has debt and owner removes them, debt goes to owner
        if ($memberBalance < 0) {
            $member->increment('reputation', -1);
            // Owner absorbs the debt - no explicit action needed, balances recalculate
        } else {
            $member->increment('reputation', 1);
        }

        $colocation->members()->updateExistingPivot($userId, ['left_at' => now()]);

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Membre retiré de la colocation.');
    }
}
