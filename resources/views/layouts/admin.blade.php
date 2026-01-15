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
        /* Prevent horizontal overflow */
        html, body {
            overflow-x: hidden;
            max-width: 100%;
        }
        
        /* Mobile First - Admin Responsive */
        @media (max-width: 575.98px) {
            .grid {
                grid-template-columns: 1fr !important;
            }
            
            .col-span-12 {
                grid-column: span 12 / span 12;
            }
            
            /* Fix sidebar on mobile */
            .side-nav {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .side-nav.mobile-menu-open {
                transform: translateX(0);
            }
            
            /* Top bar responsive */
            .top-bar {
                padding: 0.75rem 1rem;
                flex-wrap: wrap;
            }
            
            .breadcrumb {
                font-size: 0.75rem;
                margin-bottom: 0.5rem;
            }
            
            .intro-x {
                margin-right: 0.5rem;
            }
            
            /* Content area */
            .content {
                padding: 1rem 0.75rem;
            }
            
            /* Tables */
            .table {
                font-size: 0.75rem;
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .table thead,
            .table tbody,
            .table tr {
                display: block;
                width: 100%;
            }
            
            .table td,
            .table th {
                display: block;
                width: 100%;
                text-align: left;
                padding: 0.5rem;
                border-bottom: 1px solid #e5e7eb;
            }
            
            .table th {
                font-weight: 600;
                background-color: #f9fafb;
            }
            
            /* Buttons */
            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }
            
            .btn-group {
                flex-direction: column;
                width: 100%;
            }
            
            .btn-group .btn {
                margin-bottom: 0.5rem;
            }
            
            /* Forms */
            .form-control,
            .form-select {
                width: 100%;
                font-size: 0.875rem;
                padding: 0.5rem;
            }
            
            /* Cards and boxes */
            .box {
                padding: 1rem !important;
            }
            
            .report-box {
                margin-bottom: 1rem;
            }
            
            .report-box__icon {
                width: 2rem;
                height: 2rem;
            }
            
            /* Intro sections */
            .intro-y {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.75rem;
            }
            
            .intro-y h2 {
                font-size: 1rem;
                margin-bottom: 0.5rem;
            }
            
            .intro-y .btn {
                width: 100%;
                margin-top: 0.5rem;
            }
            
            /* Grid columns */
            .col-span-12.sm\:col-span-6,
            .col-span-12.xl\:col-span-3 {
                grid-column: span 12 / span 12;
            }
            
            /* Dropdown menus */
            .dropdown-menu {
                width: 100% !important;
                max-width: 100%;
                left: 0 !important;
                right: 0 !important;
            }
            
            /* Search */
            .search {
                width: 100%;
            }
            
            .search__input {
                width: 100%;
            }
            
            /* Mobile menu */
            .mobile-menu-bar {
                padding: 0.75rem 1rem;
            }
            
            /* Fix any overflow */
            * {
                max-width: 100%;
            }
            
            .content > * {
                max-width: 100%;
            }
        }
        
        @media (max-width: 767.98px) {
            /* Table responsive wrapper */
            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                -ms-overflow-style: -ms-autohiding-scrollbar;
            }
            
            .table-responsive table {
                min-width: 600px;
            }
            
            /* Card body */
            .card-body {
                padding: 1rem;
            }
            
            /* Top bar adjustments */
            .top-bar {
                flex-wrap: wrap;
            }
            
            .breadcrumb {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            
            /* Content padding */
            .content {
                padding: 1rem;
            }
            
            /* Grid adjustments */
            .grid {
                gap: 1rem;
            }
        }
        
        @media (max-width: 1024px) {
            /* Sidebar adjustments */
            .side-nav {
                width: 100%;
                max-width: 280px;
            }
            
            /* Content adjustments */
            .content {
                width: 100%;
            }
        }
        
        /* Ensure tables are scrollable */
        .overflow-auto {
            -webkit-overflow-scrolling: touch;
            overflow-x: auto;
        }
        
        /* Fix any potential horizontal scroll */
        .flex {
            flex-wrap: wrap;
        }
        
        /* Responsive images */
        img {
            max-width: 100%;
            height: auto;
        }
        
        /* Fix modals on mobile */
        @media (max-width: 575.98px) {
            .modal-dialog {
                margin: 0.5rem;
                max-width: calc(100% - 1rem);
            }
            
            .modal-content {
                padding: 1rem;
            }
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
