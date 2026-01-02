@extends('layouts.admin')

@section('title', 'Cộng Tiền Thành Viên')

@section('content')
<div class="intro-y box mt-5">
    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
        <h2 class="font-medium text-base mr-auto">Cộng Tiền Thành Viên</h2>
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
            
            @if(session('error'))
                <div class="alert alert-danger mb-4">
                    <script>
                        swal("Thông Báo", "{{ session('error') }}", "error");
                    </script>
                </div>
            @endif
            
            <form action="{{ route('admin.cards.add-balance') }}" method="post">
                @csrf
                <div class="form-inline">
                    <label for="idc" class="form-label sm:w-20">ID Thành Viên</label>
                    <input id="idc" type="text" name="idc" class="form-control" placeholder="Mã Số Thành Viên" required>
                </div>
                
                <div class="form-inline mt-5">
                    <label for="price" class="form-label sm:w-20">Số Tiền</label>
                    <input id="price" type="text" class="form-control" placeholder="Tiền Cần Cộng" name="price" required>
                </div>
                
                <div class="sm:ml-20 sm:pl-5 mt-5">
                    <button type="submit" class="btn btn-primary">Cộng Ngay</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

