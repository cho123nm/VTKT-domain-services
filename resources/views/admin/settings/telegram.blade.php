@extends('layouts.admin')

@section('title', 'Cài Đặt Telegram Bot')

@section('content')
<div class="col-span-12 mt-6">
    <div class="intro-y block sm:flex items-center h-10">
        <h2 class="text-lg font-medium truncate mr-5">Cài Đặt Telegram Bot</h2>
    </div>
    
    <div class="intro-y box mt-5">
        <div class="p-5">
            @if(session('success'))
                <div class="alert alert-success mb-4">
                    <script>
                        swal("Thông Báo", "{{ session('success') }}", "success");
                    </script>
                </div>
            @endif
            
            <form method="POST" class="form">
                @csrf
                <div class="mb-4">
                    <label class="form-label">Telegram Bot Token <span class="text-danger">*</span></label>
                    <input type="text" name="telegram_bot_token" class="form-control" value="{{ $telegramBotToken }}" placeholder="Nhập Bot Token từ @BotFather" required>
                    <div class="text-gray-500 text-sm mt-1">
                        Lấy Bot Token từ <a href="https://t.me/BotFather" target="_blank">@BotFather</a> trên Telegram
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Telegram Admin Chat ID <span class="text-danger">*</span></label>
                    <input type="text" name="telegram_admin_chat_id" class="form-control" value="{{ $telegramAdminChatId }}" placeholder="Nhập Chat ID của bạn" required>
                    <div class="text-gray-500 text-sm mt-1">
                        Lấy Chat ID từ <a href="https://t.me/userinfobot" target="_blank">@userinfobot</a> trên Telegram
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Webhook URL</label>
                    @if($isLocalhost)
                        <div class="alert alert-danger mb-3">
                            <strong>⚠️ CẢNH BÁO:</strong> Bạn đang dùng localhost! Telegram không thể truy cập localhost. 
                            Bạn cần dùng <a href="https://ngrok.com" target="_blank">ngrok</a> hoặc domain thật với HTTPS.
                        </div>
                    @endif
                    <div class="input-group">
                        <input type="text" class="form-control {{ $isLocalhost ? 'border-danger' : '' }}" value="{{ $webhookUrl }}" readonly>
                        <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard('{{ $webhookUrl }}')">
                            Copy
                        </button>
                    </div>
                    <div class="text-gray-500 text-sm mt-1">
                        Cấu hình webhook này trong BotFather: <code>/setwebhook</code> và dán URL trên
                        @if($isLocalhost)
                            <br><strong class="text-danger">Lưu ý: URL này không hoạt động! Cần dùng ngrok hoặc domain thật.</strong>
                        @endif
                    </div>
                </div>
                
                <div class="alert alert-warning">
                    <h4 class="alert-heading">⚠️ QUAN TRỌNG - Webhook URL:</h4>
                    <p><strong>localhost KHÔNG hoạt động với Telegram!</strong></p>
                    <p>Bạn cần:</p>
                    <ol class="mb-0">
                        <li><strong>Nếu đang test local:</strong> Dùng <a href="https://ngrok.com" target="_blank">ngrok</a> để tạo tunnel:
                            <ul>
                                <li>Tải ngrok: <code>ngrok http 80</code> (hoặc port của bạn)</li>
                                <li>Copy URL HTTPS từ ngrok (ví dụ: <code>https://abc123.ngrok.io</code>)</li>
                                <li>Webhook URL sẽ là: <code>https://abc123.ngrok.io/domain/api/telegram/webhook</code></li>
                            </ul>
                        </li>
                        <li><strong>Nếu đã có domain:</strong> Đảm bảo có SSL (HTTPS) và dùng domain thật</li>
                        <li>Thiết lập webhook: Gửi lệnh <code>/setwebhook</code> cho @BotFather kèm URL webhook</li>
                        <li>Kiểm tra webhook: Gửi lệnh <code>/getwebhookinfo</code> cho @BotFather</li>
                    </ol>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Cập Nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        swal("Thành Công", "Đã copy URL vào clipboard!", "success");
    }, function() {
        swal("Lỗi", "Không thể copy!", "error");
    });
}
</script>
@endsection

