<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\ForgotPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập (dành cho cả user và admin)
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        $request->validate([
            'taikhoan' => 'required',
            'matkhau' => 'required',
        ]);

        if (User::verifyCredentials($request->taikhoan, $request->matkhau)) {
            $user = User::findByUsername($request->taikhoan);
            if ($user) {
                // Lưu session (cho cả user và admin)
                session(['users' => $user->taikhoan]);
                session(['user_id' => $user->id]);
                
                // Đảm bảo session được lưu ngay lập tức
                session()->save();
                
                // Log để debug
                \Illuminate\Support\Facades\Log::info('User login - Session saved', [
                    'username' => $user->taikhoan,
                    'user_id' => $user->id,
                    'session_id' => session()->getId(),
                    'has_users' => session()->has('users'),
                    'users_value' => session('users')
                ]);
                
                // Nếu là AJAX request
                if ($request->ajax()) {
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
                
                return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
            }
        }

        // Nếu là AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Thông tin đăng nhập không hợp lệ!',
                'html' => '<script>toastr.error("Thông Tin Đăng Nhập Không Hợp Lệ!", "Thông Báo");</script>'
            ]);
        }

        return back()->withErrors(['taikhoan' => 'Thông tin đăng nhập không hợp lệ!']);
    }

    /**
     * Hiển thị form đăng ký
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Xử lý đăng ký
     */
    public function register(Request $request)
    {
        $request->validate([
            'taikhoan' => 'required|unique:users,taikhoan|regex:/^[a-zA-Z0-9_]{3,20}$/',
            'password' => 'required|min:8|regex:/^(?=.*[A-Za-z])(?=.*\d)/',
            'password2' => 'required|same:password',
            'email' => 'required|email|unique:users,email',
        ], [
            'taikhoan.regex' => 'Tên đăng nhập chỉ gồm chữ, số, gạch dưới (3-20 ký tự)',
            'password.regex' => 'Mật khẩu tối thiểu 8 ký tự, gồm chữ và số',
            'password2.same' => 'Mật khẩu xác nhận không khớp'
        ]);

        // Kiểm tra username và password không được giống nhau
        if ($request->taikhoan == $request->password) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tên đăng nhập và mật khẩu phải khác nhau!',
                    'html' => '<script>toastr.error("Tên Đăng Nhập Và Mật Khẩu Phải Khác Nhau!", "Thông Báo");</script>'
                ]);
            }
            return back()->withErrors(['taikhoan' => 'Tên đăng nhập và mật khẩu phải khác nhau!']);
        }

        $time = now()->format('d/m/Y - H:i:s');
        
        $user = User::create([
            'taikhoan' => $request->taikhoan,
            'matkhau' => md5($request->password), // Giữ nguyên MD5 như code cũ
            'email' => $request->email,
            'tien' => 0,
            'chucvu' => 0,
            'time' => $time
        ]);

        // Set session sau khi đăng ký thành công
        session(['users' => $user->taikhoan]);
        session(['user_id' => $user->id]);

        if ($request->ajax()) {
            $homeUrl = route('home');
            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công!',
                'html' => '<script>toastr.success("Đăng Ký Thành Công!", "Thông Báo");</script><script>setTimeout(function(){ window.location.href = "'.$homeUrl.'"; }, 1000);</script>'
            ]);
        }

        return redirect()->route('home')->with('success', 'Đăng ký thành công!');
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        // Log the logout action
        $username = session('users', 'Unknown');
        Log::info("User logout: {$username} at " . now()->format('Y-m-d H:i:s'));
        
        // Clear session
        session()->forget(['users', 'user_id']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home')->with('success', 'Đăng xuất thành công!');
    }

    /**
     * Hiển thị form quên mật khẩu
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Gửi email quên mật khẩu
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Vui lòng nhập email!',
            'email.email' => 'Email không hợp lệ!',
            'email.exists' => 'Email không tồn tại trong hệ thống!',
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email không tồn tại trong hệ thống!',
                    'html' => '<script>toastr.error("Email Không Tồn Tại Trong Hệ Thống!", "Thông Báo");</script>'
                ]);
            }
            return back()->withErrors(['email' => 'Email không tồn tại trong hệ thống!']);
        }

        // Tạo token reset password
        $token = Str::random(60);
        
        // Xóa token cũ nếu có (để tránh conflict)
        DB::table('password_resets')->where('email', $user->email)->delete();
        
        // Lưu token mới vào database
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now()
        ]);
        
        // Log để debug
        Log::info('Password Reset - Token created', [
            'email' => $user->email,
            'token_length' => strlen($token),
            'created_at' => now()
        ]);

        // Gửi email
        try {
            Mail::to($user->email)->send(new ForgotPasswordMail($user, $token));
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email đặt lại mật khẩu đã được gửi! Vui lòng kiểm tra hộp thư.',
                    'html' => '<script>toastr.success("Email Đặt Lại Mật Khẩu Đã Được Gửi! Vui Lòng Kiểm Tra Hộp Thư.", "Thông Báo");</script>'
                ]);
            }
            
            return back()->with('success', 'Email đặt lại mật khẩu đã được gửi! Vui lòng kiểm tra hộp thư.');
        } catch (\Exception $e) {
            Log::error('Email error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể gửi email. Vui lòng thử lại sau!',
                    'html' => '<script>toastr.error("Không Thể Gửi Email. Vui Lòng Thử Lại Sau!", "Thông Báo");</script>'
                ]);
            }
            
            return back()->withErrors(['email' => 'Không thể gửi email. Vui lòng thử lại sau!']);
        }
    }

    /**
     * Hiển thị form reset password
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

