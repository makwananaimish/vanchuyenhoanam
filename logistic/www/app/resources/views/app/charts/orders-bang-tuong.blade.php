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
                                            <option value="3" {{ request()->get('months') == '3' ? 'selected' : '' }}>3
                                                tháng</option>
                                            <option value="6" {{ request()->get('months') == '6' ? 'selected' : '' }}>6
                                                tháng</option>
                                            <option value="12" {{ request()->get('months') == '12' ? 'selected' : '' }}>
                                                1 năm</option>
                                        </select>
                                    </form>
                                </div>

                                <div id="orders-bang-tuong-chart"></div>
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
