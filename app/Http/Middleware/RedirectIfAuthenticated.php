<?php
// Khai báo namespace cho Middleware này - thuộc App\Http\Middleware
namespace App\Http\Middleware;

// Import các class cần thiết
use App\Providers\RouteServiceProvider; // ServiceProvider (không sử dụng trong code này)
use App\Models\User; // Model User để kiểm tra quyền admin
use Closure; // Closure để chuyển request đến middleware tiếp theo
use Illuminate\Http\Request; // Class xử lý HTTP request
use Illuminate\Support\Facades\Auth; // Facade Auth (không sử dụng trong code này)
use Illuminate\Support\Facades\Session; // Facade để làm việc với session
use Symfony\Component\HttpFoundation\Response; // Class Response của Symfony

/**
 * Class RedirectIfAuthenticated
 * Middleware redirect nếu user đã đăng nhập
 * Dùng cho các trang login/register - nếu đã đăng nhập thì redirect về trang chủ
 */
class RedirectIfAuthenticated
{
    /**
     * Xử lý incoming request
     * Kiểm tra user đã đăng nhập chưa, nếu đã đăng nhập thì redirect
     *
     * @param  Request  $request - HTTP request hiện tại
     * @param  Closure  $next - Closure để chuyển request đến middleware tiếp theo
     * @param  string ...$guards - Các guard để kiểm tra (không sử dụng trong code này)
     * @return Response - Response redirect hoặc response từ middleware tiếp theo
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        // Nếu không có guards, dùng [null] làm mặc định
        $guards = empty($guards) ? [null] : $guards;

        // Duyệt qua các guards (mặc dù không sử dụng trong code này)
        foreach ($guards as $guard) {
            // Kiểm tra user đã đăng nhập chưa (có session 'users')
            if (session()->has('users')) {
                // Xử lý đặc biệt cho admin login route
                // Cho phép truy cập trang admin login ngay cả khi đã đăng nhập (như user thường)
                // Chỉ redirect nếu user đã đăng nhập như admin
                if ($request->routeIs('admin.auth.login') || $request->is('admin/login')) {
                    // Tìm user trong database theo username trong session
                    $user = User::where('taikhoan', Session::get('users'))->first();
                    // Chỉ redirect nếu user là admin (đến admin dashboard)
                    // Nếu user là user thường, cho phép truy cập trang admin login
                    if ($user && $user->isAdmin()) {
                        return redirect()->route('admin.dashboard'); // Redirect đến admin dashboard
                    }
                    // Nếu là user thường, cho phép truy cập trang admin login
                    return $next($request);
                }
                
                // Đối với các routes khác (public login/register), redirect về trang chủ nếu đã đăng nhập
                return redirect()->route('home');
            }
        }

        // Nếu chưa đăng nhập, cho phép tiếp tục request
        return $next($request);
    }
}

