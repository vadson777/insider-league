<?php

namespace App\Http\Middleware;

use Closure;

class DisableDebug
{

    public function handle($request, Closure $next)
    {
        if (app()->bound('debugbar')) {
            \Debugbar::disable();
        }

        return $next($request);
    }

}