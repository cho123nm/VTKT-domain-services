@extends('layouts.admin')

@section('title', 'Cài Đặt Gạch Thẻ')

@section('content')
<div class="intro-y box mt-5">
    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
        <h2 class="font-medium text-base mr-auto">Cài Đặt Gạch Thẻ</h2>
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
            
            <form action="{{ route('admin.settings.card') }}" method="post">
                @csrf
                <div class="form-inline">
                    <label for="webgach" class="form-label sm:w-20">Tên Web Gạch</label>
                    <input id="webgach" type="text" name="webgach" class="form-control" value="{{ $settings->webgach ?? '' }}" placeholder="Tên Web Gạch" required>
                </div>
                <div class="form-inline mt-5">
                    <label for="apikey" class="form-label sm:w-20">API KEY</label>
                    <input id="apikey" type="text" name="apikey" class="form-control" value="{{ $settings->apikey ?? '' }}" placeholder="API KEY Tại CardVip.Vn" required>
                </div>
                <div class="form-inline mt-5">
                    <label for="callback" class="form-label sm:w-20">URL Callback</label>
                    <input id="callback" type="text" name="callback" class="form-control" value="{{ $settings->callback ?? '' }}" placeholder="URL TRẢ TRẠNG THÁI THẺ" required>
                </div>
                <div class="sm:ml-20 sm:pl-5 mt-5">
                    <button type="submit" class="btn btn-primary">Cập Nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

