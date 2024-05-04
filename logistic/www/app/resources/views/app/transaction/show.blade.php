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
                    Chi Tiết
                </li>
            </ol>
        </nav>
    </div>

    <div class="col-12 col-md-8">
        <div class="hp-page-title">
            <h1 class="mb-8 text-uppercase">Chi tiết giao dịch</h1>
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
                    <div class="col-12 mt-16 fix-width scroll-inner">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>Loại:</td>
                                    {{-- <td>{{ __('app.' . $transaction->type) }}</td> --}}
                                    <td>{{ $transaction->type_text }}</td>
                                </tr>

                                <tr>
                                    <td>Khách hàng:</td>
                                    <td>
                                        @if ($transaction->customer)
                                            <a href="{{ route('customer.show', ['customer' => $transaction->customer]) }}">
                                                {{ optional($transaction->customer)->code }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>

                                @auth('web')
                                    <tr>
                                        <td>Số tiền:</td>
                                        <td class="{{ $transaction->css_class }}">
                                            {{ $transaction->mark }} {{ $transaction->amount_format }}
                                        </td>
                                        <td>
                                            <button class="btn" data-clipboard-text="{{ $transaction->amount }}">
                                                <i class="ri-clipboard-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endauth

                                @if ($transaction->type === \App\Transaction::TYPE_DEPOSIT)
                                    <tr>
                                        <td>Số TK nhận:</td>
                                        <td>105877602150</td>
                                        <td>
                                            <button class="btn" data-clipboard-text="105877602150">
                                                <i class="ri-clipboard-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Tên chủ TK:</td>
                                        <td>LAM XUAN DONG</td>
                                        <td>
                                            <button class="btn" data-clipboard-text="LAM XUAN DONG">
                                                <i class="ri-clipboard-line"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>QR:</td>
                                        <td>
                                            <div class="position-relative d-inline-block image-qr"
                                                style="background-image: url('{{ $transaction->qr_link }}');">

                                                <div class="position-absolute d-block qr-icon-bg"
                                                    style="background-image: url('{{ asset('/app-assets/img/rectangle.png') }}');">
                                                </div>

                                                <div class="position-absolute d-block qr-icon">
                                                    <img src="{{ asset('/app-assets/img/logo/truck-1058.png') }}"
                                                        style=" width: 30px; height: 30px;">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif

                                <tr>
                                    <td>Nội dung:</td>
                                    <td>{{ $transaction->description }}</td>
                                </tr>

                                <tr>
                                    <td>Trạng thái:</td>
                                    <td class="{{ $transaction->status_text_css_class }} fw-bold">
                                        {{ $transaction->status_text }}</td>
                                </tr>

                                <tr>
                                    <td>Thời gian:</td>
                                    <td>{{ $transaction->datetime }}</td>
                                </tr>

                                @if ($transaction->image)
                                    <tr>
                                        <td>Ảnh:</td>
                                        <td>
                                            <img src="{{ asset('files/' . $transaction->image) }}" width="150px">
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="col-12">
                        @if (session('message'))
                            <div class="alert {{ session('alert-class') }}">
                                {{ session('message') }}
                            </div>
                        @endif
                    </div>

                    @if (auth()->check() &&
                        $transaction->type === \App\Transaction::TYPE_WITHDRAWAL &&
                        $transaction->status === \App\Transaction::STATUS_TEXT_PROCESSING)
                        <div class="col-6 mt-16">
                            <form method="POST"
                                action="{{ route('transaction.accept', ['transaction' => $transaction]) }}"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="mb-24">
                                    <label class="form-label">Ảnh</label>
                                    <input type="file" name="image"
                                        class="form-control @error('image') is-invalid @enderror" required>

                                    @error('image')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">Duyệt</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
