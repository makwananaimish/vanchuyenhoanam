@extends('layouts.app')

@section('title')
    Chi Tiết Xe
@endsection

@section('content')
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Home</a>
                </li>

                <li class="breadcrumb-item">
                    <a href="{{ route('truck.index') }}">Xe</a>
                </li>

                <li class="breadcrumb-item active">
                    Chi Tiết Xe {{ $truck->name }}
                </li>
            </ol>
        </nav>
    </div>

    <div class="col-12 col-md-6">
        <div class="hp-page-title">
            <h1 class="mb-8 text-uppercase">Chi Tiết Xe {{ $truck->name }}</h1>
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
                    <div class="col-12 mt-16 fix-width scroll-inner">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col"><b>Tên</b></th>

                                    <th scope="col"><b>Vị trí khởi hành</b></th>
                                    <th scope="col"><b>Vị trí hiện tại</b></th>

                                    <th scope="col"><b>Ngày khởi hành</b></th>
                                    <th scope="col"><b>Tổng số kiện</b></th>

                                    <th scope="col"><b>Số kg</b></th>
                                    <th scope="col"><b>Số m³</b></th>

                                    @if (!auth()->user()->is_seller)
                                        <th scope="col" class="cell-cost-per-c3"><b>Cost/m³</b></th>
                                        <th scope="col" colspan="2" class="td-fit"><b>Hành Động</b></th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td class="td-fit text-wrap">
                                        <a href="{{ route('truck.show', ['truck' => $truck]) }}">
                                            {{ $truck->name }}
                                        </a>
                                    </td>

                                    <td>{{ $truck->departureLocation->name }}</td>
                                    <td>{{ $truck->currentLocation->name }}</td>

                                    <td>{{ $truck->departure_date }}</td>
                                    <td>
                                        {{ $truck->orders->sum(function ($order) {
                                            return $order->packs->sum('quantity');
                                        }) }}
                                    </td>

                                    <td>{{ number_format($truck->orders->sum('weight'), 1, ',', '.') }}</td>
                                    <td>{{ number_format($truck->cubic_meters, 2, ',', '.') }}</td>

                                    @if (!auth()->user()->is_seller)
                                        <td class="cell-cost-per-c3">
                                            {{ number_format($truck->cost_per_cubic_meters, 0, '', '.') }}
                                        </td>
                                        <td>
                                            <button class="btn btn-primary text-nowrap" data-bs-toggle="modal"
                                                data-bs-target="#update-truck">
                                                Cập Nhật
                                            </button>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-primary text-nowrap"
                                                onclick="exportExcel('xlsx', document.querySelector('.table'));">
                                                Xuất excel
                                            </button>
                                        </td>
                                    @endif
                                </tr>

                                {{-- @if (!auth()->user()->is_seller)
                                    <tr class="table-active">
                                        <form action="{{ route('truck.add_order', ['truck' => $truck]) }}" method="post">
                                            @csrf
                                            <td colspan="3">
                                                <div>
                                                    <select class="form-control select2" name="order_id">
                                                        <option value="">Chọn vận đơn</option>
                                                        @foreach ($orders as $order)
                                                            <option value="{{ $order->id }}">
                                                                {{ $order->code }} - {{ $order->customer->name }} -
                                                                {{ $order->customer->phone }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="submit" class="btn btn-primary">Thêm</button>
                                            </td>
                                        </form>
                                        <td>
                                            <button class="btn btn-primary text-nowrap" data-bs-toggle="modal"
                                                data-bs-target="#create-order">
                                                Tạo vận đơn
                                            </button>
                                        </td>
                                        <td colspan="7">
                                        </td>
                                    </tr>
                                @endif --}}

                                <tr class="table-active">
                                    <td class="td-fit"></td>

                                    <td class="td-fit"><b>Mã vận đơn</b></td>
                                    <td><b>Mã khách hàng</b></td>
                                    <td><b>Kê khai</b></td>
                                    <td><b>Tên khách hàng</b></td>

                                    <td><b>Số kiện</b></td>

                                    <td><b>Số kg</b></td>
                                    <td><b>Số m³</b></td>

                                    <td><b>Tên hàng</b></td>

                                    @if (!auth()->user()->is_seller)
                                        <td><b>Hành động</b></td>
                                    @endif

                                    <td><b>Ghi chú</b></td>
                                </tr>

                                @foreach ($truck->orders as $order)
                                    <tr class="customer-row" style="cursor: pointer;" data-id="{{ $order->id }}"
                                        data-expand="0" data-expandable="1">
                                        <td>
                                            <i class="fa fa-caret-right customer-row-caret-right "></i>
                                        </td>

                                        <td>
                                            <a href="{{ route('order.show', ['order' => $order]) }}">
                                                {{ $order->code }}
                                            </a>
                                        </td>
                                        <td>
                                            @include('components.declaration-cell', ['order' => $order])
                                        </td>
                                        <td>
                                            @if ($order->customer)
                                                <a href="{{ route('customer.show', ['customer' => $order->customer]) }}">
                                                    {{ $order->customer->code }}
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            {{ optional($order->customer)->name }}
                                        </td>

                                        <td>{{ $order->packs->sum('quantity') }}</td>

                                        <td>{{ number_format($order->weight, 1, ',', '.') }}</td>
                                        <td>{{ number_format($order->cubic_meters, 2, ',', '.') }}</td>

                                        <td>{{ $order->product_name }}</td>

                                        @if (!auth()->user()->is_seller)
                                            <td>
                                                <div class="d-flex">
                                                    <button class="btn btn-primary text-nowrap me-6" data-bs-toggle="modal"
                                                        data-bs-target="#update-order-{{ $order->id }}">
                                                        Cập nhật
                                                    </button>

                                                    <form method="POST"
                                                        action="{{ route('truck.orders.delete', ['order' => $order]) }}"
                                                        id="frm-{{ $order->id }}">
                                                        @csrf
                                                    </form>
                                                    <button class="btn btn-danger"
                                                        onclick="deleteOrder({{ $order->id }})">
                                                        Xoá
                                                    </button>
                                                </div>
                                            </td>
                                        @endif

                                        <td>
                                            <input type="text" class="form-control" value="{{ $order->note_in_truck }}"
                                                oninput="updateNoteInTruck({{ $order->id }},$(this).val())">
                                        </td>
                                    </tr>

                                    <tr class="customer-row-header d-none" data-id="{{ $order->id }}">
                                        @if (!auth()->user()->is_seller)
                                            <td>
                                                <button class="btn btn-primary text-nowrap" data-bs-toggle="modal"
                                                    data-bs-target="#create-pack-{{ $order->id }}">
                                                    Thêm kiện
                                                </button>
                                            </td>
                                        @endif

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
                                            @if (!auth()->user()->is_seller)
                                                <td>
                                                    <div class="d-flex">
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
                                                    </div>
                                                </td>
                                            @endif

                                            <td>{{ $pack->height }}</td>
                                            <td>{{ $pack->width }}</td>
                                            <td>{{ $pack->depth }}</td>

                                            <td>{{ $pack->quantity }}</td>

                                            <td>{{ number_format($pack->cubic_meters, 2, ',', '.') }}</td>
                                            <td>{{ number_format($pack->weight, 1, ',', '.') }}</td>

                                            <td>{{ number_format($pack->cubic_meters * $pack->quantity, 3, ',', '.') }}
                                            </td>
                                            <td>{{ $pack->weight * $pack->quantity }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <form action="{{ route('truck.add_bulk_orders', ['truck' => $truck]) }}" method="post">
                        @csrf

                        <div class="col-12 mt-16 fix-width scroll-inner">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="table-active">
                                        <td>
                                            <input type="checkbox" id="check-all" class="form-check-input">
                                        </td>
                                        <td>
                                            <button class="btn btn-primary text-nowrap">
                                                Thêm
                                            </button>
                                        </td>
                                        <td colspan="3">
                                            <b>Danh mục vận đơn đang nằm tại kho {{ optional($truck->currentLocation)->name }}</b>
                                        </td>
                                    </tr>

                                    <tr class="table-active">
                                        <td class="td-fit"></td>
                                        <td class="td-fit"></td>

                                        <td class="td-fit"><b>Mã vận đơn</b></td>
                                        <td><b>Mã khách hàng</b></td>
                                        <td><b>Kê khai</b></td>
                                        <td><b>Tên khách hàng</b></td>

                                        <td><b>Số kiện</b></td>

                                        <td><b>Số kg</b></td>
                                        <td><b>Số m³</b></td>

                                        <td><b>Tên hàng</b></td>

                                        @if (!auth()->user()->is_seller)
                                            <td><b>Hành động</b></td>
                                        @endif

                                        <td><b>Ghi chú</b></td>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($orders as $order)
                                        <tr class="customer-row" style="cursor: pointer;" data-id="{{ $order->id }}"
                                            data-expand="0" data-expandable="1">
                                            <td>
                                                <input type="checkbox" class="form-check-input" name="order_ids[]"
                                                    value="{{ $order->id }}">
                                            </td>
                                            <td>
                                                <i class="fa fa-caret-right customer-row-caret-right "></i>
                                            </td>

                                            <td>
                                                <a href="{{ route('order.show', ['order' => $order]) }}">
                                                    {{ $order->code }}
                                                </a>
                                            </td>
                                            <td>
                                                @include('components.declaration-cell', [
                                                    'order' => $order,
                                                ])
                                            </td>
                                            <td>
                                                @if ($order->customer)
                                                    <a
                                                        href="{{ route('customer.show', ['customer' => $order->customer]) }}">
                                                        {{ $order->customer->code }}
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                {{ optional($order->customer)->name }}
                                            </td>

                                            <td>{{ $order->packs->sum('quantity') }}</td>

                                            <td>{{ number_format($order->weight, 1, ',', '.') }}</td>
                                            <td>{{ number_format($order->cubic_meters, 2, ',', '.') }}</td>

                                            <td>{{ $order->product_name }}</td>

                                            @if (!auth()->user()->is_seller)
                                                <td>
                                                    <div class="d-flex">
                                                        <button type="button" class="btn btn-primary text-nowrap me-6"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#update-order-{{ $order->id }}">
                                                            Cập nhật
                                                        </button>

                                                        <form method="POST"
                                                            action="{{ route('truck.orders.delete', ['order' => $order]) }}"
                                                            id="frm-{{ $order->id }}">
                                                            @csrf
                                                        </form>
                                                        <button class="btn btn-danger"
                                                            onclick="deleteOrder({{ $order->id }})">
                                                            Xoá
                                                        </button>
                                                    </div>
                                                </td>
                                            @endif

                                            <td>
                                                <input type="text" class="form-control"
                                                    value="{{ $order->note_in_truck }}"
                                                    oninput="updateNoteInTruck({{ $order->id }},$(this).val())">
                                            </td>
                                        </tr>

                                        <tr class="customer-row-header d-none" data-id="{{ $order->id }}">
                                            @if (!auth()->user()->is_seller)
                                                <td>
                                                    <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal"
                                                        data-bs-target="#create-pack-{{ $order->id }}">
                                                        Thêm kiện
                                                    </button>
                                                </td>
                                            @endif

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
                                                @if (!auth()->user()->is_seller)
                                                    <td>
                                                        <div class="d-flex">
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
                                                        </div>
                                                    </td>
                                                @endif

                                                <td>{{ $pack->height }}</td>
                                                <td>{{ $pack->width }}</td>
                                                <td>{{ $pack->depth }}</td>

                                                <td>{{ $pack->quantity }}</td>

                                                <td>{{ number_format($pack->cubic_meters, 2, ',', '.') }}</td>
                                                <td>{{ number_format($pack->weight, 1, ',', '.') }}</td>

                                                <td>{{ number_format($pack->cubic_meters * $pack->quantity, 3, ',', '.') }}
                                                </td>
                                                <td>{{ $pack->weight * $pack->quantity }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        @foreach ($truck->orders as $order)
            @include('components.modals.create-payment', [
                'truck' => $truck,
                'order' => $order,
            ])

            @include('components.modals.update-order', [
                'order' => $order,
                'customers' => $customers,
            ])

            @include('components.modals.create-pack', [
                'truck' => $truck,
                'order' => $order,
            ])

            @foreach ($order->packs as $pack)
                @include('components.modals.update-pack', [
                    'truck' => $truck,
                    'pack' => $pack,
                ])
            @endforeach
        @endforeach

        @foreach ($orders as $order)
            @include('components.modals.create-payment', [
                'truck' => $truck,
                'order' => $order,
            ])

            @include('components.modals.update-order', [
                'order' => $order,
                'customers' => $customers,
            ])

            @include('components.modals.create-pack', [
                'truck' => $truck,
                'order' => $order,
            ])

            @foreach ($order->packs as $pack)
                @include('components.modals.update-pack', [
                    'truck' => $truck,
                    'pack' => $pack,
                ])
            @endforeach
        @endforeach

        @include('components.modals.create-order', [
            'truck' => $truck,
            'customers' => $customers,
        ])

        @include('components.modals.update-truck', [
            'truck' => $truck,
        ])

        @include('components.modals.create-customer', [
            'truck' => $truck,
        ])

        @include('components.modals.create-order-declaration')

    </div>
@endsection
