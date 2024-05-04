@extends('layouts.app')

@section('title')
    Chi Tiết Khách Hàng
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
                    Chi Tiết
                </li>
            </ol>
        </nav>
    </div>

    <div class="col-12 col-md-6">
        <div class="hp-page-title">
            <h1 class="mb-8 text-uppercase">Chi Tiết Quản Trị Viên {{ $customer->name }}</h1>
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
                    <div class="col-12 mt-16 fix-width scroll-inner">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="2"><b>Tên khách hàng</b></th>
                                    <th scope="col" colspan="2"><b>Mã khách hàng</b></th>
                                    <th scope="col" colspan="2"><b>SĐT</b></th>
                                    <th scope="col" colspan="4"><b>Hành Động</b></th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr class="customer-row" style="cursor: pointer;" data-id="1" data-expand="0"
                                    data-expandable="1">
                                    <td colspan="2">{{ $customer->name }}</td>
                                    <td colspan="2">{{ $customer->code }}</td>
                                    <td colspan="2">{{ $customer->phone }}</td>
                                    <td colspan="4">
                                        <button class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#update-customer">Cập Nhật</button>
                                    </td>
                                </tr>

                                <tr class="customer-row-header" data-id="1">
                                    <td><b>Xe</b></td>
                                    <td><b>Mã vận đơn</b></td>
                                    <td><b>Bill gốc</b></td>
                                    <td><b>Số khối</b></td>
                                    <td><b>Tiền cước</b></td>
                                    <td><b>Tiền thuế</b></td>
                                    <td><b>Chi phí khác</b></td>
                                    <td><b>Doanh thu</b></td>
                                    <td><b>Đã thanh toán</b></td>
                                    <td><b>Công nợ</b></td>
                                </tr>

                                <tr>
                                    <td>Xe 1</td>
                                    <td>BT2410</td>
                                    <td>Taobao7654</td>
                                    <td>3</td>
                                    <td>500.000.000</td>
                                    <td>200.000.000</td>
                                    <td>10.000.000</td>
                                    <td>710.000.000</td>
                                    <td>
                                        <a href="assets/img/imager_3_103476_700.jpg" target="_blank">100.000.000</a>
                                    </td>
                                    <td>610.000</td>
                                </tr>
                                <tr>
                                    <td>Xe 2</td>
                                    <td>BT2411</td>
                                    <td>Taobao7654</td>
                                    <td>3</td>
                                    <td>500.000.000</td>
                                    <td>200.000.000</td>
                                    <td>10.000.000</td>
                                    <td>710.000.000</td>
                                    <td>
                                        <a href="assets/img/imager_3_103476_700.jpg" target="_blank">100.000.000</a>
                                    </td>
                                    <td>610.000</td>
                                </tr>
                                <tr>
                                    <td>Xe 3</td>
                                    <td>BT2411</td>
                                    <td>Taobao7654</td>
                                    <td>3</td>
                                    <td>500.000.000</td>
                                    <td>200.000.000</td>
                                    <td>10.000.000</td>
                                    <td>710.000.000</td>
                                    <td>
                                        <a href="assets/img/imager_3_103476_700.jpg" target="_blank">100.000.000</a>
                                    </td>
                                    <td>610.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="update-customer" tabindex="-1" aria-labelledby="update-customer" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="varyingModalLabel">Cập nhật khách hàng</h5>
                        <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                            data-bs-dismiss="modal" aria-label="Close">
                            <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px;"></i>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('customer.update', ['customer' => $customer]) }}">
                        @csrf

                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="" class="col-form-label">Tên khách hàng:</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    name="name" value="{{ $customer->name }}" required autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="" class="col-form-label">Mã khách hàng:</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                    name="code" value="{{ $customer->code }}" required autofocus>

                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="" class="col-form-label">SĐT:</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                    name="phone" value="{{ $customer->phone }}" required autofocus>

                                @error('phone')
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
                                Cập Nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
