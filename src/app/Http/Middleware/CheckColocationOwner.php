<?php

namespace App\Http\Middleware;

use App\Models\Colocation;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckColocationOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        $colocation = $request->route('colocation');

        if ($colocation instanceof Colocation) {
            if ((int) $colocation->owner_id !== (int) $request->user()->id) {
                abort(403, 'Seul le propriétaire peut effectuer cette action.');
            }
        }

        return $next($request);
    }
}
