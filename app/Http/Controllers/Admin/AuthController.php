<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers\Admin
namespace App\Http\Controllers\Admin;

// Import Controller base class
use App\Http\Controllers\Controller;
// Import các Model và Facade cần thiết
use App\Models\User; // Model quản lý người dùng
use Illuminate\Http\Request; // Class xử lý HTTP request
use Illuminate\Support\Facades\Session; // Facade để làm việc với session

/**
 * Class AuthController
 * Controller xử lý đăng nhập/đăng xuất admin
 */
class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập admin
     * Cho phép truy cập trang login ngay cả khi đã đăng nhập user thường
     * Chỉ redirect nếu đã đăng nhập VÀ là admin
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse - View form đăng nhập hoặc redirect về dashboard
     */
    public function showLogin()
    {
        // Kiểm tra đã đăng nhập chưa
        if (Session::has('users')) {
            // Tìm user trong database theo username trong session
            $user = User::where('taikhoan', Session::get('users'))->first();
            // Nếu user tồn tại và là admin, redirect về dashboard
            if ($user && $user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
        }
        
        // Trả về view form đăng nhập admin
        return view('admin.auth.login');
    }

    /**
     * Xử lý đăng nhập admin
     * Kiểm tra credentials và quyền admin, sau đó tạo session mới
     * 
     * @param Request $request - HTTP request chứa taikhoan và matkhau
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse - JSON response (nếu AJAX) hoặc redirect
     */
    public function login(Request $request)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'taikhoan' => 'required', // Tên tài khoản bắt buộc
            'matkhau' => 'required', // Mật khẩu bắt buộc
        ]);

        // Kiểm tra thông tin đăng nhập bằng phương thức verifyCredentials của User model
        if (User::verifyCredentials($request->taikhoan, $request->matkhau)) {
            // Tìm user trong database theo username
            $user = User::findByUsername($request->taikhoan);
            
            if ($user) {
                // Kiểm tra quyền admin
                if (!$user->isAdmin()) {
                    // Nếu không phải AJAX, quay lại với thông báo lỗi
                    return back()->withErrors(['taikhoan' => 'Tài khoản này không có quyền truy cập trang admin!']);
                }
                
                // Xóa session cũ (nếu có) và tạo session mới cho admin
                // Điều này cho phép đăng nhập admin ngay cả khi đã đăng nhập user thường
                session()->forget(['users', 'user_id']);
                
                // Lưu session mới cho admin
                session(['users' => $user->taikhoan]); // Lưu username vào session
                session(['user_id' => $user->id]); // Lưu user ID vào session
                
                // Nếu là AJAX request, trả về JSON response
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Đăng nhập admin thành công!',
                        'redirect' => route('admin.dashboard') // URL redirect
                    ]);
                }
                
                // Nếu không phải AJAX, redirect về dashboard với thông báo thành công
                return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập admin thành công!');
            }
        }

        // Nếu thông tin đăng nhập không hợp lệ và là AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Thông tin đăng nhập không hợp lệ hoặc không có quyền admin!'
            ], 422); // HTTP status code 422 (Unprocessable Entity)
        }

        // Nếu không phải AJAX, quay lại với thông báo lỗi
        return back()->withErrors(['taikhoan' => 'Thông tin đăng nhập không hợp lệ hoặc không có quyền admin!']);
    }

    /**
     * Đăng xuất admin
     * Xóa session và regenerate token để bảo mật
     * 
     * @param Request $request - HTTP request
     * @return \Illuminate\Http\RedirectResponse - Redirect về trang đăng nhập admin với thông báo
     */
    public function logout(Request $request)
    {
        // Xóa các key session liên quan đến user
        session()->forget(['users', 'user_id']);
        // Vô hiệu hóa session hiện tại
        $request->session()->invalidate();
        // Tạo lại CSRF token mới để bảo mật
        $request->session()->regenerateToken();
        
        // Redirect về trang đăng nhập admin với thông báo thành công
        return redirect()->route('admin.auth.login')->with('success', 'Đăng xuất thành công!');
    }
}

