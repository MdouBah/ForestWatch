<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AgentMiddleware
 * Protège les routes réservées aux agents forestiers (role='user') ET aux admins.
 * Les visiteurs sont systématiquement bloqués.
 */
class AgentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('api')->user();

        if (! $user || ! in_array($user->role, ['admin', 'user'])) {
            return response()->json([
                'message' => 'Accès réservé aux agents forestiers et aux administrateurs.',
            ], 403);
        }

        return $next($request);
    }
}
