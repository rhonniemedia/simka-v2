<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>SIMKA | </title>

    <link rel="stylesheet" href="{{ asset('assets/css/materialdesignicons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
    <link rel="shortcut icon" href="{{ asset('assets/images/icon.png') }}" />

    <!-- Demo CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/button.css') }}">

    <!-- Alpine -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        .htmx-indicator {
            display: none;
        }

        .htmx-request .htmx-indicator {
            display: inline-block;
        }

        .htmx-request.htmx-indicator {
            display: inline-block;
        }

        [x-cloak] {
            display: none !important;
        }

        /* HTML: <div class="loader"></div> */
        .loader {
            width: 65px;
            aspect-ratio: 1;
            border-radius: 50%;
            background:
                radial-gradient(farthest-side, #74068aff 94%, #0000) top/8px 8px no-repeat,
                conic-gradient(#0000 30%, #74068aff);
            -webkit-mask: radial-gradient(farthest-side, #0000 calc(100% - 8px), #000 0);
            animation: l13 1s infinite linear;
        }

        @keyframes l13 {
            100% {
                transform: rotate(1turn)
            }
        }

        /* .loader {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: inline-block;
            border-top: 4px solid #FFF;
            border-right: 4px solid transparent;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }

        .loader::after {
            content: '';
            box-sizing: border-box;
            position: absolute;
            left: 0;
            top: 0;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border-bottom: 4px solid #c21387ff;
            border-left: 4px solid transparent;
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        } */

        #loading.htmx-indicator {
            opacity: 0;
            visibility: hidden;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.2);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 400ms ease-in-out, visibility 400ms;
        }

        #loading.show-loading {
            opacity: 1 !important;
            visibility: visible !important;
        }

        .pagination .page-item {
            display: none;
        }

        .pagination .page-item:first-child,
        .pagination .page-item:nth-child(2) {
            display: inline-block;
        }

        .pagination .page-item:last-child,
        .pagination .page-item:nth-last-child(2) {
            display: inline-block;
        }

        .pagination .page-item.active,
        .pagination .page-item.active+.page-item,
        .pagination .page-item.active+.page-item+.page-item,
        .pagination .page-item:has(+ .page-item.active),
        .pagination .page-item:has(+ .page-item + .page-item.active) {
            display: inline-block;
        }

        .pagination .page-item.disabled {
            display: inline-block !important;
        }

        .filter-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
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

    <script src="https://unpkg.com/htmx.org@2.0.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function productApp() {
            return {
                bsModal: null,
                currentPage: 1,
                filters: {
                    search: '',
                    min_price: '',
                    max_price: '',
                    per_page: '10'
                },

                init() {
                    this.bsModal = new bootstrap.Modal(this.$refs.modal);

                    // Load filters from URL on page load
                    this.loadFiltersFromURL();

                    // CSRF Token
                    document.body.addEventListener('htmx:configRequest', (e) => {
                        e.detail.headers['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
                    });

                    // Buka modal setelah konten dimuat
                    document.body.addEventListener('htmx:afterSwap', (e) => {
                        if (e.detail.target.id === 'modal-content') {
                            this.bsModal.show();
                        }
                    });

                    // Alert
                    document.body.addEventListener('showAlert', (e) => {
                        const alertData = e.detail;
                        Swal.fire({
                            icon: alertData.icon,
                            title: alertData.title,
                            text: alertData.text,
                            confirmButtonColor: '#0d6efd',
                            timer: 3000
                        });
                    });

                    // Refresh after update/delete (TANPA loading indicator)
                    document.body.addEventListener('productUpdated', () => {
                        this.bsModal.hide();
                        this.loadProducts(false); // false = no loading indicator
                    });

                    // Refresh after create (TANPA loading indicator)
                    document.body.addEventListener('productSaved', () => {
                        this.bsModal.hide();
                        this.currentPage = 1;
                        this.loadProducts(false); // false = no loading indicator
                    });
                },

                loadFiltersFromURL() {
                    const params = new URLSearchParams(window.location.search);
                    this.filters.search = params.get('search') || '';
                    this.filters.min_price = params.get('min_price') || '';
                    this.filters.max_price = params.get('max_price') || '';
                    this.filters.per_page = params.get('per_page') || '10';
                    this.currentPage = parseInt(params.get('page')) || 1;
                },

                applyFilter() {
                    this.currentPage = 1;
                    this.loadProducts();
                },

                resetFilter() {
                    this.filters = {
                        search: '',
                        min_price: '',
                        max_price: '',
                        per_page: '10'
                    };
                    this.currentPage = 1;
                    this.loadProducts();
                },

                loadProducts(showLoading = true) {
                    const params = new URLSearchParams({
                        page: this.currentPage,
                        per_page: this.filters.per_page
                    });

                    if (this.filters.search) params.set('search', this.filters.search);
                    if (this.filters.min_price) params.set('min_price', this.filters.min_price);
                    if (this.filters.max_price) params.set('max_price', this.filters.max_price);

                    const url = `/products?${params.toString()}`;

                    // Show loading HANYA jika diminta (untuk filter/pagination)
                    const loading = document.getElementById('loading');
                    if (loading && showLoading) {
                        loading.classList.add('show-loading');
                    }

                    htmx.ajax('GET', url, {
                        target: '#product-table',
                        swap: 'innerHTML',
                        indicator: showLoading ? '#loading' : null
                    });

                    // Update URL
                    window.history.pushState({}, '', url);
                },

                confirmDelete(id, name) {
                    Swal.fire({
                        title: 'Hapus?',
                        text: `Yakin hapus ${name}?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // TIDAK ada hx-indicator, jadi loading TIDAK muncul
                            htmx.ajax('DELETE', `/products/${id}`, {
                                target: '#product-table'
                            });
                        }
                    });
                }
            }
        }

        // Handle pagination clicks - hanya sekali, tidak terduplikasi
        document.addEventListener('click', function(e) {
            // Check if clicked element is pagination link
            if (e.target.matches('.pagination a, .pagination a *')) {
                const link = e.target.closest('a');
                if (!link) return;

                e.preventDefault();

                const url = new URL(link.href);
                const page = url.searchParams.get('page');

                const wrapper = document.querySelector('[x-data]');
                if (wrapper && window.Alpine) {
                    const app = Alpine.$data(wrapper);
                    if (app) {
                        app.currentPage = page;
                        app.loadProducts(true);
                    }
                }
            }
        });

        // Hide loading after HTMX settles
        document.body.addEventListener('htmx:afterSettle', (e) => {
            if (e.detail.target.id === 'product-table') {
                const loading = document.getElementById('loading');
                if (loading) {
                    loading.classList.remove('show-loading');
                }
            }
        });
    </script>

</body>

</html>