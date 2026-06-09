<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotVisiteurMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('api')->user();

        if (!$user || $user->role === 'visiteur') {
            return response()->json([
                'message' => 'Accès réservé aux agents de saisie et administrateurs.',
            ], 403);
        }

        return $next($request);
    }
}
