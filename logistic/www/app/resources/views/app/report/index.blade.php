@extends('layouts.app')

@section('title')
    Danh Sách Xe
@endsection

@section('content')
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Home</a>
                </li>

                <li class="breadcrumb-item">
                    <a href="{{ route('report.index') }}">Báo Cáo</a>
                </li>
            </ol>
        </nav>
    </div>

    <div class="col-12 col-md-6">
        <div class="hp-page-title">
            <h1 class="mb-8 text-uppercase">BÁO CÁO</h1>
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

    <div class="list-trucks">
        <div class="card">
            <div class="card-body">
                <form id="filter-frm">
                    <div class="row justify-content-between">
                        <div class="col-12 mt-16">
                            <div class="row g-16 mb-16">
                                <div class="col-12 col-md-4 col-lg-3 hp-flex-none">
                                    <label for="" class="col-form-label">Từ tháng:</label>
                                    <input type="month" name="from_month" value="{{ request('from_month') }}"
                                        class="form-control" onchange="$('#filter-frm').submit()" />
                                </div>

                                <div class="col-12 col-md-4 col-lg-3 hp-flex-none">
                                    <label for="" class="col-form-label">Đến tháng:</label>
                                    <input type="month" name="to_month" value="{{ request('to_month') }}"
                                        class="form-control" onchange="$('#filter-frm').submit()" />
                                </div>

                                <div class="col-12 col-md-4 col-lg-3 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc vị trí khởi hành:</label>
                                    <select class="form-control" name="departure_location_id"
                                        onchange="$('#filter-frm').submit()">
                                        <option value="">Chọn vị trí khởi hành</option>
                                        @foreach ($locations as $location)
                                            @if ($location->name !== 'Hoàn Thành' && $location->name !== 'Kho Việt Nam')
                                                <option value="{{ $location->id }}"
                                                    {{ request('departure_location_id') == $location->id ? 'selected' : '' }}>
                                                    {{ $location->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-4 col-lg-3 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc Phương Thức Đi Hàng:</label>
                                    <select class="form-control" name="shipping_method_id"
                                        onchange="$('#filter-frm').submit()">
                                        <option value="">Chọn Phương Thức Đi Hàng</option>
                                        @foreach ($shippingMethods as $shippingMethod)
                                            <option value="{{ $shippingMethod->id }}"
                                                {{ request('shipping_method_id') == $shippingMethod->id ? 'selected' : '' }}>
                                                {{ $shippingMethod->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-2 col-lg-2 hp-flex-none">
                                    <button type="button" class="btn btn-primary"
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
                        <form action="" method="post" id="update-frm">
                            @csrf

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col"></th>

                                        <th scope="col"><b>Tên</b></th>

                                        <th scope="col"><b>Vị trí khởi hành</b></th>
                                        <th scope="col"><b>Vị trí hiện tại</b></th>

                                        <th scope="col"><b>Ngày khởi hành</b></th>
                                        <th scope="col"><b>Ngày về kho</b></th>

                                        <th scope="col"><b>Doanh thu cả xe</b></th>
                                        <th scope="col"><b>Lợi nhuận cả xe</b></th>

                                        <th scope="col"><b>Tổng thuế</b></th>
                                        <th scope="col"><b>Tổng hoa hồng</b></th>

                                        <th scope="col"><b>Hành động</b></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($trucks as $index => $truck)
                                        @php
                                            // $orders = getOrders2($truck->id);
                                            $orders = getOrdersFromView($truck->id);
                                            
                                            $orders2 = \App\Order::with(['packs', 'payments', 'declarations', 'truck', 'customer'])
                                                ->where('truck_id', $truck->id)
                                                ->get();
                                            
                                            $totalTaxes = collect($orders)->sum(function ($order) {
                                                return $order->report_taxes1 + $order->report_taxes2;
                                            });
                                            // $totalSaleRevenue = collect($orders)->sum('report_sale_revenue');
                                            $totalSaleRevenue = 0;
                                            $cubicMeters = $orders->sum('cubic_meters');
                                            $weight = $orders->sum('weight');
                                            
                                            $costPerCubicMeters = $cubicMeters == 0 ? 0 : $orders[0]->truck_total_cost / $cubicMeters;
                                            $costPerWeight = $weight == 0 ? 0 : $orders[0]->truck_total_cost / $weight;
                                            
                                            $revenue = $orders->sum(function ($order) {
                                                return $order->revenue;
                                            });
                                            
                                            // $totalNetIncome = getTotalNetIncome($truck->id);
                                            $totalNetIncome = 0;
                                        @endphp

                                        <tr class="customer-row" style="cursor: pointer;" data-id="{{ $truck->id }}"
                                            data-expand="0" data-expandable="1">
                                            <td scope="row">
                                                <i class="fa fa-caret-right customer-row-caret-right "></i>
                                            </td>

                                            <td>
                                                <b>
                                                    <a href="{{ route('truck.show', ['truck' => $truck->id]) }}">
                                                        {{ $truck->name }}
                                                    </a>
                                                </b>
                                            </td>

                                            <td><b>{{ $truck->departure_location }}</b></td>
                                            <td><b>{{ $truck->current_location }}</b></td>

                                            <td><b>{{ $truck->departure_date }}</b></td>
                                            <td><b>{{ $truck->arrival_date }}</b></td>

                                            <td><b>{{ number_format($revenue, 0, '', '.') }}</b></td>
                                            <td id="total-net-income-{{ $truck->id }}">
                                                <b>{{ number_format($totalNetIncome, 0, '', '.') }}</b>
                                            </td>

                                            <td><b>{{ number_format($totalTaxes, 0, '', '.') }}</b></td>
                                            <td id="total-sale-revenue-{{ $truck->id }}"></td>

                                            <td>
                                                <button type="submit" class="btn btn-primary">Lưu</button>
                                            </td>
                                        </tr>

                                        <tr class="d-none customer-row-header" data-id="{{ $truck->id }}">
                                            <td scope="row" rowspan="2"></td>

                                            <td rowspan="2"><b>Mã khách hàng</b></td>
                                            <td rowspan="2"><b>Mã vận đơn</b></td>
                                            <td rowspan="2"><b>Kg</b></td>
                                            <td rowspan="2"><b>m³</b></td>

                                            <td rowspan="2" class="cell-cost"><b>Chi phí TQ - Ứng</b></td>
                                            <td rowspan="2" class="cell-cost"><b>Chi phí TQ - Kéo</b></td>

                                            <td rowspan="2"><b>Cost/kg</b></td>
                                            <td rowspan="2"><b>Cost/m³</b></td>

                                            <td colspan="2" class="text-center"><b>Thuế</b></td>

                                            <td rowspan="2"><b>Chi phí khác</b></td>

                                            <td rowspan="2"><b>Tổng cost</b></td>

                                            <td rowspan="2"><b>Thực thu</b></td>
                                            <td rowspan="2"><b>Hoa hồng</b></td>
                                            <td rowspan="2"><b>Lãi</b></td>
                                        </tr>

                                        <tr class="customer-row-order d-none" data-id="{{ $truck->id }}"
                                            data-order-id="{{ $truck->id }}">
                                            <td><b>Tiền Thuế NK/Thuế CBPG/ThuếBVMT</b></td>
                                            <td><b>Thuế VAT</b></td>
                                        </tr>

                                        @foreach ($orders as $order)
                                            @php
                                                if (!is_null($order->cost_per_cubic_meters)) {
                                                    $costPerCubicMeters = $order->cost_per_cubic_meters;
                                                }
                                                
                                                if (!is_null($order->cost_per_weight)) {
                                                    $costPerWeight = $order->cost_per_weight;
                                                }
                                                
                                                $totalCostsByCubicMeters = $costPerCubicMeters * $order->cubic_meters;
                                                $totalCostsByWeight = $costPerWeight * $order->weight;
                                                $totalCosts = $totalCostsByCubicMeters > $totalCostsByWeight ? $totalCostsByCubicMeters : $totalCostsByWeight;
                                                $totalCosts += $order->report_taxes1 + $order->report_taxes2 + $order->cost_china1_vnd + $order->cost_china2_vnd;
                                                
                                                // $saleRevenue = 0.04 * ($order->revenue - $order->report_other_cost - $order->cost_china1_vnd - $order->cost_china2_vnd);
                                                $saleRevenue = 0.05 * ($order->revenue - $order->report_other_cost - $order->cost_china1_vnd - $order->cost_china2_vnd);
                                                foreach ($orders2 as $order2) {
                                                    if ($order->id === $order2->id) {
                                                        $saleRevenue = $order2->sale_commission;
                                                    }
                                                }
                                                
                                                $customer = DB::table('customers')
                                                    ->where('id', $order->customer_id)
                                                    ->first();
                                                
                                                $seller = DB::table('users')
                                                    ->where('id', optional($customer)->user_id)
                                                    ->first();
                                                
                                                if ($seller === null) {
                                                    $saleRevenue = 0;
                                                }
                                                
                                                $totalSaleRevenue += $saleRevenue;
                                                
                                                $netIncome = $order->revenue - $totalCosts - $saleRevenue;
                                                $totalNetIncome += $netIncome;
                                            @endphp

                                            <tr class="customer-row-order d-none" data-id="{{ $truck->id }}"
                                                data-order-id="{{ $truck->id }}">

                                                <td scope="row" rowspan="2"></td>

                                                <td rowspan="2">{{ $order->customer_code }}</td>
                                                <td rowspan="2">
                                                    <a href="{{ route('order.show', ['order' => $order->id]) }}">
                                                        {{ $order->code }}
                                                    </a>
                                                </td>
                                                <td rowspan="2">{{ number_format($order->weight, 1, ',', '.') }}</td>
                                                <td rowspan="2">{{ number_format($order->cubic_meters, 2, ',', '.') }}
                                                </td>

                                                <td rowspan="2" class="cell-cost">
                                                    {{ number_format($order->cost_china1_vnd, 0, '', '.') }} <br>
                                                    ({{ number_format($order->cost_china1, 0, '', '.') }} Tệ)
                                                </td>
                                                <td rowspan="2" class="cell-cost">
                                                    {{ number_format($order->cost_china2_vnd, 0, '', '.') }} <br>
                                                    ({{ number_format($order->cost_china2, 0, '', '.') }} Tệ)
                                                </td>

                                                <td rowspan="2">
                                                    <input style="width: 150px" class="form-control input-currency"
                                                        type="text" name="cost_per_weight[{{ $order->id }}]"
                                                        value="{{ number_format($costPerWeight, 0, '', '') }}">
                                                </td>
                                                <td rowspan="2">
                                                    <input style="width: 150px" class="form-control input-currency"
                                                        type="text" name="cost_per_cubic_meters[{{ $order->id }}]"
                                                        value="{{ number_format($costPerCubicMeters, 0, '', '') }}">
                                                </td>

                                                <td colspan="2" class="text-center">
                                                    {{ number_format($order->report_taxes1 + $order->report_taxes2, 0, '', '.') }}
                                                </td>

                                                <td rowspan="2">
                                                    <input style="width: 150px" class="form-control input-currency"
                                                        type="text" name="other_cost[{{ $order->id }}]"
                                                        value="{{ number_format($order->report_other_cost, 0, '', '') }}">
                                                </td>

                                                <td rowspan="2">
                                                    {{ number_format($totalCosts, 0, '', '.') }}
                                                </td>

                                                <td rowspan="2">
                                                    {{ number_format($order->revenue, 0, '', '.') }}
                                                </td>
                                                <td rowspan="2">
                                                    {{ number_format($saleRevenue, 0, '', '.') }}
                                                </td>
                                                <td rowspan="2">
                                                    {{ number_format($netIncome, 0, '', '.') }}
                                                </td>
                                            </tr>

                                            <tr class="customer-row-order d-none" data-id="{{ $truck->id }}"
                                                data-order-id="{{ $truck->id }}">
                                                <td>
                                                    <input style="width: 150px" class="form-control input-currency"
                                                        type="text" name="taxes1[{{ $order->id }}]"
                                                        value="{{ number_format($order->report_taxes1, 0, '', '') }}">
                                                </td>
                                                <td>
                                                    <input style="width: 150px" class="form-control input-currency"
                                                        type="text" name="taxes2[{{ $order->id }}]"
                                                        value="{{ number_format($order->report_taxes2, 0, '', '') }}">
                                                </td>
                                            </tr>
                                        @endforeach

                                        <script>
                                            setTimeout(() => {
                                                $(document).ready(function() {
                                                    let totalSaleRevenue = '{{ number_format($totalSaleRevenue, 0, '', '.') }}';
                                                    let totalNetIncome = '{{ number_format($totalNetIncome, 0, '', '.') }}';

                                                    $('#total-sale-revenue-{{ $truck->id }}').html(`<b>${totalSaleRevenue}</b>`);
                                                    $('#total-net-income-{{ $truck->id }}').html(`<b>${totalNetIncome}</b>`);
                                                });
                                            }, 1000);
                                        </script>
                                    @endforeach
                                </tbody>
                            </table>
                        </form>
                    </div>

                    {{ $trucks->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function goToTruck(thiz, url) {
            if ($(thiz).parents('.customer-row').attr('data-expand') == '1') {
                window.location.href = url;
            }
        }
    </script>
@endsection
