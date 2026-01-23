<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRequestHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $headers = $request->header();
        
        // Log Authorization header specifically (masked for security)
        $auth = $request->header('Authorization');
        $maskedAuth = $auth ? substr($auth, 0, 15) . '...' : 'NONE';
        
        Log::info('API Request Headers:', [
            'Authorization' => $maskedAuth,
            'Origin' => $request->header('Origin'),
            'Content-Type' => $request->header('Content-Type'),
            'URL' => $request->fullUrl()
        ]);

        return $next($request);
    }
}
