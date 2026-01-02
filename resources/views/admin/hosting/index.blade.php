@extends('layouts.admin')

@section('title', 'Danh Sách Hosting')

@section('content')
<div class="col-span-12 mt-6">
    <div class="intro-y block sm:flex items-center h-10">
        <h2 class="text-lg font-medium truncate mr-5">Danh Sách Hosting</h2>
        <div class="flex items-center sm:ml-auto mt-3 sm:mt-0">
            <a href="{{ route('admin.hosting.create') }}" class="btn btn-primary">Thêm Hosting</a>
        </div>
    </div>
    <div class="intro-y overflow-auto lg:overflow-visible mt-8 sm:mt-0">
        @if(session('success'))
            <div class="alert alert-success mb-4">
                <script>
                    swal("Thông Báo", "{{ session('success') }}", "success");
                </script>
            </div>
        @endif
        
        <table class="table table-report sm:mt-2">
            <thead>
                <tr>
                    <th class="whitespace-nowrap">TÊN GÓI</th>
                    <th class="whitespace-nowrap">GIÁ/THÁNG</th>
                    <th class="whitespace-nowrap">GIÁ/NĂM</th>
                    <th class="text-center whitespace-nowrap">HÀNH ĐỘNG</th>
                </tr>
            </thead>
            <tbody>
                @if($hostings->isEmpty())
                    <tr>
                        <td colspan="4" class="text-center">Chưa có gói hosting nào</td>
                    </tr>
                @else
                    @foreach($hostings as $item)
                    <tr class="intro-x">
                        <td>
                            <a href="" class="font-medium whitespace-nowrap">{{ $item->name }}</a>
                        </td>
                        <td>
                            {{ number_format($item->price_month) }}đ
                        </td>
                        <td>
                            {{ number_format($item->price_year) }}đ
                        </td>
                        <td class="table-report__action w-56">
                            <div class="flex justify-center items-center">
                                <a class="flex items-center mr-3" href="{{ route('admin.hosting.edit', $item->id) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Edit
                                </a>
                                <a class="flex items-center text-danger" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-modal-{{ $item->id }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="trash-2" data-lucide="trash-2" class="lucide lucide-trash-2 w-4 h-4 mr-1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

@foreach($hostings as $item)
<div id="delete-modal-{{ $item->id }}" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="p-5 text-center">
                    <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                    <div class="text-3xl mt-5">Bạn Có Chắc Muốn Xóa Nó?</div>
                    <div class="text-slate-500 mt-2">Sau Khi Thực Hiện Xóa Sẽ Không Thể Khôi Phục Nó!</div>
                    <form action="{{ route('admin.hosting.destroy', $item->id) }}" method="post">
                        @csrf
                        @method('DELETE')
                        <div class="px-5 pb-8 text-center">
                            <a data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">Đóng</a>
                            <button type="submit" class="btn btn-danger w-24">Xóa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
