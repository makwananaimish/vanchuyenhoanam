@extends('layouts.app')

@section('title')
    Danh Sách Khách Hàng
@endsection

@section('content')
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Home</a>
                </li>

                <li class="breadcrumb-item">
                    <a href="{{ route('location.index') }}">Kho</a>
                </li>

                <li class="breadcrumb-item active">
                    Danh Sách
                </li>
            </ol>
        </nav>
    </div>

    <div class="col-12 col-md-6">
        <div class="hp-page-title">
            <h1 class="mb-8 text-uppercase">Cấu hình</h1>
        </div>
    </div>

    <div class="col-12 d-none col-md-6 d-md-block">
        <div class="hp-page-title-logo d-flex justify-content-end">
            <img src="{{ asset('app-assets/img/logo/logo2@2x.png') }}">
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-between">
                    <div class="col-12 mt-16">
                        <div class="row g-16 mb-16">
                            <div class="col-12 col-md-6 col-lg-6 hp-flex-none">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create-location">
                                    Tạo Kho
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-12 mt-16 fix-width scroll-inner">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Tên</th>
                                    <th scope="col">Loại</th>
                                    <th scope="col">Hành động</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($locations as $index => $location)
                                    <tr>
                                        <td scope="row">{{ $index + 1 }}</td>
                                        <td>{{ $location->name }}</td>
                                        <td>{{ $location->type_text }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <button class="btn btn-primary text-nowrap me-6" data-bs-toggle="modal"
                                                    data-bs-target="#update-location-{{ $location->id }}">
                                                    Cập Nhật
                                                </button>

                                                <form method="POST"
                                                    action="{{ route('location.delete', ['location' => $location]) }}"
                                                    id="frm-{{ $location->id }}">
                                                    @csrf
                                                </form>

                                                <button class="btn btn-danger text-nowrap"
                                                    onclick="deleteLocation({{ $location->id }})">
                                                    Xóa
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-12 mt-16">
                        <div class="row g-16 mb-16">
                            <div class="col-12 col-md-6 col-lg-6 hp-flex-none">
                                <button class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#create-shipping-method">
                                    Tạo Phương Thức Đi Hàng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-12 mt-16 fix-width scroll-inner">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Tên</th>
                                    <th scope="col">Hành động</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($shippingMethods as $index => $shippingMethod)
                                    <tr>
                                        <td scope="row">{{ $index + 1 }}</td>
                                        <td>{{ $shippingMethod->name }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <button class="btn btn-primary text-nowrap me-6" data-bs-toggle="modal"
                                                    data-bs-target="#update-shipping-method-{{ $shippingMethod->id }}">
                                                    Cập Nhật
                                                </button>

                                                <form method="POST"
                                                    action="{{ route('shipping_method.delete', ['shippingMethod' => $shippingMethod]) }}"
                                                    id="frm-{{ $shippingMethod->id }}{{ $shippingMethod->id }}">
                                                    @csrf
                                                </form>

                                                <button class="btn btn-danger text-nowrap"
                                                    onclick="deleteLocation({{ $shippingMethod->id }}{{ $shippingMethod->id }})">
                                                    Xóa
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-12 col-md-6 mt-16">
                        <form method="POST" action="{{ route('option.update') }}">
                            @csrf

                            <div class="mb-24">
                                <label class="form-label">Tỷ giá Tệ - VND</label>
                                <input type="text" class="form-control @error('rmb_to_vnd') is-invalid @enderror"
                                    name="rmb_to_vnd" value="{{ $rmbToVND }}" required>
                                @error('rmb_to_vnd')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-24">
                                <label class="form-label">Hệ số chống outcome</label>
                                <input type="text"
                                    class="form-control input-currency @error('outcome_weight') is-invalid @enderror"
                                    name="outcome_weight" value="{{ $outcomeWeight }}" required>
                                @error('outcome_weight')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-24">
                                <label class="form-label">Telegram bot token</label>
                                <input type="text" class="form-control @error('telegram_bot_token') is-invalid @enderror"
                                    name="telegram_bot_token" value="{{ $telegramBotToken }}">
                                @error('telegram_bot_token')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-24">
                                <label class="form-label">Telegram chat id</label>
                                <input type="text" class="form-control @error('telegram_chat_id') is-invalid @enderror"
                                    name="telegram_chat_id" value="{{ $telegramChatId }}">
                                @error('telegram_chat_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-24">
                                <label class="form-label">Telegram webhook</label>
                                <input type="text" class="form-control @error('telegram_webhook') is-invalid @enderror"
                                    name="telegram_webhook" value="{{ $telegramWebhook }}">
                                @error('telegram_webhook')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-24">
                                <label class="form-label">Telegram approver</label>
                                <input type="text"
                                    class="form-control @error('telegram_approver') is-invalid @enderror"
                                    name="telegram_approver" value="{{ $telegramApprover }}">
                                @error('telegram_approver')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-24">
                                <label class="form-label">API token</label>

                                <input type="text" class="form-control @error('api_token') is-invalid @enderror"
                                    name="api_token" value="{{ $apiToken }}">

                                @error('api_token')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-24">
                                <label class="form-label">API whitelist IP(mỗi ip 1 dòng)</label>

                                <textarea class="form-control @error('api_whitelist_ip') is-invalid @enderror" name="api_whitelist_ip"
                                    rows="5">{{ $apiWhitelistIp }}</textarea>

                                @error('api_whitelist_ip')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-24">
                                <label class="form-label">Whitelist số tài khoản(mỗi số 1 dòng)</label>

                                <textarea class="form-control @error('whitelist_account_numbers') is-invalid @enderror"
                                    name="whitelist_account_numbers" rows="5">{{ $whitelistAccountNumbers }}</textarea>

                                @error('whitelist_account_numbers')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-24">
                                <label class="form-label">Webhook nhắc nợ</label>

                                <input type="text"
                                    class="form-control @error('webhook_noti_debt') is-invalid @enderror"
                                    name="webhook_noti_debt" value="{{ $webhookNotiDebt }}">

                                @error('webhook_noti_debt')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>



                            <button type="submit" class="btn btn-primary">Lưu</button>

                        </form>


                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-12 col-md-6 mt-16">

                        <form action="{{ route('option.noti-debt') }}" method="post">
                            @csrf

                            <div class="mb-24">
                                <button type="submit" class="btn btn-primary">Nhắc nợ</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

        @include('components.modals.create-location')

        @include('components.modals.create-shipping-method')

        @foreach ($locations as $location)
            @include('components.modals.update-location', [
                'location' => $location,
            ])
        @endforeach

        @foreach ($shippingMethods as $shippingMethod)
            @include('components.modals.update-shipping-method', [
                'shippingMethod' => $shippingMethod,
            ])
        @endforeach
    </div>
@endsection
