<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // eager loaded?
        $user = Auth::user()->loadMissing('roles');

        if (!$user->hasAnyRole($roles)) {
            abort(403, 'Anda tidak memiliki akses');
        }

        return $next($request);
    }
}

