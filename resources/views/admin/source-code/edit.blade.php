@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Source Code')

@section('content')
<div class="intro-y box mt-5">
    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
        <h2 class="font-medium text-base mr-auto">Chỉnh Sửa Source Code</h2>
    </div>
    <div id="horizontal-form" class="p-5">
        <div class="preview">
            <form action="{{ route('admin.source-code.update', $sourceCode->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-inline">
                    <label for="name" class="form-label sm:w-20">Tên Source Code</label>
                    <input id="name" type="text" name="name" class="form-control" placeholder="Tên source code" value="{{ $sourceCode->name }}" required>
                </div>
                <div class="form-inline mt-5">
                    <label for="description" class="form-label sm:w-20">Mô Tả</label>
                    <textarea id="description" name="description" class="form-control" placeholder="Mô tả source code">{{ $sourceCode->description ?? '' }}</textarea>
                </div>
                <div class="form-inline mt-5">
                    <label for="category" class="form-label sm:w-20">Loại</label>
                    <input id="category" type="text" name="category" class="form-control" placeholder="PHP, Laravel, etc." value="{{ $sourceCode->category ?? '' }}">
                </div>
                <div class="form-inline mt-5">
                    <label for="price" class="form-label sm:w-20">Giá Tiền</label>
                    <input id="price" type="number" class="form-control" name="price" placeholder="Giá Bán" value="{{ $sourceCode->price }}" required>
                </div>
                <div class="form-inline mt-5">
                    <label for="image" class="form-label sm:w-20">Hình Ảnh</label>
                    <div class="flex items-center space-x-3">
                        <select id="image-select" class="form-control" name="image" onchange="updateImagePreview()">
                            <option value="">-- Chọn hình ảnh --</option>
                            @foreach($availableImages as $img)
                                <option value="/domain/images/{{ $img }}" 
                                    {{ ($sourceCode->image == '/domain/images/' . $img || $sourceCode->image == '/images/' . $img) ? 'selected' : '' }}>
                                    {{ $img }}
                                </option>
                            @endforeach
                        </select>
                        <div id="image-preview" class="ml-3">
                            <img id="preview-img" src="{{ fixImagePath($sourceCode->image ?? '') }}" alt="Preview" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                        </div>
                    </div>
                </div>
                <div class="form-inline mt-5">
                    <label for="file" class="form-label sm:w-20">Upload File</label>
                    <input id="file" type="file" name="file" class="form-control" accept=".zip,.rar,.tar,.gz">
                    <small class="text-muted">Để trống nếu không muốn thay đổi file. File hiện tại: {{ $sourceCode->file_path ?? 'Chưa có' }}</small>
                </div>
                <div class="form-inline mt-5">
                    <label for="download_link" class="form-label sm:w-20">Link Download</label>
                    <input id="download_link" type="text" name="download_link" class="form-control" placeholder="https://..." value="{{ $sourceCode->download_link ?? '' }}">
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

