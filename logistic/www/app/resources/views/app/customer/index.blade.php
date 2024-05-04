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
                    <a href="{{ route('customer.index') }}">Khách Hàng</a>
                </li>

                <li class="breadcrumb-item active">
                    Danh Sách
                </li>
            </ol>
        </nav>
    </div>

    <div class="col-12 col-md-6">
        <div class="hp-page-title">
            <h1 class="mb-8 text-uppercase">DANH SÁCH KHÁCH HÀNG</h1>
        </div>
    </div>

    <div class="col-12 d-none col-md-6 d-md-block">
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

                                <div class="col-12 col-md-3 col-lg-3 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc công nợ:</label>
                                    <select class="form-control " name="debt_type" onchange="filterCustomer(this)">
                                        <option value="">Tất cả</option>
                                        <option value="1" {{ request('debt_type') == 1 ? 'selected' : '' }}>Công nợ
                                            trên 01 tháng</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-2 col-lg-2 hp-flex-none">
                                    <label for="" class="col-form-label">Chưa có nhân viên quản lý</label>
                                    <br>

                                    <input type="checkbox" class="form-check-input" name="has_user" value="0"
                                        onchange="filterCustomer(this)" {{ request('has_user') == '0' ? 'checked' : '' }}>
                                </div>

                                <div class="col-12 col-md-3 col-lg-3 hp-flex-none">
                                    <button type="button" class="btn btn-primary float-md-end" style="margin-top: 40px"
                                        onclick="exportExcel('xlsx', document.querySelector('.table'));">
                                        Xuất excel
                                    </button>

                                    <button type="button" class="btn btn-primary float-md-end mr-2" data-bs-toggle="modal"
                                        data-bs-target="#create-customer" style="margin-top: 40px">
                                        Tạo Khách Hàng
                                    </button>
                                </div>

                                <div class="col-12 col-md-3 col-lg-3 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc:</label>
                                    <select class="form-control " name="sort_type" onchange="filterCustomer(this)">
                                        <option value="">Tất cả</option>
                                        <option value="not_use_1_month"
                                            {{ request('sort_type') == 'not_use_1_month' ? 'selected' : '' }}>
                                            Không sử
                                            dụng 1 tháng</option>
                                        <option value="not_use_2_month"
                                            {{ request('sort_type') == 'not_use_2_month' ? 'selected' : '' }}>
                                            Không sử
                                            dụng 2 tháng</option>
                                        <option value="not_use_3_month"
                                            {{ request('sort_type') == 'not_use_3_month' ? 'selected' : '' }}>
                                            Không sử
                                            dụng 3 tháng</option>
                                        <option value="revenue_desc"
                                            {{ request('sort_type') == 'revenue_desc' ? 'selected' : '' }}>
                                            Doanh thu
                                            cao nhất</option>
                                        <option value="debt_desc"
                                            {{ request('sort_type') == 'debt_desc' ? 'selected' : '' }}>
                                            Công nợ
                                            cao nhất</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-2 col-lg-2 hp-flex-none">
                                    <label for="" class="col-form-label">Xếp theo công nợ</label>
                                    <br>

                                    <input type="checkbox" class="form-check-input" name="debt_desc" value="1"
                                        onchange="filterCustomer(this)" {{ request('debt_desc') == '1' ? 'checked' : '' }}>
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
                                    <th scope="col" class="cell-debt">
                                        <b>Công nợ</b><br>
                                        {{ number_format($totalDebt, 0, '', '.') }}
                                    </th>

                                    <th scope="col">
                                        <b>Lịch sử</b><br>
                                        {{ number_format($totalBalance, 0, '', '.') }}
                                    </th>

                                    <th scope="col"><b>Hành động</b></th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    $index = 1;
                                @endphp

                                @foreach ($customers as $customer)
                                    <tr>
                                        <td scope="row">{{ $index++ }}</td>
                                        <td>{{ $customer->name }}</td>
                                        <td>
                                            <a href="{{ route('customer.show', ['customer' => $customer->id]) }}">
                                                {{ $customer->code }}
                                            </a>
                                        </td>
                                        <td>{{ $customer->phone }}</td>
                                        <td>{{ formatAddress($customer->address) }}</td>

                                        <td class="cell-revenue">
                                            {{ number_format($customer->revenue, 0, '', '.') }}
                                        </td>
                                        <td class="cell-cost">
                                            {{ number_format($customer->paid, 0, '', '.') }}
                                        </td>
                                        <td class="cell-debt">
                                            {{ number_format($customer->debt, 0, '', '.') }}
                                        </td>

                                        <td>
                                            <a href="{{ route('transaction.index') }}?customer_id={{ $customer->id }}">
                                                {{ number_format($customer->balance, 0, '', '.') }}
                                            </a>
                                        </td>

                                        <td>
                                            <div class="d-flex">
                                                <a class="btn btn-primary text-nowrap me-6"
                                                    href="{{ route('customer.show', ['customer' => $customer->id]) }}">
                                                    Chi Tiết
                                                </a>

                                                @if (
                                                    $customer->code !== \App\Customer::NONAME_CODE &&
                                                        $customer->code !== \App\Customer::EXPRESS_CODE &&
                                                        showDeleteCustomerBtn() &&
                                                        isAdmin() 
                                                )
                                                    <form method="POST"
                                                        action="{{ route('customer.delete', ['customer' => $customer->id]) }}"
                                                        id="frm-{{ $customer->id }}">
                                                        @csrf
                                                    </form>

                                                    <button class="btn btn-danger"
                                                        onclick="deleteCustomer({{ $customer->id }})">Xóa</button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $paginate->appends(request()->all())->links() }}
                </div>
            </div>
        </div>

        <div class="modal fade" id="create-customer" tabindex="-1" aria-labelledby="create-customer"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="varyingModalLabel">Tạo khách hàng</h5>
                        <button type="button"
                            class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                            data-bs-dismiss="modal" aria-label="Close">
                            <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px;"></i>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('customer.create') }}">
                        @csrf

                        <input type="hidden" name="redirect" value="{{ route('customer.index') }}">

                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="" class="col-form-label">Tên khách hàng:</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    name="name" value="{{ old('name') }}" required autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="" class="col-form-label">Mã khách hàng:</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                    name="code" value="{{ old('code') }}" required autofocus>
                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="" class="col-form-label">SĐT:</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                    name="phone" value="{{ old('phone') }}" required autofocus>
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="" class="col-form-label">Địa chỉ:</label>
                                <input type="text" class="form-control @error('v') is-invalid @enderror"
                                    name="address" value="{{ old('address') }}" required autofocus>
                                @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="" class="col-form-label">Mật khẩu:</label>
                                <input type="text" class="form-control @error('password') is-invalid @enderror"
                                    name="password" value="123456" required autofocus>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-text" data-bs-dismiss="modal">
                                Đóng
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Tạo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
