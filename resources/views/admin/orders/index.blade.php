@extends('layouts.admin')

@section('content')
<div class="col-span-12 mt-6">
    <div class="intro-y block sm:flex items-center h-10">
        <h2 class="text-lg font-medium truncate mr-5"> Danh Sách Đơn Hàng </h2>
        <div class="flex items-center sm:ml-auto mt-3 sm:mt-0">
            <button class="btn box flex items-center text-slate-600 dark:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="file-text" data-lucide="file-text" class="lucide lucide-file-text hidden sm:block w-4 h-4 mr-2"><path d="M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg> Export to Excel
            </button>
            <button class="ml-3 btn box flex items-center text-slate-600 dark:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="file-text" data-lucide="file-text" class="lucide lucide-file-text hidden sm:block w-4 h-4 mr-2"><path d="M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg> Export to PDF
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="intro-y box p-5 mt-5">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-12 gap-4">
            <div class="col-span-12 sm:col-span-6 md:col-span-3">
                <label class="form-label">Loại Đơn Hàng</label>
                <select name="type" class="form-select">
                    <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Tất Cả</option>
                    <option value="domain" {{ $type === 'domain' ? 'selected' : '' }}>Domain</option>
                    <option value="hosting" {{ $type === 'hosting' ? 'selected' : '' }}>Hosting</option>
                    <option value="vps" {{ $type === 'vps' ? 'selected' : '' }}>VPS</option>
                    <option value="sourcecode" {{ $type === 'sourcecode' ? 'selected' : '' }}>Source Code</option>
                </select>
            </div>
            <div class="col-span-12 sm:col-span-6 md:col-span-3">
                <label class="form-label">Trạng Thái</label>
                <select name="status" class="form-select">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Tất Cả</option>
                    <option value="0" {{ $status === '0' ? 'selected' : '' }}>Chờ Xử Lý</option>
                    <option value="1" {{ $status === '1' ? 'selected' : '' }}>Đang Hoạt Động</option>
                    <option value="2" {{ $status === '2' ? 'selected' : '' }}>Hết Hạn</option>
                    <option value="3" {{ $status === '3' ? 'selected' : '' }}>Update DNS</option>
                    <option value="4" {{ $status === '4' ? 'selected' : '' }}>Từ Chối</option>
                </select>
            </div>
            <div class="col-span-12 sm:col-span-6 md:col-span-3 flex items-end">
                <button type="submit" class="btn btn-primary w-full">Lọc</button>
            </div>
            <div class="col-span-12 sm:col-span-6 md:col-span-3 flex items-end">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary w-full">Reset</a>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success show mb-2 mt-5" role="alert">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger show mb-2 mt-5" role="alert">{{ session('error') }}</div>
    @endif

    <div class="intro-y overflow-auto lg:overflow-visible mt-8 sm:mt-5">
        <table class="table table-report sm:mt-2">
            <thead>
                <tr>
                    <th class="whitespace-nowrap">ID</th>
                    <th class="whitespace-nowrap">LOẠI</th>
                    <th class="whitespace-nowrap">CHI TIẾT</th>
                    <th class="text-center whitespace-nowrap">USER</th>
                    <th class="text-center whitespace-nowrap">MGD</th>
                    <th class="text-center whitespace-nowrap">TRẠNG THÁI</th>
                    <th class="text-center whitespace-nowrap">TIME</th>
                    <th class="text-center whitespace-nowrap">THAO TÁC</th>
                </tr>
            </thead>
            <tbody>
                @php $counter = 0; @endphp
                @forelse($orders as $order)
                    @php $counter++; @endphp
                    <tr class="intro-x">
                        <td>{{ $counter }}</td>
                        <td>
                            @if($order['order_type'] === 'domain')
                                <span class="badge badge-primary">Domain</span>
                            @elseif($order['order_type'] === 'hosting')
                                <span class="badge badge-success">Hosting</span>
                            @elseif($order['order_type'] === 'vps')
                                <span class="badge badge-warning">VPS</span>
                            @elseif($order['order_type'] === 'sourcecode')
                                <span class="badge badge-info">Source Code</span>
                            @endif
                        </td>
                        <td>
                            @if($order['order_type'] === 'domain')
                                <b class="font-medium whitespace-nowrap">{{ $order['domain'] ?? 'N/A' }}</b>
                                <div class="text-slate-500 text-xs">NS1: {{ $order['ns1'] ?? 'N/A' }}</div>
                                <div class="text-slate-500 text-xs">NS2: {{ $order['ns2'] ?? 'N/A' }}</div>
                            @elseif($order['order_type'] === 'hosting')
                                <b class="font-medium whitespace-nowrap">{{ $order['hosting']['name'] ?? 'N/A' }}</b>
                                <div class="text-slate-500 text-xs">Period: {{ ucfirst($order['period'] ?? 'N/A') }}</div>
                            @elseif($order['order_type'] === 'vps')
                                <b class="font-medium whitespace-nowrap">{{ $order['vps']['name'] ?? 'N/A' }}</b>
                                <div class="text-slate-500 text-xs">Period: {{ ucfirst($order['period'] ?? 'N/A') }}</div>
                            @elseif($order['order_type'] === 'sourcecode')
                                <b class="font-medium whitespace-nowrap">{{ $order['source_code']['name'] ?? 'N/A' }}</b>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="text-slate-500 text-xs whitespace-nowrap">
                                {{ $order['user']['taikhoan'] ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="text-slate-500 text-xs whitespace-nowrap">{{ $order['mgd'] ?? 'N/A' }}</div>
                        </td>
                        <td class="text-center">
                            @if($order['status'] == 0)
                                <button class="btn btn-primary btn-sm">Chờ Xử Lý</button>
                            @elseif($order['status'] == 1)
                                <button class="btn btn-success btn-sm">Đang Hoạt Động</button>
                            @elseif($order['status'] == 2)
                                <button class="btn btn-danger btn-sm">Hết Hạn</button>
                            @elseif($order['status'] == 3)
                                <button class="btn btn-warning btn-sm">Update DNS</button>
                            @elseif($order['status'] == 4)
                                <button class="btn btn-danger btn-sm">Từ Chối</button>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="text-slate-500 text-xs whitespace-nowrap">{{ $order['time'] ?? 'N/A' }}</div>
                        </td>
                        <td class="text-center">
                            <div class="flex justify-center items-center gap-2">
                                <a href="{{ route('admin.orders.show', ['id' => $order['id'], 'type' => $order['order_type']]) }}" 
                                   class="btn btn-info btn-sm">Chi Tiết</a>
                                
                                @if($order['status'] == 0)
                                    <form method="POST" action="{{ route('admin.orders.approve', ['id' => $order['id'], 'type' => $order['order_type']]) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Bạn có chắc muốn duyệt đơn hàng này?')">Duyệt</button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('admin.orders.reject', ['id' => $order['id'], 'type' => $order['order_type']]) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn từ chối đơn hàng này? Tiền sẽ được hoàn lại cho khách hàng.')">Hủy</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">Không có đơn hàng nào</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
