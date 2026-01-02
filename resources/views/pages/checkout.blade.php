@extends('layouts.app')

@section('content')
<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
    <div class="app-container container-xxl d-flex flex-row flex-column-fluid">
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <div class="card mb-5 mb-xl-10"></div>
                    <div class="card card-docs flex-row-fluid mb-2">
                        <div class="card-body fs-6 py-15 px-10 py-lg-15 px-lg-15 text-gray-700">
                            <div class="py-10">
                                <h1 class="anchor fw-bold mb-5" id="text-input" data-kt-scroll-offset="50">
                                    <a href="#text-input"></a> Đăng Ký Tên Miền &nbsp; 
                                    <img src="{{ fixImagePath($images) }}" width="50px">
                                </h1>
                                <div class="py-5">
                                    <div class="form fv-plugins-bootstrap5 fv-plugins-framework">
                                        <div class="fv-row mb-10 fv-plugins-icon-container">
                                            <label class="required fw-semibold fs-6 mb-2"> Tên Miền </label>
                                            <input type="text" id="domain" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Tên Miền Cần Mua" value="{{ $domain }}" disabled>
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
                                        <button id="buy" type="submit" class="btn btn-primary">
                                            <span class="indicator-label"> Mua Ngay - {{ number_format($tienphaitra) }}đ</span>
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

<script>
(function() {
    'use strict';
    
    function initBuyButton() {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            console.log('jQuery not loaded, retrying...');
            setTimeout(initBuyButton, 100);
            return;
        }
        
        $(document).ready(function() {
            var $buyBtn = $('#buy');
            
            if ($buyBtn.length === 0) {
                console.error('Buy button not found!');
                return;
            }
            
            $buyBtn.off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var $btn = $(this);
                var originalText = $btn.find('.indicator-label').text() || $btn.text();
                
                $btn.prop("disabled", true);
                $btn.find('.indicator-label').text('Đang xử lý...');
                
                var domain = $('#domain').val() || '';
                var ns1 = $('#ns1').val() || '';
                var ns2 = $('#ns2').val() || '';
                var hsd = $('#hsd').val() || '1';
                
                $.ajax({
                    url: '/api/buy-domain',
                    type: 'POST',
                    xhrFields: {
                        withCredentials: true
                    },
                    crossDomain: false,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    data: {
                        _token: '{{ csrf_token() }}',
                        domain: domain,
                        ns1: ns1,
                        ns2: ns2,
                        hsd: hsd
                    },
                    dataType: 'json',
                    cache: false,
                    success: function(data) {
                        $btn.prop("disabled", false);
                        $btn.find('.indicator-label').text(originalText);
                        
                        if (data && data.html) {
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
                                        console.error('Error executing script:', e);
                                    }
                                });
                            }
                            
                            // Hiển thị HTML không có script tag
                            var htmlWithoutScript = htmlContent.replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '');
                            if (htmlWithoutScript.trim()) {
                                $('#status').html(htmlWithoutScript);
                            }
                        } else if (data && data.message) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(data.message, "Thông Báo");
                            }
                            $('#status').html('<div class="alert alert-success mt-3">' + data.message + '</div>');
                        }
                    },
                    error: function(xhr) {
                        $btn.prop("disabled", false);
                        $btn.find('.indicator-label').text(originalText);
                        
                        var errorMessage = 'Có lỗi xảy ra!';
                        
                        if (xhr.responseJSON && xhr.responseJSON.html) {
                            var htmlContent = xhr.responseJSON.html;
                            
                            // Extract và thực thi script tags
                            var scriptRegex = /<script[^>]*>([\s\S]*?)<\/script>/gi;
                            var scripts = htmlContent.match(scriptRegex);
                            
                            if (scripts && scripts.length > 0) {
                                scripts.forEach(function(scriptTag) {
                                    var scriptContent = scriptTag.replace(/<\/?script[^>]*>/gi, '');
                                    try {
                                        eval(scriptContent);
                                    } catch (e) {
                                        console.error('Error executing script:', e);
                                    }
                                });
                            }
                            
                            // Hiển thị HTML không có script tag
                            var htmlWithoutScript = htmlContent.replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '');
                            if (htmlWithoutScript.trim()) {
                                $('#status').html(htmlWithoutScript);
                            }
                        } else {
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            
                            if (typeof toastr !== 'undefined') {
                                toastr.error(errorMessage, "Thông Báo");
                            } else {
                                alert(errorMessage);
                            }
                            $('#status').html('<div class="alert alert-danger mt-3">' + errorMessage + '</div>');
                        }
                    }
                });
            });
        });
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBuyButton);
    } else {
        setTimeout(initBuyButton, 50);
    }
})();
</script>
@endsection

