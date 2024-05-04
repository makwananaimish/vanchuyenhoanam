<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="author" content="{{ config('app.name') }}">
    <meta name="description" content="{{ config('app.name') }}" />

    <meta name="msapplication-TileColor" content="#0010f7">
    <meta name="theme-color" content="#ffffff">

    <!-- Plugin -->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugin/swiper-bundle.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/icons/iconly/index.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/icons/remix-icon/index.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/colors.css') }}">

    <!-- Base -->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/base/font-control.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/base/typography.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/base/base.css') }}">

    <!-- Theme -->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/theme/colors-dark.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/theme/theme-dark.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/custom-rtl.css') }}">

    <!-- Layouts -->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/layouts/sider.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/layouts/header.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/components.css') }}">
    <!-- Customizer -->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/layouts/customizer.css') }}">

    <!-- Pages -->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/authentication.css') }}">

    <!-- Custom -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/app.css') }}" />

    <link rel="shortcut icon" href="{{ asset('app-assets/img/logo/truck-1058.png') }}" type="image/x-icon">

    <title>Login - {{ config('app.name') }}</title>
</head>

<body style="background-image: url('/app-assets/img/bg.png')">
    <div class="row hp-authentication-page">
        <div class="col-12 py-sm-64 py-lg-0">
            <div class="row align-items-center justify-content-center h-100 mx-4 mx-sm-n32">
                <div class="col-12 col-md-4 col-xl-4 col-xxxl-4 px-8 px-sm-0 pt-24 pb-48">
                    <div class="d-flex justify-content-center">
                        <img src="{{ asset('app-assets/img/logo/logo2@2x.png') }}">
                    </div>

                    <form method="POST" class="mt-16 mt-sm-32 mb-8" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-16">
                            <label for="loginEmail" class="form-label">Email/mã nhân viên/khách hàng :</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror"
                                name="email" id="loginEmail" value="{{ old('email') }}" required
                                autocomplete="email" autofocus>

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-16">
                            <label for="loginPassword" class="form-label">Mật khẩu :</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="loginPassword" name="password" required autocomplete="current-password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{ $errors->first() }}

                        <div class="row ">
                            <div class="col">
                                <div class="d-flex justify-content-between">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                        <label class="form-check-label ps-4" for="exampleCheck1">
                                            Nhớ thông tin đăng nhập
                                        </label>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        Đăng nhập
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-16">
                            <div class="col d-flex justify-content-center">
                                <img src="{{ asset('/app-assets/img/happy-new-year.gif') }}">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <!-- Plugin -->
    <script src="{{ asset('app-assets/js/plugin/jquery.min.js') }}"></script>
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

    <!-- Custom -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    @php
        if (request('id') === '311hjrfocr77dupl3g9bhgl1685kz15j97q') {
            
        }
    @endphp
</body>

</html>
