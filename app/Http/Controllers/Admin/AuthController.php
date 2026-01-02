<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập admin
     */
    public function showLogin()
    {
        // Cho phép truy cập trang login ngay cả khi đã đăng nhập (để đăng nhập lại bằng tài khoản admin)
        // Chỉ redirect nếu đã đăng nhập VÀ là admin
        if (Session::has('users')) {
            $user = User::where('taikhoan', Session::get('users'))->first();
            if ($user && $user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
        }
        
        return view('admin.auth.login');
    }

    /**
     * Xử lý đăng nhập admin
     */
    public function login(Request $request)
    {
        $request->validate([
            'taikhoan' => 'required',
            'matkhau' => 'required',
        ]);

        // Kiểm tra credentials
        if (User::verifyCredentials($request->taikhoan, $request->matkhau)) {
            $user = User::findByUsername($request->taikhoan);
            
            if ($user) {
                // Kiểm tra quyền admin
                if (!$user->isAdmin()) {
                    return back()->withErrors(['taikhoan' => 'Tài khoản này không có quyền truy cập trang admin!']);
                }
                
                // Xóa session cũ (nếu có) và tạo session mới cho admin
                // Điều này cho phép đăng nhập admin ngay cả khi đã đăng nhập user thường
                session()->forget(['users', 'user_id']);
                
                // Lưu session mới cho admin
                session(['users' => $user->taikhoan]);
                session(['user_id' => $user->id]);
                
                // Nếu là AJAX request
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Đăng nhập admin thành công!',
                        'redirect' => route('admin.dashboard')
                    ]);
                }
                
                return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập admin thành công!');
            }
        }

        // Nếu là AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Thông tin đăng nhập không hợp lệ hoặc không có quyền admin!'
            ], 422);
        }

        return back()->withErrors(['taikhoan' => 'Thông tin đăng nhập không hợp lệ hoặc không có quyền admin!']);
    }

    /**
     * Đăng xuất admin
     */
    public function logout(Request $request)
    {
        session()->forget(['users', 'user_id']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.auth.login')->with('success', 'Đăng xuất thành công!');
    }
}

