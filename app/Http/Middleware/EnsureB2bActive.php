<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Logs out a B2B salon account whose status is no longer "active" (pending /
 * disabled). Status is otherwise only checked at the login form, so without
 * this a disabled salon would keep its wholesale discount for the life of its
 * session / remember-cookie — including on the public checkout path, which is
 * not inside the auth:b2b group.
 */
class EnsureB2bActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Auth::guard('b2b');

        if ($guard->check() && ! $guard->user()->isActive()) {
            $guard->logout();
        }

        return $next($request);
    }
}
