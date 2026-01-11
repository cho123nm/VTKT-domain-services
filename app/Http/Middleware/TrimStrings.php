<?php
// Khai báo namespace cho Middleware này - thuộc App\Http\Middleware
namespace App\Http\Middleware;

// Import Middleware base class
use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware; // Base class cho string trimming middleware

/**
 * Class TrimStrings
 * Middleware tự động trim (loại bỏ khoảng trắng đầu cuối) các string trong request
 * Trừ các trường được liệt kê trong $except
 */
class TrimStrings extends Middleware
{
    /**
     * Danh sách tên các attributes không được trim
     * Các trường này sẽ giữ nguyên khoảng trắng đầu cuối (thường là mật khẩu)
     *
     * @var array<int, string>
     */
    protected $except = [
        'current_password', // Mật khẩu hiện tại (không trim để giữ nguyên format)
        'password', // Mật khẩu mới (không trim để giữ nguyên format)
        'password_confirmation', // Xác nhận mật khẩu mới (không trim để giữ nguyên format)
    ];
}

