<?php

namespace App\Http\Middleware;

use Inertia\Inertia;
use Closure;

class HandleInertiaRequests
{
    public function handle($request, Closure $next)
    {
        // Middleware logic here
        return $next($request);
    }
}
