@extends('layouts.admin')

@section('title', 'Dashboard - ADMIN CPANEL')

@section('breadcrumb', 'Dashboard')

@section('content')
<div class="grid grid-cols-12 gap-6">
    <!-- BEGIN: General Report -->
    <div class="col-span-12 mt-8">
        <div class="intro-y flex items-center h-10">
            <h2 class="text-lg font-medium truncate mr-5">Thống Kê Tổng Quan</h2>
            <a href="" class="ml-auto flex items-center text-primary">
                <i data-lucide="refresh-ccw" class="w-4 h-4 mr-3"></i> Reload Data
            </a>
        </div>
        <div class="grid grid-cols-12 gap-6 mt-5">

            <!-- Đơn hàng chờ -->
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <i data-lucide="shopping-cart" class="report-box__icon text-primary"></i>
                            <div class="ml-auto">
                                <div class="report-box__indicator bg-success tooltip cursor-pointer">
                                   Đơn Hàng <i data-lucide="chevron-up" class="w-4 h-4 ml-0.5"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-3xl font-medium leading-8 mt-6">{{ number_format($donhang) }}</div>
                        <div class="text-base text-slate-500 mt-1">Đơn Hàng Chờ Xử Lí</div>
                    </div>
                </div>
            </div>

            <!-- Ticket / Update -->
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <i data-lucide="credit-card" class="report-box__icon text-pending"></i>
                            <div class="ml-auto">
                                <div class="report-box__indicator bg-danger tooltip cursor-pointer">
                                   Ticket <i data-lucide="chevron-down" class="w-4 h-4 ml-0.5"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-3xl font-medium leading-8 mt-6">{{ number_format($update) }}</div>
                        <div class="text-base text-slate-500 mt-1">Cập Nhật DNS</div>
                    </div>
                </div>
            </div>

            <!-- Dịch vụ đang chạy -->
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <i data-lucide="monitor" class="report-box__icon text-warning"></i>
                            <div class="ml-auto">
                                <div class="report-box__indicator bg-success tooltip cursor-pointer">
                                    12% <i data-lucide="chevron-up" class="w-4 h-4 ml-0.5"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-3xl font-medium leading-8 mt-6">{{ number_format($donhoanthanh) }}</div>
                        <div class="text-base text-slate-500 mt-1">Dịch Vụ Đang Chạy</div>
                    </div>
                </div>
            </div>

            <!-- Thành viên -->
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <i data-lucide="user" class="report-box__icon text-success"></i>
                            <div class="ml-auto">
                                <div class="report-box__indicator bg-success tooltip cursor-pointer">
                                 Thành Viên <i data-lucide="chevron-up" class="w-4 h-4 ml-0.5"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-3xl font-medium leading-8 mt-6">{{ number_format($thanhvien) }}</div>
                        <div class="text-base text-slate-500 mt-1">Thành Viên</div>
                    </div>
                </div>
            </div>

            <!-- Doanh thu hôm nay -->
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <i data-lucide="briefcase" class="report-box__icon text-success"></i>
                            <div class="ml-auto">
                                <div class="report-box__indicator bg-success tooltip cursor-pointer">
                                   Doanh Thu <i data-lucide="chevron-up" class="w-4 h-4 ml-0.5"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-3xl font-medium leading-8 mt-6">{{ number_format($doanhthuhomnay) }}đ</div>
                        <div class="text-base text-slate-500 mt-1">Doanh Thu Hôm Nay</div>
                    </div>
                </div>
            </div>

            <!-- Doanh thu hôm qua -->
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <i data-lucide="briefcase" class="report-box__icon text-success"></i>
                            <div class="ml-auto">
                                <div class="report-box__indicator bg-success tooltip cursor-pointer">
                                   Doanh Thu <i data-lucide="chevron-up" class="w-4 h-4 ml-0.5"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-3xl font-medium leading-8 mt-6">{{ number_format($doanhthuhqua) }}đ</div>
                        <div class="text-base text-slate-500 mt-1">Doanh Thu Hôm Qua</div>
                    </div>
                </div>
            </div>

            <!-- Doanh thu tháng này -->
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <i data-lucide="briefcase" class="report-box__icon text-success"></i>
                            <div class="ml-auto">
                                <div class="report-box__indicator bg-success tooltip cursor-pointer">
                                   Doanh Thu <i data-lucide="chevron-up" class="w-4 h-4 ml-0.5"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-3xl font-medium leading-8 mt-6">{{ number_format($doanhthuthang) }}đ</div>
                        <div class="text-base text-slate-500 mt-1">Doanh Thu Tháng Này</div>
                    </div>
                </div>
            </div>

            <!-- Tổng doanh thu -->
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex">
                            <i data-lucide="briefcase" class="report-box__icon text-success"></i>
                            <div class="ml-auto">
                                <div class="report-box__indicator bg-success tooltip cursor-pointer">
                                   Doanh Thu <i data-lucide="chevron-up" class="w-4 h-4 ml-0.5"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-3xl font-medium leading-8 mt-6">{{ number_format($tongdoanhthu) }}đ</div>
                        <div class="text-base text-slate-500 mt-1">Tổng Doanh Thu</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- END: General Report -->
</div>
@endsection
