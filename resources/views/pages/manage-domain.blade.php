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
                                <h1 class="anchor fw-bold mb-5" id="form-labels" data-kt-scroll-offset="50">
                                    <input type="hidden" id="domain-id" value="{{ $domainHistory->id }}">
                                    <input type="hidden" id="mgd" value="{{ $domainHistory->mgd }}">
                                    <a href="#form-labels"></a> Quản Lý Tên Miền ({{ $domainHistory->domain }}) 
                                </h1>
                                <p> Thời Gian Cập Nhật Gần Đây : <code>
                                    @if($domainHistory->timedns == '0')
                                        Không Có Lần Cập Nhật Gần Đây
                                    @else
                                        {{ $domainHistory->timedns }}
                                    @endif
                                </code></p>
                                <div class="py-5">
                                    <div class="mb-10">
                                        <label for="ns1" class="required form-label">Nameserver 1</label>
                                        <input type="text" class="form-control form-control-solid" id="ns1" placeholder="Nameserver 1" value="{{ $domainHistory->ns1 }}">
                                    </div>
                                    <div class="mb-10">
                                        <label for="ns2" class="required form-label">Nameserver 2</label>
                                        <input type="text" class="form-control form-control-solid" id="ns2" placeholder="Nameserver 2" value="{{ $domainHistory->ns2 }}">
                                    </div>
                                    <div class="py-0">
                                        <center> 
                                            <b class="text-danger"> Chỉ Có Thể Thay Đổi DNS Sau 15 Ngày Khi Thực Hiện Đổi Bạn Phải Chờ Sau 15 Ngày Để Có Thể Thay Đổi Tiếp Tục! </b>
                                        </center>
                                        <br>
                                    </div>
                                    <div id="status"></div>
                                    <button class="btn btn-warning" type="submit" id="UpdateDns"> Thay Đổi </button>
                                    <a href="{{ route('manager.index') }}" class="btn btn-secondary"> Quay Lại </a>
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
    $('#UpdateDns').on('click', function() {
        $("#UpdateDns").text('Đang xử lý...');
        $("#UpdateDns").attr("disabled", true);
        
        var ns1 = $('#ns1').val();
        var ns2 = $('#ns2').val();
        var domainId = $('#domain-id').val();
        var mgd = $('#mgd').val();
        
        $.ajax({
            url: '/manager/domain/' + domainId + '/update-dns',
            type: 'POST',
            data: {
                ns1: ns1,
                ns2: ns2,
                mgd: mgd,
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                $("#UpdateDns").attr("disabled", false);
                $("#UpdateDns").text('Thay Đổi');
                $('#status').html(data.html);
                
                if (data.success) {
                    setTimeout(function() {
                        window.location.href = '{{ route("manager.index") }}';
                    }, 2000);
                }
            },
            error: function(xhr) {
                $("#UpdateDns").attr("disabled", false);
                $("#UpdateDns").text('Thay Đổi');
                
                if (xhr.responseJSON && xhr.responseJSON.html) {
                    $('#status').html(xhr.responseJSON.html);
                } else {
                    $('#status').html('<script>toastr.error("Có lỗi xảy ra!", "Thông Báo");<\/script>');
                }
            }
        }); 
    });
});
</script>
@endsection
