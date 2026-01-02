@extends('layouts.admin')

@section('content')
<div class="intro-y box mt-5">
    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
        <h2 class="font-medium text-base mr-auto">Chỉnh Sửa Gói VPS</h2>
    </div>
    <div id="horizontal-form" class="p-5">
        <div class="preview">
            <form action="{{ route('admin.vps.update', $vps->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="form-inline">
                    <label for="name" class="form-label sm:w-20">Tên Gói</label>
                    <input id="name" type="text" name="name" class="form-control" placeholder="Tên gói VPS" value="{{ $vps->name }}" required>
                </div>
                <div class="form-inline mt-5">
                    <label for="description" class="form-label sm:w-20">Mô Tả</label>
                    <textarea id="description" name="description" class="form-control" placeholder="Mô tả gói VPS">{{ $vps->description ?? '' }}</textarea>
                </div>
                <div class="form-inline mt-5">
                    <label for="price_month" class="form-label sm:w-20">Giá/Tháng</label>
                    <input id="price_month" type="number" class="form-control" name="price_month" placeholder="Giá theo tháng" value="{{ $vps->price_month }}" required>
                </div>
                <div class="form-inline mt-5">
                    <label for="price_year" class="form-label sm:w-20">Giá/Năm</label>
                    <input id="price_year" type="number" class="form-control" name="price_year" placeholder="Giá theo năm" value="{{ $vps->price_year }}" required>
                </div>
                <div class="form-inline mt-5">
                    <label for="specs" class="form-label sm:w-20">Thông Số</label>
                    <textarea id="specs" name="specs" class="form-control" placeholder="Thông số kỹ thuật (CPU, RAM, Storage, etc.)">{{ $vps->specs ?? '' }}</textarea>
                </div>
                <div class="form-inline mt-5">
                    <label for="image" class="form-label sm:w-20">Hình Ảnh</label>
                    <div class="flex items-center space-x-3">
                        <select id="image-select" class="form-control" name="image" onchange="updateImagePreview()">
                            <option value="">-- Chọn hình ảnh --</option>
                            @foreach($availableImages as $img)
                                @php
                                    $imgPath = '/images/vps/' . $img;
                                    $isSelected = old('image', $vps->image) == $imgPath || 
                                                  old('image', $vps->image) == '/images/vps/' . $img ||
                                                  old('image', $vps->image) == 'images/vps/' . $img;
                                @endphp
                                <option value="{{ $imgPath }}" {{ $isSelected ? 'selected' : '' }}>
                                    {{ $img }}
                                </option>
                            @endforeach
                        </select>
                        <div id="image-preview" class="ml-3">
                            <img id="preview-img" src="{{ fixImagePath($vps->image ?? '') }}" alt="Preview" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                        </div>
                    </div>
                </div>
                <div class="sm:ml-20 sm:pl-5 mt-5">
                    <button type="submit" class="btn btn-primary">Cập Nhật</button>
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
</script>
@endsection

