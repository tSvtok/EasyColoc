<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvitationRequest;
use App\Models\Colocation;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function store(StoreInvitationRequest $request, Colocation $colocation): RedirectResponse
    {
        if ((int) $colocation->owner_id !== (int) $request->user()->id) {
            abort(403);
        }

        $email = $request->validated('email');

        // Check if already a member
        $alreadyMember = $colocation->activeMembers()
            ->where('email', $email)
            ->exists();

        if ($alreadyMember) {
            return redirect()->route('colocations.show', $colocation)
                ->with('error', 'Cet utilisateur est déjà membre.');
        }

        // Check if invitation already pending
        $existingInvitation = $colocation->invitations()
            ->where('email', $email)
            ->where('status', 'pending')
            ->first();

        if ($existingInvitation) {
            return redirect()->route('colocations.show', $colocation)
                ->with('error', 'Une invitation est déjà en attente pour cet email.');
        }

        $token = Str::random(64);

        Invitation::create([
            'colocation_id' => $colocation->id,
            'email' => $email,
            'token' => $token,
        ]);

        // Send invitation email
        $inviteUrl = route('invitations.show', $token);
        Mail::raw(
            "Vous êtes invité à rejoindre la colocation \"{$colocation->name}\".\n\nCliquez ici pour accepter : {$inviteUrl}",
            function ($message) use ($email, $colocation) {
                $message->to($email)
                    ->subject("Invitation à rejoindre {$colocation->name} - EasyColoc");
            }
        );

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Invitation envoyée à '.$email);
    }

    public function show(string $token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        $colocation = $invitation->colocation;

        return view('invitations.show', compact('invitation', 'colocation'));
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        $user = $request->user();

        if (! $user) {
            return redirect()->route('login')
                ->with('error', 'Connectez-vous pour accepter cette invitation.');
        }

        // Verify email matches
        if ($user->email !== $invitation->email) {
            return redirect()->route('dashboard')
                ->with('error', "Cette invitation n'est pas destinée à votre compte.");
        }

        // Check if user already has active colocation
        if ($user->hasActiveColocation()) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous avez déjà une colocation active.');
        }

        $colocation = $invitation->colocation;

        if (! $colocation->isActive()) {
            return redirect()->route('dashboard')
                ->with('error', "Cette colocation n'est plus active.");
        }

        // Add user to colocation
        $colocation->members()->attach($user->id, [
            'role' => 'member',
            'joined_at' => now(),
        ]);

        $invitation->update(['status' => 'accepted']);

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Vous avez rejoint la colocation !');
    }

    public function refuse(Request $request, string $token): RedirectResponse
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        $invitation->update(['status' => 'refused']);

        return redirect()->route('dashboard')
            ->with('success', 'Invitation refusée.');
    }
}
