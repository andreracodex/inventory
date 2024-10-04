<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inventory Qu</title>
    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css">

    <link href="{{ asset('css/fontawesome/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fontawesome/brands.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fontawesome/fontawesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fontawesome/regular.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sweetalert/sweetalert2.min.css') }}" rel="stylesheet">

    <link href="{{ asset('css/litepicker.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}" />
</head>

<body class="nav-fixed sidenav-toggled">
    @yield('loginregforgot')
    @auth
        @include('layouts.partials.headerpos')
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
            </div>
            <div id="layoutSidenav_content">
                <main>
                    @yield('contenthead')
                    <!-- Main page content-->
                    @yield('content')
                </main>
                @include('layouts.partials.footer')
            </div>
        </div>
        <script src="{{ asset('js/jquery-3.6.4.min.js') }}"></script>
        <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
        <!-- Latest jQuery -->
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('js/fontawesome/brands.min.js') }}"></script>
        <script src="{{ asset('js/fontawesome/fontawesome.min.js') }}"></script>
        <script src="{{ asset('js/fontawesome/regular.min.js') }}"></script>
        <script src="{{ asset('js/fontawesome/all.min.js') }}"></script>

        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

        <script src="{{ asset('js/sweetalert/sweetalert2.all.min.js') }}"></script>
        <script src="{{ asset('js/feather.min.js') }}"></script>
        <script src="{{ asset('js/scripts.js') }}"></script>
        <script src="{{ asset('js/toasts.js') }}"></script>
        <script src="{{ asset('js/bundle.js') }}" crossorigin="anonymous"></script>
        <script src="{{ asset('js/litepicker.js') }}"></script>
        @stack('script')
    @endauth
</body>

</html>
