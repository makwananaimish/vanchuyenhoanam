<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />

    <meta name="author" content="{{ config('app.name') }}" />
    <meta name="description" content="{{ config('app.name') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Plugin -->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugin/swiper-bundle.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/icons/iconly/index.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/icons/remix-icon/index.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/colors.css') }}" />

    <!-- Base -->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/base/font-control.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/base/typography.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/base/base.css') }}" />

    <!-- Theme -->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/theme/colors-dark.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/theme/theme-dark.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/custom-rtl.css') }}" />

    <!-- Layouts -->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/layouts/sider.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/layouts/header.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/components.css') }}" />
    <!-- Customizer -->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/layouts/customizer.css') }}" />

    <!-- Charts -->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugin/apex-charts.css') }}" />

    <!-- Pages -->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/dashboard-analytics.css') }}" />

    <!-- Custom -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}" />

    <!-- My css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/app.css?v=3') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/qr.css?v=3') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/top-sale/global.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/top-sale/index.css?v=2') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/css/top-sale/div-relation-bounding-box.css?v=2') }}" />
    <link rel="stylesheet" type="text/css"
        href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet" type="text/css"
        href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <!-- End my css -->

    <link rel="shortcut icon" href="{{ asset('app-assets/img/logo/truck-1058.png') }}" type="image/x-icon">

    <title>@yield('title') - {{ config('app.name') }}</title>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-7Y4297XR9L"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-7Y4297XR9L');
    </script>
    <script src="{{ asset('app-assets/js/plugin/jquery.min.js') }}"></script>
</head>

<body>
    <main class="hp-bg-color-dark-90 d-flex min-vh-100">
        <div class="hp-sidebar hp-bg-color-black-0 hp-bg-color-dark-100">
            <div class="hp-sidebar-container">
                <div class="hp-sidebar-header-menu">
                    <div class="row justify-content-between align-items-end me-12 ms-24 mt-24">
                        <div class="w-auto px-0 hp-sidebar-collapse-button hp-sidebar-visible">
                            <button type="button" class="btn btn-text btn-icon-only">
                                <i class="ri-menu-unfold-line" style="font-size: 16px"></i>
                            </button>
                        </div>

                        <div class="w-auto px-0">
                            <div class="hp-header-logo d-flex align-items-end">
                                <a href="{{ route('home') }}">
                                    <img class="hp-logo hp-sidebar-hidden hp-dir-none hp-dark-none"
                                        src="{{ asset('app-assets/img/logo/logo2@2x.png') }}" alt="logo" />
                                </a>
                            </div>
                        </div>

                        <div class="w-auto px-0 hp-sidebar-collapse-button hp-sidebar-hidden">
                            <button type="button" class="btn btn-text btn-icon-only">
                                <i class="ri-menu-fold-line" style="font-size: 16px"></i>
                            </button>
                        </div>
                    </div>

                    @include('layouts.menu')
                </div>

                <div
                    class="row justify-content-between align-items-center hp-sidebar-footer pb-24 px-24 mx-0 hp-bg-color-dark-100">
                    <div class="divider border-black-20 hp-border-color-dark-70 hp-sidebar-hidden px-0"></div>

                    <div class="col">
                        <div class="row align-items-center">
                            <div class="me-8 w-auto px-0">
                                <div class="avatar-item d-flex align-items-center justify-content-center rounded-circle"
                                    style="width: 36px; height: 36px">
                                    <img src="{{ getAvatarUrl() }}" />
                                </div>
                            </div>

                            <div class="w-auto px-0 hp-sidebar-hidden">
                                @if (auth('customer')->check())
                                    <span
                                        class="d-block hp-text-color-black-100 hp-text-color-dark-0 hp-p1-body lh-1 ">
                                        Số dư :
                                        {{ number_format(\App\Repositories\CustomerRepository::user()->balance, 0, '', '.') }}
                                    </span>

                                    <div class="d-flex">
                                        <a href="{{ route('transaction.create') }}" class="me-4">Nạp</a>
                                        <a href="{{ route('transaction.create') }}">Rút</a>
                                    </div>
                                @endif

                                <span class="d-block hp-text-color-black-100 hp-text-color-dark-0 hp-p1-body lh-1">
                                    @if (auth()->guard('web')->check())
                                        {{ auth()->user()->name }}
                                    @else
                                        {{ auth('customer')->user()->name }}
                                    @endif
                                </span>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>

                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();"
                                    class="hp-badge-text hp-text-color-dark-30">
                                    Logout
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col hp-flex-none w-auto px-0 hp-sidebar-hidden">
                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#change-password">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24"
                                class="remix-icon hp-text-color-black-100 hp-text-color-dark-0" height="24"
                                width="24" xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path fill="none" d="M0 0h24v24H0z"></path>
                                    <path
                                        d="M3.34 17a10.018 10.018 0 0 1-.978-2.326 3 3 0 0 0 .002-5.347A9.99 9.99 0 0 1 4.865 4.99a3 3 0 0 0 4.631-2.674 9.99 9.99 0 0 1 5.007.002 3 3 0 0 0 4.632 2.672c.579.59 1.093 1.261 1.525 2.01.433.749.757 1.53.978 2.326a3 3 0 0 0-.002 5.347 9.99 9.99 0 0 1-2.501 4.337 3 3 0 0 0-4.631 2.674 9.99 9.99 0 0 1-5.007-.002 3 3 0 0 0-4.632-2.672A10.018 10.018 0 0 1 3.34 17zm5.66.196a4.993 4.993 0 0 1 2.25 2.77c.499.047 1 .048 1.499.001A4.993 4.993 0 0 1 15 17.197a4.993 4.993 0 0 1 3.525-.565c.29-.408.54-.843.748-1.298A4.993 4.993 0 0 1 18 12c0-1.26.47-2.437 1.273-3.334a8.126 8.126 0 0 0-.75-1.298A4.993 4.993 0 0 1 15 6.804a4.993 4.993 0 0 1-2.25-2.77c-.499-.047-1-.048-1.499-.001A4.993 4.993 0 0 1 9 6.803a4.993 4.993 0 0 1-3.525.565 7.99 7.99 0 0 0-.748 1.298A4.993 4.993 0 0 1 6 12c0 1.26-.47 2.437-1.273 3.334a8.126 8.126 0 0 0 .75 1.298A4.993 4.993 0 0 1 9 17.196zM12 15a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0-2a1 1 0 1 0 0-2 1 1 0 0 0 0 2z">
                                    </path>
                                </g>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="hp-main-layout mt-md-32">
            @include('layouts.header')

            <div class="hp-main-layout-content">
                <div class="row mb-32 gy-32">
                    @yield('content')
                </div>
            </div>

            <footer class="w-100 py-18 px-16 py-sm-24 px-sm-32 hp-bg-color-black-10 hp-bg-color-dark-100">
                <div class="row align-items-center">
                    <div class="col-12 col-sm-6">
                        <p class="hp-badge-text mb-0 text-center text-sm-start hp-text-color-dark-30">
                            COPYRIGHT ©{{ date('Y') }} {{ config('app.name') }}, All rights Reserved
                        </p>
                    </div>

                    <div class="col-12 col-sm-6 mt-8 mt-sm-0 text-center text-sm-end">
                        <a href="{{ config('app.url') }}" target="_blank"
                            class="hp-badge-text hp-text-color-dark-30">🥁 Version: 3.1</a>
                    </div>
                </div>
            </footer>
        </div>
    </main>

    @include('components.modals.change-password')

    @if (auth()->check())
        @if (auth()->user()->is_admin || auth()->user()->is_vn_inventory || auth()->user()->is_accountant)
            @include('components.modals.address')
        @endif
    @endif

    <div class="scroll-to-top">
        <button type="button" class="btn btn-primary btn-icon-only rounded-circle hp-primary-shadow">
            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="16px"
                width="16px" xmlns="http://www.w3.org/2000/svg">
                <g>
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M13 7.828V20h-2V7.828l-5.364 5.364-1.414-1.414L12 4l7.778 7.778-1.414 1.414L13 7.828z">
                    </path>
                </g>
            </svg>
        </button>
    </div>

    <!-- Plugin -->

    <script src="{{ asset('app-assets/js/plugin/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/plugin/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/plugin/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/plugin/autocomplete.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/plugin/moment.min.js') }}"></script>

    <!-- Layouts -->
    <script src="{{ asset('app-assets/js/layouts/header-search.js') }}"></script>
    <script src="{{ asset('app-assets/js/layouts/sider.js') }}"></script>
    <script src="{{ asset('app-assets/js/components/input-number.js') }}"></script>

    <!-- Base -->
    <script src="{{ asset('app-assets/js/base/index.js') }}"></script>
    <!-- Customizer -->
    <script src="{{ asset('app-assets/js/customizer.js') }}"></script>

    <!-- Charts -->
    <script src="{{ asset('app-assets/js/plugin/apexcharts.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/charts/apex-chart.js') }}"></script>

    <!-- Cards -->
    <script src="{{ asset('app-assets/js/cards/card-analytic.js') }}"></script>
    <script src="{{ asset('app-assets/js/cards/card-advance.js') }}"></script>
    <script src="{{ asset('app-assets/js/cards/card-statistic.js') }}"></script>

    <!-- Custom -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- My js -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
    <script src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/table2excel@1.0.4/dist/table2excel.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/linways/table-to-excel@v1.0.4/dist/tableToExcel.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.10/dist/clipboard.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.lazy/latest/jquery.lazy.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.lazy/latest/jquery.lazy.plugins.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-annotation/2.2.1/chartjs-plugin-annotation.min.js">
    </script>

    <script>
        const isAdmin = {!! json_encode(
            isAdmin() || auth()->user()->is_accountant || auth()->user()->is_vn_inventory || auth()->user()->is_cn_inventory,
            JSON_PRETTY_PRINT,
        ) !!};
    </script>

    <script src="{{ asset('assets/js/app.js') }}?v=69"></script>
    <script src="{{ asset('assets/js/chart.js') }}?v=124"></script>
    <script src="{{ asset('assets/js/message.js') }}?v=25"></script>
    <script src="{{ asset('assets/js/transaction.js') }}?v=24"></script>
    <script src="{{ asset('assets/js/lazy.js') }}?v=24"></script>
    <script src="{{ asset('assets/js/select2-ajax.js') }}?v=26"></script>
    <!-- End my js -->

    @yield('js')
</body>

</html>
