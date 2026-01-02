<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Lại Mật Khẩu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #007bff;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .content {
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 14px 35px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 25px 0;
            text-align: center;
            font-weight: bold;
        }
        .button:hover {
            background: #0056b3;
        }
        .link-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
            word-break: break-all;
        }
        .warning {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #ffc107;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ config('app.name', 'THANHVU.NET V4') }}</div>
            <p style="color: #666; margin: 0;">Đặt Lại Mật Khẩu</p>
        </div>
        
        <div class="content">
            <h2 style="color: #333;">Xin Chào {{ $user->taikhoan }}!</h2>
            
            <p>Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản của mình tại <strong>{{ config('app.name', 'THANHVU.NET V4') }}</strong>.</p>
            
            <p>Vui lòng click vào nút bên dưới để đặt lại mật khẩu:</p>
            
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="button">🔐 Đặt Lại Mật Khẩu</a>
            </div>
            
            <p>Hoặc copy và dán link sau vào trình duyệt của bạn:</p>
            
            <div class="link-box">
                <strong style="color: #007bff;">{{ $resetUrl }}</strong>
            </div>
            
            <div class="warning">
                <strong>⚠️ Lưu ý quan trọng:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Link này sẽ <strong>hết hạn sau 60 phút</strong>.</li>
                    <li>Chỉ sử dụng link này một lần duy nhất.</li>
                    <li>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng <strong>bỏ qua email này</strong>.</li>
                    <li>Mật khẩu của bạn sẽ không thay đổi nếu bạn không click vào link trên.</li>
                </ul>
            </div>
            
            <p style="color: #666; font-size: 14px;">
                Nếu bạn gặp vấn đề khi click vào nút, vui lòng copy link ở trên và dán vào trình duyệt.
            </p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name', 'THANHVU.NET V4') }}. All rights reserved.</p>
            <p style="color: #999; font-size: 11px;">Email này được gửi tự động, vui lòng không trả lời.</p>
            <p style="color: #999; font-size: 11px;">Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
        </div>
    </div>
</body>
</html>

