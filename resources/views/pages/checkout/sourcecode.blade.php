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
                                    <a href="#text-input"></a> Mua Source Code
                                </h1>
                                <div class="py-5">
                                    <div class="form fv-plugins-bootstrap5 fv-plugins-framework">
                                        <div class="fv-row mb-10 fv-plugins-icon-container">
                                            <label class="required fw-semibold fs-6 mb-2"> Tên Source Code </label>
                                            <input type="text" id="name" class="form-control form-control-solid mb-3 mb-lg-0" value="{{ htmlspecialchars($sourceCode->name) }}" disabled>
                                        </div>
                                        <div class="fv-row mb-10 fv-plugins-icon-container">
                                            <label class="fw-semibold fs-6 mb-2"> Mô Tả </label>
                                            <textarea class="form-control form-control-solid mb-3 mb-lg-0" disabled>{{ htmlspecialchars($sourceCode->description ?? '') }}</textarea>
                                        </div>
                                        @if (!empty($sourceCode->category))
                                        <div class="fv-row mb-10 fv-plugins-icon-container">
                                            <label class="fw-semibold fs-6 mb-2"> Loại </label>
                                            <input type="text" class="form-control form-control-solid mb-3 mb-lg-0" value="{{ htmlspecialchars($sourceCode->category) }}" disabled>
                                        </div>
                                        @endif
                                        <input type="hidden" id="source_code_id" value="{{ $sourceCode->id }}">
                                        <div id="status"></div>
                                        <button id="buy" type="submit" class="btn btn-primary">
                                            <span class="indicator-label"> Mua Ngay - {{ number_format($sourceCode->price) }}đ</span>
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
            
            var checkoutUrl = '{{ route("checkout.sourcecode.process") }}';
            var originalText = 'Mua Ngay - {{ number_format($sourceCode->price) }}đ';
            
            $buyBtn.off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var $btn = $(this);
                var $indicator = $btn.find('.indicator-label');
                
                $btn.prop("disabled", true);
                if ($indicator.length > 0) {
                    $indicator.text('Đang xử lý...');
                } else {
                    $btn.text('Đang xử lý...');
                }
                
                var sourceCodeId = $('#source_code_id').val() || '';
                
                $.ajax({
                    url: checkoutUrl,
                    type: 'POST',
                    data: {
                        source_code_id: sourceCodeId,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(data) {
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
                                
                                var htmlWithoutScript = htmlContent.replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '');
                                if (htmlWithoutScript.trim()) {
                                    $('#status').html(htmlWithoutScript);
                                }
                            } else {
                                if (typeof toastr !== 'undefined') {
                                    toastr.success(message, "Thông Báo");
                                } else {
                                    alert(message);
                                }
                                $('#status').html('<div class="alert alert-success mt-3">' + message + '</div>');
                            }
                            
                            if (data.redirect) {
                                setTimeout(function() {
                                    window.location.href = data.redirect;
                                }, 1500);
                            }
                        } else {
                            var errorMsg = (data && data.message) ? data.message : 'Có lỗi xảy ra!';
                            
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
                                            console.error('Error executing script:', e);
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
                        $btn.prop("disabled", false);
                        if ($indicator.length > 0) {
                            $indicator.text(originalText);
                        } else {
                            $btn.text(originalText);
                        }
                        
                        var errorMessage = 'Có lỗi xảy ra!';
                        var hasHtmlResponse = false;
                        
                        // Nếu response có JSON và success = true, vẫn xử lý như success
                        if (xhr.responseJSON && xhr.responseJSON.success) {
                            if (xhr.responseJSON.html) {
                                hasHtmlResponse = true;
                                var htmlContent = xhr.responseJSON.html;
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
                                
                                var htmlWithoutScript = htmlContent.replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '');
                                if (htmlWithoutScript.trim()) {
                                    $('#status').html(htmlWithoutScript);
                                }
                            } else if (xhr.responseJSON.message) {
                                if (typeof toastr !== 'undefined') {
                                    toastr.success(xhr.responseJSON.message, "Thông Báo");
                                }
                                $('#status').html('<div class="alert alert-success mt-3">' + xhr.responseJSON.message + '</div>');
                            }
                            
                            if (xhr.responseJSON.redirect) {
                                setTimeout(function() {
                                    window.location.href = xhr.responseJSON.redirect;
                                }, 1500);
                            }
                        } else {
                            // Xử lý lỗi thực sự
                            try {
                                if (xhr.responseJSON && xhr.responseJSON.html) {
                                    hasHtmlResponse = true;
                                    var htmlContent = xhr.responseJSON.html;
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
                                    
                                    var htmlWithoutScript = htmlContent.replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '');
                                    if (htmlWithoutScript.trim()) {
                                        $('#status').html(htmlWithoutScript);
                                    }
                                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                } else if (xhr.status === 419) {
                                    errorMessage = 'Phiên làm việc đã hết hạn, vui lòng tải lại trang!';
                                }
                            } catch (e) {
                                console.error('Error parsing error response:', e);
                            }
                            
                            if (!hasHtmlResponse) {
                                if (typeof toastr !== 'undefined') {
                                    toastr.error(errorMessage, "Thông Báo");
                                } else {
                                    alert(errorMessage);
                                }
                                $('#status').html('<div class="alert alert-danger mt-3">' + errorMessage + '</div>');
                            }
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
