<?php
// Khai báo namespace cho Middleware này - thuộc App\Http\Middleware
namespace App\Http\Middleware;

// Import Middleware base class
use Illuminate\Routing\Middleware\ValidateSignature as Middleware; // Base class cho signature validation middleware

/**
 * Class ValidateSignature
 * Middleware xác thực signed URL
 * Kiểm tra URL có được ký (signed) hợp lệ không để đảm bảo tính toàn vẹn của URL
 */
class ValidateSignature extends Middleware
{
    /**
     * Danh sách tên các query string parameters nên bỏ qua khi validate signature
     * Các parameters này sẽ không được tính vào signature (thường là tracking parameters)
     *
     * @var array<int, string>
     */
    protected $except = [
        // 'fbclid', // Facebook Click ID (đã comment)
        // 'utm_campaign', // UTM Campaign parameter (đã comment)
        // 'utm_content', // UTM Content parameter (đã comment)
        // 'utm_medium', // UTM Medium parameter (đã comment)
        // 'utm_source', // UTM Source parameter (đã comment)
        // 'utm_term', // UTM Term parameter (đã comment)
    ];
}

