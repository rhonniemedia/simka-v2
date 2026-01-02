<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIMKA | </title>

    <link rel="stylesheet" href="{{ asset('assets/css/materialdesignicons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
    <link rel="shortcut icon" href="{{ asset('assets/images/icon.png') }}" />

    <!-- Demo CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/button.css') }}">

    <!-- Custom CSS - Loading -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom/loading.css') }}">

    <!-- Custom CSS - Tables -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom/tables.css') }}">

    <!-- Alpine -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .link-reset,
        .link-reset:hover,
        .link-reset:focus,
        .link-reset:active,
        .link-reset:visited {
            all: unset;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
        }
    </style>

</head>

<body>
    <div class="container-scroller">

        <!-- Left Sidebar -->
        @include ('layouts.sidebar')
        <!-- End of left sidebar -->

        <div class="container-fluid page-body-wrapper">
            <div id="theme-settings" class="settings-panel">
                <i class="settings-close mdi mdi-close"></i>
                <p class="settings-heading">SIDEBAR SKINS</p>
                <div class="sidebar-bg-options selected" id="sidebar-default-theme">
                    <div class="img-ss rounded-circle bg-light border mr-3"></div> Default
                </div>
                <div class="sidebar-bg-options" id="sidebar-dark-theme">
                    <div class="img-ss rounded-circle bg-dark border mr-3"></div> Dark
                </div>
                <p class="settings-heading mt-2">HEADER SKINS</p>
                <div class="color-tiles mx-0 px-4">
                    <div class="tiles light"></div>
                    <div class="tiles dark"></div>
                </div>
            </div>

            <!-- Navbar -->
            @include ('layouts.navbar')
            <!-- End of Navbar -->
            <div class="main-panel">

                <!-- Content -->

                @yield('container')

                <!-- End of content -->

                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © SMK Negeri 1 Rejang Lebong. {{ date('Y') }}</span>
                    </div>
                </footer>
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>

    <!-- container-scroller -->
    <!-- plugins:js -->

    <script src="{{ asset('assets/js/vendor.bundle.base.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>

    <!-- Global Utilities (LOAD FIRST BEFORE HTMX) -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <script src="https://unpkg.com/htmx.org@2.0.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Module Scripts -->
    @stack('scripts')

</body>

</html>