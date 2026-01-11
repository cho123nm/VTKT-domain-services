<?php
// Khai báo namespace cho Middleware này - thuộc App\Http\Middleware
namespace App\Http\Middleware;

// Import Middleware base class
use Illuminate\Cookie\Middleware\EncryptCookies as Middleware; // Base class cho cookie encryption middleware

/**
 * Class EncryptCookies
 * Middleware mã hóa cookies
 * Tự động mã hóa tất cả cookies trước khi gửi về client (trừ các cookies trong $except)
 */
class EncryptCookies extends Middleware
{
    /**
     * Danh sách tên các cookies không được mã hóa
     * Các cookies này sẽ được gửi dưới dạng plain text (không mã hóa)
     *
     * @var array<int, string>
     */
    protected $except = [
        // Hiện tại không có cookie nào được exclude khỏi encryption
    ];
}

