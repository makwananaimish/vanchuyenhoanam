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
            <h1 class="mb-8 text-uppercase">DANH SÁCH VẬN ĐƠN</h1>
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
                        <form class="customer-filter-form" action="{{ route('order.express') }}">
                            <div class="row g-16 mb-16">
                                <div class="col-12 col-md-4 col-lg-3 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc mã vận đơn:</label>
                                    <select class="form-control select2" name="id" onchange="filterCustomer(this)">
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

                                <div class="col-12 col-md-3 col-lg-2 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc tháng:</label>
                                    <input type="month" class="form-control" name="month"
                                        value="{{ request('month') }}" onchange="filterCustomer(this)">
                                </div>

                                <div class="col-12 col-md-3 col-lg-2 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc loại vận đơn:</label>
                                    <select class="form-control " name="type" onchange="filterCustomer(this)">
                                        <option value="">Tất cả</option>
                                        <option value="1" {{ request('type') == 1 ? 'selected' : '' }}>Chưa
                                            lên xe
                                        </option>
                                        <option value="2" {{ request('type') == 2 ? 'selected' : '' }}>Đã lên
                                            xe
                                        </option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-3 col-lg-2 hp-flex-none">
                                    <label for="" class="col-form-label">Trạng thái:</label>
                                    <select class="form-control " name="status_text" onchange="filterCustomer(this)">
                                        <option value="">Tất cả</option>
                                        <option value="{{ \App\Order::STATUS_TEXT_UNDELIVERED }}"
                                            {{ request('status_text') == \App\Order::STATUS_TEXT_UNDELIVERED ? 'selected' : '' }}>
                                            {{ \App\Order::STATUS_TEXT_UNDELIVERED }}
                                        </option>
                                        <option value="{{ \App\Order::STATUS_TEXT_DELIVERED }}"
                                            {{ request('status_text') == \App\Order::STATUS_TEXT_DELIVERED ? 'selected' : '' }}>
                                            {{ \App\Order::STATUS_TEXT_DELIVERED }}
                                        </option>
                                        <option value="{{ \App\Order::STATUS_TEXT_WAIT_FOR_PAYING }}"
                                            {{ request('status_text') == \App\Order::STATUS_TEXT_WAIT_FOR_PAYING ? 'selected' : '' }}>
                                            {{ \App\Order::STATUS_TEXT_WAIT_FOR_PAYING }}
                                        </option>
                                        <option value="{{ \App\Order::STATUS_TEXT_IS_NOT_CALCULATED_COST }}"
                                            {{ request('status_text') == \App\Order::STATUS_TEXT_IS_NOT_CALCULATED_COST ? 'selected' : '' }}>
                                            {{ \App\Order::STATUS_TEXT_IS_NOT_CALCULATED_COST }}
                                        </option>
                                        <option value="{{ \App\Order::STATUS_TEXT_COMPLETED }}"
                                            {{ request('status_text') == \App\Order::STATUS_TEXT_COMPLETED ? 'selected' : '' }}>
                                            {{ \App\Order::STATUS_TEXT_COMPLETED }}
                                        </option>
                                    </select>
                                </div>

                                @if (!auth()->user()->is_seller)
                                    <div class="col-12 col-md-2 col-lg-3 hp-flex-none pt-sm-0 pt-md-42 pt-lg-42">
                                        <button type="button" class="btn btn-primary text-nowrap float-md-end"
                                            data-bs-toggle="modal" data-bs-target="#create-order">
                                            Tạo vận đơn
                                        </button>
                                    </div>

                                    <div class="col-12 col-md-4 col-lg-3 hp-flex-none ">
                                        <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal"
                                            data-bs-target="#upload-express-orders">
                                            Upload CHUYENPHATNHANH
                                        </button>
                                    </div>

                                    <div class="col-12 col-md-3 col-lg-2 hp-flex-none ">
                                        <button type="button" class="btn btn-primary text-nowrap"
                                            onclick="exportExcel('xlsx', document.querySelector('.table'));">
                                            Xuất excel
                                        </button>
                                    </div>

                                    <div class="col-12 col-md-3 col-lg-2 hp-flex-none ">
                                        <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal"
                                            data-bs-target="#merge-orders">
                                            Ghép nhiều đơn
                                        </button>
                                    </div>

                                    <div class="col-12 col-md-3 col-lg-2 hp-flex-none ">
                                        <select class="form-control" name="per_page" onchange="filterCustomer(this)">
                                            <option value="50">Hiển thị</option>
                                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50
                                            </option>
                                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100
                                            </option>
                                            <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200
                                            </option>
                                            <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500
                                            </option>
                                            <option value="99999" {{ request('per_page') == 99999 ? 'selected' : '' }}>Tất
                                                cả</option>
                                        </select>
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
                                    <th scope="col"><b>STT</b></th>
                                    <th scope="col"><b>Ngày tạo</b></th>

                                    <th scope="col"><b>Mã khách hàng</b></th>

                                    <th scope="col"><b>Mã vận đơn</b></th>
                                    <td><b>Kê khai</b></td>
                                    <th scope="col"><b>Xe</b></th>
                                    <th scope="col"><b>Kg</b></th>
                                    <th scope="col"><b>m³</b></th>

                                    <th scope="col"><b>Tên hàng</b></th>
                                    <th scope="col"><b>Bill gốc</b></th>

                                    <th scope="col"><b>Trạng thái</b></th>
                                    <th scope="col"><b>Hành động</b></th>

                                    <th scope="col"><b>Ghi chú</b></th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($orders as $index => $order)
                                    <tr>
                                        <td scope="row">{{ $index + 1 }}</td>
                                        <td>{{ $order->created_at_format }}</td>
                                        <td>
                                            @if ($order->customer)
                                                <a
                                                    href="{{ route('customer.show', ['customer' => $order->customer]) }}">{{ $order->customer->code }}</a>
                                            @endif
                                        </td>

                                        <td class="text-truncate" style="max-width: 150px;">
                                            <a href="{{ route('order.show', ['order' => $order]) }}">
                                                {{ $order->code }}
                                            </a>
                                        </td>
                                        <td>
                                            @include('components.declaration-cell', ['order' => $order])
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

                                        <td>{{ $order->product_name }}</td>
                                        <td>{{ $order->formatted_bill }}</td>

                                        <td>
                                            {{ $order->status_text }}
                                        </td>

                                        <td>
                                            <div class="d-flex">
                                                @if (!auth()->user()->is_seller)
                                                    <a class="btn btn-primary text-nowrap me-6"
                                                        href="{{ route('order.show', ['order' => $order]) }}">
                                                        Chi Tiết
                                                    </a>
                                                @endif

                                                @if (
                                                    !auth()->user()->is_seller &&
                                                        !auth()->guard('customer')->check())
                                                    <button class="btn btn-primary text-nowrap me-6" data-bs-toggle="modal"
                                                        data-bs-target="#update-order-{{ $order->id }}">
                                                        Cập Nhật
                                                    </button>
                                                @endif

                                                <form method="POST"
                                                    action="{{ route('order.delete', ['order' => $order]) }}"
                                                    id="frm-{{ $order->id }}">
                                                    @csrf
                                                </form>

                                                @if (!auth()->user()->is_seller)
                                                    <button class="btn btn-danger text-nowrap"
                                                        onclick="deleteOrder({{ $order->id }})">
                                                        Xóa
                                                    </button>
                                                @endif
                                            </div>
                                        </td>

                                        <td>
                                            <div class="d-none">{{ $order->note_in_list }}</div>

                                            <input type="text" class="form-control"
                                                value="{{ $order->note_in_list }}"
                                                oninput="updateNoteInList({{ $order->id }},$(this).val())">
                                        </td>
                                    </tr>
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

        @include('components.modals.upload-express-orders')

        @foreach ($orders as $order)
            @if ($order->is_express)
                @include('components.modals.merge-order', [
                    'order' => $order,
                    'customers' => $customers,
                ])
            @endif

            @include('components.modals.calculate-order-cost', [
                'order' => $order,
                'redirect' => route('order.show', [
                    'order' => $order,
                ]),
            ])

            @include('components.modals.update-order', [
                'order' => $order,
                'customers' => $customers,
                'redirect' => route('order.show', ['order' => $order]),
            ])
        @endforeach

        @include('components.modals.create-order-declaration')

        @include('components.modals.merge-orders', [
            'trucks' => $trucks,
        ])

    </div>
@endsection
