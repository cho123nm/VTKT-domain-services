<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Session::has('users')) {
            return redirect()->route('admin.auth.login')->with('error', 'Vui lòng đăng nhập để truy cập trang admin');
        }

        // Get user from database
        $user = User::where('taikhoan', Session::get('users'))->first();
        
        if (!$user) {
            Session::forget(['users', 'user_id']);
            return redirect()->route('admin.auth.login')->with('error', 'Không tìm thấy thông tin người dùng');
        }

        // Check if user has admin privileges
        if (!$user->isAdmin()) {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang admin');
        }
        
        return $next($request);
    }
}

