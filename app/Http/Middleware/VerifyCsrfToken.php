<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/callback',
        '/telegram/webhook',
        'admin/logout',
        // Tạm thời bỏ qua CSRF để debug - sẽ xóa sau
        // 'checkout/domain/process',
    ];
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // Log CSRF verification for checkout routes
        if (str_contains($request->path(), 'checkout') || str_contains($request->path(), 'domain/process')) {
            Log::info('CSRF Check - Checkout route', [
                'path' => $request->path(),
                'method' => $request->method(),
                'has_token' => $request->has('_token'),
                'token' => $request->input('_token') ? 'present' : 'missing',
                'token_header' => $request->header('X-CSRF-TOKEN') ? 'present' : 'missing',
                'session_id' => session()->getId(),
                'has_users_session' => session()->has('users'),
                'users_value' => session('users'),
                'cookie_header' => $request->header('Cookie') ? 'present' : 'missing'
            ]);
        }
        
        try {
            return parent::handle($request, $next);
        } catch (\Illuminate\Session\TokenMismatchException $e) {
            Log::error('CSRF Token Mismatch', [
                'path' => $request->path(),
                'method' => $request->method(),
                'session_id' => session()->getId(),
                'has_users_session' => session()->has('users'),
                'token_in_request' => $request->input('_token'),
                'token_in_header' => $request->header('X-CSRF-TOKEN'),
                'session_token' => session()->token()
            ]);
            throw $e;
        }
    }
}

