<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SourceCodeController as AdminSourceCodeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\Api\AjaxController;
use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\ContactAdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication
Route::get('/auth/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/auth/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/auth/register', [AuthController::class, 'register']);
Route::get('/auth/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/auth/logout', [AuthController::class, 'logout']);

// Password Reset
Route::get('/password/forgot', [AuthController::class, 'showForgotPassword'])->name('password.forgot');
Route::post('/password/forgot', [AuthController::class, 'forgotPassword'])->name('password.forgot.post');
Route::get('/password/reset', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset.post');

// Legacy AJAX routes - redirect to Laravel
Route::post('/Ajaxs/login.php', [AuthController::class, 'login']);
Route::post('/Ajaxs/register.php', [AuthController::class, 'register']);
Route::post('/Ajaxs/BuyDomain.php', [AjaxController::class, 'buyDomain']);
Route::post('/Ajaxs/BuyHosting.php', [AjaxController::class, 'buyHosting']);
Route::post('/Ajaxs/BuyVPS.php', [AjaxController::class, 'buyVPS']);
Route::post('/Ajaxs/BuySourceCode.php', [AjaxController::class, 'buySourceCode']);
Route::post('/Ajaxs/CheckDomain.php', [AjaxController::class, 'checkDomain']);
Route::post('/Ajaxs/UpdateDns.php', [DomainController::class, 'updateDns']);
Route::post('/Ajaxs/Cards.php', [PaymentController::class, 'processCard']);

// Legacy page routes - redirect to Laravel
Route::get('/Pages/login.php', function() {
    return redirect()->route('login');
});
Route::get('/Pages/register.php', function() {
    return redirect()->route('register');
});
Route::get('/Pages/logout.php', function() {
    return redirect()->route('logout');
});

// Profile Routes (requires authentication)
Route::middleware(['web'])->group(function() {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    
    // Manager Routes (Service Manager)
    Route::get('/manager', [\App\Http\Controllers\ManagerController::class, 'index'])->name('manager.index');
    Route::get('/manager/domain/{id}', [DomainController::class, 'manageDomain'])->name('manager.domain');
    Route::post('/manager/domain/{id}/update-dns', [DomainController::class, 'updateDns'])->name('manager.domain.update-dns');
    
    // Feedback Routes
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::post('/feedback/store', [FeedbackController::class, 'store'])->name('feedback.store');
    
    // Messages Routes
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{id}/mark-read', [MessageController::class, 'markAsRead'])->name('messages.mark-read');
    
    // Download Routes
    Route::get('/download', [\App\Http\Controllers\DownloadController::class, 'index'])->name('download.index');
    Route::get('/download/{id}', [\App\Http\Controllers\DownloadController::class, 'download'])->name('download.file');
});

// Legacy profile route
Route::get('/Pages/account_profile.php', function() {
    return redirect()->route('profile');
});

// Domain Routes
Route::prefix('domain')->name('domain.')->group(function() {
    Route::get('/checkout', [DomainController::class, 'checkout'])->name('checkout');
    Route::get('/manage', [DomainController::class, 'manage'])->name('manage');
    Route::get('/manage-dns', [DomainController::class, 'manageDns'])->name('manage-dns');
});

// Payment Routes (Recharge)
Route::middleware(['web'])->group(function() {
    Route::get('/recharge', [PaymentController::class, 'recharge'])->name('recharge');
    Route::post('/recharge/process', [PaymentController::class, 'processRecharge'])->name('recharge.process');
});

// Callback route (no auth, for cardvip)
Route::post('/callback', [PaymentController::class, 'callback'])->name('callback');

// Telegram Webhook (no auth, for Telegram Bot API)
Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle'])->name('telegram.webhook');

// AJAX Routes
Route::prefix('ajax')->name('ajax.')->group(function() {
    Route::post('/check-domain', [AjaxController::class, 'checkDomain'])->name('check-domain');
    Route::post('/buy-domain', [DomainController::class, 'buy'])->name('buy-domain');
    Route::post('/update-dns', [DomainController::class, 'updateDns'])->name('update-dns');
    Route::post('/cards', [PaymentController::class, 'processCard'])->name('cards');
    Route::post('/buy-hosting', [AjaxController::class, 'buyHosting'])->name('buy-hosting');
    Route::post('/buy-source-code', [AjaxController::class, 'buySourceCode'])->name('buy-source-code');
    Route::post('/buy-vps', [AjaxController::class, 'buyVPS'])->name('buy-vps');
});

// Admin Authentication Routes (before middleware)
Route::prefix('admin')->name('admin.')->group(function() {
    Route::get('/login', [\App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('auth.login');
    Route::post('/login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('auth.login.post');
    Route::get('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('auth.logout');
    Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('auth.logout.post');
});

// Admin Routes (protected by middleware)
Route::prefix('admin')->name('admin.')->middleware([\App\Http\Middleware\AdminMiddleware::class])->group(function() {
    // Dashboard
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Source Code Management
    Route::resource('sourcecode', AdminSourceCodeController::class);
    
    // Hosting Management
    Route::resource('hosting', \App\Http\Controllers\Admin\HostingController::class);
    
    // VPS Management
    Route::resource('vps', \App\Http\Controllers\Admin\VPSController::class);
    
    // Domain Management
    Route::resource('domain', \App\Http\Controllers\Admin\DomainController::class);
    
    // Order Management
    Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{id}/{type}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{id}/{type}/approve', [\App\Http\Controllers\Admin\OrderController::class, 'approve'])->name('orders.approve');
    Route::post('orders/{id}/{type}/reject', [\App\Http\Controllers\Admin\OrderController::class, 'reject'])->name('orders.reject');
    
    // DNS Management
    Route::get('dns', [\App\Http\Controllers\Admin\DnsController::class, 'index'])->name('dns.index');
    Route::post('dns/{id}/update', [\App\Http\Controllers\Admin\DnsController::class, 'update'])->name('dns.update');
    Route::post('dns/{id}/reject', [\App\Http\Controllers\Admin\DnsController::class, 'reject'])->name('dns.reject');
    
    // User Management
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::put('users/{id}/balance', [\App\Http\Controllers\Admin\UserController::class, 'updateBalance'])->name('users.update-balance');
    
    // Feedback Management
    Route::get('feedback', [\App\Http\Controllers\Admin\FeedbackController::class, 'index'])->name('feedback.index');
    Route::get('feedback/{id}', [\App\Http\Controllers\Admin\FeedbackController::class, 'show'])->name('feedback.show');
    Route::post('feedback/{id}/reply', [\App\Http\Controllers\Admin\FeedbackController::class, 'reply'])->name('feedback.reply');
    Route::post('feedback/{id}/update-status', [\App\Http\Controllers\Admin\FeedbackController::class, 'updateStatus'])->name('feedback.update-status');
    
    // Card Management
    Route::get('cards', [\App\Http\Controllers\Admin\CardController::class, 'index'])->name('cards.index');
    Route::get('cards/pending', [\App\Http\Controllers\Admin\CardController::class, 'pending'])->name('cards.pending');
    Route::get('cards/add-balance', [\App\Http\Controllers\Admin\CardController::class, 'showAddBalance'])->name('cards.add-balance');
    Route::post('cards/add-balance', [\App\Http\Controllers\Admin\CardController::class, 'addBalance']);
    Route::get('cards/{id}', [\App\Http\Controllers\Admin\CardController::class, 'show'])->name('cards.show');
    Route::post('cards/{id}/update-status', [\App\Http\Controllers\Admin\CardController::class, 'updateStatus'])->name('cards.update-status');
    
    // Settings Management
    Route::get('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/website', [\App\Http\Controllers\Admin\SettingsController::class, 'updateWebsite'])->name('settings.website');
    Route::post('settings/telegram', [\App\Http\Controllers\Admin\SettingsController::class, 'updateTelegram'])->name('settings.telegram');
    Route::post('settings/contact', [\App\Http\Controllers\Admin\SettingsController::class, 'updateContact'])->name('settings.contact');
    Route::post('settings/card', [\App\Http\Controllers\Admin\SettingsController::class, 'updateCard'])->name('settings.card');
});

// Frontend Routes
Route::get('/source-code', [\App\Http\Controllers\SourceCodeController::class, 'index'])->name('source-code.index');
Route::get('/hosting', [\App\Http\Controllers\HostingController::class, 'index'])->name('hosting.index');
Route::get('/vps', [\App\Http\Controllers\VPSController::class, 'index'])->name('vps.index');

// Checkout Routes (requires web middleware for session)
Route::middleware(['web'])->prefix('checkout')->name('checkout.')->group(function() {
    Route::get('/domain', [CheckoutController::class, 'domain'])->name('domain');
    Route::get('/hosting', [CheckoutController::class, 'hosting'])->name('hosting');
    Route::get('/vps', [CheckoutController::class, 'vps'])->name('vps');
    Route::get('/sourcecode', [CheckoutController::class, 'sourcecode'])->name('sourcecode');
    
    Route::post('/domain/process', [CheckoutController::class, 'processDomain'])->name('domain.process');
    Route::post('/hosting/process', [CheckoutController::class, 'processHosting'])->name('hosting.process');
    Route::post('/vps/process', [CheckoutController::class, 'processVPS'])->name('vps.process');
    Route::post('/sourcecode/process', [CheckoutController::class, 'processSourceCode'])->name('sourcecode.process');
});

// Legacy routes - redirect to Laravel routes
Route::get('/Pages/SourceCode.php', function() {
    return redirect()->route('source-code.index');
});
Route::get('/Pages/Hosting.php', function() {
    return redirect()->route('hosting.index');
});
Route::get('/Pages/VPS.php', function() {
    return redirect()->route('vps.index');
});
Route::get('/Pages/Checkout.php', function() {
    $domain = request()->get('domain', '');
    return redirect()->route('checkout.domain', ['domain' => $domain]);
});
Route::get('/Pages/CheckoutHosting.php', function() {
    $id = request()->get('id', 0);
    return redirect()->route('checkout.hosting', ['id' => $id]);
});
Route::get('/Pages/CheckoutVPS.php', function() {
    $id = request()->get('id', 0);
    return redirect()->route('checkout.vps', ['id' => $id]);
});
Route::get('/Pages/CheckoutSourceCode.php', function() {
    $id = request()->get('id', 0);
    return redirect()->route('checkout.sourcecode', ['id' => $id]);
});
Route::get('/Pages/ManagesWhois.php', function() {
    $domain = request()->get('domain', '');
    return redirect()->route('domain.manage-dns', ['domain' => $domain]);
});
Route::get('/Pages/Recharge.php', function() {
    return redirect()->route('recharge');
});
Route::get('/Pages/managers.php', function() {
    return redirect()->route('manager.index');
});
Route::get('/callback.php', function() {
    return app(\App\Http\Controllers\PaymentController::class)->callback(request());
});

// Legacy manager route with MGD
Route::get('/ManagesWhois/{mgd}', function($mgd) {
    // Find domain by MGD and redirect to new route
    $domain = \App\Models\History::where('mgd', $mgd)->first();
    if ($domain) {
        return redirect()->route('manager.domain', $domain->id);
    }
    return redirect()->route('manager.index');
});

// Legacy manager route
Route::get('/Manager', function() {
    return redirect()->route('manager.index');
});

// User-friendly legacy routes (from old .htaccess)
Route::get('/Checkout', function() {
    $domain = request()->get('domain', '');
    return redirect()->route('checkout.domain', ['domain' => $domain]);
});
Route::get('/Recharge', function() {
    return redirect()->route('recharge');
});

// Contact Admin Route (after purchase)
Route::middleware(['web'])->group(function() {
    Route::get('/contact-admin', [ContactAdminController::class, 'index'])->name('contact-admin');
});

// Legacy feedback and messages routes
Route::get('/Pages/Feedback.php', function() {
    return redirect()->route('feedback.index');
});
Route::get('/Pages/Messages.php', function() {
    return redirect()->route('messages.index');
});
Route::get('/Pages/DownloadSourceCode.php', function() {
    $mgd = request()->get('mgd', '');
    return redirect()->route('download.index', ['mgd' => $mgd]);
});

// Legacy ContactAdmin route
Route::get('/Pages/ContactAdmin.php', function() {
    $type = request()->get('type', '');
    $mgd = request()->get('mgd', '');
    return redirect()->route('contact-admin', ['type' => $type, 'mgd' => $mgd]);
});

// Legacy admin routes - redirect to Laravel admin panel (kept for backward compatibility)
// These routes redirect old PHP admin URLs to new Laravel admin routes
// Can be removed if backward compatibility is not needed
Route::get('/Adminstators/index.php', function() {
    return redirect()->route('admin.dashboard');
});
Route::get('/Adminstators/danh-sach-san-pham.php', function() {
    return redirect()->route('admin.domain.index');
});
Route::get('/Adminstators/danh-sach-hosting.php', function() {
    return redirect()->route('admin.hosting.index');
});
Route::get('/Adminstators/danh-sach-vps.php', function() {
    return redirect()->route('admin.vps.index');
});
Route::get('/Adminstators/danh-sach-source-code.php', function() {
    return redirect()->route('admin.sourcecode.index');
});
Route::get('/Adminstators/duyet-don-hang.php', function() {
    return redirect()->route('admin.orders.index');
});
Route::get('/Adminstators/quan-ly-thanh-vien.php', function() {
    return redirect()->route('admin.users.index');
});
Route::get('/Adminstators/quan-ly-feedback.php', function() {
    return redirect()->route('admin.feedback.index');
});
Route::get('/Adminstators/Gach-Cards.php', function() {
    return redirect()->route('admin.cards.index');
});
Route::get('/Adminstators/DNS.php', function() {
    return redirect()->route('admin.dns.index');
});
Route::get('/Adminstators/cai-dat-web.php', function() {
    return redirect()->route('admin.settings.index');
});
Route::get('/Adminstators/cai-dat-telegram.php', function() {
    return redirect()->route('admin.settings.index');
});
Route::get('/Adminstators/cai-dat-lien-he.php', function() {
    return redirect()->route('admin.settings.index');
});
Route::get('/Adminstators/setting-gach-card.php', function() {
    return redirect()->route('admin.settings.index');
});
