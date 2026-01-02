@extends('layouts.admin')

@section('title', 'Danh Sách Đơn Hàng')

@section('content')
<div class="col-span-12 mt-6">
    <div class="intro-y block sm:flex items-center h-10">
        <h2 class="text-lg font-medium truncate mr-5">Danh Sách Đơn Hàng</h2>
        <div class="flex items-center sm:ml-auto mt-3 sm:mt-0">
            <button class="btn box flex items-center text-slate-600 dark:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text hidden sm:block w-4 h-4 mr-2"><path d="M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg> Export to Excel
            </button>
            <button class="ml-3 btn box flex items-center text-slate-600 dark:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text hidden sm:block w-4 h-4 mr-2"><path d="M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg> Export to PDF
            </button>
        </div>
    </div>
    <div class="intro-y overflow-auto lg:overflow-visible mt-8 sm:mt-0">
        <table class="table table-report sm:mt-2">
            <thead>
                <tr>
                    <th class="whitespace-nowrap">ID</th>
                    <th class="whitespace-nowrap">TÊN MIỀN</th>
                    <th class="text-center whitespace-nowrap">NS1</th>
                    <th class="text-center whitespace-nowrap">NS2</th>
                    <th class="text-center whitespace-nowrap">UID</th>
                    <th class="text-center whitespace-nowrap">TRẠNG THÁI</th>
                    <th class="text-center whitespace-nowrap">TIME</th>
                    <th class="text-center whitespace-nowrap">THAO TÁC</th>
                </tr>
            </thead>
            <tbody>
                @if($orders->isEmpty())
                    <tr>
                        <td colspan="8" class="text-center">Chưa có đơn hàng nào</td>
                    </tr>
                @else
                    @foreach($orders as $index => $order)
                    <tr class="intro-x">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <b class="font-medium whitespace-nowrap">{{ $order->domain }}</b>
                        </td>
                        <td>
                            <b class="font-medium whitespace-nowrap">{{ $order->ns1 }}</b>
                        </td>
                        <td>
                            <b class="font-medium whitespace-nowrap">{{ $order->ns2 }}</b>
                        </td>
                        <td>
                            <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">{{ $order->uid }}</div>
                        </td>
                        <td>
                            @if($order->status == 0)
                                <button class="btn btn-primary">Chờ Xử Lí</button>
                            @elseif($order->status == 1)
                                <button class="btn btn-success">Đang Hoạt Động</button>
                            @elseif($order->status == 2)
                                <button class="btn btn-danger">Hết Hạn</button>
                            @elseif($order->status == 3)
                                <button class="btn btn-warning">Update DNS</button>
                            @elseif($order->status == 4)
                                <button class="btn btn-danger">Từ Chối Hỗ Trợ</button>
                            @endif
                        </td>
                        <td>
                            <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">{{ $order->time }}</div>
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.approve', $order->id) }}" class="btn btn-success">Duyệt</a> 
                            <a href="{{ route('admin.orders.pending-status', $order->id) }}" class="btn btn-primary">Chờ</a> 
                            <a href="{{ route('admin.orders.reject', $order->id) }}" class="btn btn-danger">Hủy</a>
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection

