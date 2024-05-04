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
            <h1 class="mb-8 text-uppercase">Lịch sử giao dịch</h1>
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
                        <form class="customer-filter-form">
                            <div class="row g-16 mb-16">
                                <div class="col-12 col-md-4 col-lg-3 hp-flex-none">
                                    <label for="" class="col-form-label">Bank:</label>
                                    <select class="form-control " name="bank" onchange="filterCustomer(this)">
                                        <option value="">Tất cả</option>
                                        <option value="{{ \App\BankTransaction::TECHCOMBANK }}"
                                            {{ request('bank') == \App\BankTransaction::TECHCOMBANK ? 'selected' : '' }}>
                                            {{ \App\BankTransaction::TECHCOMBANK }}
                                        </option>
                                        <option value="{{ \App\BankTransaction::VIETINCOMBANK }}"
                                            {{ request('bank') == \App\BankTransaction::VIETINCOMBANK ? 'selected' : '' }}>
                                            {{ \App\BankTransaction::VIETINCOMBANK }}
                                        </option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-4 col-lg-3 hp-flex-none">
                                    <label for="" class="col-form-label">Tháng:</label>
                                    <input type="month" name="month" value="{{ request('month') }}" class="form-control"
                                        onchange="filterCustomer(this)" />
                                </div>

                                <div class="col-12 col-md-4 col-lg-3 hp-flex-none">
                                    <label for="" class="col-form-label">Tài khoản:</label>

                                    <div class="form-check">
                                        <input onchange="filterCustomer(this)" class="form-check-input" type="radio"
                                            name="account_number" value="19038769553019" id="flexRadioDefault6" checked
                                            {{ request('account_number') === '19038769553019' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="flexRadioDefault6">
                                            19038769553019
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input onchange="filterCustomer(this)" class="form-check-input" type="radio"
                                            name="account_number" value="19033023453017" id="flexRadioDefault7"
                                            {{ request('account_number') === '19033023453017' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="flexRadioDefault7">
                                            19033023453017
                                        </label>
                                    </div>
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
                                    <th scope="col"><b>Bank</b></th>
                                    <th scope="col"><b>Ngày</b></th>
                                    <th scope="col"><b>Số tiền</b></th>
                                    <th scope="col"><b>Nội dung giao dịch</b></th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($transactions as $index => $transaction)
                                    <tr>
                                        <td>
                                            {{ $transaction->bank }}
                                        </td>
                                        <td>
                                            {{ $transaction->date_format }}
                                        </td>
                                        <td>
                                            {{ $transaction->amount_format }}
                                        </td>
                                        <td>
                                            {{ $transaction->content }}
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
