@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Thành Viên')

@section('content')
<div class="col-span-12 mt-6">
    <div class="intro-y block sm:flex items-center h-10">
        <h2 class="text-lg font-medium truncate mr-5">Chỉnh Sửa Thành Viên: {{ $user->taikhoan }}</h2>
        <div class="flex items-center sm:ml-auto mt-3 sm:mt-0">
            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline-secondary mr-2">Xem Chi Tiết</a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Quay Lại</a>
        </div>
    </div>

    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto">Thông Tin Tài Khoản</h2>
        </div>
        <div class="p-5">
            @if(session('success'))
                <div class="alert alert-success mb-4">
                    <script>
                        swal("Thông Báo", "{{ session('success') }}", "success");
                    </script>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger mb-4">
                    <script>
                        swal("Lỗi", "{{ session('error') }}", "error");
                    </script>
                </div>
            @endif

            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12 sm:col-span-6">
                        <label class="form-label">UID</label>
                        <input type="text" class="form-control" value="{{ $user->id }}" disabled>
                    </div>

                    <div class="col-span-12 sm:col-span-6">
                        <label class="form-label">Tài Khoản</label>
                        <input type="text" class="form-control" value="{{ $user->taikhoan }}" disabled>
                    </div>

                    <div class="col-span-12 sm:col-span-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') border-danger @enderror" 
                               value="{{ old('email', $user->email) }}" placeholder="Nhập email">
                        @error('email')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-span-12 sm:col-span-6">
                        <label class="form-label">Số Dư <span class="text-danger">*</span></label>
                        <input type="number" name="tien" class="form-control @error('tien') border-danger @enderror" 
                               value="{{ old('tien', $user->tien) }}" placeholder="Nhập số dư" required>
                        @error('tien')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-span-12 sm:col-span-6">
                        <label class="form-label">Chức Vụ <span class="text-danger">*</span></label>
                        <select name="chucvu" class="form-control @error('chucvu') border-danger @enderror" required>
                            <option value="0" {{ old('chucvu', $user->chucvu) == 0 ? 'selected' : '' }}>Thành Viên</option>
                            <option value="1" {{ old('chucvu', $user->chucvu) == 1 ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('chucvu')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-span-12 sm:col-span-6">
                        <label class="form-label">Ngày Đăng Ký</label>
                        <input type="text" class="form-control" value="{{ $user->time }}" disabled>
                    </div>

                    <div class="col-span-12 mt-4">
                        <button type="submit" class="btn btn-primary w-24">Cập Nhật</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-24 ml-2">Hủy</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete User Section -->
    @if(!$user->isAdmin())
    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 border-danger">
            <h2 class="font-medium text-base mr-auto text-danger">Xóa Tài Khoản</h2>
        </div>
        <div class="p-5">
            <p class="text-slate-600 mb-4">Xóa tài khoản này sẽ xóa vĩnh viễn tất cả dữ liệu liên quan. Hành động này không thể hoàn tác.</p>
            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này? Hành động này không thể hoàn tác!');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Xóa Tài Khoản</button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
