@extends('layouts.app')

@section('title')
    Dashboard
@endsection

@section('content')
    <div class="col-12">
        <div class="row g-32">
            <div class="col flex-grow-1 overflow-hidden">
                <div class="row g-32">
                    <div class="col-12">
                        <div class="row g-32">
                            <div class="col-12 col-md-6 col-xl-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row g-16">
                                            <div class="col-6 hp-flex-none w-auto">
                                                <div
                                                    class="avatar-item d-flex align-items-center justify-content-center avatar-lg bg-primary-4 hp-bg-color-dark-primary rounded-circle">
                                                    <i class="iconly-Curved-Category text-primary hp-text-color-dark-primary-2"
                                                        style="font-size: 24px;"></i>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <h3 class="mb-4 mt-8">
                                                    {{ $totalTrucks }}
                                                </h3>
                                                <p class="hp-p1-body mb-0 text-black-80 hp-text-color-dark-30">
                                                    Tổng Xe
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row g-16">
                                            <div class="col-6 hp-flex-none w-auto">
                                                <div
                                                    class="avatar-item d-flex align-items-center justify-content-center avatar-lg bg-secondary-4 hp-bg-color-dark-secondary rounded-circle">
                                                    <i class="iconly-Light-Buy text-secondary" style="font-size: 24px;"></i>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <h3 class="mb-4 mt-8">
                                                    {{ $totalCustomers }}
                                                </h3>
                                                <p class="hp-p1-body mb-0 text-black-80 hp-text-color-dark-30">
                                                    Tổng Khách Hàng
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row g-16">
                                            <div class="col-6 hp-flex-none w-auto">
                                                <div
                                                    class="avatar-item d-flex align-items-center justify-content-center avatar-lg bg-warning-4 hp-bg-color-dark-warning rounded-circle">
                                                    <i class="iconly-Light-Ticket text-warning"
                                                        style="font-size: 24px;"></i>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <h3 class="mb-4 mt-8">
                                                    {{ $totalOrders }}
                                                </h3>
                                                <p class="hp-p1-body mb-0 text-black-80 hp-text-color-dark-30">
                                                    Tổng Vận Đơn
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row g-16">
                                            <div class="col-6 hp-flex-none w-auto">
                                                <div
                                                    class="avatar-item d-flex align-items-center justify-content-center avatar-lg bg-danger-4 hp-bg-color-dark-danger rounded-circle">
                                                    <i class="iconly-Light-Discount text-danger"
                                                        style="font-size: 24px;"></i>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <h3 class="mb-4 mt-8">
                                                    {{ number_format($totalRevenue, 0, '', '.') }}
                                                </h3>
                                                <p class="hp-p1-body mb-0 text-black-80 hp-text-color-dark-30">
                                                    Doanh Thu
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row g-16">
                                            <div class="col-6 hp-flex-none w-auto">
                                                <div
                                                    class="avatar-item d-flex align-items-center justify-content-center avatar-lg bg-danger-4 hp-bg-color-dark-danger rounded-circle">
                                                    <i class="iconly-Light-Ticket text-danger" style="font-size: 24px;"></i>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <h3 class="mb-4 mt-8">
                                                    {{ number_format($totalCost, 0, '', '.') }}
                                                </h3>
                                                <p class="hp-p1-body mb-0 text-black-80 hp-text-color-dark-30">
                                                    Chi Phí
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row g-16">
                                            <div class="col-6 hp-flex-none w-auto">
                                                <div
                                                    class="avatar-item d-flex align-items-center justify-content-center avatar-lg bg-danger-4 hp-bg-color-dark-danger rounded-circle">
                                                    <i class="iconly-Light-Download text-danger"
                                                        style="font-size: 24px;"></i>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <h3 class="mb-4 mt-8">
                                                    {{ number_format($totalDebt, 0, '', '.') }}
                                                </h3>
                                                <p class="hp-p1-body mb-0 text-black-80 hp-text-color-dark-30">
                                                    Công Nợ
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-end mb-24">
                                    <h4 class="me-8">Biểu Đồ</h4>
                                    <p class="hp-badge-text">Doanh Thu</p>
                                </div>

                                <div class="d-flex align-items-end mb-24">
                                    <form id="months">
                                        <select id="select-months" name="months" class="form-control"
                                            onchange="$('#months').submit()">
                                            <option value="3" {{ request()->get('months') == '3' ? 'selected' : '' }}>3
                                                tháng</option>
                                            <option value="6" {{ request()->get('months') == '6' ? 'selected' : '' }}>6
                                                tháng</option>
                                            <option value="12" {{ request()->get('months') == '12' ? 'selected' : '' }}>
                                                1 năm</option>
                                        </select>
                                    </form>
                                </div>

                                <div id="column-chart2"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-end mb-24">
                                    <h4 class="me-8">Biểu Đồ</h4>
                                    <p class="hp-badge-text">Khách Hàng Kho Bằng Tường</p>
                                </div>

                                <div class="d-flex align-items-end mb-24">
                                    <form id="months">
                                        <select id="select-months" name="months" class="form-control"
                                            onchange="$('#months').submit()">
                                            <option value="3" {{ request()->get('months') == '3' ? 'selected' : '' }}>
                                                3
                                                tháng</option>
                                            <option value="6"
                                                {{ request()->get('months') == '6' ? 'selected' : '' }}>6
                                                tháng</option>
                                            <option value="12"
                                                {{ request()->get('months') == '12' ? 'selected' : '' }}>
                                                1 năm</option>
                                        </select>
                                    </form>
                                </div>

                                <div id="orders-chart"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-end mb-24">
                                    <h4 class="me-8">Biểu Đồ</h4>
                                    <p class="hp-badge-text">Doanh Thu Kho Bằng Tường</p>
                                </div>

                                <div class="d-flex align-items-end mb-24">
                                    <form id="months">
                                        <select id="select-months" name="months" class="form-control"
                                            onchange="$('#months').submit()">
                                            <option value="3"
                                                {{ request()->get('months') == '3' ? 'selected' : '' }}>3
                                                tháng</option>
                                            <option value="6"
                                                {{ request()->get('months') == '6' ? 'selected' : '' }}>6
                                                tháng</option>
                                            <option value="12"
                                                {{ request()->get('months') == '12' ? 'selected' : '' }}>
                                                1 năm</option>
                                        </select>
                                    </form>
                                </div>

                                <div id="revenue-chart"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-end mb-24">
                                    <h4 class="me-8">Biểu Đồ</h4>
                                    <p class="hp-badge-text">Vận Đơn Bằng Tường/ Tổng</p>
                                </div>

                                <div class="d-flex align-items-end mb-24">
                                    <form id="months">
                                        <select id="select-months" name="months" class="form-control"
                                            onchange="$('#months').submit()">
                                            <option value="3"
                                                {{ request()->get('months') == '3' ? 'selected' : '' }}>3
                                                tháng</option>
                                            <option value="6"
                                                {{ request()->get('months') == '6' ? 'selected' : '' }}>6
                                                tháng</option>
                                            <option value="12"
                                                {{ request()->get('months') == '12' ? 'selected' : '' }}>
                                                1 năm</option>
                                        </select>
                                    </form>
                                </div>

                                <div id="orders-bang-tuong-chart"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-end mb-24">
                                    <h4 class="me-8">Biểu Đồ</h4>
                                    <p class="hp-badge-text">Doanh Thu</p>
                                </div>

                                <form action="">
                                    <div class="d-flex align-items-end mb-24">
                                        <label for="date" class="col-1 col-form-label">Từ ngày</label>
                                        <div class="col-4">
                                            <div class="input-group date">
                                                <input type="text" name="from" class="form-control datepicker"
                                                    value="{{ $revenueChartFromToData['rangeFormatted1'] }}" />
                                            </div>
                                        </div>
                                        <label for="date" class="col-1 col-form-label">Đến ngày</label>
                                        <div class="col-4">
                                            <div class="input-group date">
                                                <input type="text" name="to" class="form-control datepicker"
                                                    value="{{ $revenueChartFromToData['rangeFormatted2'] }}" />
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <button type="submit" class="btn btn-primary float-end">Áp dụng</button>
                                        </div>
                                    </div>
                                </form>

                                <div id="revenue-chart-from-to"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const trucksGroup = {!! json_encode($trucksGroup, JSON_PRETTY_PRINT) !!};

        const trucksGroupBangTuong = {!! json_encode($trucksGroupBangTuong, JSON_PRETTY_PRINT) !!};

        const revenueChartFromToData = {!! json_encode($revenueChartFromToData, JSON_PRETTY_PRINT) !!}
    </script>
@endsection
