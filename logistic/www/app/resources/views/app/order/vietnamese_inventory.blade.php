@extends('layouts.app')

@section('title')
    Danh Sách Vận Đơn
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
                    Danh Sách
                </li>
            </ol>
        </nav>
    </div>

    <div class="col-12 col-md-6">
        <div class="hp-page-title">
            <h1 class="mb-8 text-uppercase">Kho {{ isset($location) ? $location->name : 'Việt Nam' }} </h1>
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
                                <div class="col-12 col-md-10">
                                    <div class="row">
                                        <div class="col-12 col-md-3 col-lg-3 hp-flex-none">
                                            <label for="" class="col-form-label">Lọc mã vận đơn:</label>
                                            <select class="form-control select2" name="id"
                                                onchange="filterCustomer(this)">
                                                <option value="">Tìm vận đơn</option>
                                                @foreach ($allOrders as $order)
                                                    <option value="{{ $order->id }}"
                                                        {{ request('id') == $order->id ? 'selected' : '' }}>
                                                        {{ $order->code }} | {{ $order->bill }} |
                                                        {{ optional($order->customer)->code }} |
                                                        {{ $order->product_name ?? 'null' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-12 col-md-3 col-lg-3 hp-flex-none">
                                            <label for="" class="col-form-label">Lọc mã khách hàng:</label>
                                            <select class="form-control select2" name="customer_id"
                                                onchange="filterCustomer(this)">
                                                <option value="">Tìm khách hàng</option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}"
                                                        {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                                        {{ $customer->code }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-12 col-md-3 col-lg-3 hp-flex-none">
                                            <label for="" class="col-form-label">Lọc tháng:</label>
                                            <input type="month" class="form-control" name="month"
                                                value="{{ request('month') }}" onchange="filterCustomer(this)">
                                        </div>

                                        <div class="col-12 col-md-3 col-lg-3 hp-flex-none">
                                            <label for="" class="col-form-label">Trạng thái:</label>
                                            <select class="form-control " name="status_text"
                                                onchange="filterCustomer(this)">
                                                <option value="">Tất cả</option>
                                                <option value="{{ \App\Order::STATUS_TEXT_UNDELIVERED }}"
                                                    {{ request('status_text') == \App\Order::STATUS_TEXT_UNDELIVERED ? 'selected' : '' }}>
                                                    {{ \App\Order::STATUS_TEXT_UNDELIVERED }}
                                                </option>
                                                <option value="{{ \App\Order::STATUS_TEXT_IS_NOT_CALCULATED_COST }}"
                                                    {{ request('status_text') == \App\Order::STATUS_TEXT_IS_NOT_CALCULATED_COST ? 'selected' : '' }}>
                                                    {{ \App\Order::STATUS_TEXT_IS_NOT_CALCULATED_COST }}
                                                </option>
                                            </select>
                                        </div>

                                        <div class="col-12 col-md-3 col-lg-2 hp-flex-none pt-sm-0 pt-md-10">
                                            <select class="form-control" name="per_page" onchange="filterCustomer(this)">
                                                <option value="50">Hiển thị</option>
                                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50
                                                </option>
                                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>
                                                    100
                                                </option>
                                                <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>
                                                    200
                                                </option>
                                                <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>
                                                    500
                                                </option>
                                                <option value="99999"
                                                    {{ request('per_page') == 99999 ? 'selected' : '' }}>Tất
                                                    cả</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                @if (!isSeller())
                                    <div class="col-12 col-md-2 col-lg-2 hp-flex-none">
                                        <button type="button" class="btn btn-primary float-md-end" style="margin-top: 40px"
                                            data-bs-toggle="modal" data-bs-target="#update-status-orders">
                                            Xác nhận trả hàng
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-12 mt-16 fix-width scroll-inner">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="1"><b>Tổng vận đơn</b></th>
                                </tr>
                                <tr>
                                    <th scope="col" colspan="1">{{ $total }}</th>
                                </tr>
                                <tr>
                                    <th scope="col">
                                        <button class="btn btn-primary text-nowrap" data-bs-toggle="modal"
                                            data-bs-target="#calculate-orders-cost">
                                            Tính tiền
                                        </button>
                                    </th>
                                    <th scope="col">
                                        <b>STT</b> <br>
                                        <input type="checkbox" class="form-check-input check-all" onchange="">
                                    </th>

                                    <th scope="col"><b>Ngày tạo</b></th>
                                    <th scope="col"><b>Ngày về kho</b></th>

                                    <th scope="col"><b>Mã vận đơn</b></th>
                                    <th scope="col"><b>Mã khách hàng</b></th>

                                    <th scope="col"><b>Xe</b></th>
                                    <th scope="col"><b>Cân nặng(kg)</b></th>
                                    <th scope="col"><b>Số m³</b></th>

                                    @if (!isSeller())
                                        <th scope="col"
                                            class="delivery-cell {{ request('status_text') == \App\Order::STATUS_TEXT_UNDELIVERED ? 'd-block' : 'd-none' }}">

                                            <b>Trả hàng</b>

                                            <div class="form-check">
                                                <input class="form-check-input check-all-delivery-input" type="checkbox">
                                            </div>
                                        </th>

                                        <th scope="col"
                                            class="{{ request('status_text') == \App\Order::STATUS_TEXT_UNDELIVERED ? 'd-block' : 'd-none' }}">
                                            <b>Tính tiền</b>
                                        </th>
                                    @endif

                                    <th scope="col"><b>Trạng thái</b></th>

                                    <th scope="col"><b>Tên hàng</b></th>
                                    <th scope="col"><b>Bill gốc</b></th>

                                    @if (!isSeller())
                                        <th scope="col"><b>Hành động</b></th>
                                    @endif

                                    <th scope="col"><b>Ghi chú</b></th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($orders as $index => $order)
                                    <tr class="customer-row" style="cursor: pointer;" data-id="{{ $order->id }}"
                                        data-expand="0" data-expandable="1">
                                        <td>
                                            <i class="fa fa-caret-right customer-row-caret-right "></i>
                                        </td>
                                        <td scope="row">
                                            {{-- {{ $index + 1 }} --}}
                                            <input type="checkbox" class="form-check-input" name="order_ids[]"
                                                value="{{ $order->id }}">
                                        </td>

                                        <td>{{ $order->created_at_format }}</td>
                                        <td>{{ $order->arrival_date_format }}</td>

                                        <td class="text-truncate" style="max-width: 150px;">
                                            <a href="{{ route('order.show', ['order' => $order]) }}">
                                                {{ $order->code }}
                                            </a>
                                        </td>
                                        <td>
                                            @if ($order->customer)
                                                <a
                                                    href="{{ route('customer.show', ['customer' => $order->customer]) }}">{{ $order->customer->code }}</a>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($order->truck)
                                                <a href="{{ route('truck.show', ['truck' => $order->truck]) }}">
                                                    {{ $order->truck->name }}
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            {{ number_format($order->weight, 1, ',', '.') }}
                                        </td>
                                        <td>
                                            {{ number_format($order->cubic_meters, 2, ',', '.') }}
                                        </td>

                                        @if (!isSeller())
                                            <td
                                                class="delivery-cell {{ request('status_text') == \App\Order::STATUS_TEXT_UNDELIVERED ? 'd-block' : 'd-none' }}">
                                                <div class="form-check">
                                                    <input class="form-check-input delivery-input" type="checkbox"
                                                        value="{{ $order->id }}"
                                                        {{ $order->status == \App\Pack::DELIVERED ? 'checked' : '' }}>
                                                </div>
                                            </td>

                                            <td>
                                                <button class="btn btn-primary text-nowrap" data-bs-toggle="modal"
                                                    data-bs-target="#calculate-order-cost-{{ $order->id }}">
                                                    Tính tiền
                                                </button>
                                            </td>
                                        @endif

                                        <td>
                                            {{ $order->status_text }}
                                        </td>

                                        <td>{{ $order->product_name }}</td>
                                        <td>{{ $order->formatted_bill }}</td>

                                        @if (!auth()->user()->is_seller)
                                            <td>
                                                <div class="d-flex">
                                                    <form method="POST"
                                                        action="{{ route('order.delete', ['order' => $order]) }}"
                                                        id="frm-{{ $order->id }}">
                                                        @csrf
                                                    </form>

                                                    <button class="btn btn-danger text-nowrap mr-2"
                                                        onclick="deleteOrder({{ $order->id }})">
                                                        Xóa
                                                    </button>
                                                </div>
                                            </td>
                                        @endif

                                        <td>
                                            <div class="d-none">{{ $order->note_in_vn_inventory }}</div>

                                            <input type="text" class="form-control"
                                                value="{{ $order->note_in_vn_inventory }}"
                                                oninput="updateNoteInVnInventory({{ $order->id }},$(this).val())">
                                        </td>
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

                        {{ $orders->appends(request()->all())->links() }}
                    </div>
                </div>
            </div>
        </div>

        @include('components.modals.create-order', [
            'redirect' => route('order.index'),
            'truck' => null,
            'customers' => $customers,
        ])

        @foreach ($orders as $order)
            @include('components.modals.calculate-order-cost', [
                'order' => $order,
                'redirect' => route('order.vietnamese_inventory'),
            ])
        @endforeach

        @include('components.modals.update-status-orders')

        @include('components.modals.calculate-orders-cost')
    </div>
@endsection
