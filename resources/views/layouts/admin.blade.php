<!DOCTYPE html>
<html lang="en" class="theme-light">
<!-- BEGIN: Head -->
<head>
    <meta charset="utf-8">
    <link href="{{ asset('assets/media/logos/favicon.ico') }}" rel="shortcut icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ADMIN CPANEL - THANHVU.NET V4">
    <meta name="keywords" content="ADMIN CPANEL - THANHVU.NET V4">
    <meta name="author" content="ADMIN CPANEL - THANHVU.NET V4">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ADMIN CPANEL - THANHVU.NET V4')</title>

    <!-- BEGIN: CSS Assets-->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>  
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="stylesheet" href="{{ asset('Adminstators/dist/css/app.css') }}" />
    <!-- END: CSS Assets-->
    
    <!-- Responsive CSS for Admin -->
    <style>
        /* Mobile First - Admin Responsive */
        @media (max-width: 575.98px) {
            .grid {
                grid-template-columns: 1fr !important;
            }
            
            .col-span-12 {
                grid-column: span 12 / span 12;
            }
            
            .table {
                font-size: 0.75rem;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            
            .intro-y {
                flex-direction: column;
                align-items: flex-start !important;
            }
            
            .intro-y .btn {
                width: 100%;
                margin-top: 0.5rem;
            }
        }
        
        @media (max-width: 767.98px) {
            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .card-body {
                padding: 1rem;
            }
        }
        
        /* Ensure tables are scrollable */
        .overflow-auto {
            -webkit-overflow-scrolling: touch;
        }
    </style>
    
    @stack('styles')
</head>
<!-- END: Head -->

<body class="py-5">
    <!-- BEGIN: Mobile Menu -->
    <div class="mobile-menu md:hidden">
        <div class="mobile-menu-bar">
            <a href="{{ route('admin.dashboard') }}" class="flex mr-auto">
                <img alt="Admin Logo" class="w-6" src="{{ asset('assets/media/logos/favicon.ico') }}">
            </a>
            <a href="javascript:;" class="mobile-menu-toggler">
                <i data-lucide="bar-chart-2" class="w-8 h-8 text-white transform -rotate-90"></i>
            </a>
        </div>
        <div class="scrollable">
            <a href="javascript:;" class="mobile-menu-toggler">
                <i data-lucide="x-circle" class="w-8 h-8 text-white transform -rotate-90"></i>
            </a>
            <ul class="scrollable__content py-2">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="menu {{ request()->routeIs('admin.dashboard') ? 'menu--active' : '' }}">
                        <div class="menu__icon">
                            <i data-lucide="home"></i>
                        </div>
                        <div class="menu__title">
                            Trang Chủ
                        </div>
                    </a>
                </li>
                
                <li>
                    <a href="javascript:;" class="menu">
                        <div class="menu__icon">
                            <i data-lucide="box"></i>
                        </div>
                        <div class="menu__title">
                            Quản Lý Sản Phẩm
                            <i data-lucide="chevron-down" class="menu__sub-icon"></i>
                        </div>
                    </a>
                    <ul class="">
                        <li>
                            <a href="{{ route('admin.domain.create') }}" class="menu">
                                <div class="menu__icon">
                                    <i data-lucide="activity"></i>
                                </div>
                                <div class="menu__title">
                                    Thêm Domain
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.domain.index') }}" class="menu">
                                <div class="menu__icon">
                                    <i data-lucide="activity"></i>
                                </div>
                                <div class="menu__title">
                                    Danh Sách Domain
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.sourcecode.create') }}" class="menu">
                                <div class="menu__icon">
                                    <i data-lucide="code"></i>
                                </div>
                                <div class="menu__title">
                                    Thêm Source Code
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.sourcecode.index') }}" class="menu">
                                <div class="menu__icon">
                                    <i data-lucide="code"></i>
                                </div>
                                <div class="menu__title">
                                    Danh Sách Source Code
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.hosting.create') }}" class="menu">
                                <div class="menu__icon">
                                    <i data-lucide="server"></i>
                                </div>
                                <div class="menu__title">
                                    Thêm Hosting
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.hosting.index') }}" class="menu">
                                <div class="menu__icon">
                                    <i data-lucide="server"></i>
                                </div>
                                <div class="menu__title">
                                    Danh Sách Hosting
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.vps.create') }}" class="menu">
                                <div class="menu__icon">
                                    <i data-lucide="hard-drive"></i>
                                </div>
                                <div class="menu__title">
                                    Thêm VPS
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.vps.index') }}" class="menu">
                                <div class="menu__icon">
                                    <i data-lucide="hard-drive"></i>
                                </div>
                                <div class="menu__title">
                                    Danh Sách VPS
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.orders.index') }}" class="menu">
                                <div class="menu__icon">
                                    <i data-lucide="shopping-cart"></i>
                                </div>
                                <div class="menu__title">
                                    Quản Lý Đơn Hàng
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li>
                    <a href="{{ route('admin.dns.index') }}" class="menu">
                        <div class="menu__icon">
                            <i data-lucide="inbox"></i>
                        </div>
                        <div class="menu__title">
                            Cập Nhật DNS 
                        </div>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('admin.cards.index') }}" class="menu">
                        <div class="menu__icon">
                            <i data-lucide="credit-card"></i>
                        </div>
                        <div class="menu__title">
                            Đơn Nạp Ví 
                        </div>
                    </a>
                </li>
                
                <li class="menu__devider my-6"></li>
                <li>
                    <a href="javascript:;" class="menu">
                        <div class="menu__icon">
                            <i data-lucide="edit"></i>
                        </div>
                        <div class="menu__title">
                            Cài Đặt Chung
                            <i data-lucide="chevron-down" class="menu__sub-icon"></i>
                        </div>
                    </a>
                    <ul class="">
                        <li>
                            <a href="{{ route('admin.settings.index') }}" class="menu">
                                <div class="menu__icon">
                                    <i data-lucide="settings"></i>
                                </div>
                                <div class="menu__title">
                                    Tất Cả Cài Đặt
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li>
                    <a href="{{ route('admin.feedback.index') }}" class="menu">
                        <div class="menu__icon">
                            <i data-lucide="message-square"></i>
                        </div>
                        <div class="menu__title">
                            Quản Lý Phản Hồi
                        </div>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('admin.users.index') }}" class="menu">
                        <div class="menu__icon">
                            <i data-lucide="users"></i>
                        </div>
                        <div class="menu__title">
                            Quản Lí Thành Viên
                        </div>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('admin.cards.index') }}" class="menu">
                        <div class="menu__icon">
                            <i data-lucide="trello"></i>
                        </div>
                        <div class="menu__title">
                            Đơn Gạch Thẻ
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <!-- END: Mobile Menu -->

    <div class="flex mt-[4.7rem] md:mt-0">
        <!-- BEGIN: Side Menu -->
        <nav class="side-nav">
            <a href="{{ route('admin.dashboard') }}" class="intro-x flex items-center pl-5 pt-4">
                <img alt="Midone - HTML Admin Template" class="w-6" src="{{ asset('images/logo.jpg') }}">
                <span class="hidden xl:block text-white text-lg ml-3">
                    THANHVU.NET V4
                </span>
            </a>
            <div class="side-nav__devider my-6"></div>
            <ul>
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="side-menu {{ request()->routeIs('admin.dashboard') ? 'side-menu--active' : '' }}">
                        <div class="side-menu__icon">
                            <i data-lucide="home"></i>
                        </div>
                        <div class="side-menu__title">
                            Trang Chủ
                        </div>
                    </a>
                </li>
                
                <li>
                    <a href="javascript:;" class="side-menu">
                        <div class="side-menu__icon">
                            <i data-lucide="box"></i>
                        </div>
                        <div class="side-menu__title">
                            Quản Lý Sản Phẩm
                            <div class="side-menu__sub-icon">
                                <i data-lucide="chevron-down"></i>
                            </div>
                        </div>
                    </a>
                    <ul class="">
                        <li>
                            <a href="{{ route('admin.domain.create') }}" class="side-menu">
                                <div class="side-menu__icon">
                                    <i data-lucide="activity"></i>
                                </div>
                                <div class="side-menu__title">
                                    Thêm Domain
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.domain.index') }}" class="side-menu">
                                <div class="side-menu__icon">
                                    <i data-lucide="activity"></i>
                                </div>
                                <div class="side-menu__title">
                                    Danh Sách Domain
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.sourcecode.create') }}" class="side-menu">
                                <div class="side-menu__icon">
                                    <i data-lucide="code"></i>
                                </div>
                                <div class="side-menu__title">
                                    Thêm Source Code
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.sourcecode.index') }}" class="side-menu">
                                <div class="side-menu__icon">
                                    <i data-lucide="code"></i>
                                </div>
                                <div class="side-menu__title">
                                    Danh Sách Source Code
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.hosting.create') }}" class="side-menu">
                                <div class="side-menu__icon">
                                    <i data-lucide="server"></i>
                                </div>
                                <div class="side-menu__title">
                                    Thêm Hosting
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.hosting.index') }}" class="side-menu">
                                <div class="side-menu__icon">
                                    <i data-lucide="server"></i>
                                </div>
                                <div class="side-menu__title">
                                    Danh Sách Hosting
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.vps.create') }}" class="side-menu">
                                <div class="side-menu__icon">
                                    <i data-lucide="hard-drive"></i>
                                </div>
                                <div class="side-menu__title">
                                    Thêm VPS
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.vps.index') }}" class="side-menu">
                                <div class="side-menu__icon">
                                    <i data-lucide="hard-drive"></i>
                                </div>
                                <div class="side-menu__title">
                                    Danh Sách VPS
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.orders.index') }}" class="side-menu">
                                <div class="side-menu__icon">
                                    <i data-lucide="activity"></i>
                                </div>
                                <div class="side-menu__title">
                                    Đơn Chờ Xử Lí
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li>
                    <a href="{{ route('admin.dns.index') }}" class="side-menu">
                        <div class="side-menu__icon">
                            <i data-lucide="shopping-bag"></i>
                        </div>
                        <div class="side-menu__title">
                            Cập Nhật DNS 
                        </div>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('admin.cards.index') }}" class="side-menu">
                        <div class="side-menu__icon">
                            <i data-lucide="credit-card"></i>
                        </div>
                        <div class="side-menu__title">
                            Đơn Nạp Ví
                        </div>
                    </a>
                </li>
                
                <li class="side-nav__devider my-6"></li>
                <li>
                    <a href="javascript:;" class="side-menu">
                        <div class="side-menu__icon">
                            <i data-lucide="edit"></i>
                        </div>
                        <div class="side-menu__title">
                            Cài Đặt Chung
                            <div class="side-menu__sub-icon">
                                <i data-lucide="chevron-down"></i>
                            </div>
                        </div>
                    </a>
                    <ul class="">
                        <li>
                            <a href="{{ route('admin.settings.index') }}" class="side-menu">
                                <div class="side-menu__icon">
                                    <i data-lucide="settings"></i>
                                </div>
                                <div class="side-menu__title">
                                    Tất Cả Cài Đặt
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li>
                    <a href="{{ route('admin.feedback.index') }}" class="side-menu">
                        <div class="side-menu__icon">
                            <i data-lucide="message-square"></i>
                        </div>
                        <div class="side-menu__title">
                            Quản Lý Phản Hồi
                        </div>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('admin.users.index') }}" class="side-menu">
                        <div class="side-menu__icon">
                            <i data-lucide="users"></i>
                        </div>
                        <div class="side-menu__title">
                            Quản Lí Thành Viên
                        </div>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('admin.cards.index') }}" class="side-menu">
                        <div class="side-menu__icon">
                            <i data-lucide="trello"></i>
                        </div>
                        <div class="side-menu__title">
                            Đơn Gạch Thẻ
                        </div>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- END: Side Menu -->
        
        <!-- BEGIN: Content -->
        <div class="content">
            <!-- BEGIN: Top Bar -->
            <div class="top-bar">
                <!-- BEGIN: Breadcrumb -->
                <nav aria-label="breadcrumb" class="-intro-x mr-auto hidden sm:flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Admin</a></li>
                        <li class="breadcrumb-item active" aria-current="page">@yield('breadcrumb', 'THANHVU.NET V4')</li>
                    </ol>
                </nav>
                <!-- END: Breadcrumb -->
                
                <!-- BEGIN: Search -->
                <div class="intro-x relative mr-3 sm:mr-6">
                    <div class="search hidden sm:block">
                        <input type="text" class="search__input form-control border-transparent" placeholder="Search...">
                        <i data-lucide="search" class="search__icon dark:text-slate-500"></i>
                    </div>
                    <a class="notification sm:hidden" href="#">
                        <i data-lucide="search" class="notification__icon dark:text-slate-500"></i>
                    </a>
                </div>
                <!-- END: Search -->
                
                <!-- BEGIN: Avatar Dropdown -->
                <div class="intro-x dropdown w-8 h-8">
                    <div class="dropdown-toggle w-8 h-8 rounded-full overflow-hidden shadow-lg image-fit zoom-in" role="button" aria-expanded="false" data-tw-toggle="dropdown">
                        <img alt="Admin Avatar" src="{{ asset('images/thanhvu.jpg') }}">
                    </div>
                    <div class="dropdown-menu w-56">
                        <ul class="dropdown-content bg-primary text-white">
                            <li class="p-2">
                                <div class="font-medium">Đàm Thanh Vũ</div>
                                <div class="text-xs text-white/70 mt-0.5 dark:text-slate-500">Developer & Designer</div>
                            </li>
                            <li>
                                <a href="{{ url('/') }}" target="_blank" class="dropdown-item hover:bg-white/5">
                                    <i data-lucide="toggle-right" class="w-4 h-4 mr-2"></i> Về Trang Web
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.auth.logout') }}" class="dropdown-item hover:bg-white/5">
                                    <i data-lucide="log-out" class="w-4 h-4 mr-2"></i> Đăng Xuất
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- END: Avatar Dropdown -->
            </div>
            <!-- END: Top Bar -->

            <!-- BEGIN: Page Content -->
            @yield('content')
            <!-- END: Page Content -->
        </div>
        <!-- END: Content -->
    </div>

    <!-- BEGIN: JS Assets-->
    <script src="{{ asset('Adminstators/dist/js/app.js') }}"></script>
    <!-- END: JS Assets-->

    @stack('scripts')
</body>
</html>
