<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Display user profile page
     */
    public function index()
    {
        // Check if user is logged in
        if (!Session::has('users')) {
            return redirect()->route('login');
        }

        $currentUsername = Session::get('users');
        $user = User::findByUsername($currentUsername);

        if (!$user) {
            Session::forget('users');
            return redirect()->route('login');
        }

        // Get user statistics
        $userId = $user->id;
        $waitingOrders = History::where('uid', $userId)->where('status', 0)->count();
        $completedOrders = History::where('uid', $userId)->where('status', 1)->count();
        
        // Get recent orders (last 5)
        $recentOrders = History::where('uid', $userId)
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        return view('pages.profile', compact('user', 'waitingOrders', 'completedOrders', 'recentOrders'));
    }

    /**
     * Update user profile information
     */
    public function update(Request $request)
    {
        // Check if user is logged in
        if (!Session::has('users')) {
            return redirect()->route('login');
        }

        $currentUsername = Session::get('users');
        $user = User::findByUsername($currentUsername);

        if (!$user) {
            Session::forget('users');
            return redirect()->route('login');
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'username' => 'required|min:3|max:20|regex:/^[a-zA-Z0-9_]+$/',
        ], [
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không hợp lệ',
            'username.required' => 'Tên đăng nhập không được để trống',
            'username.min' => 'Tên đăng nhập phải có ít nhất 3 ký tự',
            'username.max' => 'Tên đăng nhập không được quá 20 ký tự',
            'username.regex' => 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', implode('<br>', $validator->errors()->all()));
        }

        $newEmail = trim($request->input('email'));
        $newUsername = trim($request->input('username'));

        // Check if email already exists for another user
        $existingUserByEmail = User::where('email', $newEmail)
            ->where('id', '!=', $user->id)
            ->first();

        if ($existingUserByEmail) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Email này đã được sử dụng bởi tài khoản khác');
        }

        // Check if username already exists for another user
        $existingUserByUsername = User::where('taikhoan', $newUsername)
            ->where('id', '!=', $user->id)
            ->first();

        if ($existingUserByUsername) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tên đăng nhập này đã được sử dụng bởi tài khoản khác');
        }

        // Update user information
        $user->email = $newEmail;
        $user->taikhoan = $newUsername;

        if ($user->save()) {
            // Update session with new username
            Session::put('users', $newUsername);

            return redirect()->route('profile')
                ->with('success', 'Cập nhật thông tin thành công!');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật thông tin!');
        }
    }
}
