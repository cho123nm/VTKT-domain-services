<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AjaxController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// API routes for AJAX endpoints - cần session middleware
Route::middleware(['web'])->group(function () {
    Route::post('/check-domain', [AjaxController::class, 'checkDomain'])->name('api.check-domain');
    Route::post('/buy-domain', [AjaxController::class, 'buyDomain'])->name('api.buy-domain');
    Route::post('/buy-hosting', [AjaxController::class, 'buyHosting'])->name('api.buy-hosting');
    Route::post('/buy-vps', [AjaxController::class, 'buyVPS'])->name('api.buy-vps');
    Route::post('/buy-sourcecode', [AjaxController::class, 'buySourceCode'])->name('api.buy-sourcecode');
    Route::post('/update-dns', [AjaxController::class, 'updateDns'])->name('api.update-dns');
    Route::post('/recharge-card', [AjaxController::class, 'rechargeCard'])->name('api.recharge-card');
});

