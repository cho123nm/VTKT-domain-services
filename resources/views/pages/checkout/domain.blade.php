@extends('layouts.app')

@section('content')
<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
    <div class="app-container container-xxl d-flex flex-row flex-column-fluid">
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <div class="card mb-5 mb-xl-10">
                    </div>
                    <div class="card card-docs flex-row-fluid mb-2">
                        <div class="card-body fs-6 py-15 px-10 py-lg-15 px-lg-15 text-gray-700">
                            <div class="py-10">
                                <h1 class="anchor fw-bold mb-5" id="text-input" data-kt-scroll-offset="50">
                                    <a href="#text-input"></a> Đăng Ký Tên Miền &nbsp; <img src="{{ fixImagePath($domain->image) }}" width="50px">
                                </h1>
                                <div class="py-5">
                                    <div class="form fv-plugins-bootstrap5 fv-plugins-framework">
                                        <div class="fv-row mb-10 fv-plugins-icon-container">
                                            <label class="required fw-semibold fs-6 mb-2"> Tên Miền </label>
                                            <input type="text" id="domain" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Tên Miền Cần Mua" value="{{ $domainName }}" disabled>
                                        </div>
                                        <div class="fv-row mb-10 fv-plugins-icon-container">
                                            <label class="required fw-semibold fs-6 mb-2"> NS1 </label>
                                            <input type="text" id="ns1" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="NS1 Của Cloudflare">
                                        </div>
                                        <div class="fv-row mb-10 fv-plugins-icon-container">
                                            <label class="required fw-semibold fs-6 mb-2"> NS2 </label>
                                            <input type="text" id="ns2" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="NS2 Của Cloudflare">
                                        </div>
                                        <div class="fv-row mb-10 fv-plugins-icon-container">
                                            <label class="required fw-semibold fs-6 mb-2"> Hạn Dùng </label>
                                            <select id="hsd" class="form-select">
                                                <option value="1"> 1 Năm </option>
                                            </select>
                                            <div id="status"></div>
                                            <div class="fv-plugins-message-container invalid-feedback"></div>
                                        </div>
                                        <button id="buy" type="button" class="btn btn-primary">
                                            <span class="indicator-label">Mua Ngay - <span id="price-display">{{ number_format($price ?? 0) }}</span>đ</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden inputs để lưu URLs -->
<input type="hidden" id="checkout-url" value="{{ route('checkout.domain.process') }}">
<input type="hidden" id="profile-url" value="{{ route('profile') }}">
<input type="hidden" id="csrf-token" value="{{ csrf_token() }}">

<script type="text/javascript">
// @ts-nocheck
// eslint-disable-next-line
(function() {
    'use strict';
    
    console.log('=== DOMAIN CHECKOUT SCRIPT START ===');
    console.log('Script loaded at:', new Date().toISOString());
    console.log('Window location:', window.location.href);
    console.log('Document ready state:', document.readyState);
    
    // Lấy URLs từ hidden inputs
    var checkoutUrl = '';
    var profileUrl = '';
    var csrfToken = '';
    
    try {
        var checkoutInput = document.getElementById('checkout-url');
        var profileInput = document.getElementById('profile-url');
        var tokenInput = document.getElementById('csrf-token');
        
        if (checkoutInput) checkoutUrl = checkoutInput.value || '';
        if (profileInput) profileUrl = profileInput.value || '';
        if (tokenInput) csrfToken = tokenInput.value || '';
        
        console.log('URLs loaded:', {
            checkout: checkoutUrl,
            profile: profileUrl,
            hasToken: !!csrfToken
        });
    } catch (e) {
        console.error('Error getting URLs:', e);
    }
    
    // Hàm khởi tạo event handler
    function initBuyButton() {
        var retryCount = 0;
        var maxRetries = 50; // Tối đa 5 giây (50 * 100ms)
        
        function tryInit() {
            // Kiểm tra jQuery
            if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
                retryCount++;
                if (retryCount < maxRetries) {
                    console.log('jQuery not loaded yet, retrying... (' + retryCount + '/' + maxRetries + ')');
                    setTimeout(tryInit, 100);
                } else {
                    console.error('jQuery failed to load after ' + maxRetries + ' retries!');
                    // Fallback: sử dụng vanilla JavaScript
                    initBuyButtonVanilla();
                }
                return;
            }
            
            console.log('jQuery available, version:', jQuery.fn.jquery);
            
            // Đợi DOM ready
            jQuery(document).ready(function($) {
                console.log('DOM ready, setting up buy button handler');
                
                var $buyBtn = $('#buy');
                
                if ($buyBtn.length === 0) {
                    console.error('Buy button not found!');
                    // Thử lại sau 100ms
                    setTimeout(function() {
                        initBuyButton();
                    }, 100);
                    return;
                }
                
                console.log('Buy button found, attaching click handler');
                
                // Test click ngay lập tức
                $buyBtn.on('click', function() {
                    console.log('TEST: Button click detected!');
                });
                
                // Xóa tất cả event handlers cũ và thêm mới
                $buyBtn.off('click').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    console.log('=== BUY BUTTON CLICKED ===');
                    console.log('Timestamp:', new Date().toISOString());
                    
                    var $btn = $(this);
                    var $indicator = $btn.find('.indicator-label');
                    var originalText = $indicator.length > 0 ? $indicator.text() : $btn.text();
                    
                    // Kiểm tra URLs
                    if (!checkoutUrl) {
                        console.error('Checkout URL is empty!');
                        alert('Lỗi: Không tìm thấy URL thanh toán. Vui lòng tải lại trang.');
                        return;
                    }
                    
                    // Disable button
                    $btn.prop("disabled", true);
                    if ($indicator.length > 0) {
                        $indicator.text('Đang xử lý...');
                    } else {
                        $btn.text('Đang xử lý...');
                    }
                    
                    // Lấy dữ liệu form
                    var domain = $('#domain').val() || '';
                    var ns1 = $('#ns1').val() || '';
                    var ns2 = $('#ns2').val() || '';
                    var hsd = $('#hsd').val() || '1';
                    
                    console.log('Form data:', {
                        domain: domain,
                        ns1: ns1,
                        ns2: ns2,
                        hsd: hsd
                    });
                    
                    // Validation
                    if (!ns1 || !ns2) {
                        console.warn('Validation failed: NS1 or NS2 is empty');
                        if (typeof toastr !== 'undefined') {
                            toastr.error("Vui lòng nhập đầy đủ NS1 và NS2", "Thông Báo");
                        } else {
                            alert("Vui lòng nhập đầy đủ NS1 và NS2");
                        }
                        $btn.prop("disabled", false);
                        if ($indicator.length > 0) {
                            $indicator.text(originalText);
                        } else {
                            $btn.text(originalText);
                        }
                        return;
                    }
                    
                    // Gửi AJAX request
                    console.log('=== AJAX REQUEST DEBUG ===');
                    console.log('URL:', checkoutUrl);
                    console.log('Full URL will be:', window.location.origin + checkoutUrl);
                    console.log('CSRF Token:', csrfToken ? 'Present (' + csrfToken.substring(0, 10) + '...)' : 'Missing');
                    console.log('Cookies:', document.cookie);
                    console.log('Session Cookie:', document.cookie.match(/laravel_session=[^;]+/));
                    console.log('Form Data:', {
                        domain: domain,
                        ns1: ns1,
                        ns2: ns2,
                        hsd: hsd
                    });
                    
                    $.ajax({
                        url: checkoutUrl,
                        type: 'POST',
                        xhrFields: {
                            withCredentials: true
                        },
                        crossDomain: false,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        data: {
                            domain: domain,
                            ns1: ns1,
                            ns2: ns2,
                            hsd: hsd,
                            _token: csrfToken
                        },
                        dataType: 'json',
                        cache: false,
                        beforeSend: function(xhr) {
                            console.log('AJAX BeforeSend - Headers:', {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            });
                        },
                        success: function(data) {
                            console.log('AJAX Success:', data);
                            
                            $btn.prop("disabled", false);
                            if ($indicator.length > 0) {
                                $indicator.text(originalText);
                            } else {
                                $btn.text(originalText);
                            }
                            
                            if (data && data.success) {
                                var message = data.message || 'Thành công!';
                                
                                // Xử lý nếu server trả về HTML với script tag
                                if (data.html) {
                                    var htmlContent = data.html;
                                    
                                    // Extract và thực thi script tags
                                    var scriptRegex = /<script[^>]*>([\s\S]*?)<\/script>/gi;
                                    var scripts = htmlContent.match(scriptRegex);
                                    
                                    if (scripts && scripts.length > 0) {
                                        scripts.forEach(function(scriptTag) {
                                            var scriptContent = scriptTag.replace(/<\/?script[^>]*>/gi, '');
                                            try {
                                                eval(scriptContent);
                                            } catch (e) {
                                                console.error('Error executing extracted script:', e);
                                            }
                                        });
                                    }
                                    
                                    // Hiển thị HTML không có script tag
                                    var htmlWithoutScript = htmlContent.replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '');
                                    if (htmlWithoutScript.trim()) {
                                        $('#status').html(htmlWithoutScript);
                                    }
                                } else {
                                    // Hiển thị message thông thường
                                    if (typeof toastr !== 'undefined') {
                                        toastr.success(message, "Thông Báo");
                                    } else {
                                        alert(message);
                                    }
                                    $('#status').html('<div class="alert alert-success mt-3">' + message + '</div>');
                                }
                                
                                setTimeout(function() {
                                    if (profileUrl) {
                                        window.location.href = profileUrl;
                                    } else {
                                        window.location.reload();
                                    }
                                }, 2000);
                            } else {
                                var errorMsg = (data && data.message) ? data.message : 'Có lỗi xảy ra!';
                                
                                // Xử lý nếu server trả về HTML với script tag trong error
                                if (data && data.html) {
                                    var htmlContent = data.html;
                                    var scriptRegex = /<script[^>]*>([\s\S]*?)<\/script>/gi;
                                    var scripts = htmlContent.match(scriptRegex);
                                    
                                    if (scripts && scripts.length > 0) {
                                        scripts.forEach(function(scriptTag) {
                                            var scriptContent = scriptTag.replace(/<\/?script[^>]*>/gi, '');
                                            try {
                                                eval(scriptContent);
                                            } catch (e) {
                                                console.error('Error executing extracted script:', e);
                                            }
                                        });
                                    }
                                    
                                    var htmlWithoutScript = htmlContent.replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '');
                                    if (htmlWithoutScript.trim()) {
                                        $('#status').html(htmlWithoutScript);
                                    }
                                } else {
                                    if (typeof toastr !== 'undefined') {
                                        toastr.error(errorMsg, "Thông Báo");
                                    } else {
                                        alert(errorMsg);
                                    }
                                    $('#status').html('<div class="alert alert-danger mt-3">' + errorMsg + '</div>');
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error Details:', {
                                status: xhr.status,
                                statusText: xhr.statusText,
                                error: error,
                                statusCode: xhr.status,
                                responseText: xhr.responseText,
                                responseJSON: xhr.responseJSON,
                                readyState: xhr.readyState,
                                url: checkoutUrl,
                                method: 'POST'
                            });
                            
                            // Log chi tiết nếu là lỗi 419 (CSRF token mismatch)
                            if (xhr.status === 419) {
                                console.error('CSRF Token Mismatch! Current token:', csrfToken);
                                console.error('Response:', xhr.responseText);
                            }
                            
                            $btn.prop("disabled", false);
                            if ($indicator.length > 0) {
                                $indicator.text(originalText);
                            } else {
                                $btn.text(originalText);
                            }
                            
                            var errorMessage = 'Có lỗi xảy ra!';
                            var hasHtmlResponse = false;
                            
                            try {
                                // Xử lý responseJSON
                                if (xhr.responseJSON) {
                                    if (xhr.responseJSON.html) {
                                        // Server trả về HTML với script tag - cần extract và thực thi
                                        hasHtmlResponse = true;
                                        var htmlContent = xhr.responseJSON.html;
                                        
                                        // Extract script tags và thực thi chúng
                                        var scriptRegex = /<script[^>]*>([\s\S]*?)<\/script>/gi;
                                        var scripts = htmlContent.match(scriptRegex);
                                        
                                        if (scripts && scripts.length > 0) {
                                            scripts.forEach(function(scriptTag) {
                                                var scriptContent = scriptTag.replace(/<\/?script[^>]*>/gi, '');
                                                try {
                                                    // Thực thi script content
                                                    eval(scriptContent);
                                                } catch (e) {
                                                    console.error('Error executing extracted script:', e);
                                                }
                                            });
                                        }
                                        
                                        // Hiển thị HTML không có script tag
                                        var htmlWithoutScript = htmlContent.replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '');
                                        if (htmlWithoutScript.trim()) {
                                            $('#status').html(htmlWithoutScript);
                                        }
                                    } else if (xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                } else if (xhr.status === 419) {
                                    errorMessage = 'Phiên làm việc đã hết hạn, vui lòng tải lại trang!';
                                } else if (xhr.status === 0) {
                                    errorMessage = 'Không thể kết nối đến server!';
                                } else if (xhr.responseText) {
                                    try {
                                        var response = JSON.parse(xhr.responseText);
                                        if (response.html) {
                                            hasHtmlResponse = true;
                                            var htmlContent = response.html;
                                            var scriptRegex = /<script[^>]*>([\s\S]*?)<\/script>/gi;
                                            var scripts = htmlContent.match(scriptRegex);
                                            
                                            if (scripts && scripts.length > 0) {
                                                scripts.forEach(function(scriptTag) {
                                                    var scriptContent = scriptTag.replace(/<\/?script[^>]*>/gi, '');
                                                    try {
                                                        eval(scriptContent);
                                                    } catch (e) {
                                                        console.error('Error executing extracted script:', e);
                                                    }
                                                });
                                            }
                                            
                                            var htmlWithoutScript = htmlContent.replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '');
                                            if (htmlWithoutScript.trim()) {
                                                $('#status').html(htmlWithoutScript);
                                            }
                                        } else if (response.message) {
                                            errorMessage = response.message;
                                        }
                                    } catch (e) {
                                        console.error('Error parsing error response:', e);
                                    }
                                }
                            } catch (e) {
                                console.error('Error parsing error response:', e);
                            }
                            
                            // Chỉ hiển thị toastr nếu không có HTML response
                            if (!hasHtmlResponse) {
                                if (typeof toastr !== 'undefined') {
                                    toastr.error(errorMessage, "Thông Báo");
                                } else {
                                    alert(errorMessage);
                                }
                                $('#status').html('<div class="alert alert-danger mt-3">' + errorMessage + '</div>');
                            }
                        }
                    });
                    
                    console.log('Buy button handler attached successfully');
                });
        }
        
        // Gọi hàm tryInit để khởi tạo
        tryInit();
    }
    
    // Fallback: Sử dụng vanilla JavaScript nếu jQuery không hoạt động
    function initBuyButtonVanilla() {
        console.log('Using vanilla JavaScript fallback');
        
        function attachHandler() {
            var buyBtn = document.getElementById('buy');
            
            if (!buyBtn) {
                console.log('Buy button not found, retrying...');
                setTimeout(attachHandler, 100);
                return;
            }
            
            console.log('Buy button found, attaching vanilla JS handler');
            
            // Xóa event listeners cũ
            var newBtn = buyBtn.cloneNode(true);
            buyBtn.parentNode.replaceChild(newBtn, buyBtn);
            
            // Thêm event listener mới
            newBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Buy button clicked (vanilla JS)!');
                
                // Kiểm tra lại jQuery, nếu có thì dùng
                if (typeof jQuery !== 'undefined') {
                    jQuery(newBtn).trigger('click');
                    return;
                }
                
                alert('Vui lòng tải lại trang để sử dụng tính năng này!');
            });
            
            console.log('Vanilla JS handler attached');
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', attachHandler);
        } else {
            setTimeout(attachHandler, 50);
        }
    }
    
    // Khởi tạo ngay lập tức hoặc đợi window load
    console.log('Initializing buy button handler...');
    console.log('Document ready state:', document.readyState);
    console.log('Buy button exists:', document.getElementById('buy') !== null);
    
    if (document.readyState === 'loading') {
        console.log('DOM is loading, waiting for DOMContentLoaded...');
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded fired, initializing...');
            initBuyButton();
        });
    } else {
        // DOM đã sẵn sàng
        console.log('DOM already ready, initializing immediately...');
        setTimeout(function() {
            console.log('Timeout fired, initializing buy button...');
            initBuyButton();
        }, 50);
    }
    
    // Thêm fallback sau 1 giây để đảm bảo
    setTimeout(function() {
        var btn = document.getElementById('buy');
        if (btn) {
            console.log('Fallback: Buy button found, checking if handler is attached...');
            var hasJQueryHandler = typeof jQuery !== 'undefined' && jQuery(btn).data('events');
            console.log('Has jQuery handler:', !!hasJQueryHandler);
            
            if (!hasJQueryHandler) {
                console.warn('No handler found, trying to initialize again...');
                initBuyButton();
            }
        } else {
            console.error('Fallback: Buy button still not found!');
        }
    }, 1000);
    
    console.log('=== DOMAIN CHECKOUT SCRIPT END ===');
})();
</script>
@endsection
