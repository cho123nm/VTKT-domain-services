@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Sản Phẩm')

@section('content')
<div class="intro-y box mt-5">
    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
        <h2 class="font-medium text-base mr-auto">Chỉnh Sửa Sản Phẩm</h2>
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
            
            <form action="{{ route('admin.domain.update', $domain->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="form-inline">
                    <label for="horizontal-form-1" class="form-label sm:w-20">Đuôi Miền</label>
                    <input id="horizontal-form-1" type="text" name="duoi" class="form-control" value="{{ $domain->duoi }}" placeholder="Đuôi Miền" required>
                </div>
                <div class="form-inline mt-5">
                    <label for="horizontal-form-1" class="form-label sm:w-20">Hình Ảnh</label>
                    <div class="flex items-center space-x-3">
                        <select id="image-select" class="form-control" name="image" onchange="updateImagePreview()" required>
                            <option value="">-- Chọn hình ảnh --</option>
                            @foreach($availableImages as $img)
                                <option value="/domain/images/{{ $img }}" 
                                    {{ ($domain->image == '/domain/images/' . $img || $domain->image == '/images/' . $img) ? 'selected' : '' }}>
                                    {{ $img }}
                                </option>
                            @endforeach
                        </select>
                        <div id="image-preview" class="ml-3">
                            <img id="preview-img" src="{{ fixImagePath($domain->image) }}" alt="Preview" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                        </div>
                    </div>
                </div>
                <div class="form-inline mt-5">
                    <label for="horizontal-form-1" class="form-label sm:w-20">Giá Tiền</label>
                    <input id="horizontal-form-1" type="number" class="form-control" name="price" placeholder="Giá Bán" value="{{ $domain->price }}" required>
                </div>
                <div class="sm:ml-20 sm:pl-5 mt-5">
                    <button type="submit" class="btn btn-primary">Đăng Ngay</button>
                    <a href="{{ route('admin.domain.index') }}" class="btn btn-outline-secondary ml-2">Hủy</a>
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

