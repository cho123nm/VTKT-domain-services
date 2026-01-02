@extends('layouts.admin')

@section('title', 'Danh Sách Thành Viên')

@section('content')
<div class="col-span-12 mt-6">
    <div class="intro-y block sm:flex items-center h-10">
        <h2 class="text-lg font-medium truncate mr-5">Danh Sách Thành Viên</h2>
        <div class="flex items-center sm:ml-auto mt-3 sm:mt-0">
        </div>
    </div>
    <div class="intro-y box">
        <div class="p-5" id="head-options-table">
            <div class="preview">
                @if(session('success'))
                    <div class="alert alert-success mb-4">
                        <script>
                            swal("Thông Báo", "{{ session('success') }}", "success");
                        </script>
                    </div>
                @endif
                
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead class="table-dark">
                            <tr>
                                <th class="whitespace-nowrap">#</th>
                                <th class="whitespace-nowrap">UID</th>
                                <th class="whitespace-nowrap">Tài Khoản</th>
                                <th class="whitespace-nowrap">Mật Khẩu</th>
                                <th class="whitespace-nowrap">Tiền</th>
                                <th class="whitespace-nowrap">Time</th>
                                <th class="whitespace-nowrap">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($users->isEmpty())
                                <tr>
                                    <td colspan="7" class="text-center">Chưa có thành viên nào</td>
                                </tr>
                            @else
                                @foreach($users as $index => $user)
                                <tr>
                                    <td>#{{ $index + 1 }}</td>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->taikhoan }}</td>
                                    <td>{{ $user->matkhau }}</td>
                                    <td>{{ number_format($user->tien) }}đ</td>
                                    <td class="whitespace-nowrap">{{ $user->time }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-primary btn-sm mr-1">Xem</a>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-sm mr-1">Sửa</a>
                                        <button data-tw-toggle="modal" data-tw-target="#header-footer-modal-preview-{{ $user->id }}" class="btn btn-success btn-sm">Số Dư</button>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($users as $user)
<div id="header-footer-modal-preview-{{ $user->id }}" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Chỉnh Sửa Tài Khoản ({{ $user->taikhoan }})</h2>
                <div class="dropdown sm:hidden">
                    <a class="dropdown-toggle w-5 h-5 block" href="javascript:;" aria-expanded="false" data-tw-toggle="dropdown">
                        <i data-lucide="more-horizontal" class="w-5 h-5 text-slate-500"></i>
                    </a>
                    <div class="dropdown-menu w-40">
                    </div>
                </div>
            </div>
            <form action="{{ route('admin.users.update-balance', $user->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12">
                        <label for="modal-form-1" class="form-label">Số Dư</label>
                        <input type="text" name="tien" class="form-control" rows="4" cols="50" placeholder="Số Dư" value="{{ $user->tien }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1">Cancel</button>
                    <button type="submit" class="btn btn-primary w-20">Gửi Đi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

