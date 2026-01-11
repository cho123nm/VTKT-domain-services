<?php
// Khai báo namespace cho Middleware này - thuộc App\Http\Middleware
namespace App\Http\Middleware;

// Import các class cần thiết
use Closure; // Closure để chuyển request đến middleware tiếp theo
use Illuminate\Http\Request; // Class xử lý HTTP request
use Illuminate\Support\Facades\Session; // Facade để làm việc với session
use App\Models\User; // Model User để kiểm tra quyền admin
use Symfony\Component\HttpFoundation\Response; // Class Response của Symfony

/**
 * Class AdminMiddleware
 * Middleware kiểm tra quyền truy cập admin
 * Chỉ cho phép user có chucvu == 1 (admin) truy cập các route admin
 */
class AdminMiddleware
{
    /**
     * Xử lý request đến middleware
     * Kiểm tra user đã đăng nhập và có quyền admin không
     * 
     * @param Request $request - HTTP request hiện tại
     * @param Closure $next - Closure để chuyển request đến middleware/controller tiếp theo
     * @return Response - Response redirect hoặc tiếp tục xử lý request
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra user đã đăng nhập chưa (kiểm tra session có key 'users' không)
        if (!Session::has('users')) {
            // Nếu chưa đăng nhập, redirect đến trang đăng nhập admin với thông báo lỗi
            return redirect()->route('admin.auth.login')->with('error', 'Vui lòng đăng nhập để truy cập trang admin');
        }

        // Lấy thông tin user từ database theo username trong session
        $user = User::where('taikhoan', Session::get('users'))->first();
        
        // Nếu không tìm thấy user trong database (có thể user đã bị xóa)
        if (!$user) {
            // Xóa session để đảm bảo tính nhất quán
            Session::forget(['users', 'user_id']);
            // Redirect đến trang đăng nhập admin với thông báo lỗi
            return redirect()->route('admin.auth.login')->with('error', 'Không tìm thấy thông tin người dùng');
        }

        // Kiểm tra user có quyền admin không (chucvu == 1)
        if (!$user->isAdmin()) {
            // Nếu không phải admin, redirect về trang chủ với thông báo lỗi
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang admin');
        }
        
        // Nếu tất cả kiểm tra đều pass, tiếp tục xử lý request (cho phép truy cập)
        return $next($request);
    }
}

