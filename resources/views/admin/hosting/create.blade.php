@extends('layouts.admin')

@section('title', 'Thêm Gói Hosting')

@section('content')
<div class="intro-y box mt-5">
    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
        <h2 class="font-medium text-base mr-auto">Thêm Gói Hosting</h2>
    </div>
    <div id="horizontal-form" class="p-5">
        <div class="preview">
            <form action="{{ route('admin.hosting.store') }}" method="post">
                @csrf
                
                <div class="form-inline">
                    <label for="name" class="form-label sm:w-20">Tên Gói</label>
                    <input id="name" type="text" name="name" class="form-control @error('name') border-danger @enderror" placeholder="Tên gói hosting" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-inline mt-5">
                    <label for="description" class="form-label sm:w-20">Mô Tả</label>
                    <textarea id="description" name="description" class="form-control" placeholder="Mô tả gói hosting">{{ old('description') }}</textarea>
                </div>
                
                <div class="form-inline mt-5">
                    <label for="price_month" class="form-label sm:w-20">Giá/Tháng</label>
                    <input id="price_month" type="number" class="form-control @error('price_month') border-danger @enderror" name="price_month" placeholder="Giá theo tháng" value="{{ old('price_month') }}" required>
                    @error('price_month')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-inline mt-5">
                    <label for="price_year" class="form-label sm:w-20">Giá/Năm</label>
                    <input id="price_year" type="number" class="form-control @error('price_year') border-danger @enderror" name="price_year" placeholder="Giá theo năm" value="{{ old('price_year') }}" required>
                    @error('price_year')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-inline mt-5">
                    <label for="specs" class="form-label sm:w-20">Thông Số</label>
                    <textarea id="specs" name="specs" class="form-control" placeholder="Thông số kỹ thuật (CPU, RAM, Storage, etc.)">{{ old('specs') }}</textarea>
                </div>
                
                <div class="form-inline mt-5">
                    <label for="image" class="form-label sm:w-20">Hình Ảnh</label>
                    <div class="flex items-center space-x-3">
                        <select id="image-select" class="form-control" name="image" onchange="updateImagePreview()">
                            <option value="">-- Chọn hình ảnh --</option>
                            @foreach($availableImages as $img)
                                <option value="/images/hosting/{{ $img }}" {{ old('image') == '/images/hosting/' . $img ? 'selected' : '' }}>{{ $img }}</option>
                            @endforeach
                        </select>
                        <div id="image-preview" class="ml-3" style="display: none;">
                            <img id="preview-img" src="" alt="Preview" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                        </div>
                    </div>
                </div>
                
                <div class="sm:ml-20 sm:pl-5 mt-5">
                    <button type="submit" class="btn btn-primary">Đăng Ngay</button>
                    <a href="{{ route('admin.hosting.index') }}" class="btn btn-outline-secondary ml-2">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateImagePreview() {
    const select = document.getElementById('image-select');
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    if (select.value) {
        previewImg.src = select.value;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

// Show preview if there's an old value
document.addEventListener('DOMContentLoaded', function() {
    updateImagePreview();
});
</script>
@endsection
