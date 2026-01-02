<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Nhận Đơn Hàng</title>
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
        .success-badge {
            display: inline-block;
            padding: 10px 20px;
            background: #28a745;
            color: white;
            border-radius: 5px;
            font-weight: bold;
            margin: 15px 0;
        }
        .order-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .button:hover {
            background: #0056b3;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        .note {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ config('app.name', 'THANHVU.NET V4') }}</div>
            <p style="color: #666; margin: 0;">Xác Nhận Đơn Hàng</p>
        </div>
        
        <div class="content">
            <h2 style="color: #333;">Xin Chào {{ $user->taikhoan }}!</h2>
            
            <div class="success-badge">✅ Đơn Hàng Đã Được Tạo Thành Công</div>
            
            <p>Cảm ơn bạn đã mua hàng tại <strong>{{ config('app.name', 'THANHVU.NET V4') }}</strong>!</p>
            
            <p>Đơn hàng của bạn đã được tạo thành công và đang chờ được xử lý. Thông tin chi tiết đơn hàng:</p>
            
            <div class="order-info">
                <h3 style="margin-top: 0; color: #007bff;">Thông Tin Đơn Hàng</h3>
                
                <div class="info-row">
                    <span class="info-label">Mã Giao Dịch (MGD):</span>
                    <span class="info-value"><strong>{{ $order->mgd ?? 'N/A' }}</strong></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Loại Dịch Vụ:</span>
                    <span class="info-value">
                        @if($orderType == 'domain')
                            <strong>Domain (Tên Miền)</strong>
                        @elseif($orderType == 'hosting')
                            <strong>Hosting</strong>
                        @elseif($orderType == 'vps')
                            <strong>VPS</strong>
                        @elseif($orderType == 'sourcecode')
                            <strong>Source Code</strong>
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
                        <span class="info-value"><strong>{{ number_format($orderDetails['price']) }}₫</strong></span>
                    </div>
                @endif
                
                <div class="info-row">
                    <span class="info-label">Ngày Đặt Hàng:</span>
                    <span class="info-value">{{ $order->time ?? date('d/m/Y H:i:s') }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Trạng Thái:</span>
                    <span class="info-value">
                        @if($order->status == 0)
                            <span style="color: #ffc107; font-weight: bold;">⏳ Đang Chờ Duyệt</span>
                        @elseif($order->status == 1)
                            <span style="color: #28a745; font-weight: bold;">✅ Đã Duyệt</span>
                        @else
                            <span style="color: #dc3545; font-weight: bold;">❌ Đã Từ Chối</span>
                        @endif
                    </span>
                </div>
            </div>
            
            <div class="note">
                <strong>📌 Lưu ý:</strong> Vui lòng lưu lại <strong>Mã Giao Dịch (MGD)</strong> để tra cứu đơn hàng sau này. 
                Đơn hàng của bạn sẽ được xử lý trong thời gian sớm nhất.
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('profile') }}" class="button">Xem Chi Tiết Đơn Hàng</a>
            </div>
            
            <p>Bạn có thể theo dõi trạng thái đơn hàng tại <a href="{{ route('profile') }}" style="color: #007bff;">Trang Cá Nhân</a> của bạn.</p>
            
            <p>Nếu có thắc mắc, vui lòng liên hệ với chúng tôi qua:</p>
            <ul>
                <li>Email: {{ config('mail.from.address', 'support@thanhvu.net') }}</li>
                <li>Hoặc sử dụng tính năng "Liên Hệ Admin" trên website</li>
            </ul>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name', 'THANHVU.NET V4') }}. All rights reserved.</p>
            <p style="color: #999; font-size: 11px;">Email này được gửi tự động, vui lòng không trả lời.</p>
        </div>
    </div>
</body>
</html>

