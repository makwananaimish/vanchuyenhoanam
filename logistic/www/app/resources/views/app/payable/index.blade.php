@extends('layouts.app')

@section('title')
    Công Nợ
@endsection

@section('content')
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Home</a>
                </li>

                <li class="breadcrumb-item">
                    <a href="{{ route('customer.index') }}">Công Nợ</a>
                </li>
            </ol>
        </nav>
    </div>

    <div class="col-12 col-md-8">
        <div class="hp-page-title">
            <h1 class="mb-8 text-uppercase">Công nợ</h1>
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
                <div class="row justify-content-between">
                    <div class="col-12 mt-16 fix-width scroll-inner">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col"><b>Hành động</b></th>
                                    <th scope="col" colspan="2"><b>Tên khách hàng</b></th>
                                    <th scope="col" colspan="2"><b>Mã khách hàng</b></th>

                                    <th scope="col"><b>SĐT</b></th>
                                    <th scope="col"><b>Địa chỉ</b></th>

                                    <th scope="col"><b>Nhân viên quản lý</b></th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <button class="btn btn-primary" onclick="payPayables()">Thanh toán</button>
                                            <span class="mt-2 text-center total-debt"></span>
                                        </div>
                                    </td>
                                    <td colspan="2">{{ $customer->name }}</td>
                                    <td colspan="2">
                                        <a href="{{ route('customer.show', ['customer' => $customer]) }}">
                                            {{ $customer->code }}
                                        </a>
                                    </td>
                                    <td>{{ $customer->phone }}</td>
                                    <td>{{ $customer->address }}</td>
                                    <td>
                                        {{ optional($customer->user)->email }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-12 mt-8 fix-width scroll-inner">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="16"><b>Đợi thanh toán</b></th>
                                </tr>

                                <tr class="table-active">
                                    <td rowspan="2"></td>
                                    <td rowspan="2">
                                        <input type="checkbox" class="form-check-input check-all" onchange="">
                                    </td>
                                    <td rowspan="2"><b>Mã vận đơn</b></td>

                                    <td rowspan="2"><b>Ngày nhận hàng</b></td>
                                    <td rowspan="2"><b>Ngày trả hàng</b></td>

                                    <td rowspan="2"><b>Tên sản phẩm</b></td>
                                    <td rowspan="2"><b>Xe</b></td>
                                    <td rowspan="2"><b>Vị trí hiện tại</b></td>
                                    <td rowspan="2"><b>Chi phí Trung Quốc</b></td>
                                    <td rowspan="2"><b>Tổng kg</b></td>
                                    <td rowspan="2"><b>Tổng m³</b></td>
                                    <td rowspan="2"><b>Phí vận chuyển / kg</b></td>
                                    <td rowspan="2"><b>Phí vận chuyển / m³</b></td>
                                    <td colspan="2"><b>Tiền thuế</b></td>
                                    <td rowspan="2"><b>Chi phí Việt Nam</b></td>
                                    <td rowspan="2"><b>Số tiền phải thanh toán</b></td>
                                    <td rowspan="2"><b>Đã thanh toán</b></td>
                                    <td rowspan="2"><b>Công nợ</b></td>
                                    <td rowspan="2"><b>Trạng thái</b></td>
                                    <td rowspan="2"><b>Thanh toán</b></td>
                                </tr>
                                <tr class="table-active">
                                    <td><b>NK/CBPG</b></td>
                                    <td><b>VAT</b></td>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($waitForPayingOrders as $order)
                                    <tr class="customer-row" style="cursor: pointer;" data-id="{{ $order->id }}"
                                        data-expand="0" data-expandable="1">
                                        <td>
                                            <i class="fa fa-caret-right customer-row-caret-right "></i>
                                        </td>

                                        <td>
                                            <input type="checkbox" class="form-check-input" name="order_ids[]"
                                                value="{{ $order->id }}">
                                        </td>

                                        <td>
                                            <a href="{{ route('order.show', ['order' => $order]) }}">
                                                {{ $order->code }}
                                            </a>
                                        </td>

                                        <td>{{ $order->created_at_format }}</td>
                                        <td>{{ $order->delivery_date_format }}</td>

                                        <td>
                                            {{ $order->product_name }}
                                        </td>
                                        <td>
                                            @if ($order->truck)
                                                <a href="{{ route('truck.show', ['truck' => $order->truck]) }}">
                                                    {{ $order->truck->name }}
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            {{ optional(optional($order->truck)->currentLocation)->name }}
                                        </td>
                                        <td>{{ number_format($order->cost_china_vnd, 0, '', '.') }}</td>
                                        <td>{{ number_format($order->weight, 1, ',', '.') }}</td>
                                        <td>{{ number_format($order->cubic_meters, 2, ',', '.') }}</td>
                                        <td>{{ number_format($order->fare_unit_by_weight, 0, '', '.') }}</td>
                                        <td>{{ number_format($order->fare_unit_by_cubic_meters, 0, '', '.') }}</td>
                                        <td>{{ number_format($order->taxes1, 0, '', '.') }}</td>
                                        <td>{{ number_format($order->taxes2, 0, '', '.') }}</td>
                                        <td>{{ number_format($order->cost_vietnam, 0, '', '.') }}</td>
                                        <td>{{ number_format($order->revenue, 0, '', '.') }}</td>
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
                                        <td>{{ $order->status_text }}</td>
                                    </tr>

                                    <tr class="customer-row-header d-none" data-id="{{ $order->id }}">
                                        <td>
                                        </td>
                                        <td><b>Dài</b></td>
                                        <td><b>Rộng</b></td>
                                        <td><b>Cao</b></td>

                                        <td><b>Số kiện</b></td>

                                        <td><b>m³/kiện</b></td>
                                        <td><b>Kg/kiện</b></td>

                                        <td><b>m³</b></td>
                                        <td><b>Kg</b></td>

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

                                            <td>{{ $pack->quantity }}</td>

                                            <td>{{ number_format($pack->cubic_meters, 4, ',', '.') }}</td>
                                            <td>{{ $pack->weight }}</td>

                                            <td>{{ number_format($pack->cubic_meters * $pack->quantity, 4, ',', '.') }}
                                            </td>
                                            <td>{{ $pack->weight * $pack->quantity }}</td>

                                            <td>
                                                @if ($pack->can_delivery)
                                                    @if ($pack->status == \App\Pack::DELIVERED)
                                                        Đã trả hàng
                                                    @else
                                                        Đang ở kho Việt Nam
                                                    @endif
                                                @endif
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
