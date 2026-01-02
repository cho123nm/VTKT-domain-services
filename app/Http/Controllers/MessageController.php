<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display user's feedback history (messages from admin)
     */
    public function index()
    {
        // Check if user is logged in
        if (!session()->has('users')) {
            return redirect()->route('login');
        }

        // Get current user
        $user = User::where('taikhoan', session('users'))->first();
        if (!$user) {
            return redirect()->route('login');
        }

        // Get user's feedback history where admin has replied
        $userFeedbacks = Feedback::where('uid', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        // Count unread messages (status = 1 means admin replied but user hasn't read)
        $unreadCount = Feedback::where('uid', $user->id)
            ->where('status', 1)
            ->whereNotNull('admin_reply')
            ->count();

        return view('pages.messages', compact('user', 'userFeedbacks', 'unreadCount'));
    }

    /**
     * Mark feedback as read
     */
    public function markAsRead($id)
    {
        // Check if user is logged in
        if (!session()->has('users')) {
            return redirect()->route('login');
        }

        // Get current user
        $user = User::where('taikhoan', session('users'))->first();
        if (!$user) {
            return redirect()->route('login');
        }

        // Find feedback and verify ownership
        $feedback = Feedback::where('id', $id)
            ->where('uid', $user->id)
            ->first();

        if ($feedback) {
            // Update status to 2 (read)
            $feedback->status = 2;
            $feedback->save();
        }

        return redirect()->route('messages.index');
    }
}
