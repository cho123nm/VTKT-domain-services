<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ strtoupper($_SERVER['SERVER_NAME']) }} - {{ $settings->tieude ?? 'CloudStoreVN' }}</title>
    <meta charset="utf-8" />
    <meta name="description" content="{{ $settings->mota ?? 'Cung cấp tên miền giá rẻ' }}" />
    <meta name="keywords" content="{{ $settings->keywords ?? 'tên miền, domain, giá rẻ' }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="vi" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="Cung cấp bởi THANHVU.NET V4" />
    <meta property="og:url" content="https://www.facebook.com/thanh.vu.826734" />
    <meta property="og:site_name" content="THANHVU.NET V4" />
    <link rel="canonical" href="" />
    <link rel="shortcut icon" href="/assets/media/logos/favicon.ico" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />

    <link href="/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <!-- Responsive CSS -->
    <style>
        /* Mobile First - Responsive Utilities */
        @media (max-width: 575.98px) {
            .container-xxl, .app-container {
                padding-left: 15px;
                padding-right: 15px;
            }
            
            .card {
                margin-bottom: 1rem;
            }
            
            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            
            .btn-group .btn {
                width: auto;
            }
            
            h1, h2, h3, h4, h5, h6 {
                font-size: 1.25rem;
            }
        }
        
        @media (max-width: 767.98px) {
            .d-md-none {
                display: none !important;
            }
            
            .table {
                font-size: 0.875rem;
            }
            
            .card-body {
                padding: 1rem;
            }
        }
        
        /* Ensure tables are scrollable on mobile */
        .table-responsive {
            -webkit-overflow-scrolling: touch;
        }
        
        /* Mobile-friendly forms */
        @media (max-width: 575.98px) {
            .form-control, .form-select {
                font-size: 16px; /* Prevents zoom on iOS */
            }
        }
    </style>
    
    <!-- Google Tag Manager -->
    <script>
    (function(w,d,s,l,i){
        w[l]=w[l]||[];
        w[l].push({'gtm.start': new Date().getTime(), event:'gtm.js'});
        var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),
            dl=l!='dataLayer'?'&l='+l:'';
        j.async=true;
        j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;
        f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-5FS8GGP');
    </script>
    <!-- End Google Tag Manager -->
</head>
<body id="kt_body" data-kt-app-header-stacked="true" class="app-default">
    <!-- Theme mode setup on page load -->
    <script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-theme-mode")) { themeMode = document.documentElement.getAttribute("data-theme-mode"); } else { if ( localStorage.getItem("data-theme") !== null ) { themeMode = localStorage.getItem("data-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-theme", themeMode); }</script>
    
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5FS8GGP" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    
    <!-- App -->
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <!-- Page -->
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            <!-- Header -->
            @include('layouts.partials.header')
            
            <!-- Content -->
            @yield('content')
            
            <!-- Footer -->
            @include('layouts.partials.footer')
        </div>
    </div>

    <!-- Scripts -->
    <script>var hostUrl = "/assets/index.html";</script>
    <script src="/assets/plugins/global/plugins.bundle.js"></script>
    <script src="/assets/js/fix-search-error.js"></script>
    <script src="/assets/js/scripts.bundle.js"></script>
    
    @stack('scripts')
</body>
</html>
