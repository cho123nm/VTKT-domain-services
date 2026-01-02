@extends('layouts.admin')

@section('content')
<div class="col-span-12 mt-6">
    <div class="intro-y flex items-center h-10">
        <h2 class="text-lg font-medium truncate mr-5"> Chi Tiết Phản Hồi </h2>
        <a href="{{ route('admin.feedback.index') }}" class="ml-auto btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left w-4 h-4 mr-2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Quay Lại
        </a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success show mb-2 mt-4" role="alert">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="intro-y box p-5 mt-5">
        <div class="mb-4">
            <span class="badge badge-{{ $feedback->status == 0 ? 'warning' : ($feedback->status == 1 ? 'success' : 'secondary') }} mr-2">
                @if($feedback->status == 0)
                    Chờ xử lý
                @elseif($feedback->status == 1)
                    Đã trả lời
                @else
                    Đã đọc
                @endif
            </span>
            <span class="text-gray-500 text-sm">{{ $feedback->time }}</span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <div class="text-sm text-gray-500 mb-1">👤 Người dùng:</div>
                <div class="font-medium">{{ $feedback->username }} 
                    @if($feedback->uid > 0)
                        <span class="text-gray-400">(ID: {{ $feedback->uid }})</span>
                    @else
                        <span class="text-gray-400">(Chưa đăng ký)</span>
                    @endif
                </div>
            </div>
            
            <div>
                <div class="text-sm text-gray-500 mb-1">📧 Email:</div>
                <div class="font-medium">{{ $feedback->email }}</div>
            </div>
        </div>
        
        <div class="mb-4">
            <div class="text-sm text-gray-500 mb-2">💬 Phản hồi:</div>
            <div class="bg-gray-100 p-4 rounded">{!! nl2br(e($feedback->message)) !!}</div>
        </div>
        
        @if(!empty($feedback->admin_reply))
            <div class="mb-4 border-t pt-4">
                <div class="text-sm text-primary mb-2">✅ Phản hồi từ Admin:</div>
                <div class="bg-primary bg-opacity-10 p-4 rounded">{!! nl2br(e($feedback->admin_reply)) !!}</div>
                <div class="text-gray-400 text-sm mt-2">{{ $feedback->reply_time }}</div>
            </div>
        @endif
        
        @if($feedback->status == 0)
            <div class="mt-5 border-t pt-4">
                <h3 class="font-medium mb-3">Trả Lời Phản Hồi</h3>
                <form method="POST" action="{{ route('admin.feedback.reply', $feedback->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nội dung trả lời: <span class="text-danger">*</span></label>
                        <textarea name="admin_reply" class="form-control" rows="5" required placeholder="Nhập nội dung trả lời..."></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-send w-4 h-4 mr-2">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                            Gửi Phản Hồi
                        </button>
                    </div>
                </form>
            </div>
        @endif
        
        @if($feedback->status != 0)
            <div class="mt-5 border-t pt-4">
                <form method="POST" action="{{ route('admin.feedback.update-status', $feedback->id) }}">
                    @csrf
                    <div class="flex items-center gap-3">
                        <label class="form-label mb-0">Cập nhật trạng thái:</label>
                        <select name="status" class="form-select w-auto">
                            <option value="0" {{ $feedback->status == 0 ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="1" {{ $feedback->status == 1 ? 'selected' : '' }}>Đã trả lời</option>
                            <option value="2" {{ $feedback->status == 2 ? 'selected' : '' }}>Đã đọc</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Cập Nhật</button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
