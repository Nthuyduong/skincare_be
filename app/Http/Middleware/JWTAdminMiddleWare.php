<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTAdminMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // how to distinguish this with user middleware?
        JWTAuth::setProvider(new Tymon\JWTAuth\Providers\User\EloquentUserAdapter(Admin::class));
        $admin = JWTAuth::parseToken()->authenticate();
        return $next($request);
    }
}
