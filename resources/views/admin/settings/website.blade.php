@extends('layouts.admin')

@section('title', 'Cài Đặt Website')

@section('content')
<div class="intro-y box mt-5">
    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
        <h2 class="font-medium text-base mr-auto">Cài Đặt Trang Web</h2>
    </div>
    <div id="horizontal-form" class="p-5">
        <div class="preview">
            @if(session('success'))
                <div class="alert alert-success mb-4">
                    <script>
                        swal("Thông Báo", "{{ session('success') }}", "success");
                    </script>
                </div>
            @endif
            
            <form action="{{ route('admin.settings.website') }}" method="post">
                @csrf
                <div class="form-inline">
                    <label for="theme" class="form-label sm:w-20">Giao Diện Admin</label>
                    <select class="form-control" name="theme">
                        <option value="0" {{ ($settings->theme ?? '0') == '0' ? 'selected' : '' }}>Xanh Dương</option>
                        <option value="1" {{ ($settings->theme ?? '0') == '1' ? 'selected' : '' }}>Xanh Lá Đậm</option>
                        <option value="2" {{ ($settings->theme ?? '0') == '2' ? 'selected' : '' }}>Xanh Dương Sáng</option>
                        <option value="3" {{ ($settings->theme ?? '0') == '3' ? 'selected' : '' }}>Xanh Xám (Khuyên Dùng)</option>
                        <option value="4" {{ ($settings->theme ?? '0') == '4' ? 'selected' : '' }}>Tím</option>
                    </select>
                </div>
                <div class="form-inline mt-5">
                    <label for="title" class="form-label sm:w-20">Title</label>
                    <textarea id="title" type="text" class="form-control" name="title" placeholder="Tiêu Đề Trang Web" rows="4" cols="50">{{ $settings->tieude ?? '' }}</textarea>
                </div>
                <div class="form-inline mt-5">
                    <label for="keywords" class="form-label sm:w-20">Keywords</label>
                    <textarea id="keywords" type="text" class="form-control" name="keywords" placeholder="keywords" rows="4" cols="50">{{ $settings->keywords ?? '' }}</textarea>
                </div>
                <div class="form-inline mt-5">
                    <label for="description" class="form-label sm:w-20">Description</label>
                    <textarea id="description" type="text" class="form-control" name="description" placeholder="Mô Tả Trang Web" rows="4" cols="50">{{ $settings->mota ?? '' }}</textarea>
                </div>
                <div class="form-inline mt-5">
                    <label for="imagebanner" class="form-label sm:w-20">Ảnh Mô Tả</label>
                    <textarea id="imagebanner" type="text" class="form-control" name="imagebanner" placeholder="Ảnh Mô Tả" rows="4" cols="50">{{ $settings->imagebanner ?? '' }}</textarea>
                </div>
                <div class="form-inline mt-5">
                    <label for="phone" class="form-label sm:w-20">Số Điện Thoại Zalo</label>
                    <input id="phone" type="text" class="form-control" name="phone" placeholder="Số Điện Thoại Zalo" value="{{ $settings->sodienthoai ?? '' }}">
                </div>
                <div class="form-inline mt-5">
                    <label for="banner" class="form-label sm:w-20">ID Video Banner</label>
                    <input id="banner" type="text" class="form-control" name="banner" placeholder="banner Ở Home" value="{{ $settings->banner ?? '' }}">
                </div>
                <div class="form-inline mt-5">
                    <label for="logo" class="form-label sm:w-20">Logo</label>
                    <input id="logo" type="text" class="form-control" name="logo" placeholder="Ảnh logo" value="{{ $settings->logo ?? '' }}">
                </div>
                <div class="sm:ml-20 sm:pl-5 mt-5">
                    <button type="submit" class="btn btn-primary">Cập Nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

