<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // role: 1 = admin, 0 = user
        if (Auth::user()->role !== 1) {
            abort(403, 'Admins only');
        }

        return $next($request);
    }
}
