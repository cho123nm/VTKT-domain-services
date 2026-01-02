@extends('layouts.admin')

@section('content')
<div class="col-span-12 mt-6">
    <div class="intro-y block sm:flex items-center h-10">
        <h2 class="text-lg font-medium truncate mr-5"> Danh Sách Ticket </h2>
        <div class="flex items-center sm:ml-auto mt-3 sm:mt-0">
            <button class="btn box flex items-center text-slate-600 dark:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="file-text" data-lucide="file-text" class="lucide lucide-file-text hidden sm:block w-4 h-4 mr-2"><path d="M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg> Export to Excel
            </button>
            <button class="ml-3 btn box flex items-center text-slate-600 dark:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="file-text" data-lucide="file-text" class="lucide lucide-file-text hidden sm:block w-4 h-4 mr-2"><path d="M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg> Export to PDF
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success show mb-2 mt-4" role="alert">
        {{ session('success') }}
    </div>
    @endif

    <div class="intro-y overflow-auto lg:overflow-visible mt-8 sm:mt-0">
        <table class="table table-report sm:mt-2">
            <thead>
                <tr>
                    <th class="whitespace-nowrap"> Mã Đơn Hàng </th>
                    <th class="whitespace-nowrap"> Tên Miền </th>
                    <th class="text-center whitespace-nowrap"> UID </th>
                    <th class="text-center whitespace-nowrap">STATUS</th>
                    <th class="text-center whitespace-nowrap"> HÀNH ĐỘNG </th>
                </tr>
            </thead>
            <tbody>
                @forelse($domains as $domain)
                <tr class="intro-x">
                    <td class="w-40">
                        #{{ $domain->mgd }}
                    </td>
                    <td>
                        <a href="" class="font-medium whitespace-nowrap">{{ $domain->domain }}</a>
                        <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">{{ $domain->ns1 }} | {{ $domain->ns2 }}</div>
                    </td>
                    <td class="text-center">{{ $domain->uid }}</td>
                    <td class="w-40">
                        <div class="flex items-center justify-center text-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Chờ Cập Nhật
                        </div>
                    </td>
                    <td class="table-report__action w-56">
                        <div class="flex justify-center items-center">
                            <a class="flex items-center mr-3" data-tw-toggle="modal" data-tw-target="#edit-modal-{{ $domain->id }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="check-square" data-lucide="check-square" class="lucide lucide-check-square w-4 h-4 mr-1"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg> Edit
                            </a>
                            <a class="flex items-center text-danger" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-modal-{{ $domain->id }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="trash-2" data-lucide="trash-2" class="lucide lucide-trash-2 w-4 h-4 mr-1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Delete
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4">Không có ticket nào đang chờ xử lý</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Modals -->
@foreach($domains as $domain)
<div id="delete-modal-{{ $domain->id }}" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="p-5 text-center">
                    <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                    <div class="text-3xl mt-5">Bạn Có Chắc Muốn Từ Chối Hỗ Trợ Đơn Hàng Này?</div>
                    <div class="text-slate-500 mt-2">  </div>
                    <form action="{{ route('admin.dns.reject', $domain->id) }}" method="post">
                        @csrf
                        <div class="px-5 pb-8 text-center">
                            <a data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1"> Đóng </a>
                            <button type="submit" class="btn btn-danger w-24"> Xóa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modals -->
<div id="edit-modal-{{ $domain->id }}" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto"> Chi Tiết Ticket ({{ $domain->domain }}) </h2>
                <div class="dropdown sm:hidden">
                    <a class="dropdown-toggle w-5 h-5 block" href="javascript:;" aria-expanded="false" data-tw-toggle="dropdown">
                        <i data-lucide="more-horizontal" class="w-5 h-5 text-slate-500"></i>
                    </a>
                    <div class="dropdown-menu w-40">
                    </div>
                </div>
            </div>
            <form action="{{ route('admin.dns.update', $domain->id) }}" method="post">
                @csrf
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 sm:col-span-6">
                        <label for="ns1-{{ $domain->id }}" class="form-label"> NS1 </label>
                        <input type="text" id="ns1-{{ $domain->id }}" name="ns1" class="form-control" placeholder="Nameserver 1" value="{{ $domain->ns1 }}" required>
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label for="ns2-{{ $domain->id }}" class="form-label"> NS2 </label>
                        <input type="text" id="ns2-{{ $domain->id }}" name="ns2" class="form-control" placeholder="Nameserver 2" value="{{ $domain->ns2 }}" required>
                    </div>
                    <div class="col-span-12">
                        <label for="trangthai-{{ $domain->id }}" class="form-label"> Trạng Thái </label>
                        <select id="trangthai-{{ $domain->id }}" name="trangthai" class="form-select">
                            <option value="1"> Thành Công </option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1">Cancel</button>
                    <button type="submit" class="btn btn-primary w-20"> Gửi Đi </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
