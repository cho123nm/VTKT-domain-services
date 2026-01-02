@extends('layouts.admin')

@section('content')
<div class="col-span-12 mt-6">
    <div class="intro-y flex items-center h-10">
        <h2 class="text-lg font-medium truncate mr-5">Chi Tiết Đơn Hàng</h2>
        <a href="{{ route('admin.orders.index') }}" class="ml-auto btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Quay Lại
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success show mb-2 mt-5" role="alert">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger show mb-2 mt-5" role="alert">{{ session('error') }}</div>
    @endif

    <div class="intro-y box p-5 mt-5">
        <div class="grid grid-cols-12 gap-6">
            <!-- Order Type Badge -->
            <div class="col-span-12">
                <h3 class="text-lg font-medium mb-4">Thông Tin Đơn Hàng</h3>
                @if($type === 'domain')
                    <span class="badge badge-primary text-lg px-4 py-2">Domain Order</span>
                @elseif($type === 'hosting')
                    <span class="badge badge-success text-lg px-4 py-2">Hosting Order</span>
                @elseif($type === 'vps')
                    <span class="badge badge-warning text-lg px-4 py-2">VPS Order</span>
                @elseif($type === 'sourcecode')
                    <span class="badge badge-info text-lg px-4 py-2">Source Code Order</span>
                @endif
            </div>

            <!-- Order Details -->
            <div class="col-span-12 md:col-span-6">
                <div class="mb-4">
                    <label class="font-medium">Order ID:</label>
                    <div class="mt-1">{{ $order->id }}</div>
                </div>

                <div class="mb-4">
                    <label class="font-medium">Mã Giao Dịch (MGD):</label>
                    <div class="mt-1">{{ $order->mgd ?? 'N/A' }}</div>
                </div>

                <div class="mb-4">
                    <label class="font-medium">Trạng Thái:</label>
                    <div class="mt-1">
                        @if($order->status == 0)
                            <span class="badge badge-primary">Chờ Xử Lý</span>
                        @elseif($order->status == 1)
                            <span class="badge badge-success">Đang Hoạt Động</span>
                        @elseif($order->status == 2)
                            <span class="badge badge-danger">Hết Hạn</span>
                        @elseif($order->status == 3)
                            <span class="badge badge-warning">Update DNS</span>
                        @elseif($order->status == 4)
                            <span class="badge badge-danger">Từ Chối</span>
                        @endif
                    </div>
                </div>

                <div class="mb-4">
                    <label class="font-medium">Thời Gian:</label>
                    <div class="mt-1">{{ $order->time ?? 'N/A' }}</div>
                </div>
            </div>

            <!-- User Details -->
            <div class="col-span-12 md:col-span-6">
                <h4 class="font-medium mb-3">Thông Tin Khách Hàng</h4>
                
                <div class="mb-4">
                    <label class="font-medium">User ID:</label>
                    <div class="mt-1">{{ $order->uid }}</div>
                </div>

                @if($order->user)
                    <div class="mb-4">
                        <label class="font-medium">Tài Khoản:</label>
                        <div class="mt-1">{{ $order->user->taikhoan }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="font-medium">Email:</label>
                        <div class="mt-1">{{ $order->user->email ?? 'N/A' }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="font-medium">Số Dư Hiện Tại:</label>
                        <div class="mt-1">{{ number_format($order->user->tien) }} VNĐ</div>
                    </div>
                @endif
            </div>

            <!-- Product/Service Details -->
            <div class="col-span-12">
                <hr class="my-4">
                <h4 class="font-medium mb-3">Chi Tiết Sản Phẩm/Dịch Vụ</h4>

                @if($type === 'domain')
                    <div class="mb-4">
                        <label class="font-medium">Tên Miền:</label>
                        <div class="mt-1 text-lg">{{ $order->domain }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="font-medium">Nameserver 1:</label>
                        <div class="mt-1">{{ $order->ns1 ?? 'N/A' }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="font-medium">Nameserver 2:</label>
                        <div class="mt-1">{{ $order->ns2 ?? 'N/A' }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="font-medium">Hạn Sử Dụng:</label>
                        <div class="mt-1">{{ $order->hsd ?? 'N/A' }}</div>
                    </div>

                    @if($order->timedns)
                        <div class="mb-4">
                            <label class="font-medium">Time DNS:</label>
                            <div class="mt-1">{{ $order->timedns }}</div>
                        </div>
                    @endif

                @elseif($type === 'hosting' && $order->hosting)
                    <div class="mb-4">
                        <label class="font-medium">Gói Hosting:</label>
                        <div class="mt-1 text-lg">{{ $order->hosting->name }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="font-medium">Mô Tả:</label>
                        <div class="mt-1">{{ $order->hosting->description ?? 'N/A' }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="font-medium">Thời Hạn:</label>
                        <div class="mt-1">{{ ucfirst($order->period) }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="font-medium">Giá:</label>
                        <div class="mt-1">
                            @if($order->period === 'month')
                                {{ number_format($order->hosting->price_month) }} VNĐ/tháng
                            @else
                                {{ number_format($order->hosting->price_year) }} VNĐ/năm
                            @endif
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="font-medium">Thông Số:</label>
                        <div class="mt-1">{!! nl2br(e($order->hosting->specs ?? 'N/A')) !!}</div>
                    </div>

                @elseif($type === 'vps' && $order->vps)
                    <div class="mb-4">
                        <label class="font-medium">Gói VPS:</label>
                        <div class="mt-1 text-lg">{{ $order->vps->name }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="font-medium">Mô Tả:</label>
                        <div class="mt-1">{{ $order->vps->description ?? 'N/A' }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="font-medium">Thời Hạn:</label>
                        <div class="mt-1">{{ ucfirst($order->period) }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="font-medium">Giá:</label>
                        <div class="mt-1">
                            @if($order->period === 'month')
                                {{ number_format($order->vps->price_month) }} VNĐ/tháng
                            @else
                                {{ number_format($order->vps->price_year) }} VNĐ/năm
                            @endif
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="font-medium">Thông Số:</label>
                        <div class="mt-1">{!! nl2br(e($order->vps->specs ?? 'N/A')) !!}</div>
                    </div>

                @elseif($type === 'sourcecode' && $order->sourceCode)
                    <div class="mb-4">
                        <label class="font-medium">Tên Source Code:</label>
                        <div class="mt-1 text-lg">{{ $order->sourceCode->name }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="font-medium">Mô Tả:</label>
                        <div class="mt-1">{{ $order->sourceCode->description ?? 'N/A' }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="font-medium">Giá:</label>
                        <div class="mt-1">{{ number_format($order->sourceCode->price) }} VNĐ</div>
                    </div>

                    @if($order->sourceCode->demo_url)
                        <div class="mb-4">
                            <label class="font-medium">Demo URL:</label>
                            <div class="mt-1">
                                <a href="{{ $order->sourceCode->demo_url }}" target="_blank" class="text-primary">
                                    {{ $order->sourceCode->demo_url }}
                                </a>
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            <!-- Actions -->
            @if($order->status == 0)
                <div class="col-span-12">
                    <hr class="my-4">
                    <h4 class="font-medium mb-3">Thao Tác</h4>
                    <div class="flex gap-3">
                        <form method="POST" action="{{ route('admin.orders.approve', ['id' => $order->id, 'type' => $type]) }}">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('Bạn có chắc muốn duyệt đơn hàng này?')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                Duyệt Đơn Hàng
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.orders.reject', ['id' => $order->id, 'type' => $type]) }}">
                            @csrf
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn từ chối đơn hàng này? Tiền sẽ được hoàn lại cho khách hàng.')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                Từ Chối & Hoàn Tiền
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
