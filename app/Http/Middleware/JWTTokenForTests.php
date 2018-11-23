<?php

namespace App\Http\Middleware;

use JWTAuth;
use Closure;

class JWTTokenForTests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (env('APP_ENV') === 'testing' && (!empty($request->header('authorization')) || !empty($request->get('token')))) {
            JWTAuth::setRequest($request);
        }

        return $next($request);
    }
}
