@extends('layouts.app')

@section('content')
<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
    <div class="app-container container-xxl d-flex flex-row flex-column-fluid">
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <div class="card mb-5 mb-xl-10">
                    </div>

                    <!-- Domain Orders -->
                    <div class="card card-flush h-md-100 mb-5">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-800"> Quản Lý Tên Miền </span>
                                <span class="text-gray-400 mt-1 fw-semibold fs-6"> Managers Domain </span>
                            </h3>
                            <div class="card-toolbar">
                                <a href="{{ route('home') }}" class="btn btn-sm btn-light"> Đặt Đơn </a>
                            </div>
                        </div>
                        <div class="card-body pt-6">
                            <div class="table-responsive">
                                <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                                    <thead>
                                        <tr class="fs-7 fw-bold text-gray-400 border-bottom-0">
                                            <th class="p-0 pb-3 min-w-175px text-start">MGD</th>
                                            <th class="p-0 pb-3 min-w-100px text-start"> DOMAIN </th>
                                            <th class="p-0 pb-3 w-125px text-start pe-7"> HẠN DÙNG </th>
                                            <th class="p-0 pb-3 min-w-175px text-start"> STATUS </th>
                                            <th class="p-0 pb-3 min-w-175px text-start"> TIME </th>
                                            <th class="p-0 pb-3 min-w-175px text-start"> QUẢN LÝ </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($domainOrders as $order)
                                        <tr>
                                            <td>
                                                #{{ $order->mgd }}
                                            </td>
                                            <td>
                                                {{ $order->domain }}
                                            </td>
                                            <td>
                                                {{ $order->hsd }} Năm
                                            </td>
                                            <td>
                                                @if($order->status == 0)
                                                    <button class="btn btn-warning"> Pending </button>
                                                @elseif($order->status == 1)
                                                    <button class="btn btn-primary"> Active </button>
                                                @elseif($order->status == 2)
                                                    <button class="btn btn-danger"> Lock </button>
                                                @elseif($order->status == 3)
                                                    <button class="btn btn-warning"> Update DNS </button>
                                                @elseif($order->status == 4)
                                                    <button class="btn btn-danger"> Từ Chối Hỗ Trợ </button>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $order->time }}
                                            </td>
                                            <td class="text-start">
                                                <a href="{{ route('manager.domain', $order->id) }}" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                                    <span class="svg-icon svg-icon-5 svg-icon-gray-700">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M14.4 11H3C2.4 11 2 11.4 2 12C2 12.6 2.4 13 3 13H14.4V11Z" fill="currentColor"></path>
                                                            <path opacity="0.3" d="M14.4 20V4L21.7 11.3C22.1 11.7 22.1 12.3 21.7 12.7L14.4 20Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-gray-500">Chưa có đơn hàng nào</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Hosting Orders -->
                    <div class="card card-flush h-md-100 mb-5">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-800"> Quản Lý Hosting </span>
                                <span class="text-gray-400 mt-1 fw-semibold fs-6"> Managers Hosting </span>
                            </h3>
                            <div class="card-toolbar">
                                <a href="{{ route('hosting.index') }}" class="btn btn-sm btn-light"> Đặt Đơn </a>
                            </div>
                        </div>
                        <div class="card-body pt-6">
                            <div class="table-responsive">
                                <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                                    <thead>
                                        <tr class="fs-7 fw-bold text-gray-400 border-bottom-0">
                                            <th class="p-0 pb-3 min-w-175px text-start">MGD</th>
                                            <th class="p-0 pb-3 min-w-100px text-start"> PACKAGE </th>
                                            <th class="p-0 pb-3 w-125px text-start pe-7"> PERIOD </th>
                                            <th class="p-0 pb-3 min-w-175px text-start"> STATUS </th>
                                            <th class="p-0 pb-3 min-w-175px text-start"> TIME </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($hostingOrders as $order)
                                        <tr>
                                            <td>
                                                #{{ $order->mgd }}
                                            </td>
                                            <td>
                                                {{ $order->hosting ? $order->hosting->name : 'N/A' }}
                                            </td>
                                            <td>
                                                {{ $order->period }} Tháng
                                            </td>
                                            <td>
                                                @if($order->status == 0)
                                                    <button class="btn btn-warning"> Pending </button>
                                                @elseif($order->status == 1)
                                                    <button class="btn btn-primary"> Active </button>
                                                @elseif($order->status == 2)
                                                    <button class="btn btn-danger"> Rejected </button>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $order->time }}
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-gray-500">Chưa có đơn hàng nào</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- VPS Orders -->
                    <div class="card card-flush h-md-100 mb-5">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-800"> Quản Lý VPS </span>
                                <span class="text-gray-400 mt-1 fw-semibold fs-6"> Managers VPS </span>
                            </h3>
                            <div class="card-toolbar">
                                <a href="{{ route('vps.index') }}" class="btn btn-sm btn-light"> Đặt Đơn </a>
                            </div>
                        </div>
                        <div class="card-body pt-6">
                            <div class="table-responsive">
                                <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                                    <thead>
                                        <tr class="fs-7 fw-bold text-gray-400 border-bottom-0">
                                            <th class="p-0 pb-3 min-w-175px text-start">MGD</th>
                                            <th class="p-0 pb-3 min-w-100px text-start"> PACKAGE </th>
                                            <th class="p-0 pb-3 w-125px text-start pe-7"> PERIOD </th>
                                            <th class="p-0 pb-3 min-w-175px text-start"> STATUS </th>
                                            <th class="p-0 pb-3 min-w-175px text-start"> TIME </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($vpsOrders as $order)
                                        <tr>
                                            <td>
                                                #{{ $order->mgd }}
                                            </td>
                                            <td>
                                                {{ $order->vps ? $order->vps->name : 'N/A' }}
                                            </td>
                                            <td>
                                                {{ $order->period }} Tháng
                                            </td>
                                            <td>
                                                @if($order->status == 0)
                                                    <button class="btn btn-warning"> Pending </button>
                                                @elseif($order->status == 1)
                                                    <button class="btn btn-primary"> Active </button>
                                                @elseif($order->status == 2)
                                                    <button class="btn btn-danger"> Rejected </button>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $order->time }}
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-gray-500">Chưa có đơn hàng nào</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Source Code Orders -->
                    <div class="card card-flush h-md-100 mb-5">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-800"> Quản Lý Source Code </span>
                                <span class="text-gray-400 mt-1 fw-semibold fs-6"> Managers Source Code </span>
                            </h3>
                            <div class="card-toolbar">
                                <a href="{{ route('source-code.index') }}" class="btn btn-sm btn-light"> Đặt Đơn </a>
                            </div>
                        </div>
                        <div class="card-body pt-6">
                            <div class="table-responsive">
                                <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                                    <thead>
                                        <tr class="fs-7 fw-bold text-gray-400 border-bottom-0">
                                            <th class="p-0 pb-3 min-w-175px text-start">MGD</th>
                                            <th class="p-0 pb-3 min-w-100px text-start"> PRODUCT </th>
                                            <th class="p-0 pb-3 min-w-175px text-start"> STATUS </th>
                                            <th class="p-0 pb-3 min-w-175px text-start"> TIME </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($sourceCodeOrders as $order)
                                        <tr>
                                            <td>
                                                #{{ $order->mgd }}
                                            </td>
                                            <td>
                                                {{ $order->sourceCode ? $order->sourceCode->name : 'N/A' }}
                                            </td>
                                            <td>
                                                @if($order->status == 0)
                                                    <button class="btn btn-warning"> Pending </button>
                                                @elseif($order->status == 1)
                                                    <button class="btn btn-primary"> Active </button>
                                                @elseif($order->status == 2)
                                                    <button class="btn btn-danger"> Rejected </button>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $order->time }}
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-gray-500">Chưa có đơn hàng nào</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <br>
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
