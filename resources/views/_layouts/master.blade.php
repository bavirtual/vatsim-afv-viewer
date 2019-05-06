<!DOCTYPE html>
<html class="loading" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="AFV Viewer - Powered by BAVirtual">
    <meta name="keywords" content="bavirtual,ba,virtual airline">
    <meta name="author" content="Matt Bozwood-Davies">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VATSIM Viewer</title>
    <link rel="apple-touch-icon" href="{{ asset_path('ico/apple-touch-icon.png') }}">
    <link rel="shortcut icon" type="image/png" sizes="32x32" href="{{ asset_path('ico/favicon-32x32.png') }}">
    <link rel="shortcut icon" type="image/png" sizes="16x16" href="{{ asset_path('ico/favicon-16x16.png') }}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700" rel="stylesheet">
    <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css" rel="stylesheet">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="{{ app_asset_path('css/vendors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ app_asset_path('vendors/css/ui/prism.min.css') }}">
    <!-- END VENDOR CSS-->
    <!-- BEGIN MODERN CSS-->
    <link rel="stylesheet" type="text/css" href="{{ app_asset_path('css/app.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ app_asset_path('css/core/menu/menu-types/horizontal-menu.css') }}">
    <!-- END MODERN CSS-->
    <!-- BEGIN Page Level CSS-->
    @yield('page_css')
<!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset_path('css/style.css') }}">
    <style>
        .broadcast {
            position: relative;
            padding: 0.75rem 1rem;
            margin-bottom: 0;
            border: 1px solid transparent;
            border-radius: 0;
        }
    </style>
    <!-- END Custom CSS-->

    <!-- BEGIN VENDOR JS-->
    <script src="{{ app_asset_path('vendors/js/vendors.min.js') }}" type="text/javascript"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    <script type="text/javascript" src="{{ app_asset_path('vendors/js/ui/jquery.sticky.js') }}"></script>
    <script type="text/javascript" src="{{ app_asset_path('vendors/js/charts/jquery.sparkline.min.js') }}"></script>
    <script type="text/javascript" src="{{ app_asset_path('vendors/js/ui/prism.min.js') }}"></script>
    <script type="text/javascript" src="{{ app_asset_path('vendors/js/extensions/sweetalert.min.js') }}"></script>
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN MODERN JS-->
    <script src="https://use.fontawesome.com/f59467d6b3.js"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-131758926-2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-131758926-2');
    </script>

@yield('page_js')
<!-- END MODERN JS-->
</head>
<body class="horizontal-layout horizontal-menu 2-columns ssmenu-expanded" data-open="hover" data-menu="horizontal-menu" data-col="2-columns">
<!-- fixed-top-->
<nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow navbar-static-top navbar-light navbar-brand-center">
    <div class="navbar-wrapper">
        <div class="navbar-header" style="position: relative;">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item">
                    <a class="navbar-brand" href="/">
                        <img class="brand-logo" alt="VATSIM Logo" src="{{ asset_path('img/vatsim_0.png') }}">
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="col-md-6 col-12 mb-2">
                <h3 class="content-header-title">@yield('pageTitle', 'BAVMS')</h3>
            </div>
            <div class="col-md-6 col-12 mb-2 ">
                @yield('pageHeaderRight')
            </div>
        </div>
        <div class="content-body">
            @yield('content')
        </div>
    </div>
</div>

<footer class="footer fixed-bottom footer-dark navbar-shadow">
    <p class="clearfix blue-grey lighten-2 text-sm-center mb-0 px-2">
        <span class="float-md-left d-block d-md-inline-block">Copyright &copy; {{ date('Y') }} <a class="text-bold-800 grey darken-2" href="https://bavirtual.co.uk" target="_blank">BAVirtual </a>, All rights reserved. </span>
    </p>
</footer>

<script>
    @if(session()->get('flash-class'))
    swal("{{ session()->get('flash-title') }}", "{{ session()->get('flash-message') }}", "{{ session()->get('flash-class') }}");
    @endif
</script>

<script src="{{ app_asset_path('js/core/app-menu.js') }}" type="text/javascript"></script>
<script src="{{ app_asset_path('js/core/app.js') }}" type="text/javascript"></script>
<script src="{{ app_asset_path('js/scripts/customizer.js') }}" type="text/javascript"></script>
</body>
</html>