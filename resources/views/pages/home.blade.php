@extends('layouts.app')

@section('content')
<!-- Modal Chào Mừng -->
<div class="modal fade" id="welcomeModal" tabindex="-1" aria-labelledby="welcomeModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white fw-bold" id="welcomeModalLabel">
                    <span style="font-size: 1.5rem; margin-right: 0.5rem;">🎉</span> Chào Mừng Quý Khách
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-5">
                <div class="mb-4">
                    <span style="font-size: 4rem; display: inline-block;">🎁</span>
                </div>
                <h4 class="text-gray-800 mb-3 fw-bold">Chào Mừng Đến THANHVU.NET V4</h4>
                <p class="text-primary fs-5 mb-4 fw-semibold">Hân hạnh được phục vụ quý khách!</p>
                <p class="text-gray-600 mb-4">Chúng tôi chuyên cung cấp các dịch vụ: <strong>Tên Miền</strong>, <strong>Hosting</strong>, <strong>VPS</strong> và <strong>Source Code</strong> uy tín, giá rẻ với nhiều ưu đãi hấp dẫn.</p>
                <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input me-2" type="checkbox" id="hideInSession" style="cursor: pointer;">
                    <label class="form-check-label text-gray-600" for="hideInSession" style="cursor: pointer;">
                        Ẩn trong phiên làm việc này
                    </label>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-top-0">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    Đồng Ý
                </button>
            </div>
        </div>
    </div>
</div>

<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
    <div class="app-container container-xxl d-flex flex-row flex-column-fluid">
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <div class="card mb-5 mb-xl-10">
                    </div>
                    <div class="row gy-5 g-xl-10">
                        <!-- Domain Types List -->
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="card card-flush h-xl-60">
                                <div class="card-header pt-7">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold text-dark">Loại Miền</span>
                                        <span class="text-gray-400 mt-1 fw-semibold fs-6">Các Đuôi Miền Đang Bán</span>
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="hover-scroll-overlay-y pe-6 me-n6" style="height: 345px">
                                        @foreach($domains as $domain)
                                        <div class="border border-dashed border-gray-300 rounded px-7 py-3 mb-6">
                                            <div class="d-flex flex-stack mb-3">
                                                <div class="me-3">
                                                    &emsp;
                                                    <img src="{{ fixImagePath($domain->image) }}" class="w-50px ms-n1 me-1" alt="">
                                                </div>
                                                <div class="m-0">
                                                    <span class="badge badge-light-success"> {{ number_format($domain->price) }}Đ </span>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-stack">
                                                <span class="text-gray-400 fw-bold"> Không Bảo Hành </span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Domain Search Form -->
                        <div class="col-12 col-md-6 col-xl-8">
                            <div class="card card-flush h-lg-20" id="kt_contacts_main">
                                <div class="card-header pt-7" id="kt_chat_contacts_header">
                                    <div class="card-title">
                                        <span class="svg-icon svg-icon-1 me-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M20 14H18V10H20C20.6 10 21 10.4 21 11V13C21 13.6 20.6 14 20 14ZM21 19V17C21 16.4 20.6 16 20 16H18V20H20C20.6 20 21 19.6 21 19ZM21 7V5C21 4.4 20.6 4 20 4H18V8H20C20.6 8 21 7.6 21 7Z" fill="currentColor"></path>
                                                <path opacity="0.3" d="M17 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H17C17.6 2 18 2.4 18 3V21C18 21.6 17.6 22 17 22ZM10 7C8.9 7 8 7.9 8 9C8 10.1 8.9 11 10 11C11.1 11 12 10.1 12 9C12 7.9 11.1 7 10 7ZM13.3 16C14 16 14.5 15.3 14.3 14.7C13.7 13.2 12 12 10.1 12C8.10001 12 6.49999 13.1 5.89999 14.7C5.59999 15.3 6.19999 16 7.39999 16H13.3Z" fill="currentColor"></path>
                                            </svg>
                                        </span>
                                        <h2> Kiểm Tra Tên Miền </h2>
                                    </div>
                                </div>
                                <div class="card-body pt-5">
                                    <div class="form fv-plugins-bootstrap5 fv-plugins-framework">
                                        <div class="row row-cols-1 row-cols-md-2">
                                            <div class="col">
                                                <div class="fv-row mb-7 fv-plugins-icon-container">
                                                    <label class="fs-6 fw-semibold form-label mt-3">
                                                        <span class="required"> Tên Miền </span>
                                                        <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" aria-label="Enter the contact's email." data-bs-original-title="Enter the contact's email." data-kt-initialized="1"></i>
                                                    </label>
                                                    <input type="text" class="form-control form-control-solid" placeholder="Nhập Tên Miền" id="name">
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="fv-row mb-7">
                                                    <label class="fs-6 fw-semibold form-label mt-3">
                                                        <span> Đuôi Miền </span>
                                                        <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" aria-label="Enter the contact's phone number (optional)." data-bs-original-title="Enter the contact's phone number (optional)." data-kt-initialized="1"></i>
                                                    </label>
                                                    <select class="form-select" id="domain">
                                                        @foreach($domains as $domain)
                                                            <option value="{{ $domain->duoi }}">{{ $domain->duoi }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div id="status"></div>
                                            <div class="fv-row mb-7">
                                                <div class="d-flex justify-content-end">
                                                    <button type="reset" data-kt-contacts-type="cancel" class="btn btn-light me-3">Cancel</button>
                                                    <button type="submit" id="whois" class="btn btn-primary">
                                                        <span class="indicator-label"> Kiểm Tra </span>
                                                        <span class="indicator-progress">Please wait... 
                                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Hiển thị modal chào mừng
    function showWelcomeModal() {
        // Kiểm tra sessionStorage xem user đã ẩn modal trong session này chưa
        var hideInSession = sessionStorage.getItem('hideWelcomeModal');
        
        if (!hideInSession) {
            // Đợi 800ms để trang load hoàn tất rồi hiển thị modal
            setTimeout(function() {
                // Kiểm tra xem có Bootstrap hay không
                if (typeof bootstrap !== 'undefined') {
                    // Sử dụng Bootstrap 5
                    var welcomeModalElement = document.getElementById('welcomeModal');
                    if (welcomeModalElement) {
                        var welcomeModal = new bootstrap.Modal(welcomeModalElement, {
                            backdrop: 'static',
                            keyboard: false
                        });
                        welcomeModal.show();
                    }
                } else {
                    // Fallback: Sử dụng jQuery nếu không có Bootstrap
                    $('#welcomeModal').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $('#welcomeModal').modal('show');
                }
            }, 800);
        }
        
        // Xử lý khi user đóng modal
        $('#welcomeModal').on('hidden.bs.modal', function () {
            var hideInSessionCheck = $('#hideInSession').is(':checked');
            if (hideInSessionCheck) {
                // Lưu vào sessionStorage để ẩn trong session này
                // sessionStorage sẽ tự động clear khi đóng tab
                // Khi đăng xuất và đăng nhập lại, sessionStorage sẽ được clear và modal hiện lại
                sessionStorage.setItem('hideWelcomeModal', 'true');
            }
            // Reset checkbox
            $('#hideInSession').prop('checked', false);
        });
    }
    
    // Gọi hàm hiển thị modal
    showWelcomeModal();
    
    $('#whois').on('click', function() {
        var $btn = $(this);
        var originalText = $btn.text();
        
        // Validate input
        var domain = $('#domain').val();
        var name = $('#name').val();
        
        if (!name || name.trim() === '') {
            toastr.error('Vui lòng nhập tên miền!', 'Thông Báo');
            return;
        }
        
        if (!domain || domain.trim() === '') {
            toastr.error('Vui lòng chọn đuôi miền!', 'Thông Báo');
            return;
        }
        
        // Disable button và hiển thị loading
        $btn.prop('disabled', true);
        $btn.text('Đang xử lý...');
        $('#status').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Đang kiểm tra...</div>');
        
        $.ajax({
            url: '{{ route("ajax.check-domain") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                name: name.trim(),
                domain: domain.trim()
            },
            timeout: 30000, // 30 seconds timeout
            success: function(response) {
                $btn.prop('disabled', false);
                $btn.text(originalText);
                
                if (response && response.html) {
                    $('#status').html(response.html);
                } else if (response && response.message) {
                    var alertType = response.success ? 'success' : 'error';
                    $('#status').html('<div class="alert alert-' + (response.success ? 'success' : 'danger') + '">' + response.message + '</div>');
                    toastr[alertType](response.message, 'Thông Báo');
                } else {
                    $('#status').html('<div class="alert alert-warning">Không có phản hồi từ server</div>');
                    toastr.warning('Không có phản hồi từ server', 'Thông Báo');
                }
            },
            error: function(xhr, status, error) {
                $btn.prop('disabled', false);
                $btn.text(originalText);
                
                var errorMessage = 'Có lỗi xảy ra, vui lòng thử lại!';
                
                if (xhr.responseJSON && xhr.responseJSON.html) {
                    $('#status').html(xhr.responseJSON.html);
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                    $('#status').html('<div class="alert alert-danger">' + errorMessage + '</div>');
                } else if (status === 'timeout') {
                    errorMessage = 'Yêu cầu quá thời gian chờ, vui lòng thử lại!';
                    $('#status').html('<div class="alert alert-danger">' + errorMessage + '</div>');
                } else if (xhr.status === 419) {
                    errorMessage = 'Phiên làm việc đã hết hạn, vui lòng tải lại trang!';
                    $('#status').html('<div class="alert alert-danger">' + errorMessage + '</div>');
                } else {
                    $('#status').html('<div class="alert alert-danger">' + errorMessage + '</div>');
                }
                
                toastr.error(errorMessage, 'Thông Báo');
                
                // Log error for debugging
                console.error('AJAX Error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    error: error,
                    response: xhr.responseJSON
                });
            }
        }); 
    });
});
</script>
@endpush
