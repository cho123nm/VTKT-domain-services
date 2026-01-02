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
                                <span class="card-label fw-bold text-gray-800">Source Code</span>
                                <span class="text-gray-400 mt-1 fw-semibold fs-6">Danh Sách Source Code</span>
                            </h3>
                        </div>
                    </div>
                    <div class="row gy-5 g-xl-10">
                        @if($sourceCodes->isEmpty())
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h4>Chưa có source code nào</h4>
                                    <p>Vui lòng quay lại sau.</p>
                                </div>
                            </div>
                        @else
                            @foreach($sourceCodes as $sourceCode)
                                <div class="col-xl-4 col-lg-6 col-md-6">
                                    <div class="card card-flush h-100 sourcecode-card">
                                        <div class="card-header pt-7 pb-4">
                                            <h3 class="card-label fw-bold text-gray-800 text-center fs-2">{{ $sourceCode->name }}</h3>
                                        </div>
                                        <div class="card-body pt-4 pb-7">
                                            <div class="sourcecode-image-wrapper mb-5">
                                                @if(!empty($sourceCode->image))
                                                    <img src="{{ fixImagePath($sourceCode->image) }}" class="sourcecode-image" alt="{{ $sourceCode->name }}">
                                                @else
                                                    <div class="sourcecode-image-placeholder">
                                                        <i class="fas fa-code fs-1 text-gray-400"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <p class="text-gray-600 mb-4 text-center">{{ $sourceCode->description ?? '' }}</p>
                                            @if(!empty($sourceCode->category))
                                                <div class="mb-4 text-center">
                                                    <span class="badge badge-primary">{{ $sourceCode->category }}</span>
                                                </div>
                                            @endif
                                            <div class="d-flex flex-column gap-3 mb-5">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-gray-600">Giá:</span>
                                                    <span class="text-primary fw-bold fs-4">{{ number_format($sourceCode->price) }}đ</span>
                                                </div>
                                            </div>
                                            <a href="{{ route('checkout.sourcecode', ['id' => $sourceCode->id]) }}" class="btn btn-primary w-100">
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
    .sourcecode-card {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .sourcecode-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .sourcecode-image-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        padding: 20px;
        overflow: hidden;
    }

    .sourcecode-image {
        width: 100%;
        max-width: 250px;
        height: auto;
        object-fit: contain;
        transition: all 0.4s ease;
        position: relative;
        z-index: 1;
    }

    .sourcecode-image-placeholder {
        width: 100%;
        max-width: 250px;
        height: 200px;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #f5f8fa;
        border-radius: 8px;
    }

    .sourcecode-card:hover .sourcecode-image {
        transform: scale(1.1);
    }

    /* Shimmer effect */
    .sourcecode-card::before {
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

    .sourcecode-card:hover::before {
        left: 100%;
    }

    /* Glow effect on image */
    .sourcecode-image-wrapper::after {
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

    .sourcecode-card:hover .sourcecode-image-wrapper::after {
        width: 300px;
        height: 300px;
    }

    /* Additional shine effect */
    .sourcecode-image {
        filter: brightness(1);
        transition: filter 0.3s ease;
    }

    .sourcecode-card:hover .sourcecode-image {
        filter: brightness(1.1) drop-shadow(0 0 20px rgba(0, 123, 255, 0.3));
    }
</style>
@endsection

