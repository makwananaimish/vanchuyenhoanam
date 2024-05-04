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
                        <form class="frm-filter">
                            <div class="row g-16 mb-16">
                                <div class="col-12 col-md-4 col-lg-4 hp-flex-none">
                                    <label for="" class="col-form-label">Vị trí hiện tại:</label>
                                    <select class="form-control" name="current_location_id"
                                        onchange="$('.frm-filter').submit()">
                                        <option value="">Chọn vị trí</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}"
                                                {{ request('current_location_id') == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-4 col-lg-4 hp-flex-none">
                                    <label for="" class="col-form-label">Trạng thái:</label>
                                    <select class="form-control " name="status_text" onchange="$('.frm-filter').submit()">
                                        <option value="">Tất cả</option>
                                        <option value="{{ \App\Order::STATUS_TEXT_IS_RECEIVED_IN_CHINA }}"
                                            {{ request('status_text') == \App\Order::STATUS_TEXT_IS_RECEIVED_IN_CHINA ? 'selected' : '' }}>
                                            {{ \App\Order::STATUS_TEXT_IS_RECEIVED_IN_CHINA }}
                                        </option>
                                        <option value="{{ \App\Order::STATUS_TEXT_ON_TRUCK }}"
                                            {{ request('status_text') == \App\Order::STATUS_TEXT_ON_TRUCK ? 'selected' : '' }}>
                                            {{ \App\Order::STATUS_TEXT_ON_TRUCK }}
                                        </option>
                                        <option value="{{ \App\Order::STATUS_TEXT_IN_VIETNAM }}"
                                            {{ request('status_text') == \App\Order::STATUS_TEXT_IN_VIETNAM ? 'selected' : '' }}>
                                            {{ \App\Order::STATUS_TEXT_IN_VIETNAM }}
                                        </option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-4 col-lg-4 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc mã vận đơn:</label>
                                    <select class="form-control select2" name="id"
                                        onchange="$('.frm-filter').submit()">
                                        <option value="">Tìm vận đơn</option>

                                        @foreach ($customer->orders as $order)
                                            <option value="{{ $order->id }}"
                                                {{ request('id') == $order->id ? 'selected' : '' }}>
                                                {{ $order->code }} | {{ $order->bill }} |
                                                {{ $customer->code }} |
                                                {{ $order->product_name ?? 'null' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-4 col-lg-4 hp-flex-none">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="show_completed" value="1"
                                            onchange="$('.frm-filter').submit()"
                                            {{ request('show_completed') == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="flexRadioDefault1">
                                            Đơn hoàn thành
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @include('components.list-orders', ['title' => 'Công nợ', 'orders' => $debtOrders])

                <div class="row justify-content-between">
                    <div class="col-12 mt-16 fix-width scroll-inner">
                        <table class="table table-bordered table-undelivered-orders">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="2"><b>Tên khách hàng</b></th>
                                    <th scope="col" colspan="2"><b>Mã khách hàng</b></th>
                                    <th scope="col" colspan="2"><b>Id khách hàng</b></th>

                                    <th scope="col"><b>SĐT</b></th>
                                    <th scope="col"><b>Địa chỉ</b></th>

                                    <th scope="col" colspan="2"><b>Nhân viên quản lý</b></th>
                                    <th scope="col" colspan="2"><b>Công nợ đơn đã tính tiền</b></th>

                                    @if (auth()->guard('web')->check())
                                        <th scope="col" colspan="3"><b>Hành Động</b></th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td colspan="2">{{ $customer->name }}</td>
                                    <td colspan="2">
                                        <a href="">
                                            {{ $customer->code }}
                                        </a>
                                    </td>
                                    <td colspan="2">
                                        <a href="">
                                            {{ $customer->id }}
                                        </a>
                                    </td>

                                    <td>{{ $customer->phone }}</td>
                                    <td>{{ $customer->address }}</td>

                                    @if (auth()->guard('web')->check())
                                        <td colspan="2">
                                            @if (isSeller())
                                                {{ optional($customer->user)->name }}({{ optional($customer->user)->email }})
                                            @else
                                                <form method="POST"
                                                    action="{{ route('customer.update_user', ['customer' => $customer]) }}"
                                                    id="update-user-frm">
                                                    @csrf

                                                    <select name="user_id" onchange="$('#update-user-frm').submit()"
                                                        class="form-control">
                                                        <option value="">Chọn nhân viên</option>
                                                        @foreach ($users as $user)
                                                            <option value="{{ $user->id }}"
                                                                {{ $customer->user_id === $user->id ? 'selected' : '' }}>
                                                                {{ $user->email }}</option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            @endif
                                        </td>
                                    @else
                                        <td colspan="2">
                                            {{ optional($customer->user)->email }}
                                        </td>
                                    @endif

                                    <td colspan="2">
                                        {{-- {{ number_format($debt, 0, ',', '.') }} --}}
                                        {{ number_format($customer->debt, 0, ',', '.') }}
                                    </td>

                                    @if (auth()->guard('web')->check())
                                        <td colspan="1">
                                            <button class="btn btn-primary text-nowrap" data-bs-toggle="modal"
                                                data-bs-target="#update-customer-{{ $customer->id }}">
                                                Cập Nhật
                                            </button>
                                        </td>
                                        <td colspan="4">
                                            <button type="button" class="btn btn-primary text-nowrap"
                                                onclick="exportExcel('xlsx', document.querySelector('.table-undelivered-orders'));">
                                                Xuất excel
                                            </button>
                                        </td>
                                    @endif
                                </tr>

                                <tr class="table-active">
                                    <td rowspan="1"></td>
                                    <td rowspan="1"><b>Mã vận đơn</b></td>

                                    <td rowspan="1"><b>Ngày nhận hàng</b></td>
                                    <td rowspan="1"><b>Ngày về kho</b></td>
                                    <td rowspan="1"><b>Ngày trả hàng</b></td>

                                    <td rowspan="1"><b>Xe</b></td>
                                    <td rowspan="1"><b>Vị trí hiện tại</b></td>

                                    <td rowspan="1"><b>Tổng kg</b></td>
                                    <td rowspan="1"><b>Tổng m³</b></td>
                                    <td rowspan="1"><b>Tên hàng</b></td>
                                    <td rowspan="1"><b>Bill gốc</b></td>

                                    <td rowspan="1"><b>Trạng thái</b></td>
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

                                        <td>{{ $order->created_at_format }}</td>
                                        <td>{{ optional($order->truck)->arrival_date }}</td>
                                        <td>{{ $order->delivery_date_format }}</td>

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

                                        <td>{{ number_format($order->weight, 1, ',', '.') }}</td>
                                        <td>{{ number_format($order->cubic_meters, 2, ',', '.') }}</td>
                                        <td>{{ $order->product_name }}</td>
                                        <td>{{ $order->formatted_bill }}</td>

                                        <td>{{ $order->status_text }}</td>
                                    </tr>

                                    <tr class="customer-row-header d-none" data-id="{{ $order->id }}">
                                        <td>
                                        </td>
                                        <td><b>Dài</b></td>
                                        <td><b>Rộng</b></td>
                                        <td><b>Cao</b></td>
                                        <td><b>Số kiện</b></td>
                                        <td><b>Số m³</b></td>
                                        <td><b>Cân nặng</b></td>
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
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-12 mt-16">
                        <form class="frm-filter2">
                            <div class="row g-16 mb-16">
                                <div class="col-12 col-md-3 col-lg-3 hp-flex-none">
                                    <label for="" class="col-form-label">Trạng thái:</label>
                                    <select class="form-control " name="delivered_status_text"
                                        onchange="$('.frm-filter2').submit()">
                                        <option value="">Tất cả</option>
                                        <option value="{{ \App\Order::STATUS_TEXT_WAIT_FOR_PAYING }}"
                                            {{ request('delivered_status_text') == \App\Order::STATUS_TEXT_WAIT_FOR_PAYING ? 'selected' : '' }}>
                                            {{ \App\Order::STATUS_TEXT_WAIT_FOR_PAYING }}
                                        </option>
                                        <option value="{{ \App\Order::STATUS_TEXT_IS_NOT_CALCULATED_COST }}"
                                            {{ request('delivered_status_text') == \App\Order::STATUS_TEXT_IS_NOT_CALCULATED_COST ? 'selected' : '' }}>
                                            {{ \App\Order::STATUS_TEXT_IS_NOT_CALCULATED_COST }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-12 mt-8 fix-width scroll-inner">
                        <table class="table table-bordered table-delivered-orders">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="1"><b>Đơn đã giao</b></th>
                                    <th scope="col" colspan="1">
                                        <button type="button" class="btn btn-primary text-nowrap"
                                            onclick="exportExcel('xlsx', document.querySelector('.table-delivered-orders'));">
                                            Xuất excel
                                        </button>
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr class="table-active">
                                    <td>
                                        <button class="btn btn-primary text-nowrap" data-bs-toggle="modal"
                                            data-bs-target="#calculate-orders-cost">
                                            Tính tiền
                                        </button>
                                    </td>
                                    <th scope="col">
                                        <b>STT</b> <br>
                                        <input type="checkbox" class="form-check-input check-all" onchange="">
                                    </th>
                                    <td><b>Ngày trả hàng</b></td>
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

                                    {{-- <td><b>Đã thanh toán</b></td> --}}
                                    <td><b>Công nợ</b></td>
                                    <td><b>Trạng thái</b></td>
                                </tr>

                                @foreach ($deliveredOrders as $order)
                                    <tr class="customer-row" style="cursor: pointer;" data-id="{{ $order->id }}"
                                        data-expand="0" data-expandable="1">
                                        <td>
                                            <i class="fa fa-caret-right customer-row-caret-right "></i>
                                        </td>
                                        <td scope="row">
                                            <input type="checkbox" class="form-check-input" name="order_ids[]"
                                                value="{{ $order->id }}">
                                        </td>
                                        <td>{{ $order->delivery_date_format }}</td>
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
                                        {{-- <td>
                                            {{ number_format($order->paid, 0, '', '.') }}
                                            </br>

                                            @foreach ($order->payments as $payment)
                                                <div style="width: 180px">
                                                    <form id="frm-{{ $payment->id }}"
                                                        action="{{ route('payment.delete', ['payment' => $payment]) }}"
                                                        method="post">
                                                        @csrf
                                                    </form>

                                                    <a href="{{ $payment->transaction ? route('transaction.show', ['transaction' => $payment->transaction]) : asset('files/' . $payment->image) }}"
                                                        target="_blank">{{ number_format($payment->amount, 0, '', '.') }}
                                                        <br>({{ $payment->formatted_created_at }})
                                                    </a>

                                                    <button class="btn btn-danger float-md-end"
                                                        onclick="deleteRecord({{ $payment->id }})">Xóa</button>
                                                </div>

                                                </br>
                                            @endforeach

                                            @if (!auth()->user()->is_seller)
                                                <button class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#create-payment-{{ $order->id }}">
                                                    +
                                                </button>
                                            @endif
                                        </td> --}}
                                        <td>{{ number_format($order->debt, 0, '', '.') }}</td>

                                        <td>
                                            {{ $order->status_text }} <br>

                                            @if ($order->status_text === \App\Order::STATUS_TEXT_WAIT_FOR_PAYING && !auth()->user()->is_seller)
                                                <button class="btn btn-primary text-nowrap" data-bs-toggle="modal"
                                                    data-bs-target="#update-order-{{ $order->id }}">Cập nhật</button>

                                                <button class="btn btn-primary text-nowrap" data-bs-toggle="modal"
                                                    data-bs-target="#calculate-order-cost-{{ $order->id }}">Sửa
                                                    giá</button>
                                            @endif

                                            @if ($order->status_text === \App\Order::STATUS_TEXT_IS_NOT_CALCULATED_COST && !auth()->user()->is_seller)
                                                <button class="btn btn-primary text-nowrap" data-bs-toggle="modal"
                                                    data-bs-target="#calculate-order-cost-{{ $order->id }}">Tính
                                                    tiền</button>
                                            @endif
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

                @if (request('show_completed') == 1)
                    <div class="row justify-content-between">
                        <div class="col-12 mt-8 fix-width scroll-inner">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col" colspan="10"><b>Đơn hoàn thành</b></th>
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

                                    @foreach ($completeOrders as $order)
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
                @endif

                @if (
                    !isAdmin() &&
                        auth()->user()->role !== \App\User::ROLE_ACCOUNTANT &&
                        auth()->user()->role !== \App\User::ROLE_VN_INVENTORY)
                    <div class="row justify-content-between">
                        <div class="col-12 mt-8 fix-width scroll-inner">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col" colspan="10"><b>Hàng không tên</b></th>
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

                                    @foreach ($noNameOrders as $order)
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
                @endif

                @if (auth()->guard('customer')->check())
                    <div class="row justify-content-between">
                        <div class="col-12 mt-8 fix-width scroll-inner">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col" colspan="10"><b>Hàng {{ \App\Customer::EXPRESS_CODE }}</b>
                                        </th>
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

                                    @foreach ($expressOrders as $order)
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
                @endif


            </div>
        </div>

        @foreach ($deliveredOrders as $order)
            @if ($order->status_text === \App\Order::STATUS_TEXT_WAIT_FOR_PAYING)
                @include('components.modals.update-order', [
                    'order' => $order,
                    'customers' => $customers,
                    'redirect' => route('customer.show', [
                        'customer' => $customer,
                    ]),
                ])
            @endif


            @include('components.modals.calculate-order-cost', [
                'order' => $order,
                'redirect' => route('customer.show', [
                    'customer' => $customer,
                ]),
            ])
        @endforeach

        @include('components.modals.update-customer', [
            'customer' => $customer,
        ])

        @if (auth()->guard('web')->check())
            @foreach ($customer->orders as $order)
                @include('components.modals.create-payment', [
                    'order' => $order,
                    'redirect' => route('customer.show', ['customer' => $customer]),
                ])
            @endforeach
        @endif

        @foreach ($orders as $order)
            @include('components.modals.message', [
                'order' => $order,
            ])
        @endforeach

        @foreach ($deliveredOrders as $order)
            @include('components.modals.message', [
                'order' => $order,
            ])
        @endforeach

        @foreach ($completeOrders as $order)
            @include('components.modals.message', [
                'order' => $order,
            ])
        @endforeach

        @foreach ($noNameOrders as $order)
            @include('components.modals.message', [
                'order' => $order,
            ])
        @endforeach

        @foreach ($expressOrders as $order)
            @include('components.modals.message', [
                'order' => $order,
            ])
        @endforeach

        @include('components.modals.calculate-orders-cost')
    </div>
@endsection
