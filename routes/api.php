<?php
// Khai báo namespace và import các class cần thiết
use Illuminate\Http\Request; // Class xử lý HTTP request
use Illuminate\Support\Facades\Route; // Facade để định nghĩa routes
use App\Http\Controllers\Api\AjaxController; // Controller xử lý AJAX requests

/*
|--------------------------------------------------------------------------
| API Routes - Định nghĩa các routes cho API/AJAX endpoints
|--------------------------------------------------------------------------
| File này chứa các routes cho AJAX requests từ frontend
| Tất cả routes đều sử dụng 'web' middleware để có session và CSRF protection
*/

// Routes API cho AJAX endpoints - cần session middleware (web middleware)
Route::middleware(['web'])->group(function () {
    Route::post('/check-domain', [AjaxController::class, 'checkDomain'])->name('api.check-domain'); // Kiểm tra domain có sẵn không
    Route::post('/buy-domain', [AjaxController::class, 'buyDomain'])->name('api.buy-domain'); // Mua domain qua AJAX
    Route::post('/buy-hosting', [AjaxController::class, 'buyHosting'])->name('api.buy-hosting'); // Mua hosting qua AJAX
    Route::post('/buy-vps', [AjaxController::class, 'buyVPS'])->name('api.buy-vps'); // Mua VPS qua AJAX
    Route::post('/buy-sourcecode', [AjaxController::class, 'buySourceCode'])->name('api.buy-sourcecode'); // Mua source code qua AJAX
    Route::post('/update-dns', [AjaxController::class, 'updateDns'])->name('api.update-dns'); // Cập nhật DNS qua AJAX
    Route::post('/recharge-card', [AjaxController::class, 'rechargeCard'])->name('api.recharge-card'); // Nạp thẻ cào qua AJAX
});

