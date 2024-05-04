<header class="d-md-none">
    <div class="row w-100 m-0">
        <div class="col ps-18 pe-16 px-sm-24">
            <div class="row w-100 align-items-center justify-content-between position-relative">
                <div class="col w-auto hp-flex-none hp-mobile-sidebar-button me-24 px-0" data-bs-toggle="offcanvas"
                    data-bs-target="#mobileMenu" aria-controls="mobileMenu">
                    <button type="button" class="btn btn-text btn-icon-only">
                        <i class="ri-menu-fill hp-text-color-black-80 hp-text-color-dark-30 lh-1"
                            style="font-size: 24px;"></i>
                    </button>
                </div>


                <div class="col hp-flex-none w-auto pe-0">
                    <div class="row align-items-center justify-content-end">
                        <div class="hover-dropdown-fade w-auto px-0 ms-6 position-relative hp-cursor-pointer">
                            <div class="avatar-item d-flex align-items-center justify-content-center rounded-circle"
                                style="width: 40px; height: 40px;">
                                <img src="{{ asset('app-assets/img/memoji/memoji-1.png') }}">
                            </div>

                            {{-- <div class="hp-header-profile-menu dropdown-fade position-absolute pt-18"
                                style="top: 100%; width: 260px;">
                                <div
                                    class="rounded border hp-border-color-black-40 hp-bg-black-0 hp-bg-dark-100 hp-border-color-dark-80 p-24">
                                    <span class="d-block h5 hp-text-color-black-100 hp-text-color-dark-0 mb-6">Profile
                                        Settings</span>

                                    <a href="profile-information.html"
                                        class="hp-p1-body hp-text-color-primary-1 hp-text-color-dark-primary-2 hp-hover-text-color-primary-2">View
                                        Profile</a>

                                    <div class="divider my-12"></div>

                                    <div class="row">
                                        <div class="col-12">
                                            <a href="app-contact.html"
                                                class="d-flex align-items-center hp-p1-body py-4 px-10 hp-transition hp-hover-bg-primary-4 hp-hover-bg-dark-primary hp-hover-bg-dark-80 rounded"
                                                style="margin-left: -10px; margin-right: -10px;">
                                                <i class="iconly-Curved-People me-8" style="font-size: 16px;"></i>

                                                <span class="ml-8">Explore Creators</span>
                                            </a>
                                        </div>

                                        <div class="col-12">
                                            <a href="page-knowledge-base-1.html"
                                                class="d-flex align-items-center hp-p1-body py-4 px-10 hp-transition hp-hover-bg-primary-4 hp-hover-bg-dark-primary hp-hover-bg-dark-80 rounded"
                                                style="margin-left: -10px; margin-right: -10px;">
                                                <i class="iconly-Curved-Game me-8" style="font-size: 16px;"></i>

                                                <span class="hp-ml-8">Help Desk</span>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="divider my-12"></div>

                                    <a class="hp-p1-body" href="index.html">Logout</a>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="offcanvas offcanvas-start hp-mobile-sidebar" tabindex="-1" id="mobileMenu"
    aria-labelledby="mobileMenuLabel" style="width: 256px;">
    <div class="offcanvas-header justify-content-between align-items-end me-16 ms-24 mt-16 p-0">
        <div class="w-auto px-0">
            <div class="hp-header-logo d-flex align-items-end">
                <a href="{{ route('home') }}">
                    <img class="hp-logo hp-sidebar-hidden hp-dir-none hp-dark-none"
                        src="{{ asset('app-assets/img/logo/logo2@2x.png') }}" alt="">
                </a>
            </div>
        </div>

        <div class="w-auto px-0 hp-sidebar-collapse-button hp-sidebar-hidden" data-bs-dismiss="offcanvas"
            aria-label="Close">
            <button type="button" class="btn btn-text btn-icon-only">
                <i class="ri-close-fill lh-1 hp-text-color-black-80" style="font-size: 24px;"></i>
            </button>
        </div>
    </div>

    <div class="hp-sidebar hp-bg-color-black-0 hp-bg-color-dark-100">
        <div class="hp-sidebar-container">
            <div class="hp-sidebar-header-menu">
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
                                <img src="{{ asset('app-assets/img/memoji/memoji-1.png') }}" />
                            </div>
                        </div>

                        <div class="w-auto px-0 hp-sidebar-hidden">
                            @if (auth('customer')->check())
                                <span class="d-block hp-text-color-black-100 hp-text-color-dark-0 hp-p1-body lh-1 ">
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
</div>
