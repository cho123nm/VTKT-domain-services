<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (session()->has('users')) {
                // Special handling for admin login route
                // Allow access to admin login page even if user is logged in (as regular user)
                // Only redirect if user is already logged in as admin
                if ($request->routeIs('admin.auth.login') || $request->is('admin/login')) {
                    $user = User::where('taikhoan', Session::get('users'))->first();
                    // Only redirect if user is admin (to admin dashboard)
                    // If user is regular user, allow them to access admin login page
                    if ($user && $user->isAdmin()) {
                        return redirect()->route('admin.dashboard');
                    }
                    // If regular user, allow access to admin login page
                    return $next($request);
                }
                
                // For other routes (public login/register), redirect to home if authenticated
                return redirect()->route('home');
            }
        }

        return $next($request);
    }
}

