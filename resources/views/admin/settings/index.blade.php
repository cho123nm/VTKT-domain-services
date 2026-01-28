@extends('layouts.admin')

@section('content')
<div class="intro-y flex items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">
        Cài Đặt Hệ Thống
    </h2>
</div>

@if(session('success'))
<div class="alert alert-success show mb-2 mt-5" role="alert">
    {{ session('success') }}
</div>
@endif

<!-- BEGIN: Settings Tabs -->
<div class="intro-y box mt-5">
    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
        <h2 class="font-medium text-base mr-auto">
            Quản Lý Cài Đặt
        </h2>
    </div>
    <div class="intro-y">
        <ul class="nav nav-tabs flex-col sm:flex-row justify-center lg:justify-start" role="tablist">
            <li id="website-tab" class="nav-item" role="presentation">
                <button class="nav-link w-full py-2 active" data-tw-toggle="pill" data-tw-target="#website" type="button" role="tab" aria-controls="website" aria-selected="true">
                    <i data-lucide="monitor" class="w-4 h-4 mr-2"></i> Cài Đặt Website
                </button>
            </li>
            <li id="telegram-tab" class="nav-item" role="presentation">
                <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#telegram" type="button" role="tab" aria-controls="telegram" aria-selected="false">
                    <i data-lucide="send" class="w-4 h-4 mr-2"></i> Cài Đặt Telegram
                </button>
            </li>
            <li id="contact-tab" class="nav-item" role="presentation">
                <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">
                    <i data-lucide="phone" class="w-4 h-4 mr-2"></i> Thông Tin Liên Hệ
                </button>
            </li>
            <li id="card-tab" class="nav-item" role="presentation">
                <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#card" type="button" role="tab" aria-controls="card" aria-selected="false">
                    <i data-lucide="credit-card" class="w-4 h-4 mr-2"></i> Cài Đặt Gạch Thẻ
                </button>
            </li>
        </ul>
    </div>
    <div class="tab-content p-5">
        <!-- Website Settings Tab -->
        <div id="website" class="tab-pane active" role="tabpanel" aria-labelledby="website-tab">
            <form action="{{ route('admin.settings.website') }}" method="POST">
                @csrf
                <div class="form-inline">
                    <label for="theme" class="form-label sm:w-20">Giao Diện Admin</label>
                    <select class="form-control" name="theme" id="theme">
                        <option value="0" {{ ($settings->theme ?? '0') == '0' ? 'selected' : '' }}>Xanh Dương</option>
                        <option value="1" {{ ($settings->theme ?? '0') == '1' ? 'selected' : '' }}>Xanh Lá Đậm</option>
                        <option value="2" {{ ($settings->theme ?? '0') == '2' ? 'selected' : '' }}>Xanh Dương Sáng</option>
                        <option value="3" {{ ($settings->theme ?? '0') == '3' ? 'selected' : '' }}>Xanh Xám (Khuyên Dùng)</option>
                        <option value="4" {{ ($settings->theme ?? '0') == '4' ? 'selected' : '' }}>Tím</option>
                    </select>
                </div>
                <div class="form-inline mt-5">
                    <label for="title" class="form-label sm:w-20">Title</label>
                    <textarea id="title" name="title" class="form-control" placeholder="Tiêu Đề Trang Web" rows="4">{{ $settings->tieude ?? '' }}</textarea>
                </div>
                <div class="form-inline mt-5">
                    <label for="keywords" class="form-label sm:w-20">Keywords</label>
                    <textarea id="keywords" name="keywords" class="form-control" placeholder="keywords" rows="4">{{ $settings->keywords ?? '' }}</textarea>
                </div>
                <div class="form-inline mt-5">
                    <label for="description" class="form-label sm:w-20">Description</label>
                    <textarea id="description" name="description" class="form-control" placeholder="Mô Tả Trang Web" rows="4">{{ $settings->mota ?? '' }}</textarea>
                </div>
                <div class="form-inline mt-5">
                    <label for="imagebanner" class="form-label sm:w-20">Ảnh Mô Tả Trang Web</label>
                    <textarea id="imagebanner" name="imagebanner" class="form-control" placeholder="Ảnh Mô Tả" rows="4">{{ $settings->imagebanner ?? '' }}</textarea>
                </div>
                <div class="form-inline mt-5">
                    <label for="phone" class="form-label sm:w-20">Số Điện Thoại Zalo</label>
                    <input id="phone" type="text" name="phone" class="form-control" placeholder="Số Điện Thoại Zalo" value="{{ $settings->sodienthoai ?? '' }}">
                </div>
                <div class="form-inline mt-5">
                    <label for="banner" class="form-label sm:w-20">ID Video Banner</label>
                    <input id="banner" type="text" name="banner" class="form-control" placeholder="banner Ở Home" value="{{ $settings->banner ?? '' }}">
                </div>
                <div class="form-inline mt-5">
                    <label for="logo" class="form-label sm:w-20">Logo</label>
                    <input id="logo" type="text" name="logo" class="form-control" placeholder="Ảnh logo" value="{{ $settings->logo ?? '' }}">
                </div>
                <div class="sm:ml-20 sm:pl-5 mt-5">
                    <button type="submit" class="btn btn-primary">Cập Nhật</button>
                </div>
            </form>
        </div>

        <!-- Telegram Settings Tab -->
        <div id="telegram" class="tab-pane" role="tabpanel" aria-labelledby="telegram-tab">
            <form action="{{ route('admin.settings.telegram') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label">Telegram Bot Token <span class="text-danger">*</span></label>
                    <input type="text" name="telegram_bot_token" class="form-control" value="{{ $settings->telegram_bot_token ?? '' }}" placeholder="Nhập Bot Token từ @BotFather" required>
                    <div class="text-slate-500 text-xs mt-1">
                        Lấy Bot Token từ <a href="https://t.me/BotFather" target="_blank" class="text-primary">@BotFather</a> trên Telegram
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Telegram Admin Chat ID <span class="text-danger">*</span></label>
                    <input type="text" name="telegram_admin_chat_id" class="form-control" value="{{ $settings->telegram_admin_chat_id ?? '' }}" placeholder="Nhập Chat ID của bạn" required>
                    <div class="text-slate-500 text-xs mt-1">
                        Lấy Chat ID từ <a href="https://t.me/userinfobot" target="_blank" class="text-primary">@userinfobot</a> trên Telegram
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Webhook URL</label>
                    @php
                        $webhookUrl = url('/telegram/webhook');
                    @endphp
                    <div class="input-group">
                        <input type="text" class="form-control" value="{{ $webhookUrl }}" readonly id="webhookUrl">
                        <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard()">
                            Copy
                        </button>
                    </div>
                    <div class="text-slate-500 text-xs mt-1">
                        Cấu hình webhook này trong BotFather: <code>/setwebhook</code> và dán URL trên
                    </div>
                </div>
                
                <div class="sm:ml-20 sm:pl-5 mt-5">
                    <button type="submit" class="btn btn-primary">Cập Nhật</button>
                </div>
            </form>
        </div>

        <!-- Contact Settings Tab -->
        <div id="contact" class="tab-pane" role="tabpanel" aria-labelledby="contact-tab">
            <form action="{{ route('admin.settings.contact') }}" method="POST">
                @csrf
                <div class="form-inline">
                    <label for="facebook_link" class="form-label sm:w-20">Facebook Link</label>
                    <input id="facebook_link" type="text" name="facebook_link" class="form-control" placeholder="https://www.facebook.com/..." value="{{ $settings->facebook_link ?? '' }}">
                </div>
                <div class="form-inline mt-5">
                    <label for="zalo_phone" class="form-label sm:w-20">Zalo Phone</label>
                    <input id="zalo_phone" type="text" name="zalo_phone" class="form-control" placeholder="0856761038" value="{{ $settings->zalo_phone ?? '' }}">
                </div>
                <div class="sm:ml-20 sm:pl-5 mt-5">
                    <button type="submit" class="btn btn-primary">Lưu Cài Đặt</button>
                </div>
            </form>
        </div>

        <!-- Card Gateway Settings Tab -->
        <div id="card" class="tab-pane" role="tabpanel" aria-labelledby="card-tab">
            <form action="{{ route('admin.settings.card') }}" method="POST">
                @csrf
                <div class="form-inline">
                    <label for="cardvip_partner_id" class="form-label sm:w-20">Partner ID</label>
                    <input id="cardvip_partner_id" type="text" name="cardvip_partner_id" class="form-control" value="{{ $settings->cardvip_partner_id ?? '' }}" placeholder="Partner ID từ CardVIP" required>
                </div>
                <div class="form-inline mt-5">
                    <label for="cardvip_partner_key" class="form-label sm:w-20">Partner Key</label>
                    <input id="cardvip_partner_key" type="text" name="cardvip_partner_key" class="form-control" value="{{ $settings->cardvip_partner_key ?? '' }}" placeholder="Partner Key từ CardVIP" required>
                </div>
                <div class="form-inline mt-5">
                    <label for="cardvip_api_url" class="form-label sm:w-20">API URL</label>
                    <input id="cardvip_api_url" type="text" name="cardvip_api_url" class="form-control" value="{{ $settings->cardvip_api_url ?? 'http://api.cardvip.vn/chargingws/v2' }}" placeholder="URL API CardVIP" required>
                </div>
                <div class="form-inline mt-5">
                    <label for="cardvip_callback" class="form-label sm:w-20">Callback URL</label>
                    <input id="cardvip_callback" type="text" name="cardvip_callback" class="form-control" value="{{ $settings->cardvip_callback ?? '' }}" placeholder="URL Callback nhận kết quả" required>
                </div>
                <div class="sm:ml-20 sm:pl-5 mt-5">
                    <button type="submit" class="btn btn-primary">Cập Nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END: Settings Tabs -->

<script>
function copyToClipboard() {
    const webhookUrl = document.getElementById('webhookUrl').value;
    navigator.clipboard.writeText(webhookUrl).then(function() {
        // Show success message
        alert('Đã copy URL vào clipboard!');
    }, function() {
        // Fallback for older browsers
        const textarea = document.createElement("textarea");
        textarea.value = webhookUrl;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('Đã copy URL vào clipboard!');
    });
}
</script>
@endsection
