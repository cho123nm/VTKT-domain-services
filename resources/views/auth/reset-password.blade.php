<!DOCTYPE html>
<html lang="en">
<head>
    <title>ĐẶT LẠI MẬT KHẨU</title>
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
                                <h1 class="text-dark mb-3 fs-3x">Đặt Lại Mật Khẩu</h1>
                                <div class="text-gray-400 fw-semibold fs-6">Nhập mật khẩu mới cho tài khoản của bạn</div>
                                @if(isset($error))
                                <div class="alert alert-danger mt-3">
                                    <strong>Lỗi:</strong> {{ $error }}
                                </div>
                                @endif
                            </div>

                            <input type="hidden" id="reset_token" name="token" value="{{ $token }}">
                            <input type="hidden" id="reset_email" name="email" value="{{ $email }}">

                            <div class="fv-row mb-10 fv-plugins-icon-container" data-kt-password-meter="true">
                                <div class="mb-1">
                                    <div class="position-relative mb-3">
                                        <input class="form-control form-control-lg form-control-solid" type="password" placeholder="Mật Khẩu Mới" id="password" autocomplete="new-password">
                                        <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                                            <i class="bi bi-eye-slash fs-2"></i>
                                            <i class="bi bi-eye fs-2 d-none"></i>
                                        </span>
                                    </div>
                                    <div class="text-muted fs-7">Mật khẩu phải có ít nhất 8 ký tự, gồm chữ và số</div>
                                </div>
                                <div class="fv-plugins-message-container invalid-feedback"></div>
                            </div>

                            <div class="fv-row mb-10 fv-plugins-icon-container" data-kt-password-meter="true">
                                <div class="mb-1">
                                    <div class="position-relative mb-3">
                                        <input class="form-control form-control-lg form-control-solid" type="password" placeholder="Xác Nhận Mật Khẩu" id="password_confirmation" autocomplete="new-password">
                                        <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                                            <i class="bi bi-eye-slash fs-2"></i>
                                            <i class="bi bi-eye fs-2 d-none"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="fv-plugins-message-container invalid-feedback"></div>
                            </div>

                            <div id="status"></div>

                            <div class="d-flex flex-stack">
                                <button id="resetPassword" class="btn btn-primary">
                                    <span class="indicator-label">Đặt Lại Mật Khẩu</span>
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
        // Lấy email và token trực tiếp từ URL để tránh bị thay đổi
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }
        
        // Lấy email và token từ URL
        var urlToken = getUrlParameter('token');
        var urlEmail = getUrlParameter('email');
        
        // Đảm bảo hidden inputs có giá trị đúng
        if (urlToken && $('#reset_token').length) {
            $('#reset_token').val(urlToken);
        }
        if (urlEmail && $('#reset_email').length) {
            $('#reset_email').val(urlEmail);
        }
        
        // Log để debug
        console.log('Reset Password - Initial Values:', {
            url_token: urlToken ? urlToken.substring(0, 20) + '...' : 'empty',
            url_email: urlEmail,
            hidden_token: $('#reset_token').val() ? $('#reset_token').val().substring(0, 20) + '...' : 'empty',
            hidden_email: $('#reset_email').val()
        });
        
        $('#resetPassword').on('click', function() {
            var $btn = $(this);
            
            // Lấy email và token từ URL (ưu tiên) hoặc từ hidden inputs
            var token = urlToken || $('#reset_token').val();
            var email = urlEmail || $('#reset_email').val();
            var password = $('#password').val();
            var password_confirmation = $('#password_confirmation').val();
            
            // Log để debug
            console.log('Reset Password - Data on Click:', {
                token: token ? token.substring(0, 20) + '...' : 'empty',
                email: email,
                email_from_url: urlEmail,
                email_from_hidden: $('#reset_email').val(),
                password_length: password ? password.length : 0,
                all_email_inputs: $('input[id*="email"]').map(function() { return $(this).attr('id') + '=' + $(this).val(); }).get()
            });
            
            // Validate email
            if (!email || email === '') {
                toastr.error("Email không hợp lệ! Vui lòng tải lại trang.", "Thông Báo");
                console.error('Email is empty!');
                return;
            }
            
            // Validate token
            if (!token || token === '') {
                toastr.error("Token không hợp lệ! Vui lòng tải lại trang.", "Thông Báo");
                console.error('Token is empty!');
                return;
            }
            
            if (!password) {
                toastr.error("Vui lòng nhập mật khẩu mới!", "Thông Báo");
                return;
            }

            if (password.length < 8) {
                toastr.error("Mật khẩu phải có ít nhất 8 ký tự!", "Thông Báo");
                return;
            }

            if (!password.match(/^(?=.*[A-Za-z])(?=.*\d)/)) {
                toastr.error("Mật khẩu phải gồm chữ và số!", "Thông Báo");
                return;
            }

            if (password !== password_confirmation) {
                toastr.error("Mật khẩu xác nhận không khớp!", "Thông Báo");
                return;
            }

            $btn.attr("disabled", true);
            $btn.find('.indicator-label').addClass('hidden');
            $btn.find('.indicator-progress').removeClass('hidden');

            // Đảm bảo gửi đúng email và token
            var formData = {
                _token: '{{ csrf_token() }}',
                token: token,
                email: email,
                password: password,
                password_confirmation: password_confirmation
            };
            
            console.log('Reset Password - Sending Data:', {
                email: formData.email,
                token: formData.token ? formData.token.substring(0, 20) + '...' : 'empty',
                password_length: formData.password ? formData.password.length : 0
            });

            $.ajax({
                url: '{{ route("password.reset") }}',
                type: 'POST',
                data: formData,
                success: function(data) {
                    $btn.attr("disabled", false);
                    $btn.find('.indicator-label').removeClass('hidden');
                    $btn.find('.indicator-progress').addClass('hidden');
                    
                    if (data.html) {
                        $('#status').html(data.html);
                    } else {
                        toastr.success(data.message || "Đặt lại mật khẩu thành công!", "Thông Báo");
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

