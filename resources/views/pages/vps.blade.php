@extends('layouts.app')

@section('content')
<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
    <div class="app-container container-xxl d-flex flex-row flex-column-fluid">
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <div class="card mb-5 mb-xl-10">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-800">VPS</span>
                                <span class="text-gray-400 mt-1 fw-semibold fs-6">Danh Sách Gói VPS</span>
                            </h3>
                        </div>
                    </div>
                    
                    <!-- Admin Contact Information -->
                    <div class="card card-flush mb-5">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-800">Liên Hệ Admin</span>
                                <span class="text-gray-400 mt-1 fw-semibold fs-6">Cần hỗ trợ? Liên hệ với chúng tôi</span>
                            </h3>
                        </div>
                        <div class="card-body pt-6">
                            <div class="row g-5">
                                @if($settings && $settings->sodienthoai)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-4 bg-light-primary rounded">
                                        <div class="symbol symbol-50px me-4">
                                            <span class="symbol-label bg-primary">
                                                <i class="fas fa-phone text-white fs-2"></i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-400 fw-semibold fs-7 mb-1">Số Điện Thoại</span>
                                            <a href="tel:{{ $settings->sodienthoai }}" class="text-gray-800 fw-bold fs-5 text-hover-primary">
                                                {{ $settings->sodienthoai }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if($settings && $settings->facebook_link)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-4 bg-light-info rounded">
                                        <div class="symbol symbol-50px me-4">
                                            <span class="symbol-label bg-info">
                                                <i class="fab fa-facebook-f text-white fs-2"></i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-400 fw-semibold fs-7 mb-1">Facebook</span>
                                            <a href="{{ $settings->facebook_link }}" target="_blank" class="text-gray-800 fw-bold fs-5 text-hover-primary">
                                                Nhắn tin Facebook
                                                <i class="fas fa-external-link-alt ms-2 fs-7"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="row gy-5 g-xl-10">
                        @if($vpss->isEmpty())
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h4>Chưa có gói VPS nào</h4>
                                    <p>Vui lòng quay lại sau.</p>
                                </div>
                            </div>
                        @else
                            @foreach($vpss as $vps)
                                <div class="col-xl-4 col-lg-6 col-md-6">
                                    <div class="card card-flush h-100 vps-card">
                                        <div class="card-header pt-7 pb-4">
                                            <h3 class="card-label fw-bold text-gray-800 text-center fs-2">{{ $vps->name }}</h3>
                                        </div>
                                        <div class="card-body pt-4 pb-7">
                                            <div class="vps-image-wrapper mb-5">
                                                @if(!empty($vps->image))
                                                    <img src="{{ fixImagePath($vps->image) }}" class="vps-image" alt="{{ $vps->name }}">
                                                @else
                                                    <div class="vps-image-placeholder">
                                                        <i class="fas fa-server fs-1 text-gray-400"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <p class="text-gray-600 mb-4 text-center">{{ $vps->description ?? '' }}</p>
                                            @if(!empty($vps->specs))
                                                <div class="mb-4">
                                                    <pre class="text-gray-600 fs-7 mb-0 text-center">{{ $vps->specs }}</pre>
                                                </div>
                                            @endif
                                            <div class="d-flex flex-column gap-3 mb-5">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-gray-600">Giá theo tháng:</span>
                                                    <span class="text-primary fw-bold fs-4">{{ number_format($vps->price_month) }}đ</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-gray-600">Giá theo năm:</span>
                                                    <span class="text-success fw-bold fs-4">{{ number_format($vps->price_year) }}đ</span>
                                                </div>
                                            </div>
                                            <a href="{{ route('checkout.vps', ['id' => $vps->id]) }}" class="btn btn-primary w-100">
                                                Mua Ngay
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .vps-card {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .vps-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .vps-image-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        padding: 20px;
        overflow: hidden;
    }

    .vps-image {
        width: 100%;
        max-width: 250px;
        height: auto;
        object-fit: contain;
        transition: all 0.4s ease;
        position: relative;
        z-index: 1;
    }

    .vps-image-placeholder {
        width: 100%;
        max-width: 250px;
        height: 200px;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #f5f8fa;
        border-radius: 8px;
    }

    .vps-card:hover .vps-image {
        transform: scale(1.1);
    }

    /* Shimmer effect */
    .vps-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(255, 255, 255, 0.4),
            transparent
        );
        transition: left 0.5s ease;
        z-index: 2;
        pointer-events: none;
    }

    .vps-card:hover::before {
        left: 100%;
    }

    /* Glow effect on image */
    .vps-image-wrapper::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 0;
        height: 0;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(0, 123, 255, 0.3) 0%, transparent 70%);
        transition: all 0.5s ease;
        z-index: 0;
        pointer-events: none;
    }

    .vps-card:hover .vps-image-wrapper::after {
        width: 300px;
        height: 300px;
    }

    /* Additional shine effect */
    .vps-image {
        filter: brightness(1);
        transition: filter 0.3s ease;
    }

    .vps-card:hover .vps-image {
        filter: brightness(1.1) drop-shadow(0 0 20px rgba(0, 123, 255, 0.3));
    }
</style>
@endsection

