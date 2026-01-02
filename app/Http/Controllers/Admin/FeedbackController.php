<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Settings;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Hiển thị danh sách feedback
     */
    public function index()
    {
        $feedbacks = Feedback::orderBy('id', 'desc')->get();
        $pendingCount = Feedback::where('status', 0)->count();
        
        return view('admin.feedback.index', compact('feedbacks', 'pendingCount'));
    }

    /**
     * Hiển thị chi tiết feedback
     */
    public function show($id)
    {
        $feedback = Feedback::findOrFail($id);
        
        return view('admin.feedback.show', compact('feedback'));
    }

    /**
     * Trả lời feedback
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'admin_reply' => 'required|string',
        ]);

        $feedback = Feedback::findOrFail($id);
        $replyTime = date('d/m/Y - H:i:s');
        
        $feedback->admin_reply = $request->admin_reply;
        $feedback->reply_time = $replyTime;
        $feedback->status = 1; // Đã trả lời
        $feedback->save();

        // Gửi tin nhắn qua Telegram nếu có chat ID
        $telegramSent = false;
        if (!empty($feedback->telegram_chat_id)) {
            $settings = Settings::first();
            $telegramBotToken = $settings->telegram_bot_token ?? '';
            
            if (!empty($telegramBotToken)) {
                $message = "✅ PHẢN HỒI TỪ ADMIN\n\n";
                $message .= $request->admin_reply . "\n\n";
                $message .= "⏰ " . $replyTime;
                
                $telegramSent = $this->sendTelegramMessage($telegramBotToken, $feedback->telegram_chat_id, $message);
            }
        }

        $successMsg = "Đã gửi phản hồi thành công!";
        if ($telegramSent) {
            $successMsg .= " Tin nhắn đã được gửi qua Telegram.";
        } elseif (!empty($feedback->telegram_chat_id)) {
            $successMsg .= " Lưu ý: Không thể gửi qua Telegram.";
        }

        return redirect()->route('admin.feedback.index')
            ->with('success', $successMsg);
    }

    /**
     * Cập nhật trạng thái feedback (đánh dấu đã đọc)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|integer|in:0,1,2',
        ]);

        $feedback = Feedback::findOrFail($id);
        $feedback->status = $request->status;
        $feedback->save();

        return redirect()->route('admin.feedback.index')
            ->with('success', 'Đã cập nhật trạng thái thành công!');
    }

    /**
     * Gửi tin nhắn qua Telegram
     */
    private function sendTelegramMessage(string $token, string $chatId, string $message): bool
    {
        if (empty($token) || empty($chatId)) {
            return false;
        }
        
        $url = "https://api.telegram.org/bot{$token}/sendMessage";
        
        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode === 200;
    }
}

