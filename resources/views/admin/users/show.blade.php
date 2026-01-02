@extends('layouts.admin')

@section('title', 'Chi Tiết Thành Viên')

@section('content')
<div class="col-span-12 mt-6">
    <div class="intro-y block sm:flex items-center h-10">
        <h2 class="text-lg font-medium truncate mr-5">Chi Tiết Thành Viên: {{ $user->taikhoan }}</h2>
        <div class="flex items-center sm:ml-auto mt-3 sm:mt-0">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary shadow-md mr-2">Chỉnh Sửa</a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Quay Lại</a>
        </div>
    </div>

    <!-- User Information -->
    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto">Thông Tin Tài Khoản</h2>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 sm:col-span-6">
                    <label class="form-label font-medium">UID:</label>
                    <p class="text-slate-600">{{ $user->id }}</p>
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="form-label font-medium">Tài Khoản:</label>
                    <p class="text-slate-600">{{ $user->taikhoan }}</p>
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="form-label font-medium">Email:</label>
                    <p class="text-slate-600">{{ $user->email ?? 'Chưa cập nhật' }}</p>
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="form-label font-medium">Số Dư:</label>
                    <p class="text-slate-600 text-success font-medium">{{ number_format($user->tien) }}đ</p>
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="form-label font-medium">Chức Vụ:</label>
                    <p class="text-slate-600">
                        @if($user->isAdmin())
                            <span class="badge badge-primary">Admin</span>
                        @else
                            <span class="badge badge-secondary">Thành Viên</span>
                        @endif
                    </p>
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="form-label font-medium">Ngày Đăng Ký:</label>
                    <p class="text-slate-600">{{ $user->time }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Domain Orders -->
    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto">Đơn Hàng Domain ({{ $user->domainOrders->count() }})</h2>
        </div>
        <div class="p-5">
            @if($user->domainOrders->isEmpty())
                <p class="text-center text-slate-500">Chưa có đơn hàng domain nào</p>
            @else
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead class="table-dark">
                            <tr>
                                <th>MGD</th>
                                <th>Domain</th>
                                <th>NS1</th>
                                <th>NS2</th>
                                <th>HSD</th>
                                <th>Trạng Thái</th>
                                <th>Thời Gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->domainOrders as $order)
                            <tr>
                                <td>{{ $order->mgd }}</td>
                                <td>{{ $order->domain }}</td>
                                <td>{{ $order->ns1 ?? 'N/A' }}</td>
                                <td>{{ $order->ns2 ?? 'N/A' }}</td>
                                <td>{{ $order->hsd }}</td>
                                <td>
                                    @if($order->status == 0)
                                        <span class="badge badge-warning">Chờ Duyệt</span>
                                    @elseif($order->status == 1)
                                        <span class="badge badge-success">Đã Duyệt</span>
                                    @elseif($order->status == 2)
                                        <span class="badge badge-info">Đang Xử Lý</span>
                                    @else
                                        <span class="badge badge-danger">Từ Chối</span>
                                    @endif
                                </td>
                                <td>{{ $order->time }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Hosting Orders -->
    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto">Đơn Hàng Hosting ({{ $user->hostingOrders->count() }})</h2>
        </div>
        <div class="p-5">
            @if($user->hostingOrders->isEmpty())
                <p class="text-center text-slate-500">Chưa có đơn hàng hosting nào</p>
            @else
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead class="table-dark">
                            <tr>
                                <th>MGD</th>
                                <th>Gói Hosting</th>
                                <th>Thời Hạn</th>
                                <th>Trạng Thái</th>
                                <th>Thời Gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->hostingOrders as $order)
                            <tr>
                                <td>{{ $order->mgd }}</td>
                                <td>{{ $order->hosting->name ?? 'N/A' }}</td>
                                <td>{{ $order->period }} tháng</td>
                                <td>
                                    @if($order->status == 0)
                                        <span class="badge badge-warning">Chờ Duyệt</span>
                                    @elseif($order->status == 1)
                                        <span class="badge badge-success">Đã Duyệt</span>
                                    @else
                                        <span class="badge badge-danger">Từ Chối</span>
                                    @endif
                                </td>
                                <td>{{ $order->time }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- VPS Orders -->
    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto">Đơn Hàng VPS ({{ $user->vpsOrders->count() }})</h2>
        </div>
        <div class="p-5">
            @if($user->vpsOrders->isEmpty())
                <p class="text-center text-slate-500">Chưa có đơn hàng VPS nào</p>
            @else
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead class="table-dark">
                            <tr>
                                <th>MGD</th>
                                <th>Gói VPS</th>
                                <th>Thời Hạn</th>
                                <th>Trạng Thái</th>
                                <th>Thời Gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->vpsOrders as $order)
                            <tr>
                                <td>{{ $order->mgd }}</td>
                                <td>{{ $order->vps->name ?? 'N/A' }}</td>
                                <td>{{ $order->period }} tháng</td>
                                <td>
                                    @if($order->status == 0)
                                        <span class="badge badge-warning">Chờ Duyệt</span>
                                    @elseif($order->status == 1)
                                        <span class="badge badge-success">Đã Duyệt</span>
                                    @else
                                        <span class="badge badge-danger">Từ Chối</span>
                                    @endif
                                </td>
                                <td>{{ $order->time }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Source Code Orders -->
    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto">Đơn Hàng Source Code ({{ $user->sourceCodeOrders->count() }})</h2>
        </div>
        <div class="p-5">
            @if($user->sourceCodeOrders->isEmpty())
                <p class="text-center text-slate-500">Chưa có đơn hàng source code nào</p>
            @else
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead class="table-dark">
                            <tr>
                                <th>MGD</th>
                                <th>Sản Phẩm</th>
                                <th>Trạng Thái</th>
                                <th>Thời Gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->sourceCodeOrders as $order)
                            <tr>
                                <td>{{ $order->mgd }}</td>
                                <td>{{ $order->sourceCode->name ?? 'N/A' }}</td>
                                <td>
                                    @if($order->status == 0)
                                        <span class="badge badge-warning">Chờ Duyệt</span>
                                    @elseif($order->status == 1)
                                        <span class="badge badge-success">Đã Duyệt</span>
                                    @else
                                        <span class="badge badge-danger">Từ Chối</span>
                                    @endif
                                </td>
                                <td>{{ $order->time }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
