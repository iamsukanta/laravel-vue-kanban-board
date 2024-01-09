<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if($request->has('access_token')) {
            if($request->access_token === '42gA1S5') {
                return $next($request);
            }
            throw new \ErrorException('Invalid Access Token!', 401);
        }
        throw new \ErrorException('Access Token Required!', 401);
    }
}
