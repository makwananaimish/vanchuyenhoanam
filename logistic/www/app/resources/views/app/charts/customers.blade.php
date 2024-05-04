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
                                    <p class="hp-badge-text">Khách Hàng Kho Bằng Tường</p>
                                </div>

                                <div class="d-flex align-items-end mb-24">
                                    <form id="months">
                                        <select id="select-months" name="months" class="form-control"
                                            onchange="$('#months').submit()">
                                            <option value="3" {{ request()->get('months') == '3' ? 'selected' : '' }}>
                                                3
                                                tháng</option>
                                            <option value="6" {{ request()->get('months') == '6' ? 'selected' : '' }}>6
                                                tháng</option>
                                            <option value="12" {{ request()->get('months') == '12' ? 'selected' : '' }}>
                                                1 năm</option>
                                        </select>
                                    </form>
                                </div>

                                <form action="">
                                    <div class="d-flex align-items-end mb-24">
                                        <label for="date" class="col-1 col-form-label">Từ ngày</label>
                                        <div class="col-4">
                                            <div class="input-group date">
                                                <input type="text" name="from" class="form-control datepicker"
                                                    value="{{ $rangeFormatted1 }}" />
                                            </div>
                                        </div>
                                        <label for="date" class="col-1 col-form-label">Đến ngày</label>
                                        <div class="col-4">
                                            <div class="input-group date">
                                                <input type="text" name="to" class="form-control datepicker"
                                                    value="{{ $rangeFormatted2 }}" />
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <button type="submit" class="btn btn-primary float-end">Áp dụng</button>
                                        </div>
                                    </div>
                                </form>

                                <div id="customers-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const trucksGroup = {!! json_encode($trucksGroup, JSON_PRETTY_PRINT) !!};
    </script>
@endsection
