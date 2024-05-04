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
                    <a href="{{ route('order.index') }}">Vận Đơn</a>
                </li>

                <li class="breadcrumb-item active">
                    Chi Tiết
                </li>
            </ol>
        </nav>
    </div>

    <div class="col-12 col-md-8">
        <div class="hp-page-title">
            <h1 class="mb-8 text-uppercase">CHI TIẾT VẬN ĐƠN {{ $order->code }}</h1>
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
                        <div class="d-flex">
                            @if (is_array($order->images))
                                @foreach ($order->images as $image)
                                    @if (is_string($image))
                                        <div class="mt-6 mr-2">
                                            <img style="max-width: 400px; max-height: 200px" height="200px"
                                                src="{{ asset('files/' . $image) }}">
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-12 mt-16 fix-width scroll-inner">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="3"><b>Tên khách hàng</b></th>
                                    <th scope="col" colspan="3"><b>Mã khách hàng</b></th>
                                    <th scope="col" colspan="3"><b>SĐT</b></th>
                                    <th scope="col" colspan="1"><b>Số điện thoại lái xe</b></th>
                                    <th scope="col" colspan="1"><b>Biển số xe</b></th>
                                    <th scope="col" colspan="4"><b>Hành Động</b></th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td colspan="3">{{ optional($order->customer)->name }}</td>

                                    <td colspan="3">
                                        @if ($order->customer)
                                            <a href="{{ route('customer.show', ['customer' => $order->customer]) }}">
                                                {{ $order->customer->code }}
                                            </a>
                                        @endif
                                    </td>

                                    <td colspan="3">{{ optional($order->customer)->phone }}</td>

                                    <td colspan="1">
                                        {{ $order->driver_phone }}
                                    </td>
                                    <td colspan="1">
                                        {{ $order->license_plate_number }}
                                    </td>
                                    <td colspan="4">
                                        @if (
                                            !auth()->user()->is_seller &&
                                                !auth()->guard('customer')->check())
                                            <button class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#update-order-{{ $order->id }}">
                                                Cập Nhật
                                            </button>
                                        @endif
                                    </td>
                                </tr>

                                <tr class="table-active">
                                    <td></td>
                                    <td>
                                        <b>Mã vận đơn</b>
                                    </td>
                                    <td>
                                        <b>Xe</b>
                                    </td>
                                    <td>
                                        <b>Tên hàng</b>
                                    </td>
                                    <td>
                                        <b>Bill gốc</b>
                                    </td>
                                    <td>
                                        <b>Vị trí hiện tại</b>
                                    </td>
                                    <td>
                                        <b>Tỉ giá</b>
                                    </td>

                                    <td class="cell-cost" colspan="2">
                                        <b>Chi phí Trung Quốc</b>
                                    </td>

                                    <td><b>Thuế</b></td>
                                    <td><b>Cần thanh toán</b></td>
                                    <td><b>Đã thanh toán</b></td>
                                    <td><b>Công nợ</b></td>
                                    <td><b>Số kiện</b></td>
                                    <td><b>Trạng thái</b></td>
                                </tr>

                                <tr class="customer-row" style="cursor: pointer;" data-id="{{ $order->id }}"
                                    data-expand="0" data-expandable="1">
                                    <td rowspan="2">
                                        <i class="fa fa-caret-right customer-row-caret-right "></i>
                                    </td>
                                    <td rowspan="2">
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
                                    <td rowspan="2">
                                        @if ($order->truck)
                                            <a href="{{ route('truck.show', ['truck' => $order->truck]) }}">
                                                {{ $order->truck->name }}
                                            </a>
                                        @endif
                                    </td>

                                    <td rowspan="2">{{ $order->product_name }}</td>
                                    <td rowspan="2">{{ $order->bill }}</td>

                                    <td rowspan="2">
                                        {{ optional(optional($order->truck)->currentLocation)->name }}
                                    </td>
                                    <td rowspan="2">
                                        {{ number_format($order->rmb_to_vnd, 0, '', '.') }}
                                    </td>

                                    <td class="cell-cost" colspan="2">
                                        {{ number_format($order->cost_china_vnd, 0, '', '.') }} <br>
                                        ({{ number_format($order->cost_china, 0, '', '.') }} Tệ)
                                    </td>

                                    <td rowspan="2">
                                        {{ number_format($order->taxes, 0, '', '.') }}
                                    </td>
                                    <td rowspan="2">
                                        {{ number_format($order->revenue, 0, '', '.') }}
                                    </td>

                                    <td rowspan="2">
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
                                                    target="_blank">{{ number_format($payment->amount, 0, '', '.') }} <br>
                                                    ({{ $payment->formatted_created_at }})
                                                </a>

                                                <button class="btn btn-danger float-md-end"
                                                    onclick="deleteRecord({{ $payment->id }})">Xóa</button>
                                            </div>

                                            </br>
                                        @endforeach

                                        @if (!auth()->guard('customer')->check() && !auth()->user()->is_seller)
                                            <button class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#create-payment-{{ $order->id }}">
                                                +
                                            </button>
                                        @endif

                                    </td>

                                    <td rowspan="2">{{ number_format($order->debt, 0, '', '.') }}</td>
                                    <td rowspan="2">{{ $order->packs->sum('quantity') }}</td>
                                    <td rowspan="2">{{ $order->status_text }}</td>
                                </tr>

                                <tr>
                                    <td class="cell-cost">
                                        Ứng <br>
                                        {{ number_format($order->cost_china1_vnd, 0, '', '.') }} <br>
                                        ({{ number_format($order->cost_china1, 0, '', '.') }} Tệ)
                                    </td>
                                    <td class="cell-cost">
                                        Kéo <br>
                                        {{ number_format($order->cost_china2_vnd, 0, '', '.') }} <br>
                                        ({{ number_format($order->cost_china2, 0, '', '.') }} Tệ)
                                    </td>
                                </tr>

                                <tr class="customer-row-header d-none" data-id="{{ $order->id }}">
                                    <td>
                                        @if (
                                            !auth()->user()->is_seller &&
                                                !auth()->guard('customer')->check())
                                            <button class="btn btn-primary text-nowrap" data-bs-toggle="modal"
                                                data-bs-target="#create-pack-{{ $order->id }}">
                                                Thêm kiện
                                            </button>
                                        @endif
                                    </td>
                                    <td><b>Dài</b></td>
                                    <td><b>Rộng</b></td>
                                    <td><b>Cao</b></td>

                                    <td><b>Số kiện</b></td>

                                    <td><b>m³/kiện</b></td>
                                    <td><b>Kg/kiện</b></td>

                                    <td><b>m³</b></td>
                                    <td><b>Kg</b></td>
                                </tr>

                                @foreach ($order->packs as $pack)
                                    <tr class="customer-row-order d-none" data-id="{{ $order->id }}"
                                        data-order-id="{{ $order->id }}">
                                        <td>
                                            <div class="d-flex">
                                                @if (
                                                    !auth()->user()->is_seller &&
                                                        !auth()->guard('customer')->check())
                                                    <button class="btn btn-primary text-nowrap me-6"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#update-pack-{{ $pack->id }}">
                                                        Cập nhật
                                                    </button>

                                                    <form method="POST"
                                                        action="{{ route('pack.delete', ['pack' => $pack]) }}"
                                                        id="frm-del-pack-{{ $pack->id }}">
                                                        @csrf
                                                    </form>
                                                    <button class="btn btn-danger text-nowrap"
                                                        onclick="deletePack({{ $pack->id }})">
                                                        Xoá
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $pack->height }}</td>
                                        <td>{{ $pack->width }}</td>
                                        <td>{{ $pack->depth }}</td>

                                        <td>{{ $pack->quantity }}</td>

                                        <td>{{ number_format($pack->cubic_meters, 4, ',', '.') }}</td>
                                        <td>{{ $pack->weight }}</td>

                                        <td>{{ number_format($pack->cubic_meters * $pack->quantity, 4, ',', '.') }}</td>
                                        <td>{{ $pack->weight * $pack->quantity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @include('components.list-order-declarations', [
                    'declarations' => $order->declarations,
                ])
            </div>
        </div>

        @include('components.modals.create-pack', [
            'redirect' => route('order.show', ['order' => $order]),
            'order' => $order,
        ])

        @foreach ($order->packs as $pack)
            @include('components.modals.update-pack', [
                'redirect' => route('order.show', ['order' => $order]),
                'pack' => $pack,
            ])
        @endforeach

        @include('components.modals.update-order', [
            'order' => $order,
            'customers' => $customers,
            'redirect' => route('order.show', ['order' => $order]),
        ])

        @include('components.modals.create-payment', [
            'order' => $order,
            'redirect' => route('order.show', ['order' => $order]),
        ])

        @include('components.modals.message', [
            'order' => $order,
        ])
    </div>
@endsection
