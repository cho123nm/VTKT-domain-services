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
                                <span class="card-label fw-bold text-gray-800">Download Source Code</span>
                                <span class="text-gray-400 mt-1 fw-semibold fs-6">Tải về source code đã mua</span>
                            </h3>
                        </div>
                    </div>
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if($history && $sourceCode)
                        <div class="card card-flush mb-5">
                            <div class="card-body pt-7">
                                <h4 class="fw-bold mb-5">Source Code: {{ $sourceCode->name }}</h4>
                                <p class="text-gray-600 mb-5">{{ $sourceCode->description ?? '' }}</p>
                                
                                @if(!empty($sourceCode->file_path))
                                    <a href="{{ route('download.file', $history->id) }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-download me-2"></i> Download File
                                    </a>
                                @elseif(!empty($sourceCode->download_link))
                                    <a href="{{ $sourceCode->download_link }}" class="btn btn-primary btn-lg" target="_blank">
                                        <i class="fas fa-external-link-alt me-2"></i> Mở Link Download
                                    </a>
                                @else
                                    <div class="alert alert-warning">
                                        Link download chưa được cập nhật. Vui lòng liên hệ admin.
                                    </div>
                                @endif
                                
                                <div class="mt-5">
                                    <span class="text-gray-600">Mã giao dịch: <strong>#{{ $history->mgd }}</strong></span><br>
                                    <span class="text-gray-600">Thời gian mua: <strong>{{ $history->time }}</strong></span>
                                </div>
                            </div>
                        </div>
                    @endif
                    
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

                    <div class="card card-flush">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-800">Lịch Sử Mua Source Code</span>
                            </h3>
                        </div>
                        <div class="card-body pt-6">
                            <div class="table-responsive">
                                <table class="table table-row-dashed align-middle gs-0 gy-3">
                                    <thead>
                                        <tr class="fs-7 fw-bold text-gray-400 border-bottom-0">
                                            <th class="p-0 pb-3 min-w-175px text-start">MGD</th>
                                            <th class="p-0 pb-3 min-w-200px text-start">Source Code</th>
                                            <th class="p-0 pb-3 min-w-175px text-start">Thời Gian</th>
                                            <th class="p-0 pb-3 min-w-100px text-start">Trạng Thái</th>
                                            <th class="p-0 pb-3 min-w-100px text-start">Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($purchases->isEmpty())
                                            <tr>
                                                <td colspan="5" class="text-center text-gray-600">Chưa có lịch sử mua source code</td>
                                            </tr>
                                        @else
                                            @foreach($purchases as $purchase)
                                                @php
                                                    $sc = \App\Models\SourceCode::find($purchase->source_code_id);
                                                @endphp
                                                @if($sc)
                                                    <tr>
                                                        <td>#{{ $purchase->mgd }}</td>
                                                        <td>{{ $sc->name }}</td>
                                                        <td>{{ $purchase->time }}</td>
                                                        <td>
                                                            @if($purchase->status == 1)
                                                                <span class="badge badge-success">Thành công</span>
                                                            @else
                                                                <span class="badge badge-warning">Chờ xử lý</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($purchase->status == 1)
                                                                <a href="{{ route('download.index', ['mgd' => $purchase->mgd]) }}" class="btn btn-sm btn-primary">
                                                                    Download
                                                                </a>
                                                            @else
                                                                <span class="text-muted">Chờ duyệt</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
