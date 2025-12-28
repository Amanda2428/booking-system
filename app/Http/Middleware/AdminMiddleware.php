<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is not logged in, redirect to login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // If logged-in user is not admin, redirect to home
        if (Auth::user()->role !== 1) {
            return redirect('/'); // or any page you want
        }

        // If admin, allow request to continue
        return $next($request);
    }
}
