<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale('fr');

        return $next($request);
    }
}
