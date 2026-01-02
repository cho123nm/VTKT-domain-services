<div id="kt_app_header" class="app-header">
    <div class="app-header-primary">
        <div class="app-container container-xxl d-flex align-items-stretch justify-content-between">
            <div class="d-flex flex-stack flex-grow-1 flex-lg-grow-0">
                <!-- Logo wrapper -->
                <div class="d-flex align-items-center me-7">
                    <button class="d-lg-none btn btn-icon btn-color-white bg-hover-white bg-hover-opacity-10 ms-n2 me-2" id="kt_header_secondary_toggle">
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="currentColor" />
                                <path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="currentColor" />
                            </svg>
                        </span>
                    </button>
                    <a href="{{ route('home') }}" class="d-flex align-items-center">
                        <img alt="Logo" src="/assets/media/logos/demo22.png" class="h-25px h-lg-30px" />
                    </a>
                </div>
                
                <!-- Search -->
                <div id="kt_header_search" class="header-search d-flex align-items-center w-lg-300px me-2" data-kt-search-keypress="true" data-kt-search-min-length="2" data-kt-search-enter="enter" data-kt-search-layout="menu" data-kt-search-responsive="lg" data-kt-menu-trigger="auto" data-kt-menu-permanent="true" data-kt-menu-placement="bottom-start">
                    <div data-kt-search-element="toggle" class="d-flex d-lg-none align-items-center">
                        <div class="btn btn-icon btn-color-white bg-hover-white bg-hover-opacity-10">
                            <span class="svg-icon svg-icon-1">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor" />
                                </svg>
                            </span>
                        </div>
                    </div>
                    <form data-kt-search-element="form" class="d-none d-lg-block w-100 position-relative mb-5 mb-lg-0" autocomplete="off">
                        <input type="hidden" />
                        <span class="svg-icon svg-icon-2 svg-icon-lg-3 svg-icon-gray-800 position-absolute top-50 translate-middle-y ms-5">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor" />
                            </svg>
                        </span>
                        <input type="text" class="search-input form-control form-control-solid ps-13" name="search" value="" placeholder="Search..." data-kt-search-element="input" />
                        <span class="position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-5" data-kt-search-element="spinner">
                            <span class="spinner-border h-15px w-15px align-middle text-gray-400"></span>
                        </span>
                        <span class="btn btn-flush btn-active-color-primary position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-4" data-kt-search-element="clear">
                            <span class="svg-icon svg-icon-2 svg-icon-lg-1 me-0">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                </svg>
                            </span>
                        </span>
                    </form>
                    <div data-kt-search-element="content" class="menu menu-sub menu-sub-dropdown w-300px w-md-350px py-7 px-7 overflow-hidden"></div>
                </div>
            </div>
            
            <!-- Navbar -->
            <div class="app-navbar gap-2">
                <div class="app-navbar-item">
                    <div class="btn btn-icon btn-color-white btn-custom bg-hover-white bg-hover-opacity-10" id="kt_activities_toggle">
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="8" y="9" width="3" height="10" rx="1.5" fill="currentColor" />
                                <rect opacity="0.5" x="13" y="5" width="3" height="14" rx="1.5" fill="currentColor" />
                                <rect x="18" y="11" width="3" height="8" rx="1.5" fill="currentColor" />
                                <rect x="3" y="13" width="3" height="6" rx="1.5" fill="currentColor" />
                            </svg>
                        </span>
                    </div>
                </div>

                @if(session()->has('users'))
                <div class="app-navbar-item">
                    <a href="{{ route('messages.index') }}" class="btn btn-icon btn-color-white bg-hover-white bg-hover-opacity-10 lh-1 position-relative">
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z" fill="currentColor" />
                                <rect x="6" y="12" width="7" height="2" rx="1" fill="currentColor" />
                                <rect x="6" y="7" width="12" height="2" rx="1" fill="currentColor" />
                            </svg>
                        </span>
                        <span class="bullet bullet-dot bg-white h-6px w-6px position-absolute translate-middle top-0 start-50 animation-blink"></span>
                    </a>
                </div>
                @endif
                
                <!-- Theme mode -->
                <div class="app-navbar-item">
                    <a href="#" class="btn btn-icon btn-color-white bg-hover-white bg-hover-opacity-10" data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <span class="svg-icon theme-light-show svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11.9905 5.62598C10.7293 5.62574 9.49646 5.9995 8.44775 6.69997C7.39903 7.40045 6.58159 8.39619 6.09881 9.56126C5.61603 10.7263 5.48958 12.0084 5.73547 13.2453C5.98135 14.4823 6.58852 15.6185 7.48019 16.5104C8.37186 17.4022 9.50798 18.0096 10.7449 18.2557C11.9818 18.5019 13.2639 18.3757 14.429 17.8931C15.5942 17.4106 16.5901 16.5933 17.2908 15.5448C17.9915 14.4962 18.3655 13.2634 18.3655 12.0023C18.3637 10.3119 17.6916 8.69129 16.4964 7.49593C15.3013 6.30056 13.6808 5.62806 11.9905 5.62598Z" fill="currentColor" />
                                <path d="M22.1258 10.8771H20.627C20.3286 10.8771 20.0424 10.9956 19.8314 11.2066C19.6204 11.4176 19.5018 11.7038 19.5018 12.0023C19.5018 12.3007 19.6204 12.5869 19.8314 12.7979C20.0424 13.0089 20.3286 13.1274 20.627 13.1274H22.1258C22.4242 13.1274 22.7104 13.0089 22.9214 12.7979C23.1324 12.5869 23.2509 12.3007 23.2509 12.0023C23.2509 11.7038 23.1324 11.4176 22.9214 11.2066C22.7104 10.9956 22.4242 10.8771 22.1258 10.8771Z" fill="currentColor" />
                            </svg>
                        </span>
                        <span class="svg-icon theme-dark-show svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.0647 5.43757C19.3421 5.43757 19.567 5.21271 19.567 4.93534C19.567 4.65796 19.3421 4.43311 19.0647 4.43311C18.7874 4.43311 18.5625 4.65796 18.5625 4.93534C18.5625 5.21271 18.7874 5.43757 19.0647 5.43757Z" fill="currentColor" />
                                <path d="M20.0692 9.48884C20.3466 9.48884 20.5714 9.26398 20.5714 8.98661C20.5714 8.70923 20.3466 8.48438 20.0692 8.48438C19.7918 8.48438 19.567 8.70923 19.567 8.98661C19.567 9.26398 19.7918 9.48884 20.0692 9.48884Z" fill="currentColor" />
                                <path d="M12.0335 20.5714C15.6943 20.5714 18.9426 18.2053 20.1168 14.7338C20.1884 14.5225 20.1114 14.289 19.9284 14.161C19.746 14.034 19.5003 14.0418 19.3257 14.1821C18.2432 15.0546 16.9371 15.5156 15.5491 15.5156C12.2257 15.5156 9.48884 12.8122 9.48884 9.48886C9.48884 7.41079 10.5773 5.47137 12.3449 4.35752C12.5342 4.23832 12.6 4.00733 12.5377 3.79251C12.4759 3.57768 12.2571 3.42859 12.0335 3.42859C7.32556 3.42859 3.42857 7.29209 3.42857 12C3.42857 16.7079 7.32556 20.5714 12.0335 20.5714Z" fill="currentColor" />
                            </svg>
                        </span>
                    </a>
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-muted menu-active-bg menu-state-color fw-semibold py-4 fs-base w-175px" data-kt-menu="true" data-kt-element="theme-mode-menu">
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                                <span class="menu-title"> Sáng </span>
                            </a>
                        </div>
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                                <span class="menu-title"> Tối </span>
                            </a>
                        </div>
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
                                <span class="menu-title"> Hệ Thống </span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User menu -->
                <div class="app-navbar-item" id="kt_header_user_menu_toggle">
                    <div class="btn btn-flex align-items-center bg-hover-white bg-hover-opacity-10 py-2 px-2 px-md-3" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <div class="d-none d-md-flex flex-column align-items-end justify-content-center me-2 me-md-4">
                            <span class="text-white fs-8 fw-bold lh-1 mb-1"> {{ $username }} </span>
                            <span class="text-white fs-8 opacity-75 fw-semibold lh-1"> Số Dư: {{ number_format($sodu) }}đ </span>
                        </div>
                        <div class="symbol symbol-30px symbol-md-40px">
                            <img src="https://ui-avatars.com/api/?name={{ $username }}" alt="image" />
                        </div>
                    </div>
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex align-items-center px-3">
                                <div class="symbol symbol-50px me-5">
                                    <img alt="Logo" src="https://ui-avatars.com/api/?name={{ $username }}" />
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fw-bold d-flex align-items-center fs-5"> {{ $username }}</div>
                                    <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2"> Số Dư: {{ number_format($sodu) }}đ  </span>
                                    <a href="#" class="fw-semibold text-muted text-hover-primary fs-7"> {{ $email }} </a>
                                </div>
                            </div>
                        </div>
                        <div class="separator my-2"></div>
                        
                        @if(session()->has('users'))
                            <div class="menu-item px-5">
                                <a href="{{ route('profile') }}" class="menu-link px-5"> Trang Cá Nhân </a>
                            </div>
                            <div class="menu-item px-5">
                                <a href="{{ route('manager.index') }}" class="menu-link px-5">
                                    <span class="menu-text"> Đơn Chờ </span>
                                    <span class="menu-badge">
                                        <span class="badge badge-light-danger badge-circle fw-bold fs-7">3</span>
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item px-5">
                                <a href="{{ route('recharge') }}" class="menu-link px-5"> Nạp Tiền </a>
                            </div>
                            <div class="menu-item px-5">
                                <a href="{{ route('messages.index') }}" class="menu-link px-5">
                                    <span class="menu-text"> Tin Nhắn </span>
                                </a>
                            </div>
                            <div class="menu-item px-5">
                                <a href="{{ route('feedback.index') }}" class="menu-link px-5"> Gửi Phản Hồi </a>
                            </div>
                            <div class="separator my-2"></div>
                            <div class="menu-item px-5">
                                <a href="{{ route('logout') }}" class="menu-link px-5"> Đăng Xuất </a>
                            </div>
                        @else
                            <div class="separator my-2"></div>
                            <div class="menu-item px-5 my-1">
                                <a href="{{ route('login') }}" class="menu-link px-5"> Đăng Nhập </a>
                            </div>
                            <div class="menu-item px-5">
                                <a href="{{ route('register') }}" class="menu-link px-5"> Đăng Ký </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary header (navigation menu) -->
    <div class="app-header-secondary app-header-mobile-drawer" data-kt-drawer="true" data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_header_secondary_toggle" data-kt-sticky="true" data-kt-sticky-name="app-header-secondary-sticky" data-kt-sticky-offset="{default: 'false', lg: '300px'}" data-kt-swapper="true" data-kt-swapper-mode="append" data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_app_header'}">
        <div class="app-container container-xxl app-container-fit-mobile d-flex align-items-stretch">
            <div class="header-menu d-flex align-items-stretch w-100">
                <div class="menu menu-lg-rounded menu-column menu-lg-row menu-state-bg menu-state-primary menu-title-gray-700 menu-arrow-gray-400 menu-bullet-gray-400 fw-semibold my-5 my-lg-0 align-items-stretch" id="#kt_header_menu" data-kt-menu="true">
                    <div class="menu-item">
                        <a class="menu-link py-3" href="{{ route('home') }}">
                            <span class="menu-title">
                                <span class="menu-text"> Trang Chủ </span>
                                <span class="menu-desc"> Dashboards </span>
                            </span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link py-3" href="{{ route('recharge') }}">
                            <span class="menu-title">
                                <span class="menu-text"> Nạp Tiền </span>
                                <span class="menu-desc"> Nạp Thẻ & Nạp Ví </span>
                            </span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link py-3" href="{{ route('manager.index') }}">
                            <span class="menu-title">
                                <span class="menu-text"> Quản Lý Dịch Vụ </span>
                                <span class="menu-desc"> Managers Domain </span>
                            </span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link py-3" href="{{ route('source-code.index') }}">
                            <span class="menu-title">
                                <span class="menu-text"> Source Code </span>
                                <span class="menu-desc"> Mua Source Code </span>
                            </span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link py-3" href="{{ route('hosting.index') }}">
                            <span class="menu-title">
                                <span class="menu-text"> Hosting </span>
                                <span class="menu-desc"> Thuê Hosting </span>
                            </span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link py-3" href="{{ route('vps.index') }}">
                            <span class="menu-title">
                                <span class="menu-text"> VPS </span>
                                <span class="menu-desc"> Thuê VPS </span>
                            </span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link py-3" href="https://www.facebook.com/thanh.vu.826734" target="_blank">
                            <span class="menu-title">
                                <span class="menu-text"> Đối Tác </span>
                                <span class="menu-desc"> Nhà Cung Cấp </span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
