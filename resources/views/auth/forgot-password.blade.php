<!DOCTYPE html>
<html lang="en">
<head>
    <title>QUÊN MẬT KHẨU</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="/assets/media/logos/favicon.ico" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <style>
        /* Đồng bộ theme với web */
        :root {
            --bg-color: #f5f8fa;
            --text-color: #181c32;
            --card-bg: #ffffff;
            --input-bg: #ffffff;
        }
        
        [data-theme="dark"] {
            --bg-color: #1e1e2d;
            --text-color: #e4e6ea;
            --card-bg: #2b2b40;
            --input-bg: #3a3a52;
        }
        
        body {
            background-color: var(--bg-color) !important;
            color: var(--text-color) !important;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        .form {
            background-color: var(--card-bg);
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }
        
        [data-theme="dark"] .form {
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }
        
        .form-control, .form-control-lg {
            background-color: var(--input-bg) !important;
            color: var(--text-color) !important;
            border-color: rgba(0, 0, 0, 0.1);
        }
        
        [data-theme="dark"] .form-control, [data-theme="dark"] .form-control-lg {
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        .text-dark {
            color: var(--text-color) !important;
        }
        
        .text-gray-400 {
            color: var(--text-color) !important;
            opacity: 0.7;
        }
    </style>
    <script>
        // Detect theme từ localStorage hoặc system preference
        var defaultThemeMode = "dark"; // Mặc định dark mode
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-theme-mode");
            } else {
                if (localStorage.getItem("data-theme") !== null) {
                    themeMode = localStorage.getItem("data-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-theme", themeMode);
        }
    </script>
</head>

<body class="header-fixed header-tablet-and-mobile-fixed aside-enabled sidebar-enabled page-loading-enabled">
    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <div class="d-flex flex-column flex-column-fluid flex-center w-100 p-10" style="min-height: 100vh;">
                <div class="d-flex justify-content-center align-items-center w-100" style="min-height: 100%;">
                    <div class="w-100 mw-450px">
                    <div class="d-flex flex-stack py-2">
                        <div class="me-2">
                            <a href="{{ route('home') }}" class="btn btn-icon bg-light rounded-circle">
                                <span class="svg-icon svg-icon-2 svg-icon-gray-800">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.60001 11H21C21.6 11 22 11.4 22 12C22 12.6 21.6 13 21 13H9.60001V11Z" fill="currentColor"></path>
                                        <path opacity="0.3" d="M9.6 20V4L2.3 11.3C1.9 11.7 1.9 12.3 2.3 12.7L9.6 20Z" fill="currentColor"></path>
                                    </svg>
                                </span>
                            </a>
                        </div>
                        <div class="m-0">
                            <span class="text-gray-400 fw-bold fs-5 me-2">Đã có tài khoản?</span>
                            <a href="{{ route('login') }}" class="link-primary fw-bold fs-5">Đăng Nhập</a>
                        </div>
                    </div>

                    <div class="py-20">
                        <div class="form w-100 fv-plugins-bootstrap5 fv-plugins-framework">
                            <div class="text-start mb-10">
                                <h1 class="text-dark mb-3 fs-3x">Quên Mật Khẩu</h1>
                                <div class="text-gray-400 fw-semibold fs-6">Nhập email của bạn để nhận link đặt lại mật khẩu</div>
                            </div>

                            <div class="fv-row mb-10 fv-plugins-icon-container">
                                <input class="form-control form-control-lg form-control-solid" type="email" placeholder="Email" id="email" value="" autocomplete="email">
                                <div class="fv-plugins-message-container invalid-feedback"></div>
                            </div>

                            <div id="status"></div>

                            <div class="d-flex flex-stack">
                                <button id="forgotPassword" class="btn btn-primary">
                                    <span class="indicator-label">Gửi Email Đặt Lại Mật Khẩu</span>
                                    <span class="indicator-progress hidden">Đang xử lý...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>

                            <div class="text-center mt-5">
                                <a href="{{ route('login') }}" class="link-primary fw-semibold">Quay lại đăng nhập</a>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $('#forgotPassword').on('click', function() {
            var $btn = $(this);
            var email = $('#email').val().trim();
            
            if (!email) {
                toastr.error("Vui lòng nhập email!", "Thông Báo");
                return;
            }

            if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                toastr.error("Email không hợp lệ!", "Thông Báo");
                return;
            }

            $btn.attr("disabled", true);
            $btn.find('.indicator-label').addClass('hidden');
            $btn.find('.indicator-progress').removeClass('hidden');

            $.ajax({
                url: '{{ route("password.forgot") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    email: email
                },
                success: function(data) {
                    $btn.attr("disabled", false);
                    $btn.find('.indicator-label').removeClass('hidden');
                    $btn.find('.indicator-progress').addClass('hidden');
                    
                    if (data.html) {
                        $('#status').html(data.html);
                    } else {
                        toastr.success(data.message || "Email đã được gửi!", "Thông Báo");
                    }
                },
                error: function(xhr) {
                    $btn.attr("disabled", false);
                    $btn.find('.indicator-label').removeClass('hidden');
                    $btn.find('.indicator-progress').addClass('hidden');
                    
                    if (xhr.responseJSON && xhr.responseJSON.html) {
                        $('#status').html(xhr.responseJSON.html);
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        toastr.error(xhr.responseJSON.message, "Thông Báo");
                    } else {
                        toastr.error("Có lỗi xảy ra!", "Thông Báo");
                    }
                }
            });
        });
    });
    </script>
</body>
</html>

