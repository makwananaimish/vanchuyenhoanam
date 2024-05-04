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

    <div class="col-12 col-md-8">
        <div class="hp-page-title">
            <h1 class="mb-8 text-uppercase">CHI TIẾT KHÁCH HÀNG {{ $customer->name }}</h1>
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
                <div class="row justify-content-between">
                    <div class="col-12 mt-16 fix-width scroll-inner">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="2"><b>Tên khách hàng</b></th>
                                    <th scope="col" colspan="2"><b>Mã khách hàng</b></th>
                                    <th scope="col" colspan="2"><b>SĐT</b></th>
                                    <th scope="col" colspan="5"><b>Hành Động</b></th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td colspan="2">{{ $customer->name }}</td>
                                    <td colspan="2">{{ $customer->code }}</td>
                                    <td colspan="2">{{ $customer->phone }}</td>
                                    <td colspan="5">
                                        <button class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#update-customer-{{ $customer->id }}">
                                            Cập Nhật
                                        </button>
                                    </td>
                                </tr>

                                <tr class="table-active">
                                    <td rowspan="2"></td>
                                    <td rowspan="2"><b>Mã vận đơn</b></td>
                                    <td rowspan="2"><b>Xe</b></td>
                                    <td colspan="3"><b>Doanh Thu</b></td>
                                    <td rowspan="2"><b>Đã Thanh Toán</b></td>
                                    <td rowspan="2"><b>Công Nợ</b></td>
                                    <td rowspan="2"><b>Trả Hàng</b></td>
                                </tr>

                                <tr class="table-active">
                                    <td><b>Tiền vận chuyển</b></td>
                                    <td><b>Tiền thuế</b></td>
                                    <td><b>Chi phí khác</b></td>
                                </tr>

                                @foreach ($customer->orders as $order)
                                    <tr class="customer-row" style="cursor: pointer;" data-id="{{ $order->id }}"
                                        data-expand="0" data-expandable="1">
                                        <td>
                                            <i class="fa fa-caret-right customer-row-caret-right "></i>
                                        </td>
                                        <td>{{ $order->code }}</td>
                                        <td>
                                            <a href="{{ route('truck.show', ['truck' => $order->truck]) }}">
                                                {{ $order->truck->name }}
                                            </a>
                                        </td>
                                        <td>{{ number_format($order->fare, 0, '', '.') }}</td>
                                        <td>{{ number_format($order->taxes, 0, '', '.') }}</td>
                                        <td>{{ number_format($order->other_costs, 0, '', '.') }}</td>
                                        <td>
                                            {{ number_format($order->paid, 0, '', '.') }}
                                            </br>
                                            @foreach ($order->payments as $payment)
                                                <a href="{{ $payment->transaction ? route('transaction.show', ['transaction' => $payment->transaction]) : asset('files/' . $payment->image) }}"
                                                    target="_blank">{{ number_format($payment->amount, 0, '', '.') }}</a>
                                                </br>
                                            @endforeach
                                        </td>
                                        <td>{{ number_format($order->debt, 0, '', '.') }}</td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    {{ $order->status == \App\Pack::DELIVERED ? 'checked' : '' }}
                                                    onclick='updateOrderStatus(this, {{ $order->id }});'>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr class="customer-row-header d-none" data-id="{{ $order->id }}">
                                        <td>
                                        </td>
                                        <td><b>Dài</b></td>
                                        <td><b>Rộng</b></td>
                                        <td><b>Cao</b></td>
                                        <td><b>Cân nặng</b></td>
                                        <td><b>Trả Hàng</b></td>
                                    </tr>

                                    @foreach ($order->packs as $pack)
                                        <tr class="customer-row-order d-none" data-id="{{ $order->id }}"
                                            data-order-id="{{ $order->id }}">
                                            <td>
                                            </td>
                                            <td>{{ $pack->height }}</td>
                                            <td>{{ $pack->width }}</td>
                                            <td>{{ $pack->depth }}</td>
                                            <td>{{ $pack->weight }}</td>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        {{ $pack->status == \App\Pack::DELIVERED ? 'checked' : '' }}
                                                        onclick='updatePackStatus(this, {{ $pack->id }});'>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('components.modals.update-customer', [
    'customer' => $customer,
])
