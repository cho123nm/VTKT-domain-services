<?php
// Khai báo namespace cho Middleware này - thuộc App\Http\Middleware
namespace App\Http\Middleware;

// Import Middleware base class
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware; // Base class cho maintenance mode middleware

/**
 * Class PreventRequestsDuringMaintenance
 * Middleware ngăn chặn requests khi ứng dụng đang ở chế độ bảo trì
 * Khi chạy lệnh `php artisan down`, tất cả requests sẽ bị chặn (trừ các URI trong $except)
 */
class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * Danh sách các URI vẫn có thể truy cập được khi maintenance mode được bật
     * Các routes này sẽ không bị chặn khi ứng dụng đang bảo trì
     *
     * @var array<int, string>
     */
    protected $except = [
        // Hiện tại không có URI nào được exclude khỏi maintenance mode
    ];
}

