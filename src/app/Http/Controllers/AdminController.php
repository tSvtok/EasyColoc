<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $totalUsers = User::count();
        $totalColocations = Colocation::count();
        $activeColocations = Colocation::where('status', 'active')->count();
        $totalExpenses = Expense::count();
        $totalExpenseAmount = Expense::sum('amount');
        $bannedUsers = User::where('is_banned', true)->count();

        $users = User::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.index', compact(
            'totalUsers',
            'totalColocations',
            'activeColocations',
            'totalExpenses',
            'totalExpenseAmount',
            'bannedUsers',
            'users'
        ));
    }

    public function ban(Request $request, User $user): RedirectResponse
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.index')
                ->with('error', 'Impossible de bannir un administrateur.');
        }

        $user->update(['is_banned' => true]);

        return redirect()->route('admin.index')
            ->with('success', "L'utilisateur {$user->name} a été banni.");
    }

    public function unban(Request $request, User $user): RedirectResponse
    {
        $user->update(['is_banned' => false]);

        return redirect()->route('admin.index')
            ->with('success', "L'utilisateur {$user->name} a été débanni.");
    }
}
