<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Đặt Lại Mật Khẩu - {{ config('app.name', 'SHOP VTKT') }}</title>
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
        
        /* Security Badge */
        .security-section {
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
        }
        
        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 16px 32px;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: #ffffff;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 8px 20px rgba(255, 107, 107, 0.3);
            margin-bottom: 20px;
        }
        
        .security-icon {
            font-size: 24px;
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-10deg); }
            75% { transform: rotate(10deg); }
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
        
        /* Info Card */
        .info-card {
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
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-title::before {
            content: '🔐';
            font-size: 28px;
        }
        
        /* Link Box */
        .link-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border: 2px dashed #667eea;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            word-break: break-all;
            text-align: center;
        }
        
        .link-text {
            color: #667eea;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Courier New', monospace;
        }
        
        /* Warning Box */
        .warning-box {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border-left: 4px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.2);
        }
        
        .warning-title {
            font-weight: 700;
            color: #856404;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
        }
        
        .warning-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .warning-list li {
            color: #856404;
            font-size: 14px;
            line-height: 1.8;
            padding: 8px 0;
            padding-left: 28px;
            position: relative;
        }
        
        .warning-list li::before {
            content: '⚠️';
            position: absolute;
            left: 0;
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
        
        /* Help Section */
        .help-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            margin: 30px 0;
        }
        
        .help-title {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 12px;
            font-size: 16px;
        }
        
        .help-text {
            color: #4a5568;
            font-size: 14px;
            line-height: 1.7;
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
            
            .info-card {
                padding: 20px;
            }
            
            .link-box {
                padding: 15px;
            }
            
            .link-text {
                font-size: 12px;
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
            <div class="logo">{{ config('app.name', 'SHOP VTKT') }}</div>
            <div class="header-subtitle">Đặt Lại Mật Khẩu</div>
        </div>
        
        <!-- Security Badge -->
        <div class="security-section">
            <div class="security-badge">
                <span class="security-icon">🔒</span>
                <span>Yêu Cầu Đặt Lại Mật Khẩu</span>
            </div>
        </div>
        
        <!-- Content -->
        <div class="email-content">
            <div class="greeting">Xin Chào {{ $user->taikhoan }}! 👋</div>
            
            <div class="welcome-text">
                Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản của mình tại 
                <strong style="color: #667eea;">{{ config('app.name', 'SHOP VTKT') }}</strong>.
            </div>
            
            <div class="welcome-text">
                Vui lòng click vào nút bên dưới để đặt lại mật khẩu mới cho tài khoản của bạn:
            </div>
            
            <!-- CTA Button -->
            <div class="cta-section">
                <a href="{{ $resetUrl }}" class="cta-button">🔐 Đặt Lại Mật Khẩu</a>
            </div>
            
            <!-- Link Box -->
            <div class="info-card">
                <div class="card-title">Hoặc Copy Link Này</div>
                <p style="color: #4a5568; margin-bottom: 15px; font-size: 14px;">
                    Nếu nút trên không hoạt động, vui lòng copy và dán link sau vào trình duyệt của bạn:
                </p>
                <div class="link-box">
                    <div class="link-text">{{ $resetUrl }}</div>
                </div>
            </div>
            
            <!-- Warning Box -->
            <div class="warning-box">
                <div class="warning-title">
                    <span>⚠️ Lưu Ý Quan Trọng</span>
                </div>
                <ul class="warning-list">
                    <li>Link này sẽ <strong>hết hạn sau 60 phút</strong> kể từ khi bạn nhận được email.</li>
                    <li>Chỉ sử dụng link này <strong>một lần duy nhất</strong> để đặt lại mật khẩu.</li>
                    <li>Nếu bạn <strong>không yêu cầu</strong> đặt lại mật khẩu, vui lòng <strong>bỏ qua email này</strong>.</li>
                    <li>Mật khẩu của bạn sẽ <strong>không thay đổi</strong> nếu bạn không click vào link trên.</li>
                    <li>Để bảo mật tài khoản, <strong>không chia sẻ</strong> link này với bất kỳ ai.</li>
                </ul>
            </div>
            
            <!-- Help Section -->
            <div class="help-section">
                <div class="help-title">❓ Cần Hỗ Trợ?</div>
                <div class="help-text">
                    Nếu bạn gặp vấn đề khi click vào nút hoặc link không hoạt động, vui lòng:
                    <ul style="margin-top: 10px; padding-left: 20px; color: #4a5568;">
                        <li>Copy toàn bộ link ở trên</li>
                        <li>Dán vào thanh địa chỉ trình duyệt</li>
                        <li>Nhấn Enter để truy cập</li>
                    </ul>
                </div>
            </div>
            
            <div class="welcome-text" style="text-align: center; margin-top: 20px; color: #718096; font-size: 14px;">
                Nếu bạn không yêu cầu đặt lại mật khẩu, bạn có thể bỏ qua email này một cách an toàn.
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-text">
                <strong style="color: #ffffff;">{{ config('app.name', 'SHOP VTKT') }}</strong>
            </div>
            <div class="footer-copyright">
                © {{ date('Y') }} {{ config('app.name', 'SHOP VTKT') }}. Tất cả quyền được bảo lưu.
            </div>
            <div class="footer-copyright" style="margin-top: 10px;">
                Email này được gửi tự động, vui lòng không trả lời trực tiếp email này.
            </div>
            <div class="footer-copyright" style="margin-top: 5px;">
                Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.
            </div>
        </div>
    </div>
</body>
</html>
