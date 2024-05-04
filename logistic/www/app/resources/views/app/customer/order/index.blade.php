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
                        <form class="customer-filter-form" action="">
                            <div class="row g-16 mb-16">
                                <div class="col-12 col-md-4 col-lg-3 hp-flex-none">
                                    <label for="" class="col-form-label">Kê khai:</label>
                                    <select class="form-control " name="declaration" onchange="filterCustomer(this)">
                                        <option value="">Tất cả</option>
                                        <option value="0" {{ request('declaration') === '0' ? 'selected' : '' }}>Chưa
                                            kê khai</option>
                                        <option value="1" {{ request('declaration') === '1' ? 'selected' : '' }}>Đã
                                            kê khai</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-4 col-lg-3 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc mã vận đơn:</label>
                                    <select class="form-control select2" name="id" onchange="filterCustomer(this)">
                                        <option value="">Tìm vận đơn</option>
                                        @foreach ($orders as $order)
                                            <option value="{{ $order->id }}"
                                                {{ request('id') == $order->id ? 'selected' : '' }}>
                                                {{ $order->code }} | {{ $order->bill }} |
                                                {{ optional($order->customer)->code }} |
                                                {{ $order->product_name ?? 'null' }}
                                            </option>
                                        @endforeach
                                    </select>
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
                                    <th scope="col" colspan="2"><b>Tên khách hàng</b></th>
                                    <th scope="col" colspan="2"><b>Mã khách hàng</b></th>

                                    <th scope="col"><b>SĐT</b></th>
                                    <th scope="col"><b>Địa chỉ</b></th>

                                    <th scope="col"><b>Nhân viên quản lý</b></th>
                                    <th scope="col"><b>Tổng công nợ</b></th>

                                    @if (auth()->guard('web')->check())
                                        <th scope="col" colspan="5"><b>Hành Động</b></th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
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
                                    <td>
                                        {{-- {{ number_format($orders->sum('debt'), 0, '', '.') }} --}}
                                        {{ number_format($customer->debt, 0, '', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                @include('components.list-orders', ['title' => 'Công nợ', 'orders' => $debtOrders])

                <div class="row justify-content-between">
                    <div class="col-12 mt-8 fix-width scroll-inner">
                        <table class="table table-bordered table-china">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="2"><b>Đã nhận hàng bên Trung Quốc</b></th>
                                    <th scope="col" colspan="14">
                                        <button type="button" class="btn btn-primary"
                                            onclick="exportExcel('xlsx', document.querySelector('.table-china'))">
                                            Xuất excel
                                        </button>
                                    </th>
                                </tr>
                                <tr class="table-active">
                                    <td rowspan="2"></td>
                                    <td rowspan="2"><b>Mã vận đơn</b></td>
                                    <td rowspan="2"><b>Kê khai</b></td>

                                    <td rowspan="2"><b>Ngày nhận hàng</b></td>

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
                                </tr>
                                <tr class="table-active">
                                    <td><b>NK/CBPG</b></td>
                                    <td><b>VAT</b></td>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($isReceivedInChinaOrders as $order)
                                    <tr class="customer-row" style="cursor: pointer;" data-id="{{ $order->id }}"
                                        data-expand="0" data-expandable="1">
                                        <td>
                                            <i class="fa fa-caret-right customer-row-caret-right "></i>
                                        </td>
                                        <td>
                                            <a href="{{ route('order.show', ['order' => $order]) }}">
                                                {{ $order->code }}
                                            </a>

                                            <button class="btn bg-warning position-relative" data-bs-toggle="modal"
                                                data-bs-target="#message-{{ $order->id }}">
                                                <div class="position-absolute bg-danger rounded-circle text-white unseen-messages"
                                                    style=" width: 15px; height: 15px; top : -9px; right : -9px"
                                                    data-unseen-messages="{{ $order->unseen_messages }}">
                                                    {{ $order->unseen_messages }}</div>
                                            </button>
                                        </td>
                                        <td>
                                            @include('components.declaration-cell', ['order' => $order])
                                        </td>

                                        <td>{{ $order->created_at }}</td>

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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-12 mt-8 fix-width scroll-inner">
                        <table class="table table-bordered table-undelivery">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="2"><b>Đơn chưa giao</b></th>
                                    <th scope="col" colspan="3">
                                        <button type="button" class="btn btn-primary"
                                            onclick="exportExcel('xlsx', document.querySelector('.table-undelivery'));">
                                            Xuất excel
                                        </button>
                                    </th>
                                    <th scope="col" colspan="11">
                                        <input type="checkbox" class="form-check-input check-all" onchange="">

                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#notify-address" onclick="notifyAddress()">
                                            Báo địa chỉ
                                        </button>
                                    </th>
                                </tr>
                                <tr class="table-active">
                                    <td rowspan="2"></td>
                                    <td rowspan="2"><b>Mã vận đơn</b></td>
                                    <td rowspan="2"><b>Kê khai</b></td>

                                    <td rowspan="2"><b>Ngày nhận hàng</b></td>
                                    <td rowspan="2"><b>Ngày về kho</b></td>

                                    <td rowspan="2"><b>Báo địa chỉ</b></td>

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
                                </tr>

                                <tr class="table-active">
                                    <td><b>NK/CBPG</b></td>
                                    <td><b>VAT</b></td>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($undeliveredOrders as $order)
                                    <tr class="customer-row" style="cursor: pointer;" data-id="{{ $order->id }}"
                                        data-expand="0" data-expandable="1">
                                        <td>
                                            <i class="fa fa-caret-right customer-row-caret-right "></i>
                                        </td>
                                        <td>
                                            <a href="{{ route('order.show', ['order' => $order]) }}">
                                                {{ $order->code }}
                                            </a>

                                            <button class="btn bg-warning position-relative" data-bs-toggle="modal"
                                                data-bs-target="#message-{{ $order->id }}">
                                                <div class="position-absolute bg-danger rounded-circle text-white unseen-messages"
                                                    style=" width: 15px; height: 15px; top : -9px; right : -9px"
                                                    data-unseen-messages="{{ $order->unseen_messages }}">
                                                    {{ $order->unseen_messages }}</div>
                                            </button>
                                        </td>
                                        <td>
                                            @include('components.declaration-cell', ['order' => $order])
                                        </td>

                                        <td>{{ $order->created_at_format }}</td>
                                        <td>{{ optional($order->truck)->arrival_date }}</td>

                                        <td>
                                            @if (optional($order->truck)->arrival_date)
                                                <input type="checkbox" class="form-check-input" name="order_ids[]"
                                                    value="{{ $order->id }}">

                                                {{ $order->is_notified_address ? 'Đã gửi địa chỉ' : '' }}
                                            @endif
                                        </td>

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
                                                @if (optional($order)->can_delivery)
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

                <div class="row justify-content-between">
                    <div class="col-12 mt-8 fix-width scroll-inner">
                        <table class="table table-bordered table-wait-for-pay">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="2"><b>Đợi thanh toán</b></th>
                                    <th scope="col" colspan="14">
                                        <button type="button" class="btn btn-primary"
                                            onclick="exportExcel('xlsx', document.querySelector('.table-wait-for-pay'));">
                                            Xuất excel
                                        </button>
                                    </th>
                                </tr>

                                <tr class="table-active">
                                    <td rowspan="2"></td>
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
                                            <a href="{{ route('order.show', ['order' => $order]) }}">
                                                {{ $order->code }}
                                            </a>

                                            <button class="btn bg-warning position-relative" data-bs-toggle="modal"
                                                data-bs-target="#message-{{ $order->id }}">
                                                <div class="position-absolute bg-danger rounded-circle text-white unseen-messages"
                                                    style=" width: 15px; height: 15px; top : -9px; right : -9px"
                                                    data-unseen-messages="{{ $order->unseen_messages }}">
                                                    {{ $order->unseen_messages }}</div>
                                            </button>
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
                                                @if (optional($order)->can_delivery)
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

                <div class="row justify-content-between">
                    <div class="col-12 mt-8 fix-width scroll-inner">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="10"><b>Tất cả</b></th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr class="table-active">
                                    <td></td>
                                    <td><b>Mã vận đơn</b></td>
                                    <td><b>Tên sản phẩm</b></td>
                                    <td><b>Xe</b></td>
                                    <td><b>Vị trí hiện tại</b></td>
                                    <td><b>Chi phí Trung Quốc</b></td>
                                    <td><b>Tổng kg</b></td>
                                    <td><b>Tổng m³</b></td>
                                    <td><b>Phí vận chuyển / kg</b></td>
                                    <td><b>Phí vận chuyển / m³</b></td>
                                    <td><b>Tiền thuế</b></td>
                                    <td><b>Chi phí Việt Nam</b></td>
                                    <td><b>Số tiền phải thanh toán</b></td>
                                    <td><b>Đã thanh toán</b></td>
                                    <td><b>Công nợ</b></td>
                                    <td><b>Trạng thái</b></td>
                                </tr>

                                @foreach ($orders as $order)
                                    <tr class="customer-row" style="cursor: pointer;" data-id="{{ $order->id }}"
                                        data-expand="0" data-expandable="1">
                                        <td>
                                            <i class="fa fa-caret-right customer-row-caret-right "></i>
                                        </td>
                                        <td>
                                            <a href="{{ route('order.show', ['order' => $order]) }}">
                                                {{ $order->code }}
                                            </a>
                                            <button class="btn bg-warning position-relative" data-bs-toggle="modal"
                                                data-bs-target="#message-{{ $order->id }}">
                                                <div class="position-absolute bg-danger rounded-circle text-white unseen-messages"
                                                    style=" width: 15px; height: 15px; top : -9px; right : -9px"
                                                    data-unseen-messages="{{ $order->unseen_messages }}">
                                                    {{ $order->unseen_messages }}</div>
                                            </button>
                                        </td>
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
                                        <td>{{ number_format($order->taxes, 0, '', '.') }}</td>
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

                                            @if (!auth()->user()->is_seller)
                                                <button class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#create-payment-{{ $order->id }}">
                                                    +
                                                </button>
                                            @endif
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
                                                @if (auth('customer')->check())
                                                    @if ($order->can_delivery)
                                                        @if ($pack->status == \App\Pack::DELIVERED)
                                                            Đã trả hàng
                                                        @else
                                                            Đang ở kho Việt Nam
                                                        @endif
                                                    @endif
                                                @else
                                                    @if (auth()->check())
                                                        @if ($order->can_delivery)
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    {{ $pack->status == \App\Pack::DELIVERED ? 'checked' : '' }}
                                                                    onclick='updatePackStatus(this, {{ $pack->id }});'>
                                                            </div>
                                                        @endif
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

        @include('components.modals.create-order-declaration')

        @include('components.modals.notify-address', [
            'customer' => $customer,
        ])

        @foreach ($isReceivedInChinaOrders as $order)
            @include('components.modals.message', [
                'order' => $order,
            ])
        @endforeach

        @foreach ($undeliveredOrders as $order)
            @include('components.modals.message', [
                'order' => $order,
            ])
        @endforeach

        @foreach ($waitForPayingOrders as $order)
            @include('components.modals.message', [
                'order' => $order,
            ])
        @endforeach

        @foreach ($completedOrders as $order)
            @include('components.modals.message', [
                'order' => $order,
            ])
        @endforeach
    </div>
@endsection
