<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Locale
{
    public function handle($request, Closure $next)
    {
        $locale = $request->header('x-locale');
        if ($locale) {
            app()->setLocale($locale);
        }
        return $next($request);
    }
}