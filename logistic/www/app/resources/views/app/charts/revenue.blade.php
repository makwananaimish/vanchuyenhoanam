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
