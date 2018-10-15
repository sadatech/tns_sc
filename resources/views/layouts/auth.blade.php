<!doctype html>
<!--[if lte IE 9]>     <html lang="en" class="no-focus lt-ie10 lt-ie10-msg"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en" class="no-focus"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">

        <title>@yield('title') - HRBA</title>

        <meta name="description" content="Codebase - Bootstrap 4 Admin Template &amp; UI Framework created by pixelcave and published on Themeforest">
        <meta name="author" content="pixelcave">
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
        <link rel="stylesheet" id="css-main" href="{{ ('assets/css/codebase.min.css') }}">
    </head>
    <body>

        <!-- Page Container -->
        <div id="page-container" class="main-content-boxed">

            <!-- Main Container -->
            <main id="main-container">

                <!-- Page Content -->
           @yield('content')
                <!-- END Page Content -->

            </main>
            <!-- END Main Container -->
        </div>
        <!-- END Page Container -->

        <!-- Codebase Core JS -->
        <script src="{{ ('assets/js/core/jquery.min.js') }}"></script>
        <script src="{{ ('assets/js/core/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ ('assets/js/core/jquery.slimscroll.min.js') }}"></script>
        <script src="{{ ('assets/js/core/jquery-scrollLock.min.js') }}"></script>
        <script src="{{ ('assets/js/core/jquery.appear.min.js') }}"></script>
        <script src="{{ ('assets/js/core/jquery.countTo.min.js') }}"></script>
        <script src="{{ ('assets/js/core/js.cookie.min.js') }}"></script>
        <script src="{{ ('assets/js/codebase.js') }}"></script>

     @yield('script')
    </body>
</html>