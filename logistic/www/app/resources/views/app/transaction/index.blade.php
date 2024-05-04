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
                    Danh Sách
                </li>
            </ol>
        </nav>
    </div>

    <div class="col-12 col-md-6">
        <div class="hp-page-title">
            <h1 class="mb-8 text-uppercase">Danh sách giao dịch</h1>
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
                        @if (auth()->guard('customer')->check())
                            Số dư : {{ number_format($balance, 0, '', '.') }}
                        @endif
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-12 mt-16">
                        <form class="customer-filter-form">
                            <div class="row g-16 mb-16">
                                <div class="col-12 col-md-3 col-lg-3 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc tên, mã, sđt:</label>
                                    <select class="form-control select2" name="customer_id" onchange="filterCustomer(this)">
                                        <option value="">Tìm khách hàng</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->code }} - {{ $customer->name }} -
                                                {{ $customer->phone }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-3 col-lg-3 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc loại giao dịch:</label>
                                    <select class="form-control " name="type" onchange="filterCustomer(this)">
                                        <option value="">Tất cả</option>
                                        <option value="{{ \App\Transaction::TYPE_DEPOSIT }}"
                                            {{ request('type') == \App\Transaction::TYPE_DEPOSIT ? 'selected' : '' }}>
                                            {{ __('app.' . \App\Transaction::TYPE_DEPOSIT) }}</option>
                                        <option value="{{ \App\Transaction::TYPE_WITHDRAWAL }}"
                                            {{ request('type') == \App\Transaction::TYPE_WITHDRAWAL ? 'selected' : '' }}>
                                            {{ __('app.' . \App\Transaction::TYPE_WITHDRAWAL) }}</option>
                                        <option value="{{ \App\Transaction::TYPE_PAYMENT }}"
                                            {{ request('type') == \App\Transaction::TYPE_PAYMENT ? 'selected' : '' }}>
                                            {{ __('app.' . \App\Transaction::TYPE_PAYMENT) }}</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-3 col-lg-3 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc trạng thái:</label>
                                    <select class="form-control " name="status" onchange="filterCustomer(this)">
                                        <option value="">Tất cả</option>
                                        <option value="{{ \App\Transaction::STATUS_TEXT_PROCESSING }}"
                                            {{ request('status') == \App\Transaction::STATUS_TEXT_PROCESSING ? 'selected' : '' }}>
                                            Đang xử lý</option>
                                        <option value="{{ \App\Transaction::STATUS_TEXT_COMPLETED }}"
                                            {{ request('status') == \App\Transaction::STATUS_TEXT_COMPLETED ? 'selected' : '' }}>
                                            Đã hoàn thành</option>
                                        <option value="{{ \App\Transaction::STATUS_TEXT_CANCELLED }}"
                                            {{ request('status') == \App\Transaction::STATUS_TEXT_CANCELLED ? 'selected' : '' }}>
                                            Đã hủy</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-3 col-lg-3 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc giao dịch nạp:</label>
                                    <select class="form-control " name="deposit_type" onchange="filterCustomer(this)">
                                        <option value="">Tất cả</option>
                                        <option value="{{ \App\Transaction::DEPOSIT_TYPE_AUTO }}"
                                            {{ request('deposit_type') == \App\Transaction::DEPOSIT_TYPE_AUTO ? 'selected' : '' }}>
                                            Tự động</option>
                                        <option value="{{ \App\Transaction::DEPOSIT_TYPE_MANUAL }}"
                                            {{ request('deposit_type') == \App\Transaction::DEPOSIT_TYPE_MANUAL ? 'selected' : '' }}>
                                            Thủ công</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-2 col-lg-2 hp-flex-none">
                                    <button type="button" class="btn btn-primary"
                                        onclick="exportExcel('xlsx', document.querySelector('.table'));">
                                        Xuất excel
                                    </button>
                                </div>

                                <div class="col-12 col-md-2 col-lg-2 hp-flex-none">
                                    <a href="{{ route('transaction.create') }}" class="btn btn-primary">
                                        Tạo
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-12">
                        @if (session('message'))
                            <div class="alert {{ session('alert-class') }}">
                                {{ session('message') }}
                            </div>
                        @endif
                    </div>

                    <div class="col-12 mt-16 fix-width scroll-inner">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col"><b>Id</b></th>
                                    <th scope="col"><b>Ngày</b></th>
                                    <th scope="col"><b>Khách hàng</b></th>
                                    <th scope="col"><b>Loại</b></th>
                                    <th scope="col"><b>Số tiền</b></th>
                                    <th scope="col"><b>Số dư</b></th>
                                    <th scope="col"><b>Người tạo</b></th>
                                    <th scope="col"><b>Trạng thái</b></th>
                                    <th scope="col"><b>Hành động</b></th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($transactions as $index => $transaction)
                                    <tr>
                                        <td>
                                            <a href="{{ route('transaction.show', ['transaction' => $transaction]) }}">
                                                {{ $transaction->id }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $transaction->datetime }}
                                        </td>
                                        <td>
                                            @if ($transaction->customer)
                                                <a
                                                    href="{{ route('customer.show', ['customer' => $transaction->customer]) }}">
                                                    {{ optional($transaction->customer)->code }}
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            {{ __('app.' . $transaction->type) }}
                                        </td>
                                        <td class="{{ $transaction->css_class }} fw-bold">
                                            {{ $transaction->mark }} {{ number_format($transaction->amount, 0, '', '.') }}
                                        </td>
                                        <td class="text-green fw-bold">
                                            {{ number_format($transaction->balance, 0, '', '.') }}
                                        </td>
                                        <td>
                                            {{ $transaction->created_by }}
                                        </td>
                                        <td>
                                            {{ $transaction->status_text }}
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                @if (($transaction->type === \App\Transaction::TYPE_DEPOSIT &&
                                                    $transaction->status === \App\Transaction::STATUS_TEXT_PROCESSING) ||
                                                    ($transaction->type === \App\Transaction::TYPE_WITHDRAWAL &&
                                                        $transaction->status === \App\Transaction::STATUS_TEXT_PROCESSING &&
                                                        auth()->check()))
                                                    <form method="POST"
                                                        action="{{ route('transaction.cancel', ['transaction' => $transaction]) }}"
                                                        id="frm-{{ $transaction->id }}">
                                                        @csrf
                                                    </form>

                                                    <button class="btn btn-danger"
                                                        onclick="$('#frm-{{ $transaction->id }}').submit()">Hủy</button>
                                                @endif

                                                @if ($transaction->type === \App\Transaction::TYPE_WITHDRAWAL &&
                                                    $transaction->status === \App\Transaction::STATUS_TEXT_PROCESSING &&
                                                    auth()->check())
                                                    <a href="{{ route('transaction.show', ['transaction' => $transaction]) }}"
                                                        class="btn btn-primary ms-6">Duyệt</a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{ $transactions->appends(request()->all())->links() }}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
