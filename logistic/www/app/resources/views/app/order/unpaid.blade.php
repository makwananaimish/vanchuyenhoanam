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
            <h1 class="mb-8 text-uppercase">Đơn hàng chưa tính tiền</h1>
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
                                        <div class="col-12 col-md-3 col-lg-2 hp-flex-none ">
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
                                                <option value="99999" {{ request('per_page') == 99999 ? 'selected' : '' }}>
                                                    Tất
                                                    cả</option>
                                            </select>
                                        </div>
                                    </div>
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

                                    <th scope="col"><b>Khách hàng</b></th>
                                    <th scope="col"><b>Tên hàng</b></th>
                                    <th scope="col"><b>Bill gốc</b></th>

                                    <th scope="col"><b>Mã vận đơn</b></th>
                                    <th scope="col"><b>Mã khách hàng</b></th>
                                    <th scope="col"><b>Xe</b></th>
                                    <th scope="col"><b>Cân nặng(kg)</b></th>
                                    <th scope="col"><b>Số m³</b></th>
                                    <th scope="col" class="d-none delivery-cell"><b>Trả hàng</b></th>
                                    <th scope="col"><b>Hành động</b></th>
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

                                        <td>
                                            @if ($order->customer)
                                                <a
                                                    href="{{ route('customer.show', ['customer' => $order->customer]) }}">{{ $order->customer->name }}</a>
                                            @endif
                                        </td>
                                        <td>{{ $order->product_name }}</td>
                                        <td>{{ $order->formatted_bill }}</td>

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
                                        <td class="d-none delivery-cell">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    {{ $order->status == \App\Pack::DELIVERED ? 'checked' : '' }}
                                                    onclick='updateOrderStatus(this, {{ $order->id }});'>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                @if (!auth()->user()->is_seller)
                                                    <button class="btn btn-primary text-nowrap" data-bs-toggle="modal"
                                                        data-bs-target="#calculate-order-cost-{{ $order->id }}">
                                                        Tính tiền
                                                    </button>
                                                @endif
                                            </div>
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

        @foreach ($orders as $order)
            @include('components.modals.calculate-order-cost', [
                'order' => $order,
                'redirect' => route('order.unpaid'),
            ])
        @endforeach

        @include('components.modals.calculate-orders-cost')
    </div>
@endsection

@section('js')
    <script>
        function showDeliveryCell() {
            $('.delivery-cell').removeClass('d-none');
        }
    </script>
@endsection
