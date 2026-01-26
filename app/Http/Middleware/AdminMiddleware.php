<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Strict Admin Check: Only admin@store.com allowed
        if (!$request->user() || $request->user()->role !== 'admin' || $request->user()->email !== 'admin@store.com') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized Access. Strict Admin Policy Enforced.'
            ], 403);
        }
        return $next($request);
    }

}
