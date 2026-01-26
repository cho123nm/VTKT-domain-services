<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Xác Nhận Đơn Hàng - {{ config('app.name', 'SHOP VTKT') }}</title>
    <style>
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .email-wrapper {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        /* Header với gradient */
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }
        
        .email-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 20s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        
        .logo {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 1;
        }
        
        .header-subtitle {
            font-size: 16px;
            opacity: 0.95;
            font-weight: 400;
            position: relative;
            z-index: 1;
        }
        
        /* Success Badge */
        .success-section {
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
        }
        
        .success-badge {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 16px 32px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: #ffffff;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
            margin-bottom: 20px;
        }
        
        .success-icon {
            font-size: 24px;
            animation: checkmark 0.6s ease-in-out;
        }
        
        @keyframes checkmark {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        /* Content Section */
        .email-content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 24px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 16px;
        }
        
        .welcome-text {
            font-size: 16px;
            color: #4a5568;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        
        /* Order Info Card */
        .order-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 12px;
            padding: 30px;
            margin: 30px 0;
            border: 1px solid #e9ecef;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .card-title {
            font-size: 22px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-title::before {
            content: '📦';
            font-size: 28px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 16px 0;
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.2s;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-row:hover {
            background-color: #f8f9fa;
            margin: 0 -15px;
            padding-left: 15px;
            padding-right: 15px;
            border-radius: 8px;
        }
        
        .info-label {
            font-weight: 600;
            color: #4a5568;
            font-size: 15px;
            flex: 0 0 45%;
        }
        
        .info-value {
            color: #2d3748;
            font-size: 15px;
            text-align: right;
            flex: 1;
            font-weight: 500;
        }
        
        .info-value strong {
            color: #667eea;
            font-weight: 700;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-approved {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }
        
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        
        /* Note Box */
        .note-box {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border-left: 4px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.2);
        }
        
        .note-title {
            font-weight: 700;
            color: #856404;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
        }
        
        .note-text {
            color: #856404;
            font-size: 14px;
            line-height: 1.7;
        }
        
        /* CTA Button */
        .cta-section {
            text-align: center;
            margin: 40px 0;
        }
        
        .cta-button {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(102, 126, 234, 0.5);
        }
        
        /* Contact Section */
        .contact-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            margin: 30px 0;
        }
        
        .contact-title {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .contact-list {
            list-style: none;
            padding: 0;
        }
        
        .contact-list li {
            padding: 8px 0;
            color: #4a5568;
            font-size: 14px;
        }
        
        .contact-list li::before {
            content: '📧';
            margin-right: 10px;
        }
        
        /* Footer */
        .email-footer {
            background: #2d3748;
            color: #a0aec0;
            padding: 30px;
            text-align: center;
            font-size: 13px;
            line-height: 1.8;
        }
        
        .footer-text {
            margin-bottom: 8px;
        }
        
        .footer-copyright {
            color: #718096;
            font-size: 12px;
            margin-top: 15px;
        }
        
        /* Responsive */
        @media only screen and (max-width: 600px) {
            body {
                padding: 20px 10px;
            }
            
            .email-wrapper {
                border-radius: 12px;
            }
            
            .email-header {
                padding: 30px 20px;
            }
            
            .logo {
                font-size: 26px;
            }
            
            .email-content {
                padding: 30px 20px;
            }
            
            .order-card {
                padding: 20px;
            }
            
            .info-row {
                flex-direction: column;
                gap: 8px;
            }
            
            .info-label {
                flex: 1;
            }
            
            .info-value {
                text-align: left;
                flex: 1;
            }
            
            .cta-button {
                padding: 14px 30px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Header -->
        <div class="email-header">
            <div class="logo">{{ config('app.name', 'SHOP vtkt') }}</div>
            <div class="header-subtitle">Xác Nhận Đơn Hàng</div>
        </div>
        
        <!-- Success Badge -->
        <div class="success-section">
            <div class="success-badge">
                <span class="success-icon">✅</span>
                <span>Đơn Hàng Đã Được Tạo Thành Công</span>
            </div>
        </div>
        
        <!-- Content -->
        <div class="email-content">
            <div class="greeting">Xin Chào {{ $user->taikhoan }}! 👋</div>
            
            <div class="welcome-text">
                Cảm ơn bạn đã mua hàng tại <strong style="color: #667eea;">{{ config('app.name', 'SHOP vtkt') }}</strong>! 
                Đơn hàng của bạn đã được tạo thành công và đang chờ được xử lý. 
                Dưới đây là thông tin chi tiết đơn hàng của bạn:
            </div>
            
            <!-- Order Info Card -->
            <div class="order-card">
                <div class="card-title">Thông Tin Đơn Hàng</div>
                
                <div class="info-row">
                    <span class="info-label">Mã Giao Dịch (MGD):</span>
                    <span class="info-value"><strong>{{ $order->mgd ?? 'N/A' }}</strong></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Loại Dịch Vụ:</span>
                    <span class="info-value">
                        @if($orderType == 'domain')
                            <strong>🌐 Domain (Tên Miền)</strong>
                        @elseif($orderType == 'hosting')
                            <strong>💾 Hosting</strong>
                        @elseif($orderType == 'vps')
                            <strong>🖥️ VPS</strong>
                        @elseif($orderType == 'sourcecode')
                            <strong>💻 Source Code</strong>
                        @endif
                    </span>
                </div>
                
                @if($orderType == 'domain')
                    <div class="info-row">
                        <span class="info-label">Domain:</span>
                        <span class="info-value"><strong>{{ $order->domain ?? 'N/A' }}</strong></span>
                    </div>
                    @if(isset($order->ns1) && $order->ns1)
                        <div class="info-row">
                            <span class="info-label">Nameserver 1:</span>
                            <span class="info-value">{{ $order->ns1 }}</span>
                        </div>
                    @endif
                    @if(isset($order->ns2) && $order->ns2)
                        <div class="info-row">
                            <span class="info-label">Nameserver 2:</span>
                            <span class="info-value">{{ $order->ns2 }}</span>
                        </div>
                    @endif
                @elseif($orderType == 'hosting')
                    @if(isset($orderDetails['package_name']))
                        <div class="info-row">
                            <span class="info-label">Gói Hosting:</span>
                            <span class="info-value"><strong>{{ $orderDetails['package_name'] }}</strong></span>
                        </div>
                    @endif
                    @if(isset($orderDetails['period']))
                        <div class="info-row">
                            <span class="info-label">Thời Hạn:</span>
                            <span class="info-value">{{ $orderDetails['period'] == '1' ? '1 Tháng' : '12 Tháng' }}</span>
                        </div>
                    @endif
                @elseif($orderType == 'vps')
                    @if(isset($orderDetails['package_name']))
                        <div class="info-row">
                            <span class="info-label">Gói VPS:</span>
                            <span class="info-value"><strong>{{ $orderDetails['package_name'] }}</strong></span>
                        </div>
                    @endif
                    @if(isset($orderDetails['period']))
                        <div class="info-row">
                            <span class="info-label">Thời Hạn:</span>
                            <span class="info-value">{{ $orderDetails['period'] == 'month' ? '1 Tháng' : '12 Tháng' }}</span>
                        </div>
                    @endif
                @elseif($orderType == 'sourcecode')
                    @if(isset($orderDetails['source_code_name']))
                        <div class="info-row">
                            <span class="info-label">Source Code:</span>
                            <span class="info-value"><strong>{{ $orderDetails['source_code_name'] }}</strong></span>
                        </div>
                    @endif
                @endif
                
                @if(isset($orderDetails['price']))
                    <div class="info-row">
                        <span class="info-label">Giá:</span>
                        <span class="info-value"><strong style="color: #28a745; font-size: 18px;">{{ number_format($orderDetails['price']) }}₫</strong></span>
                    </div>
                @endif
                
                <div class="info-row">
                    <span class="info-label">Ngày Đặt Hàng:</span>
                    <span class="info-value">📅 {{ $order->time ?? date('d/m/Y H:i:s') }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Trạng Thái:</span>
                    <span class="info-value">
                        @if($order->status == 0)
                            <span class="status-badge status-pending">⏳ Đang Chờ Duyệt</span>
                        @elseif($order->status == 1)
                            <span class="status-badge status-approved">✅ Đã Duyệt</span>
                        @else
                            <span class="status-badge status-rejected">❌ Đã Từ Chối</span>
                        @endif
                    </span>
                </div>
            </div>
            
            <!-- Note Box -->
            <div class="note-box">
                <div class="note-title">
                    <span>⚠️ Lưu Ý Quan Trọng</span>
                </div>
                <div class="note-text">
                    Vui lòng <strong>lưu lại Mã Giao Dịch (MGD)</strong> để tra cứu đơn hàng sau này. 
                    Đơn hàng của bạn sẽ được xử lý trong thời gian sớm nhất. 
                    Chúng tôi sẽ thông báo cho bạn ngay khi đơn hàng được xử lý.
                </div>
            </div>
            
            <!-- CTA Button -->
            <div class="cta-section">
                <a href="{{ route('profile') }}" class="cta-button">Xem Chi Tiết Đơn Hàng</a>
            </div>
            
            <div class="welcome-text" style="text-align: center; margin-top: 20px;">
                Bạn có thể theo dõi trạng thái đơn hàng tại 
                <a href="{{ route('profile') }}" style="color: #667eea; text-decoration: none; font-weight: 600;">Trang Cá Nhân</a> của bạn.
            </div>
            
            <!-- Contact Section -->
            <div class="contact-section">
                <div class="contact-title">📞 Cần Hỗ Trợ?</div>
                <ul class="contact-list">
                    <li>Email: <strong>{{ config('mail.from.address', 'support@thanhvu.net') }}</strong></li>
                    <li>Hoặc sử dụng tính năng "Liên Hệ Admin" trên website</li>
                </ul>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-text">
                <strong style="color: #ffffff;">{{ config('app.name', 'SHOP vtkt') }}</strong>
            </div>
            <div class="footer-copyright">
                © {{ date('Y') }} {{ config('app.name', 'SHOP vtkt') }}. Tất cả quyền được bảo lưu.
            </div>
            <div class="footer-copyright" style="margin-top: 10px;">
                Email này được gửi tự động, vui lòng không trả lời trực tiếp email này.
            </div>
        </div>
    </div>
</body>
</html>
