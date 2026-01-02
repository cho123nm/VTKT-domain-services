@extends('layouts.app')

@section('content')
<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
    <div class="app-container container-xxl d-flex flex-row flex-column-fluid">
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <div class="d-flex flex-column flex-column-fluid">
                        <div class="app-toolbar py-3 py-lg-0">
                            <div class="app-container container-xxl d-flex flex-stack">
                                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                                        Tin Nhắn
                                        @if($unreadCount > 0)
                                            <span class="badge badge-danger ms-2">{{ $unreadCount }} mới</span>
                                        @endif
                                    </h1>
                                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                        <li class="breadcrumb-item text-muted">
                                            <a href="{{ route('home') }}" class="text-muted text-hover-primary">Trang Chủ</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                                        </li>
                                        <li class="breadcrumb-item text-muted">Tin Nhắn</li>
                                    </ul>
                                </div>
                                <div class="d-flex align-items-center gap-2 gap-lg-3">
                                    <a href="{{ route('feedback.index') }}" class="btn btn-sm btn-primary">
                                        <span class="svg-icon svg-icon-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3" d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z" fill="currentColor"/>
                                                <rect x="6" y="12" width="7" height="2" rx="1" fill="currentColor"/>
                                                <rect x="6" y="7" width="12" height="2" rx="1" fill="currentColor"/>
                                            </svg>
                                        </span>
                                        Gửi Phản Hồi
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="app-content flex-column-fluid">
                            <div class="card card-flush">
                                <div class="card-header pt-7">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold text-dark">Tin Nhắn Từ Admin</span>
                                        <span class="text-gray-400 mt-1 fw-semibold fs-6">
                                            @if($unreadCount > 0)
                                                <span class="badge badge-danger">{{ $unreadCount }} tin nhắn chưa đọc</span>
                                            @else
                                                Tất cả tin nhắn đã được đọc
                                            @endif
                                        </span>
                                    </h3>
                                </div>
                                <div class="card-body pt-6">
                                    <div class="hover-scroll-overlay-y pe-6 me-n6" style="max-height: 700px">
                                        @php
                                            $hasMessages = false;
                                            foreach($userFeedbacks as $feedback) {
                                                if(!empty($feedback->admin_reply)) {
                                                    $hasMessages = true;
                                                    break;
                                                }
                                            }
                                        @endphp
                                        
                                        @if(!$hasMessages)
                                            <div class="text-center py-10">
                                                <span class="svg-icon svg-icon-3x svg-icon-muted">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path opacity="0.3" d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z" fill="currentColor"/>
                                                        <rect x="6" y="12" width="7" height="2" rx="1" fill="currentColor"/>
                                                        <rect x="6" y="7" width="12" height="2" rx="1" fill="currentColor"/>
                                                    </svg>
                                                </span>
                                                <div class="fw-semibold text-gray-500 mt-5">Chưa có tin nhắn nào</div>
                                                <a href="{{ route('feedback.index') }}" class="btn btn-primary mt-5">Gửi Phản Hồi Ngay</a>
                                            </div>
                                        @else
                                            @foreach($userFeedbacks as $feedback)
                                                @if(!empty($feedback->admin_reply))
                                                    <div class="border border-dashed border-gray-300 rounded px-7 py-5 mb-6 {{ $feedback->status == 1 ? 'bg-light-primary border-primary' : '' }}">
                                                        <div class="d-flex flex-stack mb-3">
                                                            <div class="d-flex align-items-center">
                                                                @if($feedback->status == 1)
                                                                    <span class="badge badge-primary me-2">Mới</span>
                                                                @endif
                                                                <span class="fw-bold text-gray-800">Phản hồi từ Admin</span>
                                                            </div>
                                                            <div class="text-gray-400 fs-7">{{ $feedback->reply_time ?? $feedback->time }}</div>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <div class="text-gray-600">{!! nl2br(e($feedback->admin_reply)) !!}</div>
                                                        </div>
                                                        
                                                        <div class="border-top pt-3 mt-3">
                                                            <div class="text-gray-500 fs-7 mb-2">Phản hồi của bạn:</div>
                                                            <div class="text-gray-600">{!! nl2br(e($feedback->message)) !!}</div>
                                                            <div class="text-gray-400 fs-7 mt-2">{{ $feedback->time }}</div>
                                                        </div>
                                                        
                                                        @if($feedback->status == 1)
                                                            <div class="mt-3">
                                                                <a href="{{ route('messages.mark-read', $feedback->id) }}" class="btn btn-sm btn-light-primary">Đánh dấu đã đọc</a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
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
@endsection
