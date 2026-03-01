<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $colocation = $user->activeColocation();

        // Check pending invitations for this user
        $pendingInvitations = \App\Models\Invitation::where('email', $user->email)
            ->where('status', 'pending')
            ->with('colocation')
            ->get();

        return view('dashboard', compact('user', 'colocation', 'pendingInvitations'));
    }
}
