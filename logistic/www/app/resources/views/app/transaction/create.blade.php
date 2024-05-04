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
        @if (session('message'))
            <div class="alert {{ session('alert-class') }}">
                {{ session('message') }}
            </div>
        @endif
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-6 mt-16">
                        <form method="POST" action="{{ route('transaction.create') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-24">
                                <label class="col-form-label">Khách hàng:</label>

                                <select class="form-control form-select select2 @error('customer_id') is-invalid @enderror"
                                    name="customer_id">
                                    <option value="">Chọn khách hàng</option>
                                    @foreach ($allCustomers as $customer)
                                        <option value="{{ $customer->id }}"
                                            {{ request('id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->code }} - {{ $customer->name }} -
                                            {{ $customer->phone }} - Số dư :
                                            {{ number_format($customer->balance, 0, '', '.') }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('customer_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-24">
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

                            <button type="submit" class="btn btn-primary">Xác nhận</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-between">
                    <div class="col-12 mt-16">
                        <form class="customer-filter-form">
                            <div class="row g-16 mb-16">
                                <div class="col-12 col-md-4 col-lg-4 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc tên, mã, sđt:</label>
                                    <select class="form-control select2" name="id" onchange="filterCustomer(this)">
                                        <option value="">Tìm khách hàng</option>
                                        @foreach ($allCustomers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ request('id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->code }} - {{ $customer->name }} -
                                                {{ $customer->phone }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-4 col-lg-4 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc công nợ:</label>
                                    <select class="form-control " name="debt_type" onchange="filterCustomer(this)">
                                        <option value="">Tất cả</option>
                                        <option value="1" {{ request('debt_type') == 1 ? 'selected' : '' }}>Công nợ
                                            trên 01 tháng</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-4 col-lg-4 hp-flex-none">
                                    <button type="button" class="btn btn-primary float-md-end" style="margin-top: 40px"
                                        onclick="exportExcel('xlsx', document.querySelector('.table'));">
                                        Xuất excel
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-12 mt-16 fix-width scroll-inner">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col"><b>STT</b></th>
                                    <th scope="col"><b>Tên khách hàng</b></th>
                                    <th scope="col"><b>Mã khách hàng</b></th>
                                    <th scope="col"><b>SĐT</b></th>
                                    <th scope="col"><b>Địa chỉ</b></th>

                                    <th scope="col" class="cell-revenue"><b>Doanh thu</b></th>
                                    <th scope="col" class="cell-cost"><b>Đã thanh toán</b></th>
                                    <th scope="col" class="cell-debt"><b>Công nợ</b> <br>
                                        {{ number_format($totalDebt, 0, '', '.') }} </th>

                                    <th scope="col" class="cell-revenue"><b>Đã nạp</b></th>
                                    <th scope="col" class="cell-cost"><b>Đã tiêu</b></th>

                                    <th scope="col"><b>Hành động</b></th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($customers as $index => $customer)
                                    <tr>
                                        <td scope="row">{{ $index + 1 }}</td>
                                        <td>{{ $customer->name }}</td>
                                        <td>
                                            <a href="{{ route('customer.show', ['customer' => $customer->id]) }}">
                                                {{ $customer->code }}
                                            </a>
                                        </td>
                                        <td>{{ $customer->phone }}</td>
                                        <td>{{ $customer->address }}</td>

                                        <td class="cell-revenue">
                                            {{ number_format($customer->revenue, 0, '', '.') }}
                                        </td>
                                        <td class="cell-cost">
                                            {{ number_format($customer->paid, 0, '', '.') }}
                                        </td>
                                        <td class="cell-debt">
                                            {{ number_format($customer->debt, 0, '', '.') }}
                                        </td>

                                        <td class="cell-revenue">
                                            {{ number_format($customer->total_deposit, 0, '', '.') }}
                                        </td>
                                        <td class="cell-cost">
                                            {{ number_format($customer->total_spend, 0, '', '.') }}
                                        </td>

                                        <td>
                                            <div class="d-flex">
                                                <a class="btn btn-primary text-nowrap me-6"
                                                    href="{{ route('transaction.create') }}?id={{ $customer->id }}">
                                                    Nạp
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $customers->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
