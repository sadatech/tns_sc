<!doctype html>
<!--[if lte IE 9]>     <html lang="en" class="no-focus lt-ie10 lt-ie10-msg"> <![endif]-->
<!--[if gt IE 9]><!--> 
<html lang="en" class="no-focus"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">

    <title>@yield('title') - HRBA </title>
    <meta name="description" content="HRBA - Demonstrasi Aplikasi">
    <meta name="author" content="Sada Technology">
    <meta name="robots" content="noindex, nofollow">

    <!-- Open Graph Meta -->
    <meta property="og:title" content="HRBA - Demonstrasi Aplikasi">
    <meta property="og:site_name" content="HRBA">
    <meta property="og:description" content="HRBA - Demonstrasi Aplikasi yang dibuat oleh Sada Technology">
    <meta property="og:type" content="website">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Icons -->
    <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <!-- END Icons -->

    <!-- Stylesheets -->

    <!-- Fonts and Codebase framework -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Muli:300,400,400i,600,700">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/codebase.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/emoji.min.css') }}">

    @yield('css')
</head>
<body>

    <!-- Page Container -->
    <div id="page-container" class="enable-page-overlay side-scroll page-header-fixed main-content-boxed">
        <!-- Header -->
        <header id="page-header">
            <div class="content-header">
                <div class="content-header-section">
                </div>
                <div class="content-header-section">
                    Logout <a class="btn btn-circle btn-dual-secondary" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa fa-power-off"></i>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </div>
            <div id="page-header-search" class="overlay-header">
                <div class="content-header content-header-fullrow">
                    <form action="be_pages_generic_search.php" method="post">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <button type="button" class="btn btn-secondary" data-toggle="layout" data-action="header_search_off">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control" placeholder="Search or hit ESC.." id="page-header-search-input" name="page-header-search-input">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div id="page-header-loader" class="overlay-header bg-primary">
                <div class="content-header content-header-fullrow text-center">
                    <div class="content-header-item">
                        <i class="fa fa-sun-o fa-spin text-white"></i>
                    </div>
                </div>
            </div>
        </header>
        <!-- END Header -->

        <!-- Main Container -->
        <main id="main-container">

            <!-- Page Content -->
            @yield('content')
            <!-- END Page Content -->

        </main>
        <!-- END Main Container -->

        <!-- Footer -->
        <footer id="page-footer" class="opacity-0">
            <div class="content py-20 font-size-xs clearfix">
                <div class="float-right">
                    Created with <i class="fa fa-heart text-pulse"></i> by <a class="font-w600" href="https://www.sada.co.id/" target="_blank">Sada Technology</a>
                </div>
            </div>
        </footer>
        <!-- END Footer -->
    </div>
    <!-- Codebase Core JS -->
    <script src="{{ asset('assets/js/core/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/codebase.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/select2/js/select2.full.min.js') }}"></script>
    @yield('script')
</body>
</html>