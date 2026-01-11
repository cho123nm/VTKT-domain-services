<?php
// Khai báo namespace cho Middleware này - thuộc App\Http\Middleware
namespace App\Http\Middleware;

// Import Middleware base class và Request class
use Illuminate\Http\Middleware\TrustProxies as Middleware; // Base class cho trust proxies middleware
use Illuminate\Http\Request; // Class xử lý HTTP request

/**
 * Class TrustProxies
 * Middleware tin cậy proxies (load balancer, reverse proxy, etc.)
 * Cho phép ứng dụng nhận đúng IP address và thông tin từ client khi đứng sau proxy
 */
class TrustProxies extends Middleware
{
    /**
     * Danh sách các proxies được tin cậy cho ứng dụng này
     * Có thể là mảng IP addresses, '*' (tin cậy tất cả), hoặc null (không tin cậy proxy nào)
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * Các headers được dùng để phát hiện proxies
     * Kết hợp các header flags bằng toán tử bitwise OR (|)
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR | // IP address của client (qua proxy)
        Request::HEADER_X_FORWARDED_HOST | // Host name (qua proxy)
        Request::HEADER_X_FORWARDED_PORT | // Port (qua proxy)
        Request::HEADER_X_FORWARDED_PROTO | // Protocol (HTTP/HTTPS) (qua proxy)
        Request::HEADER_X_FORWARDED_AWS_ELB; // AWS Elastic Load Balancer header
}

