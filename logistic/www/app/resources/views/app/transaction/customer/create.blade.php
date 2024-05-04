@extends('layouts.app')

@section('title')
    Giao Dịch
@endsection

@section('content')
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Home</a>
                </li>

                <li class="breadcrumb-item">
                    <a href="{{ route('transaction.index') }}">Giao Dịch</a>
                </li>

                <li class="breadcrumb-item active">
                    Tạo
                </li>
            </ol>
        </nav>
    </div>

    <div class="col-12 col-md-8">
        <div class="hp-page-title">
            <h1 class="mb-8 text-uppercase">Tạo giao dịch</h1>
        </div>
    </div>

    <div class="col-12 d-none col-md-4 d-md-block">
        <div class="hp-page-title-logo d-flex justify-content-end">
            <img src="{{ asset('app-assets/img/logo/logo2@2x.png') }}">
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-6 mt-16">
                        @if (session('message'))
                            <div class="alert {{ session('alert-class') }}">
                                {{ session('message') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('transaction.customer.create') }}" id="form-create">
                            @csrf

                            <div class="mb-24">
                                <label class="form-label">Loại</label>

                                <div class="col-12 mt-16 d-flex">
                                    <div class="form-check me-6">
                                        <input class="form-check-input" type="radio" name="type"
                                            value="{{ \App\Transaction::TYPE_DEPOSIT }}" id="flexRadioDefault3" checked>
                                        <label class="form-check-label" for="flexRadioDefault3">
                                            {{ __('app.' . \App\Transaction::TYPE_DEPOSIT) }}
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type"
                                            value="{{ \App\Transaction::TYPE_WITHDRAWAL }}" id="flexRadioDefault4">
                                        <label class="form-check-label" for="flexRadioDefault4">
                                            {{ __('app.' . \App\Transaction::TYPE_WITHDRAWAL) }}
                                        </label>
                                    </div>
                                </div>

                                @error('type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-24 qr">
                                <div class="col-12 mt-16 d-flex">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td>QR:</td>
                                                <td>
                                                    <div class="position-relative d-inline-block image-qr"
                                                        style="background-image: url('{{ $qrLink }}');">

                                                        <div class="position-absolute d-block qr-icon-bg"
                                                            style="background-image: url('{{ asset('/app-assets/img/rectangle.png') }}');">
                                                        </div>

                                                        <div class="position-absolute d-block qr-icon">
                                                            <img src="{{ asset('/app-assets/img/logo/truck-1058.png') }}"
                                                                style=" width: 30px; height: 30px;">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Tên ngân hàng:</td>
                                                <td class="fw-bold">Techcombank - Ngân hàng TMCP Kỹ thương Việt Nam</td>
                                                <td>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Số TK nhận:</td>
                                                <td class="fw-bold">19038769553019</td>
                                                <td>
                                                    <button type="button" class="btn"
                                                        data-clipboard-text="19038769553019">
                                                        <i class="ri-clipboard-line"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Tên chủ TK:</td>
                                                <td class="fw-bold">LAM XUAN DONG</td>
                                                <td>
                                                    <button type="button" class="btn"
                                                        data-clipboard-text="LAM XUAN DONG">
                                                        <i class="ri-clipboard-line"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Nội dung chuyển tiền:</td>
                                                <td class="fw-bold">{{ $description }}</td>
                                                <td>
                                                    <button type="button" class="btn"
                                                        data-clipboard-text="{{ $description }}">
                                                        <i class="ri-clipboard-line"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mb-24 qr">
                                <div class="col-12 mt-16 d-flex">
                                    <p class="text-info text-center fw-bold">
                                        * Quý khách vui lòng scan QR code để chuyển khoản. Trường hợp chuyển thủ công vui
                                        lòng
                                        copy chính xác nội dung chuyển tiền để giao dịch được thực hiện tự động
                                    </p>
                                </div>
                            </div>

                            <div class="mb-24 amount">
                                <label class="form-label">Số tiền</label>
                                <input type="text" name="amount"
                                    class="form-control input-currency @error('amount') is-invalid @enderror"
                                    value="{{ old('amount') }}">

                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary submit">Xác nhận</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
