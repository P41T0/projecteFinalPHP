<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Hide administration routes from non-administrators by returning 404.
        abort_unless($request->user()?->isAdmin(), 404);

        return $next($request);
    }
}
