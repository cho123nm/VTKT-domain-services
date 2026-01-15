<!DOCTYPE html>
<html lang="vi" class="theme-dark">
<head>
    <meta charset="utf-8">
    <link href="{{ asset('assets/media/logos/favicon.ico') }}" rel="shortcut icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ADMIN LOGIN - THANHVU.NET V4">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng Nhập Admin - THANHVU.NET V4</title>

    <!-- BEGIN: CSS Assets-->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>  
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="stylesheet" href="{{ asset('Adminstators/dist/css/app.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- END: CSS Assets-->
    
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        
        /* Complex Dark Gradient Background */
        .login-container {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            position: relative;
            overflow: hidden;
            background: 
                radial-gradient(circle at 20% 30%, rgba(99, 102, 241, 0.4) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(236, 72, 153, 0.4) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(59, 130, 246, 0.3) 0%, transparent 60%),
                linear-gradient(135deg, #1e1b4b 0%, #312e81 25%, #581c87 50%, #7c2d12 75%, #1e3a8a 100%);
            background-size: 200% 200%, 200% 200%, 200% 200%, 400% 400%;
            animation: gradientShift 20s ease infinite;
        }
        
        /* Geometric Pattern Overlay */
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                linear-gradient(30deg, rgba(255, 255, 255, 0.05) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, 0.05) 87.5%, rgba(255, 255, 255, 0.05) 100%),
                linear-gradient(150deg, rgba(255, 255, 255, 0.05) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, 0.05) 87.5%, rgba(255, 255, 255, 0.05) 100%),
                linear-gradient(30deg, rgba(255, 255, 255, 0.05) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, 0.05) 87.5%, rgba(255, 255, 255, 0.05) 100%),
                linear-gradient(150deg, rgba(255, 255, 255, 0.05) 12%, transparent 12.5%, transparent 87%, rgba(255, 255, 255, 0.05) 87.5%, rgba(255, 255, 255, 0.05) 100%);
            background-size: 80px 140px, 80px 140px, 40px 70px, 40px 70px;
            background-position: 0 0, 0 0, 40px 70px, 40px 70px;
            opacity: 0.3;
            z-index: 1;
        }
        
        /* Glowing Light Blobs */
        .glow-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.3;
            animation: blobFloat 15s ease-in-out infinite;
        }
        
        .blob-1 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.6), transparent);
            top: -200px;
            left: -200px;
            animation-delay: 0s;
        }
        
        .blob-2 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(236, 72, 153, 0.6), transparent);
            bottom: -150px;
            right: -150px;
            animation-delay: 5s;
        }
        
        .blob-3 {
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.6), transparent);
            top: 50%;
            left: 20%;
            animation-delay: 10s;
        }
        
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%, 100% 50%, 50% 50%, 0% 50%; }
            50% { background-position: 100% 50%, 0% 50%, 50% 50%, 100% 50%; }
        }
        
        @keyframes blobFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }
        
        /* Left Side - 3D Dashboard Illustration */
        .left-panel {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            z-index: 2;
        }
        
        .dashboard-illustration {
            position: relative;
            width: 100%;
            max-width: 600px;
            height: 500px;
        }
        
        /* Floating Dashboard Elements */
        .dashboard-card {
            position: absolute;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            animation: floatCard 6s ease-in-out infinite;
        }
        
        .card-1 {
            top: 10%;
            left: 10%;
            width: 180px;
            height: 120px;
            animation-delay: 0s;
        }
        
        .card-2 {
            top: 30%;
            right: 15%;
            width: 200px;
            height: 140px;
            animation-delay: 2s;
        }
        
        .card-3 {
            bottom: 20%;
            left: 20%;
            width: 160px;
            height: 100px;
            animation-delay: 4s;
        }
        
        .chart-bar {
            width: 100%;
            height: 60px;
            display: flex;
            align-items: flex-end;
            gap: 8px;
            margin-top: 10px;
        }
        
        .bar {
            flex: 1;
            background: linear-gradient(180deg, #6366f1 0%, #8b5cf6 100%);
            border-radius: 4px 4px 0 0;
            animation: barGrow 2s ease-out;
        }
        
        .bar:nth-child(1) { height: 40%; animation-delay: 0.1s; }
        .bar:nth-child(2) { height: 70%; animation-delay: 0.2s; }
        .bar:nth-child(3) { height: 50%; animation-delay: 0.3s; }
        .bar:nth-child(4) { height: 85%; animation-delay: 0.4s; }
        .bar:nth-child(5) { height: 60%; animation-delay: 0.5s; }
        
        @keyframes barGrow {
            from { height: 0; }
            to { height: var(--height); }
        }
        
        /* Floating Network Icons */
        .network-icon {
            position: absolute;
            width: 50px;
            height: 50px;
            background: rgba(99, 102, 241, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(99, 102, 241, 0.4);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #818cf8;
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.5);
            animation: iconFloat 8s ease-in-out infinite;
        }
        
        .icon-1 {
            top: 15%;
            right: 25%;
            animation-delay: 0s;
        }
        
        .icon-2 {
            bottom: 25%;
            right: 10%;
            animation-delay: 2s;
        }
        
        .icon-3 {
            top: 50%;
            left: 5%;
            animation-delay: 4s;
        }
        
        /* Glowing Connection Lines */
        .connection-line {
            position: absolute;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(99, 102, 241, 0.6), transparent);
            box-shadow: 0 0 10px rgba(99, 102, 241, 0.8);
            animation: linePulse 3s ease-in-out infinite;
        }
        
        .line-1 {
            top: 25%;
            left: 30%;
            width: 150px;
            transform: rotate(25deg);
        }
        
        .line-2 {
            bottom: 30%;
            left: 25%;
            width: 120px;
            transform: rotate(-35deg);
        }
        
        @keyframes floatCard {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
        }
        
        @keyframes iconFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(10px, -15px) scale(1.1); }
        }
        
        @keyframes linePulse {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 1; }
        }
        
        /* Right Side - Login Form */
        .right-panel {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            z-index: 2;
        }
        
        .login-box {
            width: 100%;
            max-width: 550px;
            margin: 0 auto;
        }
        
        /* Glassmorphism Card */
        .login-card {
            position: relative;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px) saturate(180%);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset,
                0 0 80px rgba(99, 102, 241, 0.3);
            padding: 48px;
            transition: all 0.3s ease;
            width: 100%;
            box-sizing: border-box;
        }
        
        .login-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.6), rgba(236, 72, 153, 0.6), rgba(59, 130, 246, 0.6));
            border-radius: 24px;
            z-index: -1;
            opacity: 0.5;
            filter: blur(20px);
            animation: borderGlow 3s ease-in-out infinite;
        }
        
        @keyframes borderGlow {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.6; }
        }
        
        /* Logo Section */
        .logo-container {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo-image {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            animation: logoFloat 3s ease-in-out infinite;
        }
        
        @keyframes logoFloat {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-8px) scale(1.02); }
        }
        
        .logo-title {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, #e0e7ff 50%, #c7d2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }
        
        .logo-subtitle {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            letter-spacing: 1px;
            font-weight: 500;
        }
        
        /* Input Fields - Using Tailwind classes for flexbox alignment */
        .input-group {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
            width: 100%;
            box-sizing: border-box;
        }
        
        .input-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            min-width: 80px;
            text-align: right;
            flex-shrink: 0;
        }
        
        .input-wrapper {
            position: relative;
            flex: 1;
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: rgba(255, 255, 255, 0.5);
            z-index: 10;
            pointer-events: none;
            transition: color 0.3s ease;
        }
        
        .input-field {
            width: 100%;
            padding: 14px 16px 14px 48px;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #ffffff;
            font-size: 15px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            box-sizing: border-box;
        }
        
        .input-field::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }
        
        .input-field:focus {
            outline: none;
            border-color: rgba(99, 102, 241, 0.6);
            background: rgba(0, 0, 0, 0.4);
            box-shadow: 
                0 0 0 3px rgba(99, 102, 241, 0.1),
                0 0 20px rgba(99, 102, 241, 0.3);
        }
        
        .input-field:focus + .input-icon,
        .input-wrapper:has(.input-field:focus) .input-icon {
            color: #818cf8;
        }
        
        /* Login Button */
        .btn-login {
            width: 100%;
            padding: 16px 24px;
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 50%, #ec4899 100%);
            background-size: 200% 200%;
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 
                0 4px 20px rgba(99, 102, 241, 0.4),
                0 0 30px rgba(236, 72, 153, 0.3);
            position: relative;
            overflow: hidden;
            margin-top: 0;
            box-sizing: border-box;
        }
        
        /* Form Container */
        #adminLoginForm {
            width: 100%;
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
            margin-left: -70px;
            padding-left: 70px;
        }
        
        /* Button Wrapper to match input width */
        .btn-wrapper {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-top: 8px;
            width: 100%;
            box-sizing: border-box;
        }
        
        .btn-spacer {
            min-width: 80px;
            flex-shrink: 0;
        }
        
        .btn-container {
            flex: 1;
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
        }
        
        /* Fix overflow issues */
        .login-box {
            overflow-x: hidden;
        }
        
        .login-card {
            overflow-x: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-login:hover {
            background-position: 100% 0;
            transform: translateY(-2px);
            box-shadow: 
                0 6px 30px rgba(99, 102, 241, 0.6),
                0 0 40px rgba(236, 72, 153, 0.5);
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        /* Alerts */
        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
        }
        
        .alert-danger-soft {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }
        
        .alert-success-soft {
            background: rgba(34, 197, 94, 0.2);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #86efac;
        }
        
        /* Footer Links */
        .footer-links {
            text-align: center;
            margin-top: 24px;
        }
        
        .footer-link {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }
        
        .footer-link:hover {
            color: rgba(255, 255, 255, 0.9);
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .login-container {
                grid-template-columns: 1fr;
            }
            
            .left-panel {
                display: none;
            }
            
            .right-panel {
                padding: 40px 20px;
            }
        }
        
        @media (max-width: 768px) {
            .right-panel {
                padding: 30px 15px;
            }
            
            .login-card {
                padding: 32px 24px;
                max-width: 100%;
            }
            
            .logo-title {
                font-size: 24px;
            }
            
            /* Fix form container for mobile */
            #adminLoginForm {
                margin-left: 0;
                padding-left: 0;
                width: 100%;
                max-width: 100%;
            }
            
            .input-group {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
                width: 100%;
                margin-bottom: 20px;
            }
            
            .input-label {
                min-width: auto;
                text-align: left;
                width: 100%;
            }
            
            .input-wrapper {
                width: 100%;
                max-width: 100%;
            }
            
            .input-field {
                width: 100%;
                max-width: 100%;
            }
            
            .btn-wrapper {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
                width: 100%;
            }
            
            .btn-spacer {
                display: none;
            }
            
            .btn-container {
                width: 100%;
            }
            
            .btn-login {
                width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            .right-panel {
                padding: 20px 10px;
            }
            
            .login-card {
                padding: 24px 16px;
                border-radius: 16px;
                max-width: 100%;
                margin: 0 auto;
            }
            
            .login-box {
                width: 100%;
                max-width: 100%;
                padding: 0;
            }
            
            .logo-image {
                width: 60px;
                height: 60px;
            }
            
            .logo-title {
                font-size: 20px;
            }
            
            .logo-subtitle {
                font-size: 12px;
            }
            
            /* Ensure no horizontal overflow */
            body {
                overflow-x: hidden;
            }
            
            .login-container {
                overflow-x: hidden;
            }
            
            /* Fix any potential overflow */
            * {
                max-width: 100%;
            }
            
            .input-field,
            .btn-login,
            .alert {
                max-width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body class="login-container">
    <!-- Glowing Light Blobs -->
    <div class="glow-blob blob-1"></div>
    <div class="glow-blob blob-2"></div>
    <div class="glow-blob blob-3"></div>
    
    <!-- Left Panel - 3D Dashboard Illustration -->
    <div class="left-panel">
        <div class="dashboard-illustration">
            <!-- Dashboard Cards -->
            <div class="dashboard-card card-1">
                <div class="chart-bar">
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                </div>
            </div>
            
            <div class="dashboard-card card-2">
                <div style="color: rgba(255,255,255,0.8); font-size: 12px; margin-bottom: 10px;">Analytics</div>
                <div style="color: #818cf8; font-size: 24px; font-weight: 700;">1,234</div>
                <div style="color: rgba(255,255,255,0.5); font-size: 11px; margin-top: 5px;">+12% from last month</div>
            </div>
            
            <div class="dashboard-card card-3">
                <div style="color: rgba(255,255,255,0.8); font-size: 12px; margin-bottom: 10px;">Users</div>
                <div style="color: #ec4899; font-size: 20px; font-weight: 700;">5,678</div>
            </div>
            
            <!-- Floating Network Icons -->
            <div class="network-icon icon-1">
                <i data-lucide="wifi" class="w-6 h-6"></i>
            </div>
            <div class="network-icon icon-2">
                <i data-lucide="globe" class="w-6 h-6"></i>
            </div>
            <div class="network-icon icon-3">
                <i data-lucide="server" class="w-6 h-6"></i>
            </div>
            
            <!-- Connection Lines -->
            <div class="connection-line line-1"></div>
            <div class="connection-line line-2"></div>
        </div>
    </div>
    
    <!-- Right Panel - Login Form -->
    <div class="right-panel">
        <div class="login-box">
            <div class="login-card">
                <!-- Logo Section -->
                <div class="logo-container">
                    <div class="logo-image">
                        @if(file_exists(public_path('images/admin/logo.png')))
                            <img alt="Admin Logo" src="{{ asset('images/admin/logo.png') }}" class="w-full h-full rounded-lg">
                        @elseif(file_exists(public_path('images/admin/logo.jpg')))
                            <img alt="Admin Logo" src="{{ asset('images/admin/logo.jpg') }}" class="w-full h-full rounded-lg">
                        @else
                            <img alt="Logo" src="{{ asset('images/logo.jpg') }}" class="w-full h-full rounded-lg">
                        @endif
                    </div>
                    <h1 class="logo-title">Admin Panel</h1>
                    <p class="logo-subtitle">THANHVU.NET V4</p>
                </div>
                
                <!-- Alerts -->
                @if(session('error'))
                    <div class="alert alert-danger-soft">
                        <i data-lucide="alert-circle" class="w-5 h-5 inline mr-2"></i>
                        {{ session('error') }}
                    </div>
                @endif
                
                @if(session('success'))
                    <div class="alert alert-success-soft">
                        <i data-lucide="check-circle" class="w-5 h-5 inline mr-2"></i>
                        {{ session('success') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger-soft">
                        <i data-lucide="alert-circle" class="w-5 h-5 inline mr-2"></i>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                
                <!-- Login Form -->
                <form id="adminLoginForm" method="POST" action="{{ route('admin.auth.login.post') }}">
                    @csrf
                    
                    <div class="input-group flex items-center gap-4 mb-6">
                        <label for="taikhoan" class="input-label">Tài khoản</label>
                        <div class="input-wrapper flex-1">
                            <i data-lucide="user" class="input-icon"></i>
                            <input 
                                id="taikhoan" 
                                type="text" 
                                name="taikhoan" 
                                class="input-field w-full" 
                                placeholder="Nhập tài khoản"
                                value="{{ old('taikhoan') }}"
                                required 
                                autofocus
                            >
                        </div>
                    </div>
                    
                    <div class="input-group flex items-center gap-4 mb-6">
                        <label for="matkhau" class="input-label">Mật khẩu</label>
                        <div class="input-wrapper flex-1">
                            <i data-lucide="lock" class="input-icon"></i>
                            <input 
                                id="matkhau" 
                                type="password" 
                                name="matkhau" 
                                class="input-field w-full" 
                                placeholder="Nhập mật khẩu"
                                required
                            >
                        </div>
                    </div>
                    
                    <div class="btn-wrapper flex items-center gap-4">
                        <div class="btn-spacer"></div>
                        <div class="btn-container flex-1">
                            <button type="submit" class="btn-login w-full">
                                Đăng Nhập
                            </button>
                        </div>
                    </div>
                </form>
                
                <div class="footer-links">
                    <a href="{{ route('home') }}" class="footer-link">← Về trang chủ</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- BEGIN: JS Assets-->
    <script src="{{ asset('Adminstators/dist/js/app.js') }}"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        
        // Handle form submission with AJAX
        document.getElementById('adminLoginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // Lấy CSRF token từ meta tag hoặc từ form
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                       form.querySelector('input[name="_token"]')?.value;
            
            if (!csrfToken) {
                toastr.error('CSRF token không tìm thấy, vui lòng tải lại trang!', 'Lỗi');
                return;
            }
            
            // Đảm bảo _token có trong formData
            if (!formData.has('_token')) {
                formData.append('_token', csrfToken);
            }
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang đăng nhập...';
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                // Kiểm tra nếu response không phải JSON (có thể là redirect hoặc error page)
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        throw new Error('Server trả về response không hợp lệ');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    toastr.success(data.message || 'Đăng nhập thành công!');
                    setTimeout(() => {
                        window.location.href = data.redirect || '{{ route("admin.dashboard") }}';
                    }, 1000);
                } else {
                    toastr.error(data.message || 'Đăng nhập thất bại!');
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Login error:', error);
                toastr.error('Có lỗi xảy ra: ' + error.message, 'Lỗi');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                
                // Fallback: submit form thông thường nếu AJAX fail
                // form.submit();
            });
        });
    </script>
    <!-- END: JS Assets-->
</body>
</html>
