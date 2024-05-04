@extends('layouts.app')

@section('title')
    Danh Sách Seller
@endsection

@section('content')
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Home</a>
                </li>

                <li class="breadcrumb-item">
                    <a href="{{ route('seller.index') }}">Seller</a>
                </li>

                <li class="breadcrumb-item active">
                    Danh Sách
                </li>
            </ol>
        </nav>
    </div>

    <div class="col-12 col-md-6">
        <div class="hp-page-title">
            <h1 class="mb-8 text-uppercase">DANH SÁCH SELLER</h1>
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
                <form id="filter-frm">
                    <div class="row justify-content-between">
                        <div class="col-12 mt-16">
                            <div class="row g-16 mb-16">
                                <div class="col-12 col-md-4 col-lg-3 hp-flex-none">
                                    <label for="" class="col-form-label">Tháng:</label>
                                    <input type="month" name="month" value="{{ request('month') }}" class="form-control"
                                        onchange="$('#filter-frm').submit()" />
                                </div>

                                <div class="col-12 col-md-2 col-lg-2 hp-flex-none">
                                    <button type="button" class="btn btn-primary float-md-end" style="margin-top: 40px"
                                        onclick="exportExcel('xlsx', document.querySelector('.table'));">
                                        Xuất excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="row justify-content-between">
                    <div class="col-12 mt-16 fix-width scroll-inner">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col"><b>STT</b></th>
                                    <th scope="col"><b>Tên</b></th>
                                    <th scope="col"><b>Email</b></th>
                                    <th scope="col"><b>Tổng hoa hồng</b></th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($sellers as $index => $seller)
                                    <tr class="customer-row" style="cursor: pointer;" data-id="{{ $seller->id }}"
                                        data-expand="0" data-expandable="1">
                                        <td scope="row">
                                            <i class="fa fa-caret-right customer-row-caret-right "></i>
                                        </td>
                                        <td scope="row">{{ $index + 1 }}</td>
                                        <td>{{ $seller->name }}</td>
                                        <td>{{ $seller->email }}</td>
                                        {{-- <td class="cell-revenue">
                                            {{ number_format(
                                                $seller->customers->sum(function ($customer) {
                                                    return $customer->orders->sum('commission');
                                                }),
                                                0,
                                                '',
                                                '.',
                                            ) }}
                                        </td> --}}

                                        <td class="cell-revenue" id="total-commission-{{ $seller->id }}">

                                        </td>

                                        <script>
                                            fetch(`/sellers/{{ $seller->id }}/total_commission`)
                                                .then((resp) => resp.json())
                                                .then((resp) => {
                                                    $('#total-commission-{{ $seller->id }}').text(resp.formatted_total)
                                                })
                                        </script>
                                    </tr>

                                    <tr class="d-none customer-row-header" data-id="{{ $seller->id }}">
                                        <td scope="row"></td>

                                        <td><b>Mã khách hàng</b></td>
                                        <td><b>Mã vận đơn</b></td>
                                        <td><b>Kg</b></td>
                                        <td><b>m³</b></td>
                                        <td><b>Ứng</b></td>
                                        <td><b>Kéo</b></td>
                                        <td><b>Thuế</b></td>
                                        <td><b>Chi phí ngoài</b></td>

                                        <td class="cell-revenue"><b>Thực thu</b></td>
                                        <td class="cell-revenue"><b>Doanh thu</b></td>
                                        <td class="cell-revenue"><b>Cost chống outcome</b></td>
                                        <td class="cell-revenue"><b>Hoa hồng</b></td>
                                    </tr>

                                    @foreach ($seller->customers as $customer)
                                        @foreach ($customer->orders as $order)
                                            <tr class="customer-row-order d-none" data-id="{{ $seller->id }}"
                                                data-order-id="{{ $seller->id }}">

                                                <td scope="row"></td>
                                                <td>
                                                    <a href="{{ route('customer.show', ['customer' => $customer->id]) }}"
                                                        target="_blank">
                                                        {{ $customer->code }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="{{ route('order.show', ['order' => $order->id]) }}"
                                                        target="_blank">
                                                        {{ $order->code }}
                                                    </a>
                                                </td>
                                                <td>{{ number_format($order->weight, 1, ',', '.') }}</td>
                                                <td>{{ number_format($order->cubic_meters, 2, ',', '.') }}</td>
                                                <td>{{ number_format($order->cost_china1_vnd, 0, '', '.') }}</td>
                                                <td>{{ number_format($order->cost_china2_vnd, 0, '', '.') }}</td>
                                                <td>{{ number_format($order->sale_taxes, 0, '', '.') }}
                                                </td>
                                                <td>{{ number_format($order->cost_vietnam, 0, '', '.') }}</td>

                                                <td class="cell-revenue">
                                                    {{ number_format($order->sale_net_income, 0, '', '.') }}
                                                </td>
                                                <td class="cell-revenue">
                                                    {{ number_format($order->sale_revenue, 0, '', '.') }}
                                                </td>
                                                <td class="cell-revenue">
                                                    {{ number_format($order->cost_against_outcome, 0, '', '.') }}
                                                </td>
                                                <td class="cell-revenue">
                                                    {{ number_format($order->sale_commission, 0, '', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $sellers->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
