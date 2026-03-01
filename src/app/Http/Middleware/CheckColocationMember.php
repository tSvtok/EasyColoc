<?php

namespace App\Http\Middleware;

use App\Models\Colocation;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckColocationMember
{
    public function handle(Request $request, Closure $next): Response
    {
        $colocation = $request->route('colocation');

        if ($colocation instanceof Colocation) {
            $isMember = $colocation->activeMembers()
                ->where('user_id', $request->user()->id)
                ->exists();

            if (! $isMember) {
                abort(403, 'Vous ne faites pas partie de cette colocation.');
            }
        }

        return $next($request);
    }
}
