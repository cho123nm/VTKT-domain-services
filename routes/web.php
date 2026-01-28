<?php
// Khai báo namespace và import các class cần thiết
use Illuminate\Support\Facades\Route; // Facade để định nghĩa routes
use App\Http\Controllers\Admin\SourceCodeController as AdminSourceCodeController; // Controller admin quản lý source code (alias để tránh conflict)
use App\Http\Controllers\HomeController; // Controller trang chủ
use App\Http\Controllers\AuthController; // Controller xử lý đăng nhập/đăng ký
use App\Http\Controllers\DomainController; // Controller quản lý domain
use App\Http\Controllers\PaymentController; // Controller xử lý thanh toán
use App\Http\Controllers\CheckoutController; // Controller xử lý checkout
use App\Http\Controllers\ProfileController; // Controller quản lý profile user
use App\Http\Controllers\FeedbackController; // Controller xử lý feedback
use App\Http\Controllers\MessageController; // Controller xử lý tin nhắn
use App\Http\Controllers\DownloadController; // Controller xử lý download
use App\Http\Controllers\Api\AjaxController; // Controller xử lý AJAX requests
use App\Http\Controllers\TelegramWebhookController; // Controller xử lý Telegram webhook
use App\Http\Controllers\ContactAdminController; // Controller hiển thị thông tin liên hệ admin

/*
|--------------------------------------------------------------------------
| Web Routes - Định nghĩa các routes cho web application
|--------------------------------------------------------------------------
| File này chứa tất cả các routes cho phần frontend của ứng dụng
| Bao gồm: trang chủ, đăng nhập/đăng ký, quản lý domain, thanh toán, etc.
*/

// Route trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');

// Routes xử lý đăng nhập/đăng ký
Route::get('/auth/login', [AuthController::class, 'showLogin'])->name('login'); // Hiển thị form đăng nhập
Route::post('/auth/login', [AuthController::class, 'login']); // Xử lý đăng nhập
Route::get('/auth/register', [AuthController::class, 'showRegister'])->name('register'); // Hiển thị form đăng ký
Route::post('/auth/register', [AuthController::class, 'register']); // Xử lý đăng ký
Route::get('/auth/logout', [AuthController::class, 'logout'])->name('logout'); // Đăng xuất (GET)
Route::post('/auth/logout', [AuthController::class, 'logout']); // Đăng xuất (POST)

// Routes xử lý quên mật khẩu và đặt lại mật khẩu
Route::get('/password/forgot', [AuthController::class, 'showForgotPassword'])->name('password.forgot'); // Hiển thị form quên mật khẩu
Route::post('/password/forgot', [AuthController::class, 'forgotPassword'])->name('password.forgot.post'); // Xử lý quên mật khẩu
Route::get('/password/reset', [AuthController::class, 'showResetPassword'])->name('password.reset'); // Hiển thị form đặt lại mật khẩu
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset.post'); // Xử lý đặt lại mật khẩu

// Legacy AJAX routes - redirect đến Laravel (để tương thích với code cũ)
Route::post('/Ajaxs/login.php', [AuthController::class, 'login']); // Legacy login AJAX
Route::post('/Ajaxs/register.php', [AuthController::class, 'register']); // Legacy register AJAX
Route::post('/Ajaxs/BuyDomain.php', [AjaxController::class, 'buyDomain']); // Legacy mua domain AJAX
Route::post('/Ajaxs/BuyHosting.php', [AjaxController::class, 'buyHosting']); // Legacy mua hosting AJAX
Route::post('/Ajaxs/BuyVPS.php', [AjaxController::class, 'buyVPS']); // Legacy mua VPS AJAX
Route::post('/Ajaxs/BuySourceCode.php', [AjaxController::class, 'buySourceCode']); // Legacy mua source code AJAX
Route::post('/Ajaxs/CheckDomain.php', [AjaxController::class, 'checkDomain']); // Legacy kiểm tra domain AJAX
Route::post('/Ajaxs/UpdateDns.php', [DomainController::class, 'updateDns']); // Legacy cập nhật DNS AJAX
Route::post('/Ajaxs/Cards.php', [PaymentController::class, 'processCard']); // Legacy nạp thẻ AJAX

// Legacy page routes - redirect đến Laravel (để tương thích với code cũ)
Route::get('/Pages/login.php', function() {
    return redirect()->route('login'); // Redirect đến route login
});
Route::get('/Pages/register.php', function() {
    return redirect()->route('register'); // Redirect đến route register
});
Route::get('/Pages/logout.php', function() {
    return redirect()->route('logout'); // Redirect đến route logout
});

// Routes quản lý profile (yêu cầu đăng nhập)
Route::middleware(['web'])->group(function() {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile'); // Hiển thị profile
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update'); // Cập nhật profile
    
    // Routes quản lý dịch vụ (Service Manager)
    Route::get('/manager', [\App\Http\Controllers\ManagerController::class, 'index'])->name('manager.index'); // Danh sách dịch vụ đã mua
    Route::get('/manager/domain/{id}', [DomainController::class, 'manageDomain'])->name('manager.domain'); // Quản lý domain cụ thể
    Route::post('/manager/domain/{id}/update-dns', [DomainController::class, 'updateDns'])->name('manager.domain.update-dns'); // Cập nhật DNS cho domain
    
    // Routes xử lý feedback
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index'); // Hiển thị form feedback
    Route::post('/feedback/store', [FeedbackController::class, 'store'])->name('feedback.store'); // Lưu feedback
    
    // Routes xử lý tin nhắn (phản hồi từ admin)
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index'); // Danh sách tin nhắn
    Route::get('/messages/{id}/mark-read', [MessageController::class, 'markAsRead'])->name('messages.mark-read'); // Đánh dấu đã đọc
    
    // Routes xử lý download
    Route::get('/download', [\App\Http\Controllers\DownloadController::class, 'index'])->name('download.index'); // Trang download
    Route::get('/download/{id}', [\App\Http\Controllers\DownloadController::class, 'download'])->name('download.file'); // Download file cụ thể
});

// Legacy profile route - redirect đến Laravel
Route::get('/Pages/account_profile.php', function() {
    return redirect()->route('profile'); // Redirect đến route profile
});

// Routes quản lý domain (prefix 'domain')
Route::prefix('domain')->name('domain.')->group(function() {
    Route::get('/checkout', [DomainController::class, 'checkout'])->name('checkout'); // Trang checkout domain
    Route::get('/manage', [DomainController::class, 'manage'])->name('manage'); // Trang quản lý domain
    Route::get('/manage-dns', [DomainController::class, 'manageDns'])->name('manage-dns'); // Trang quản lý DNS
});

// Routes thanh toán (Nạp thẻ)
Route::middleware(['web'])->group(function() {
    Route::get('/recharge', [PaymentController::class, 'recharge'])->name('recharge');
    Route::post('/recharge/process', [PaymentController::class, 'processRecharge'])->name('recharge.process'); // Xử lý nạp thẻ
});

// Route callback từ cổng nạp thẻ (không yêu cầu đăng nhập, cho cardvip)
Route::post('/callback', [PaymentController::class, 'callback'])->name('callback');
// Route GET để test/verify callback URL (CardVIP có thể test bằng GET)
Route::get('/callback', function() {
    return response()->json([
        'status' => 'ok',
        'message' => 'Callback URL is working. Please use POST method for actual callbacks.',
        'method' => 'GET'
    ], 200);
})->name('callback.test');

// Route Telegram Webhook (không yêu cầu đăng nhập, cho Telegram Bot API)
Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle'])->name('telegram.webhook');
// Route GET để hiển thị thông báo thân thiện khi truy cập bằng browser
Route::get('/telegram/webhook', [TelegramWebhookController::class, 'info'])->name('telegram.webhook.info');

// Routes AJAX (prefix 'ajax')
Route::prefix('ajax')->name('ajax.')->group(function() {
    Route::post('/check-domain', [AjaxController::class, 'checkDomain'])->name('check-domain'); // Kiểm tra domain
    Route::post('/buy-domain', [DomainController::class, 'buy'])->name('buy-domain'); // Mua domain
    Route::post('/update-dns', [DomainController::class, 'updateDns'])->name('update-dns'); // Cập nhật DNS
    Route::post('/cards', [PaymentController::class, 'processCard'])->name('cards'); // Xử lý thẻ cào
    Route::post('/buy-hosting', [AjaxController::class, 'buyHosting'])->name('buy-hosting'); // Mua hosting
    Route::post('/buy-source-code', [AjaxController::class, 'buySourceCode'])->name('buy-source-code'); // Mua source code
    Route::post('/buy-vps', [AjaxController::class, 'buyVPS'])->name('buy-vps'); // Mua VPS
});

// Routes xác thực admin (trước middleware, để admin có thể đăng nhập)
Route::prefix('admin')->name('admin.')->group(function() {
    Route::get('/login', [\App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('auth.login'); // Hiển thị form đăng nhập admin
    Route::post('/login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('auth.login.post'); // Xử lý đăng nhập admin
    Route::get('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('auth.logout'); // Đăng xuất admin (GET)
    Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('auth.logout.post'); // Đăng xuất admin (POST)
});

// Routes admin (được bảo vệ bởi AdminMiddleware)
Route::prefix('admin')->name('admin.')->middleware([\App\Http\Middleware\AdminMiddleware::class])->group(function() {
    // Dashboard
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard'); // Trang dashboard admin
    
    // Quản lý Source Code (resource routes: index, create, store, show, edit, update, destroy)
    Route::resource('sourcecode', AdminSourceCodeController::class);
    
    // Quản lý Hosting (resource routes)
    Route::resource('hosting', \App\Http\Controllers\Admin\HostingController::class);
    
    // Quản lý VPS (resource routes)
    Route::resource('vps', \App\Http\Controllers\Admin\VPSController::class);
    
    // Quản lý Domain (resource routes)
    Route::resource('domain', \App\Http\Controllers\Admin\DomainController::class);
    
    // Quản lý Đơn hàng
    Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index'); // Danh sách đơn hàng
    Route::get('orders/{id}/{type}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show'); // Chi tiết đơn hàng
    Route::post('orders/{id}/{type}/approve', [\App\Http\Controllers\Admin\OrderController::class, 'approve'])->name('orders.approve'); // Duyệt đơn hàng
    Route::post('orders/{id}/{type}/reject', [\App\Http\Controllers\Admin\OrderController::class, 'reject'])->name('orders.reject'); // Từ chối đơn hàng
    
    // Quản lý DNS
    Route::get('dns', [\App\Http\Controllers\Admin\DnsController::class, 'index'])->name('dns.index'); // Danh sách yêu cầu cập nhật DNS
    Route::post('dns/{id}/update', [\App\Http\Controllers\Admin\DnsController::class, 'update'])->name('dns.update'); // Cập nhật DNS
    Route::post('dns/{id}/reject', [\App\Http\Controllers\Admin\DnsController::class, 'reject'])->name('dns.reject'); // Từ chối yêu cầu DNS
    
    // Quản lý User
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class); // Resource routes cho user
    Route::put('users/{id}/balance', [\App\Http\Controllers\Admin\UserController::class, 'updateBalance'])->name('users.update-balance'); // Cập nhật số dư user
    
    // Quản lý Feedback
    Route::get('feedback', [\App\Http\Controllers\Admin\FeedbackController::class, 'index'])->name('feedback.index'); // Danh sách feedback
    Route::get('feedback/{id}', [\App\Http\Controllers\Admin\FeedbackController::class, 'show'])->name('feedback.show'); // Chi tiết feedback
    Route::post('feedback/{id}/reply', [\App\Http\Controllers\Admin\FeedbackController::class, 'reply'])->name('feedback.reply'); // Trả lời feedback
    Route::post('feedback/{id}/update-status', [\App\Http\Controllers\Admin\FeedbackController::class, 'updateStatus'])->name('feedback.update-status'); // Cập nhật trạng thái feedback
    
    // Quản lý Thẻ cào
    Route::get('cards', [\App\Http\Controllers\Admin\CardController::class, 'index'])->name('cards.index'); // Danh sách thẻ cào
    Route::get('cards/pending', [\App\Http\Controllers\Admin\CardController::class, 'pending'])->name('cards.pending'); // Danh sách thẻ chờ duyệt
    Route::get('cards/add-balance', [\App\Http\Controllers\Admin\CardController::class, 'showAddBalance'])->name('cards.add-balance'); // Form cộng tiền thủ công
    Route::post('cards/add-balance', [\App\Http\Controllers\Admin\CardController::class, 'addBalance']); // Xử lý cộng tiền thủ công
    Route::get('cards/{id}', [\App\Http\Controllers\Admin\CardController::class, 'show'])->name('cards.show'); // Chi tiết thẻ cào
    Route::post('cards/{id}/update-status', [\App\Http\Controllers\Admin\CardController::class, 'updateStatus'])->name('cards.update-status'); // Cập nhật trạng thái thẻ
    
    // Quản lý Cài đặt
    Route::get('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index'); // Trang cài đặt
    Route::post('settings/website', [\App\Http\Controllers\Admin\SettingsController::class, 'updateWebsite'])->name('settings.website'); // Cập nhật cài đặt website
    Route::post('settings/telegram', [\App\Http\Controllers\Admin\SettingsController::class, 'updateTelegram'])->name('settings.telegram'); // Cập nhật cài đặt Telegram
    Route::post('settings/contact', [\App\Http\Controllers\Admin\SettingsController::class, 'updateContact'])->name('settings.contact'); // Cập nhật thông tin liên hệ
    Route::post('settings/card', [\App\Http\Controllers\Admin\SettingsController::class, 'updateCard'])->name('settings.card'); // Cập nhật cài đặt cổng nạp thẻ
});

// Routes frontend (trang công khai)
Route::get('/source-code', [\App\Http\Controllers\SourceCodeController::class, 'index'])->name('source-code.index'); // Trang danh sách source code
Route::get('/hosting', [\App\Http\Controllers\HostingController::class, 'index'])->name('hosting.index'); // Trang danh sách hosting
Route::get('/vps', [\App\Http\Controllers\VPSController::class, 'index'])->name('vps.index'); // Trang danh sách VPS

// Routes checkout (yêu cầu web middleware để có session)
Route::middleware(['web'])->prefix('checkout')->name('checkout.')->group(function() {
    Route::get('/domain', [CheckoutController::class, 'domain'])->name('domain'); // Trang checkout domain
    Route::get('/hosting', [CheckoutController::class, 'hosting'])->name('hosting'); // Trang checkout hosting
    Route::get('/vps', [CheckoutController::class, 'vps'])->name('vps'); // Trang checkout VPS
    Route::get('/sourcecode', [CheckoutController::class, 'sourcecode'])->name('sourcecode'); // Trang checkout source code
    
    Route::post('/domain/process', [CheckoutController::class, 'processDomain'])->name('domain.process'); // Xử lý mua domain
    Route::post('/hosting/process', [CheckoutController::class, 'processHosting'])->name('hosting.process'); // Xử lý mua hosting
    Route::post('/vps/process', [CheckoutController::class, 'processVPS'])->name('vps.process'); // Xử lý mua VPS
    Route::post('/sourcecode/process', [CheckoutController::class, 'processSourceCode'])->name('sourcecode.process'); // Xử lý mua source code
});

// Legacy routes - redirect đến Laravel routes (để tương thích với code cũ)
Route::get('/Pages/SourceCode.php', function() {
    return redirect()->route('source-code.index'); // Redirect đến trang source code
});
Route::get('/Pages/Hosting.php', function() {
    return redirect()->route('hosting.index'); // Redirect đến trang hosting
});
Route::get('/Pages/VPS.php', function() {
    return redirect()->route('vps.index'); // Redirect đến trang VPS
});
Route::get('/Pages/Checkout.php', function() {
    $domain = request()->get('domain', ''); // Lấy domain từ query string
    return redirect()->route('checkout.domain', ['domain' => $domain]); // Redirect đến checkout domain
});
Route::get('/Pages/CheckoutHosting.php', function() {
    $id = request()->get('id', 0); // Lấy ID từ query string
    return redirect()->route('checkout.hosting', ['id' => $id]); // Redirect đến checkout hosting
});
Route::get('/Pages/CheckoutVPS.php', function() {
    $id = request()->get('id', 0); // Lấy ID từ query string
    return redirect()->route('checkout.vps', ['id' => $id]); // Redirect đến checkout VPS
});
Route::get('/Pages/CheckoutSourceCode.php', function() {
    $id = request()->get('id', 0); // Lấy ID từ query string
    return redirect()->route('checkout.sourcecode', ['id' => $id]); // Redirect đến checkout source code
});
Route::get('/Pages/ManagesWhois.php', function() {
    $domain = request()->get('domain', ''); // Lấy domain từ query string
    return redirect()->route('domain.manage-dns', ['domain' => $domain]); // Redirect đến quản lý DNS
});
Route::get('/Pages/Recharge.php', function() {
    return redirect()->route('recharge'); // Redirect đến trang nạp thẻ
});
Route::get('/Pages/managers.php', function() {
    return redirect()->route('manager.index'); // Redirect đến trang quản lý dịch vụ
});
Route::get('/callback.php', function() {
    return app(\App\Http\Controllers\PaymentController::class)->callback(request()); // Xử lý callback từ cổng nạp thẻ
});

// Legacy route quản lý domain với MGD (Mã Giao Dịch)
Route::get('/ManagesWhois/{mgd}', function($mgd) {
    // Tìm domain theo MGD và redirect đến route mới
    $domain = \App\Models\History::where('mgd', $mgd)->first(); // Tìm domain theo mã giao dịch
    if ($domain) {
        return redirect()->route('manager.domain', $domain->id); // Redirect đến trang quản lý domain
    }
    return redirect()->route('manager.index'); // Nếu không tìm thấy, redirect về trang quản lý
});

// Legacy route quản lý
Route::get('/Manager', function() {
    return redirect()->route('manager.index'); // Redirect đến trang quản lý dịch vụ
});

// Legacy routes thân thiện với user (từ .htaccess cũ)
Route::get('/Checkout', function() {
    $domain = request()->get('domain', ''); // Lấy domain từ query string
    return redirect()->route('checkout.domain', ['domain' => $domain]); // Redirect đến checkout domain
});
Route::get('/Recharge', function() {
    return redirect()->route('recharge'); // Redirect đến trang nạp thẻ
});

// Route liên hệ admin (sau khi mua hàng)
Route::middleware(['web'])->group(function() {
    Route::get('/contact-admin', [ContactAdminController::class, 'index'])->name('contact-admin'); // Trang liên hệ admin
});

// Legacy routes feedback và messages
Route::get('/Pages/Feedback.php', function() {
    return redirect()->route('feedback.index'); // Redirect đến trang feedback
});
Route::get('/Pages/Messages.php', function() {
    return redirect()->route('messages.index'); // Redirect đến trang tin nhắn
});
Route::get('/Pages/DownloadSourceCode.php', function() {
    $mgd = request()->get('mgd', ''); // Lấy MGD từ query string
    return redirect()->route('download.index', ['mgd' => $mgd]); // Redirect đến trang download
});

// Legacy route ContactAdmin
Route::get('/Pages/ContactAdmin.php', function() {
    $type = request()->get('type', ''); // Lấy type từ query string
    $mgd = request()->get('mgd', ''); // Lấy MGD từ query string
    return redirect()->route('contact-admin', ['type' => $type, 'mgd' => $mgd]); // Redirect đến trang liên hệ admin
});

// Legacy admin routes - redirect đến Laravel admin panel (giữ lại để tương thích ngược)
// Các routes này redirect các URL admin PHP cũ đến các route Laravel admin mới
// Có thể xóa nếu không cần tương thích ngược
Route::get('/Adminstators/index.php', function() {
    return redirect()->route('admin.dashboard'); // Redirect đến dashboard admin
});
Route::get('/Adminstators/danh-sach-san-pham.php', function() {
    return redirect()->route('admin.domain.index'); // Redirect đến danh sách domain
});
Route::get('/Adminstators/danh-sach-hosting.php', function() {
    return redirect()->route('admin.hosting.index'); // Redirect đến danh sách hosting
});
Route::get('/Adminstators/danh-sach-vps.php', function() {
    return redirect()->route('admin.vps.index'); // Redirect đến danh sách VPS
});
Route::get('/Adminstators/danh-sach-source-code.php', function() {
    return redirect()->route('admin.sourcecode.index'); // Redirect đến danh sách source code
});
Route::get('/Adminstators/duyet-don-hang.php', function() {
    return redirect()->route('admin.orders.index'); // Redirect đến danh sách đơn hàng
});
Route::get('/Adminstators/quan-ly-thanh-vien.php', function() {
    return redirect()->route('admin.users.index'); // Redirect đến danh sách thành viên
});
Route::get('/Adminstators/quan-ly-feedback.php', function() {
    return redirect()->route('admin.feedback.index'); // Redirect đến danh sách feedback
});
Route::get('/Adminstators/Gach-Cards.php', function() {
    return redirect()->route('admin.cards.index'); // Redirect đến danh sách thẻ cào
});
Route::get('/Adminstators/DNS.php', function() {
    return redirect()->route('admin.dns.index'); // Redirect đến danh sách DNS
});
Route::get('/Adminstators/cai-dat-web.php', function() {
    return redirect()->route('admin.settings.index'); // Redirect đến trang cài đặt
});
Route::get('/Adminstators/cai-dat-telegram.php', function() {
    return redirect()->route('admin.settings.index'); // Redirect đến trang cài đặt
});
Route::get('/Adminstators/cai-dat-lien-he.php', function() {
    return redirect()->route('admin.settings.index'); // Redirect đến trang cài đặt
});
Route::get('/Adminstators/setting-gach-card.php', function() {
    return redirect()->route('admin.settings.index'); // Redirect đến trang cài đặt
});
