<?php

namespace App\Http\Middleware;

use Closure;

class VerifyCsrfTokenGet
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check matching token from GET
        $sessionToken = $request->session()->token();
        $token = $request->input('_token');
        if (! is_string($sessionToken) || ! is_string($token) || !hash_equals($sessionToken, $token) ) {
            abort(419);
        }
    
        return $next($request);
    }
}
