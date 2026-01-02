@extends('layouts.admin')

@section('title', 'Cài Đặt Liên Hệ')

@section('content')
<div class="intro-y box mt-5">
    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
        <h2 class="font-medium text-base mr-auto">Cài Đặt Liên Hệ</h2>
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
            
            <form action="{{ route('admin.settings.contact') }}" method="post">
                @csrf
                <div class="form-inline">
                    <label for="facebook_link" class="form-label sm:w-20">Facebook Link</label>
                    <input id="facebook_link" type="text" name="facebook_link" class="form-control" placeholder="https://www.facebook.com/..." value="{{ $facebookLink }}">
                </div>
                <div class="form-inline mt-5">
                    <label for="zalo_phone" class="form-label sm:w-20">Zalo Phone</label>
                    <input id="zalo_phone" type="text" name="zalo_phone" class="form-control" placeholder="0856761038" value="{{ $zaloPhone }}">
                </div>
                <div class="sm:ml-20 sm:pl-5 mt-5">
                    <button type="submit" class="btn btn-primary">Lưu Cài Đặt</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

