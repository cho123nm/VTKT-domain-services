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
                                    <a href="#text-input"></a> Mua VPS
                                </h1>
                                <div class="py-5">
                                    <div class="form fv-plugins-bootstrap5 fv-plugins-framework">
                                        <div class="fv-row mb-10 fv-plugins-icon-container">
                                            <label class="required fw-semibold fs-6 mb-2"> Tên Gói VPS </label>
                                            <input type="text" id="name" class="form-control form-control-solid mb-3 mb-lg-0" value="{{ htmlspecialchars($vps->name) }}" disabled>
                                        </div>
                                        <div class="fv-row mb-10 fv-plugins-icon-container">
                                            <label class="fw-semibold fs-6 mb-2"> Mô Tả </label>
                                            <textarea class="form-control form-control-solid mb-3 mb-lg-0" disabled>{{ htmlspecialchars($vps->description ?? '') }}</textarea>
                                        </div>
                                        <div class="fv-row mb-10 fv-plugins-icon-container">
                                            <label class="required fw-semibold fs-6 mb-2"> Thời Gian Thuê </label>
                                            <select id="period" class="form-select">
                                                <option value="month">Theo Tháng - {{ number_format($vps->price_month) }}đ</option>
                                                <option value="year">Theo Năm - {{ number_format($vps->price_year) }}đ</option>
                                            </select>
                                        </div>
                                        <div class="fv-row mb-10">
                                            <div class="alert alert-info">
                                                <strong>Lưu ý:</strong> Sau khi thanh toán, bạn sẽ được chuyển đến trang liên hệ admin để nhận thông tin VPS.
                                            </div>
                                        </div>
                                        <input type="hidden" id="vps_id" value="{{ $vps->id }}">
                                        <div id="status"></div>
                                        <button id="buy" type="submit" class="btn btn-primary">
                                            <span class="indicator-label"> Mua Ngay</span>
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
$(document).ready(function() {
    function updatePrice() {
        var period = $('#period').val();
        var priceMonth = {{ $vps->price_month }};
        var priceYear = {{ $vps->price_year }};
        var price = period === 'month' ? priceMonth : priceYear;
        $('#buy .indicator-label').text('Mua Ngay - ' + price.toLocaleString('vi-VN') + 'đ');
    }
    
    $('#period').on('change', function() {
        updatePrice();
    });
    
    $('#buy').on('click', function() {
        $("#buy").text('Đang xử lý...');
        var vpsId = $('#vps_id').val();
        var period = $('#period').val();
        $.ajax({
            url: '{{ route('checkout.vps.process') }}',
            type: 'POST',
            data: {
                vps_id: vpsId,
                period: period,
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                $("#buy").attr("disabled", false);
                updatePrice();
                
                if (data.success) {
                    $('#status').html('<script>toastr.success("' + data.message + '", "Thông Báo");<\/script>');
                    if (data.redirect) {
                        setTimeout(function() {
                            window.location.href = data.redirect;
                        }, 1500);
                    }
                } else {
                    $('#status').html('<script>toastr.error("' + data.message + '", "Thông Báo");<\/script>');
                }
            },
            error: function() {
                $("#buy").attr("disabled", false);
                updatePrice();
                $('#status').html('<script>toastr.error("Có lỗi xảy ra!", "Thông Báo");<\/script>');
            }
        }); 
    });
});
</script>
@endsection
