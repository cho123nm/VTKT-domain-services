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
                                        Gửi Phản Hồi
                                    </h1>
                                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                        <li class="breadcrumb-item text-muted">
                                            <a href="{{ route('home') }}" class="text-muted text-hover-primary">Trang Chủ</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                                        </li>
                                        <li class="breadcrumb-item text-muted">Phản Hồi</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="app-content flex-column-fluid">
                            <div class="row gy-5 g-xl-10">
                                <!-- Form Gửi Phản Hồi -->
                                <div class="col-xl-6">
                                    <div class="card card-flush">
                                        <div class="card-header pt-7">
                                            <h3 class="card-title align-items-start flex-column">
                                                <span class="card-label fw-bold text-dark">Gửi Phản Hồi / Báo Lỗi</span>
                                                <span class="text-gray-400 mt-1 fw-semibold fs-6">Gửi phản hồi qua Telegram hoặc Form</span>
                                            </h3>
                                        </div>
                                        <div class="card-body pt-6">
                                            <form method="POST" action="{{ route('feedback.store') }}" class="form fv-plugins-bootstrap5 fv-plugins-framework">
                                                @csrf
                                                
                                                <div class="fv-row mb-7">
                                                    <label class="fs-6 fw-semibold form-label mb-2">
                                                        <span class="required">Email Liên Hệ</span>
                                                    </label>
                                                    <input type="email" name="email" class="form-control form-control-solid @error('email') is-invalid @enderror" value="{{ old('email', $user->email ?? '') }}" required>
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="fv-row mb-7">
                                                    <label class="fs-6 fw-semibold form-label mb-2">
                                                        <span class="required">Nội Dung Phản Hồi / Mô Tả Lỗi</span>
                                                    </label>
                                                    <textarea name="message" class="form-control form-control-solid @error('message') is-invalid @enderror" rows="6" placeholder="Mô tả chi tiết vấn đề bạn gặp phải..." required>{{ old('message') }}</textarea>
                                                    @error('message')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="fv-row mb-7">
                                                    <div class="alert alert-info d-flex align-items-center p-5">
                                                        <span class="svg-icon svg-icon-2hx svg-icon-info me-4">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8204 2.02473L3.44586 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.906C3 14.2874 3.29864 14.6031 3.68197 14.6435L12 15.75L20.318 14.6435C20.7014 14.6031 21 14.2874 21 13.906V4.93945C21 4.6807 20.8188 4.45258 20.5541 4.37824H20.5543Z" fill="currentColor"/>
                                                                <path d="M10.5606 6.64111C10.8811 6.64111 11.1406 6.90061 11.1406 7.22111V11.2211C11.1406 11.5416 10.8811 11.8011 10.5606 11.8011C10.2401 11.8011 9.98058 11.5416 9.98058 11.2211V7.22111C9.98058 6.90061 10.2401 6.64111 10.5606 6.64111Z" fill="currentColor"/>
                                                                <path d="M13.4394 6.64111C13.7599 6.64111 14.0194 6.90061 14.0194 7.22111V11.2211C14.0194 11.5416 13.7599 11.8011 13.4394 11.8011C13.1189 11.8011 12.8594 11.5416 12.8594 11.2211V7.22111C12.8594 6.90061 13.1189 6.64111 13.4394 6.64111Z" fill="currentColor"/>
                                                                <path d="M12 15.75C12.4142 15.75 12.75 15.4142 12.75 15V12.5C12.75 12.0858 12.4142 11.75 12 11.75C11.5858 11.75 11.25 12.0858 11.25 12.5V15C11.25 15.4142 11.5858 15.75 12 15.75Z" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                        <div class="d-flex flex-column">
                                                            <h4 class="mb-1 text-dark">Gửi qua Telegram</h4>
                                                            <span>Bạn cũng có thể gửi phản hồi trực tiếp qua Telegram Bot. Nhắn tin theo format:</span>
                                                            <span class="mt-2"><strong>Email của bạn</strong></span>
                                                            <span><strong>Nội dung phản hồi</strong></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex justify-content-end">
                                                    <button type="reset" class="btn btn-light me-3">Hủy</button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <span class="indicator-label">Gửi Phản Hồi</span>
                                                        <span class="indicator-progress">Đang gửi...
                                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                        </span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Danh Sách Phản Hồi -->
                                <div class="col-xl-6">
                                    <div class="card card-flush">
                                        <div class="card-header pt-7">
                                            <h3 class="card-title align-items-start flex-column">
                                                <span class="card-label fw-bold text-dark">Lịch Sử Phản Hồi</span>
                                                <span class="text-gray-400 mt-1 fw-semibold fs-6">
                                                    @if($unreadCount > 0)
                                                        <span class="badge badge-danger">{{ $unreadCount }} tin nhắn mới</span>
                                                    @else
                                                        Không có tin nhắn mới
                                                    @endif
                                                </span>
                                            </h3>
                                        </div>
                                        <div class="card-body pt-6">
                                            <div class="hover-scroll-overlay-y pe-6 me-n6" style="max-height: 600px">
                                                @if($userFeedbacks->isEmpty())
                                                    <div class="text-center py-10">
                                                        <span class="svg-icon svg-icon-3x svg-icon-muted">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z" fill="currentColor"/>
                                                                <rect x="6" y="12" width="7" height="2" rx="1" fill="currentColor"/>
                                                                <rect x="6" y="7" width="12" height="2" rx="1" fill="currentColor"/>
                                                            </svg>
                                                        </span>
                                                        <div class="fw-semibold text-gray-500 mt-5">Chưa có phản hồi nào</div>
                                                    </div>
                                                @else
                                                    @foreach($userFeedbacks as $feedback)
                                                        <div class="border border-dashed border-gray-300 rounded px-7 py-5 mb-6 {{ $feedback->status == 1 ? 'bg-light-success' : '' }}">
                                                            <div class="d-flex flex-stack mb-3">
                                                                <div class="me-3">
                                                                    <span class="badge badge-{{ $feedback->status == 0 ? 'warning' : ($feedback->status == 1 ? 'success' : 'secondary') }}">
                                                                        @if($feedback->status == 0)
                                                                            Chờ xử lý
                                                                        @elseif($feedback->status == 1)
                                                                            Đã trả lời
                                                                        @else
                                                                            Đã đọc
                                                                        @endif
                                                                    </span>
                                                                </div>
                                                                <div class="text-gray-400 fs-7">{{ $feedback->time }}</div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <div class="fw-bold text-gray-800 mb-2">Phản hồi của bạn:</div>
                                                                <div class="text-gray-600">{!! nl2br(e($feedback->message)) !!}</div>
                                                            </div>
                                                            @if(!empty($feedback->admin_reply))
                                                                <div class="border-top pt-3 mt-3">
                                                                    <div class="fw-bold text-primary mb-2">Phản hồi từ Admin:</div>
                                                                    <div class="text-gray-700">{!! nl2br(e($feedback->admin_reply)) !!}</div>
                                                                    @if(!empty($feedback->reply_time))
                                                                        <div class="text-gray-400 fs-7 mt-2">{{ $feedback->reply_time }}</div>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
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
    </div>
</div>

@if(session('success'))
<script>
    swal("Thành Công", "{{ session('success') }}", "success");
</script>
@endif

@if(session('error'))
<script>
    swal("Thông Báo", "{{ session('error') }}", "error");
</script>
@endif
@endsection
