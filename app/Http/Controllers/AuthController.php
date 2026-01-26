<?php
// Khai báo namespace cho Controller này - thuộc App\Http\Controllers
namespace App\Http\Controllers;

// Import các class cần thiết
use App\Models\User; // Model quản lý người dùng
use App\Mail\ForgotPasswordMail; // Class gửi email quên mật khẩu
use Illuminate\Http\Request; // Class xử lý HTTP request
use Illuminate\Support\Facades\Log; // Facade để ghi log
use Illuminate\Support\Facades\Mail; // Facade để gửi email
use Illuminate\Support\Facades\DB; // Facade để thao tác database
use Illuminate\Support\Facades\Hash; // Facade để hash mật khẩu
use Illuminate\Support\Str; // Helper class để tạo chuỗi ngẫu nhiên

/**
 * Class AuthController
 * Controller xử lý các thao tác xác thực: đăng nhập, đăng ký, quên mật khẩu
 */
class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập (dành cho cả user và admin)
     * 
     * @return \Illuminate\View\View - View form đăng nhập
     */
    public function showLogin()
    {
        // Trả về view đăng nhập
        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập
     * Kiểm tra thông tin đăng nhập và tạo session cho người dùng
     * 
     * @param Request $request - HTTP request chứa taikhoan và matkhau
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'taikhoan' => 'required', // Tài khoản bắt buộc
            'matkhau' => 'required', // Mật khẩu bắt buộc
        ]);

        // Kiểm tra thông tin đăng nhập bằng phương thức static của User model
        // verifyCredentials sẽ hash mật khẩu MD5 và so sánh với database
        if (User::verifyCredentials($request->taikhoan, $request->matkhau)) {
            // Tìm thông tin user trong database theo username
            $user = User::findByUsername($request->taikhoan);
            
            // Nếu tìm thấy user
            if ($user) {
                // Lưu session (cho cả user và admin)
                // Lưu username vào session với key 'users'
                session(['users' => $user->taikhoan]);
                // Lưu user ID vào session với key 'user_id'
                session(['user_id' => $user->id]);
                
                // Đảm bảo session được lưu ngay lập tức (quan trọng cho AJAX requests)
                session()->save();
                
                // Ghi log để debug - theo dõi quá trình đăng nhập
                \Illuminate\Support\Facades\Log::info('User login - Session saved', [
                    'username' => $user->taikhoan, // Username đã đăng nhập
                    'user_id' => $user->id, // ID người dùng
                    'session_id' => session()->getId(), // ID của session
                    'has_users' => session()->has('users'), // Kiểm tra session có key 'users' không
                    'users_value' => session('users') // Giá trị của key 'users' trong session
                ]);
                
                // Nếu là AJAX request (đăng nhập từ modal/popup)
                if ($request->ajax()) {
                    // Trả về JSON response với script để hiển thị thông báo và redirect
                    return response()->json([
                        'success' => true,
                        'message' => 'Đăng nhập thành công!',
                        'html' => '<script>
                            toastr.success("Đăng Nhập Thành Công!", "Thông Báo");
                            if (typeof(Storage) !== "undefined") {
                                sessionStorage.removeItem("hideWelcomeModal");
                            }
                        </script><script>setTimeout(function(){ window.location.href = "/"; }, 1000);</script>'
                    ]);
                }
                
                // Nếu không phải AJAX request, redirect về trang chủ với thông báo thành công
                return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
            }
        }

        // Nếu thông tin đăng nhập không hợp lệ và là AJAX request
        if ($request->ajax()) {
            // Trả về JSON response lỗi với script hiển thị thông báo
            return response()->json([
                'success' => false,
                'message' => 'Thông tin đăng nhập không hợp lệ!',
                'html' => '<script>toastr.error("Thông Tin Đăng Nhập Không Hợp Lệ!", "Thông Báo");</script>'
            ]);
        }

        // Nếu không phải AJAX request, quay lại trang trước với thông báo lỗi
        return back()->withErrors(['taikhoan' => 'Thông tin đăng nhập không hợp lệ!']);
    }

    /**
     * Hiển thị form đăng ký
     * 
     * @return \Illuminate\View\View - View form đăng ký
     */
    public function showRegister()
    {
        // Trả về view đăng ký
        return view('auth.register');
    }

    /**
     * Xử lý đăng ký tài khoản mới
     * Validate thông tin, tạo user mới và tự động đăng nhập
     * 
     * @param Request $request - HTTP request chứa taikhoan, password, password2, email
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // Validate dữ liệu đầu vào từ form với các rule cụ thể
        $request->validate([
            'taikhoan' => 'required|unique:users,taikhoan|regex:/^[a-zA-Z0-9_]{3,20}$/', 
            // Tài khoản: bắt buộc, duy nhất trong bảng users, chỉ chữ/số/gạch dưới, 3-20 ký tự
            'password' => 'required|min:8|regex:/^(?=.*[A-Za-z])(?=.*\d)/', 
            // Mật khẩu: bắt buộc, tối thiểu 8 ký tự, phải có cả chữ và số
            'password2' => 'required|same:password', 
            // Xác nhận mật khẩu: bắt buộc, phải giống password
            'email' => [
                'required',
                'email',
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._-]+@gmail\.com$/'
            ],
            // Email: bắt buộc, định dạng email hợp lệ, chỉ chấp nhận @gmail.com, duy nhất trong bảng users
        ], [
            // Thông báo lỗi tùy chỉnh cho từng rule - Tài khoản
            'taikhoan.required' => 'Vui lòng nhập tên đăng nhập!',
            'taikhoan.unique' => 'Tên đăng nhập đã tồn tại!',
            'taikhoan.regex' => 'Tên đăng nhập chỉ gồm chữ, số, gạch dưới (3-20 ký tự)',
            // Thông báo lỗi tùy chỉnh cho từng rule - Mật khẩu
            'password.required' => 'Vui lòng nhập mật khẩu!',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự!',
            'password.regex' => 'Mật khẩu phải gồm cả chữ và số!',
            // Thông báo lỗi tùy chỉnh cho từng rule - Xác nhận mật khẩu
            'password2.required' => 'Vui lòng xác nhận mật khẩu!',
            'password2.same' => 'Mật khẩu xác nhận không khớp!',
            // Thông báo lỗi tùy chỉnh cho từng rule - Email
            'email.required' => 'Vui lòng nhập email!',
            'email.email' => 'Email không hợp lệ! Email phải có định dạng:ví dụ: user@gmail.com',
            'email.regex' => 'Chỉ chấp nhận email Gmail (@gmail.com)!',
            'email.unique' => 'Email đã được sử dụng!'
        ]);

        // Kiểm tra username và password không được giống nhau (bảo mật)
        if ($request->taikhoan == $request->password) {
            // Nếu là AJAX request, trả về JSON response lỗi
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tên đăng nhập và mật khẩu phải khác nhau!',
                    'html' => '<script>toastr.error("Tên Đăng Nhập Và Mật Khẩu Phải Khác Nhau!", "Thông Báo");</script>'
                ]);
            }
            // Nếu không phải AJAX, quay lại với thông báo lỗi
            return back()->withErrors(['taikhoan' => 'Tên đăng nhập và mật khẩu phải khác nhau!']);
        }

        // Tạo chuỗi thời gian định dạng Việt Nam
        $time = now()->format('d/m/Y - H:i:s');
        
        // Tạo user mới trong database
        $user = User::create([
            'taikhoan' => $request->taikhoan, // Tên đăng nhập
            'matkhau' => md5($request->password), // Mật khẩu được hash MD5 (giữ nguyên như code cũ)
            'email' => $request->email, // Email
            'tien' => 0, // Số dư ban đầu = 0
            'chucvu' => 0, // Chức vụ: 0 = User thường, 1 = Admin
            'time' => $time // Thời gian đăng ký
        ]);

        // Tự động đăng nhập sau khi đăng ký thành công
        // Lưu username vào session với key 'users'
        session(['users' => $user->taikhoan]);
        // Lưu user ID vào session với key 'user_id'
        session(['user_id' => $user->id]);

        // Nếu là AJAX request, trả về JSON response thành công với script redirect
        if ($request->ajax()) {
            $homeUrl = route('home'); // Lấy URL trang chủ
            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công!',
                'html' => '<script>toastr.success("Đăng Ký Thành Công!", "Thông Báo");</script><script>setTimeout(function(){ window.location.href = "'.$homeUrl.'"; }, 1000);</script>'
            ]);
        }

        // Nếu không phải AJAX request, redirect về trang chủ với thông báo thành công
        return redirect()->route('home')->with('success', 'Đăng ký thành công!');
    }

    /**
     * Xử lý đăng xuất
     * Xóa session và chuyển hướng về trang chủ
     * 
     * @param Request $request - HTTP request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // Lấy username từ session để ghi log (mặc định 'Unknown' nếu không có)
        $username = session('users', 'Unknown');
        // Ghi log hành động đăng xuất
        Log::info("User logout: {$username} at " . now()->format('Y-m-d H:i:s'));
        
        // Xóa các key cụ thể trong session
        session()->forget(['users', 'user_id']);
        // Vô hiệu hóa toàn bộ session (xóa tất cả dữ liệu)
        $request->session()->invalidate();
        // Tạo lại CSRF token mới (bảo mật)
        $request->session()->regenerateToken();
        
        // Redirect về trang chủ với thông báo thành công
        return redirect()->route('home')->with('success', 'Đăng xuất thành công!');
    }

    /**
     * Hiển thị form quên mật khẩu
     * 
     * @return \Illuminate\View\View - View form quên mật khẩu
     */
    public function showForgotPassword()
    {
        // Trả về view form quên mật khẩu
        return view('auth.forgot-password');
    }

    /**
     * Gửi email quên mật khẩu
     * Tạo token reset password và gửi email chứa link reset
     * 
     * @param Request $request - HTTP request chứa email
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function forgotPassword(Request $request)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'email' => 'required|email|exists:users,email', 
            // Email: bắt buộc, định dạng email hợp lệ, phải tồn tại trong bảng users
        ], [
            // Thông báo lỗi tùy chỉnh
            'email.required' => 'Vui lòng nhập email!',
            'email.email' => 'Email không hợp lệ!',
            'email.exists' => 'Email không tồn tại trong hệ thống!',
        ]);

        // Tìm user trong database theo email
        $user = User::where('email', $request->email)->first();
        
        // Nếu không tìm thấy user (double check)
        if (!$user) {
            // Nếu là AJAX request, trả về JSON response lỗi
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email không tồn tại trong hệ thống!',
                    'html' => '<script>toastr.error("Email Không Tồn Tại Trong Hệ Thống!", "Thông Báo");</script>'
                ]);
            }
            // Nếu không phải AJAX, quay lại với thông báo lỗi
            return back()->withErrors(['email' => 'Email không tồn tại trong hệ thống!']);
        }

        // Tạo token reset password ngẫu nhiên (60 ký tự)
        $token = Str::random(60);
        
        // Xóa token cũ nếu có (để tránh conflict và đảm bảo chỉ có 1 token hợp lệ)
        DB::table('password_resets')->where('email', $user->email)->delete();
        
        // Lưu token mới vào database (đã được hash bằng bcrypt)
        DB::table('password_resets')->insert([
            'email' => $user->email, // Email của user
            'token' => Hash::make($token), // Token đã được hash (bcrypt)
            'created_at' => now() // Thời gian tạo token
        ]);
        
        // Ghi log để debug - theo dõi quá trình tạo token
        Log::info('Password Reset - Token created', [
            'email' => $user->email,
            'token_length' => strlen($token), // Độ dài token (60 ký tự)
            'created_at' => now()
        ]);

        // Gửi email chứa link reset password
        try {
            // Gửi email sử dụng ForgotPasswordMail class
            Mail::to($user->email)->send(new ForgotPasswordMail($user, $token));
            
            // Nếu là AJAX request, trả về JSON response thành công
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email đặt lại mật khẩu đã được gửi! Vui lòng kiểm tra hộp thư.',
                    'html' => '<script>toastr.success("Email Đặt Lại Mật Khẩu Đã Được Gửi! Vui Lòng Kiểm Tra Hộp Thư.", "Thông Báo");</script>'
                ]);
            }
            
            // Nếu không phải AJAX, quay lại với thông báo thành công
            return back()->with('success', 'Email đặt lại mật khẩu đã được gửi! Vui lòng kiểm tra hộp thư.');
        } catch (\Exception $e) {
            // Nếu có lỗi khi gửi email, ghi log lỗi
            Log::error('Email error: ' . $e->getMessage());
            
            // Nếu là AJAX request, trả về JSON response lỗi
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể gửi email. Vui lòng thử lại sau!',
                    'html' => '<script>toastr.error("Không Thể Gửi Email. Vui Lòng Thử Lại Sau!", "Thông Báo");</script>'
                ]);
            }
            
            // Nếu không phải AJAX, quay lại với thông báo lỗi
            return back()->withErrors(['email' => 'Không thể gửi email. Vui lòng thử lại sau!']);
        }
    }

    /**
     * Hiển thị form reset password
     * Kiểm tra token hợp lệ và hiển thị form đặt lại mật khẩu
     * 
     * @param Request $request - HTTP request chứa token và email từ URL query
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showResetPassword(Request $request)
    {
        // Lấy token và email từ URL (có thể đã được encode)
        $tokenRaw = $request->query('token', '');
        $emailRaw = $request->query('email', '');
        
        // Decode token và email
        $token = urldecode($tokenRaw);
        $email = urldecode($emailRaw);

        if (!$token || !$email) {
            return redirect()->route('login')->with('error', 'Link không hợp lệ!');
        }

        // Log để debug
        Log::info('Password Reset - Show Form', [
            'email_raw' => $emailRaw,
            'email_decoded' => $email,
            'token_length' => strlen($token),
            'token_preview' => substr($token, 0, 20) . '...',
            'all_emails_in_db' => DB::table('password_resets')->pluck('email')->toArray()
        ]);

        // Kiểm tra token trong database
        $passwordReset = DB::table('password_resets')
            ->where('email', $email)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$passwordReset) {
            // Log để debug
            Log::warning('Password Reset - Token not found', [
                'email' => $email,
                'email_raw' => $emailRaw,
                'all_emails_in_db' => DB::table('password_resets')->pluck('email')->toArray()
            ]);
            
            return view('auth.reset-password', [
                'token' => $token,
                'email' => $email,
                'error' => 'Token không tồn tại trong hệ thống! Vui lòng yêu cầu lại email.'
            ]);
        }

        // Kiểm tra token hết hạn (60 phút)
        $minutesDiff = now()->diffInMinutes($passwordReset->created_at);
        Log::info('Password Reset - Token found, checking expiry', [
            'email' => $email,
            'minutes_diff' => $minutesDiff,
            'created_at' => $passwordReset->created_at
        ]);
        
        if ($minutesDiff > 60) {
            DB::table('password_resets')->where('email', $email)->delete();
            return view('auth.reset-password', [
                'token' => $token,
                'email' => $email,
                'error' => 'Token đã hết hạn! Vui lòng yêu cầu lại.'
            ]);
        }

        // Kiểm tra token có đúng không
        $tokenValid = Hash::check($token, $passwordReset->token);
        Log::info('Password Reset - Token validation', [
            'email' => $email,
            'token_valid' => $tokenValid,
            'token_length' => strlen($token),
            'hash_preview' => substr($passwordReset->token, 0, 30) . '...'
        ]);
        
        if (!$tokenValid) {
            return view('auth.reset-password', [
                'token' => $token,
                'email' => $email,
                'error' => 'Token không hợp lệ! Vui lòng kiểm tra lại link trong email hoặc yêu cầu email mới.'
            ]);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|regex:/^(?=.*[A-Za-z])(?=.*\d)/',
            'password_confirmation' => 'required|same:password',
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu mới!',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự!',
            'password.regex' => 'Mật khẩu phải gồm chữ và số!',
            'password_confirmation.required' => 'Vui lòng xác nhận mật khẩu!',
            'password_confirmation.same' => 'Mật khẩu xác nhận không khớp!',
        ]);

        // Lấy email và token từ request
        $emailRaw = $request->input('email', '');
        $tokenRaw = $request->input('token', '');
        
        // Decode email và token nếu bị encode
        $email = urldecode($emailRaw);
        $token = urldecode($tokenRaw);
        
        // Log để debug
        Log::info('Password Reset - Process', [
            'email_raw' => $emailRaw,
            'email_decoded' => $email,
            'token_raw' => $tokenRaw ? substr($tokenRaw, 0, 20) . '...' : 'empty',
            'token_decoded' => $token ? substr($token, 0, 20) . '...' : 'empty',
            'token_length' => strlen($token),
            'all_emails_in_db' => DB::table('password_resets')->pluck('email')->toArray()
        ]);
        
        // Kiểm tra token - thử cả email gốc và email đã decode
        $passwordReset = DB::table('password_resets')
            ->where('email', $email)
            ->first();

        // Nếu không tìm thấy, thử tìm với email gốc từ request
        if (!$passwordReset) {
            $emailOriginal = $request->email;
            if ($emailOriginal && $emailOriginal !== $email) {
                $passwordReset = DB::table('password_resets')
                    ->where('email', urldecode($emailOriginal))
                    ->first();
                if ($passwordReset) {
                    $email = urldecode($emailOriginal);
                }
            }
        }

        if (!$passwordReset) {
            // Log để debug
            Log::warning('Password Reset - Token not found in process', [
                'email' => $email,
                'all_tokens' => DB::table('password_resets')->pluck('email')->toArray()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token không tồn tại trong hệ thống! Vui lòng yêu cầu lại email.',
                    'html' => '<script>toastr.error("Token Không Tồn Tại Trong Hệ Thống! Vui Lòng Yêu Cầu Lại Email.", "Thông Báo");</script>'
                ]);
            }
            return back()->withErrors(['token' => 'Token không tồn tại trong hệ thống! Vui lòng yêu cầu lại email.']);
        }

        // Kiểm tra token hết hạn (60 phút)
        $minutesDiff = now()->diffInMinutes($passwordReset->created_at);
        if ($minutesDiff > 60) {
            DB::table('password_resets')->where('email', $email)->delete();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token đã hết hạn! Vui lòng yêu cầu lại.',
                    'html' => '<script>toastr.error("Token Đã Hết Hạn! Vui Lòng Yêu Cầu Lại.", "Thông Báo");</script>'
                ]);
            }
            return back()->withErrors(['token' => 'Token đã hết hạn! Vui lòng yêu cầu lại.']);
        }

        // Kiểm tra token có đúng không
        if (!Hash::check($token, $passwordReset->token)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token không hợp lệ! Vui lòng kiểm tra lại link trong email.',
                    'html' => '<script>toastr.error("Token Không Hợp Lệ! Vui Lòng Kiểm Tra Lại Link Trong Email.", "Thông Báo");</script>'
                ]);
            }
            return back()->withErrors(['token' => 'Token không hợp lệ! Vui lòng kiểm tra lại link trong email.']);
        }

        // Cập nhật mật khẩu
        $user = User::where('email', $email)->first();
        if (!$user) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy tài khoản!',
                    'html' => '<script>toastr.error("Không Tìm Thấy Tài Khoản!", "Thông Báo");</script>'
                ]);
            }
            return back()->withErrors(['email' => 'Không tìm thấy tài khoản!']);
        }

        // Cập nhật mật khẩu (giữ nguyên MD5 như code cũ)
        $user->matkhau = md5($request->password);
        $user->save();

        // Xóa token
        DB::table('password_resets')->where('email', $email)->delete();

        if ($request->ajax()) {
            $loginUrl = route('login');
            return response()->json([
                'success' => true,
                'message' => 'Đặt lại mật khẩu thành công!',
                'html' => '<script>toastr.success("Đặt Lại Mật Khẩu Thành Công!", "Thông Báo");</script><script>setTimeout(function(){ window.location.href = "'.$loginUrl.'"; }, 1500);</script>'
            ]);
        }

        return redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập lại.');
    }
}

