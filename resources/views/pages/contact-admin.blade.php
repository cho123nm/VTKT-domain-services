@extends('layouts.app')

@section('title', 'Liên Hệ Admin')

@section('content')
<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
    <div class="app-container container-xxl d-flex flex-row flex-column-fluid">
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <div class="card mb-5 mb-xl-10">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-800">Liên Hệ Admin</span>
                                <span class="text-gray-400 mt-1 fw-semibold fs-6">Nhận thông tin {{ strtoupper($type) }}</span>
                            </h3>
                        </div>
                    </div>
                    
                    <div class="card card-flush mb-5">
                        <div class="card-body pt-7">
                            <div class="alert alert-success mb-10">
                                <h4 class="alert-heading">Thanh toán thành công!</h4>
                                <p>Mã giao dịch: <strong>#{{ $mgd }}</strong></p>
                                <p>Gói: <strong>{{ htmlspecialchars($productName) }}</strong></p>
                                <p>Thời gian thuê: <strong>{{ $order->period === 'month' ? 'Theo tháng' : 'Theo năm' }}</strong></p>
                                <hr>
                                <p class="mb-0">Vui lòng liên hệ admin để nhận thông tin {{ strtoupper($type) }} của bạn.</p>
                            </div>
                            
                            <div class="row g-5">
                                @if(!empty($contactInfo['facebook_link']))
                                <div class="col-md-6">
                                    <div class="card card-flush h-100">
                                        <div class="card-body text-center p-10">
                                            <div class="mb-5">
                                                <i class="fab fa-facebook fs-3x text-primary"></i>
                                            </div>
                                            <h4 class="fw-bold mb-5">Liên Hệ Qua Facebook</h4>
                                            <p class="text-gray-600 mb-5">Nhắn tin trực tiếp cho admin trên Facebook</p>
                                            <a href="{{ htmlspecialchars($contactInfo['facebook_link']) }}" target="_blank" class="btn btn-primary btn-lg w-100">
                                                <i class="fab fa-facebook me-2"></i> Mở Facebook
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
                                @if(!empty($contactInfo['zalo_phone']))
                                <div class="col-md-6">
                                    <div class="card card-flush h-100">
                                        <div class="card-body text-center p-10">
                                            <div class="mb-5">
                                                <i class="fas fa-phone fs-3x text-info"></i>
                                            </div>
                                            <h4 class="fw-bold mb-5">Liên Hệ Qua Zalo</h4>
                                            <p class="text-gray-600 mb-5">Gọi hoặc nhắn tin Zalo cho admin</p>
                                            <a href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $contactInfo['zalo_phone']) }}" target="_blank" class="btn btn-info btn-lg w-100">
                                                <i class="fas fa-phone me-2"></i> {{ htmlspecialchars($contactInfo['zalo_phone']) }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            
                            @if(empty($contactInfo['facebook_link']) && empty($contactInfo['zalo_phone']))
                                <div class="alert alert-warning mt-5">
                                    <h4>Thông tin liên hệ chưa được cấu hình</h4>
                                    <p>Vui lòng liên hệ admin qua email hoặc các kênh khác.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

