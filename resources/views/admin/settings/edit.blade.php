@extends('layouts.admin')

@section('title', 'Cài Đặt Trang Web')

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
            
            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('admin.settings.update') }}" method="post">
                @csrf
                @method('PUT')
                <div class="form-inline">
                    <label for="horizontal-form-1" class="form-label sm:w-20">Giao Diện Admin</label>
                    <select class="form-control" name="theme">
                        <option value="{{ $settings->theme ?? '0' }}">
                            @php
                            $themeValue = $settings->theme ?? '0';
                            if($themeValue == '0') echo 'Xanh Dương';
                            if($themeValue == '1') echo 'Xanh Lá Đậm';
                            if($themeValue == '2') echo 'Xanh Dương Sáng';
                            if($themeValue == '3') echo 'Xanh Xám';
                            if($themeValue == '4') echo 'Tím';
                            @endphp
                        </option>
                        <option value="0">Xanh Dương</option>
                        <option value="1">Xanh Lá Đậm</option>
                        <option value="2">Xanh Dương Sáng</option>
                        <option value="3">Xanh Xám (Khuyên Dùng)</option>
                        <option value="4">Tím</option>
                    </select>
                </div>
                <div class="form-inline mt-5">
                    <label for="horizontal-form-2" class="form-label sm:w-20">title</label>
                    <textarea id="horizontal-form-2" type="text" class="form-control" name="title" placeholder="Tiêu Đề Trang Web" rows="4" cols="50">{{ htmlspecialchars($settings->tieude ?? '') }}</textarea>
                </div>
                <div class="form-inline mt-5">
                    <label for="horizontal-form-1" class="form-label sm:w-20">keywords</label>
                    <textarea id="horizontal-form-2" type="text" class="form-control" name="keywords" placeholder="keywords" rows="4" cols="50">{{ htmlspecialchars($settings->keywords ?? '') }}</textarea>
                </div>
                <div class="form-inline mt-5">
                    <label for="horizontal-form-1" class="form-label sm:w-20">description</label>
                    <textarea id="horizontal-form-2" type="text" class="form-control" name="description" placeholder="Mô Tả Trang Web" rows="4" cols="50">{{ htmlspecialchars($settings->mota ?? '') }}</textarea>
                </div>
                <div class="form-inline mt-5">
                    <label for="horizontal-form-2" class="form-label sm:w-20">Ảnh Mô Tả Trang Web</label>
                    <textarea id="horizontal-form-2" type="text" class="form-control" name="imagebanner" placeholder="Ảnh Mô Tả" rows="4" cols="50">{{ htmlspecialchars($settings->imagebanner ?? '') }}</textarea>
                </div>
                <div class="form-inline mt-5">
                    <label for="horizontal-form-2" class="form-label sm:w-20">Số Điện Thoại Zalo</label>
                    <input id="horizontal-form-2" type="text" class="form-control" name="phone" placeholder="Số Điện Thoại Zalo" value="{{ htmlspecialchars($settings->sodienthoai ?? '') }}">
                </div>
                <div class="form-inline mt-5">
                    <label for="horizontal-form-2" class="form-label sm:w-20">ID Video banner</label>
                    <input id="horizontal-form-2" type="text" class="form-control" name="banner" placeholder="banner Ở Home" value="{{ htmlspecialchars($settings->banner ?? '') }}">
                </div>
                <div class="form-inline mt-5">
                    <label for="horizontal-form-2" class="form-label sm:w-20">lOGO</label>
                    <input id="horizontal-form-2" type="text" class="form-control" name="logo" placeholder="Ảnh logo" value="{{ htmlspecialchars($settings->logo ?? '') }}">
                </div>
                <div class="sm:ml-20 sm:pl-5 mt-5">
                    <button type="submit" class="btn btn-primary">Cập Nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

