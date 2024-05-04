@extends('layouts.app')

@section('title')
    Danh Sách Xe Đã Hoàn Thành
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

                <li class="breadcrumb-item active">Danh Sách Đã Hoàn Thành</li>
            </ol>
        </nav>
    </div>

    <div class="col-12 col-md-8">
        <div class="hp-page-title">
            <h1 class="mb-8 text-uppercase">DANH SÁCH XE Đã Hoàn Thành</h1>
        </div>
    </div>

    <div class="col-12 d-none col-md-4 d-md-block">
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
                <div class="row justify-content-between">
                    <div class="col-12 mt-16">
                        <form action="" method="get" class="filter-trucks">
                            <input type="hidden" name="departure_date_from" value="{{ request('departure_date_from') }}">
                            <input type="hidden" name="departure_date_to" value="{{ request('departure_date_to') }}">
                            <input type="hidden" name="arrival_date_from" value="{{ request('arrival_date_from') }}">
                            <input type="hidden" name="arrival_date_to" value="{{ request('arrival_date_to') }}">
                            <input type="hidden" name="scroll_to" value=".list-trucks">

                            <div class="row g-16 mb-16">
                                @if (!isSeller())
                                    <div class="col-12 col-md-3 col-lg-2 hp-flex-none pt-6">
                                        <button type="button" class="btn btn-primary" style="margin-top: 40px"
                                            data-bs-toggle="modal" data-bs-target="#create-truck">
                                            Thêm Xe
                                        </button>
                                    </div>
                                @endif

                                <div class="col-12 col-md-3 col-lg-2 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc tên:</label>
                                    <input type="text" class="form-control" placeholder="Tên" name="name"
                                        value="{{ request('name') }}" onchange="filterTrucks()" />
                                </div>

                                <div class="col-12 col-md-3 col-lg-2 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc vị trí khởi hành:</label>
                                    <select class="form-control" name="departure_location_id" onchange="filterTrucks()">
                                        <option value="">Chọn vị trí khởi hành</option>
                                        @foreach ($locations as $location)
                                            @if ($location->name !== 'Hoàn Thành' && $location->name !== 'Kho Việt Nam')
                                                <option value="{{ $location->id }}"
                                                    {{ request('departure_location_id') == $location->id ? 'selected' : '' }}>
                                                    {{ $location->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-3 col-lg-2 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc vị trí hiện tại:</label>
                                    <select class="form-control" name="current_location_id" onchange="filterTrucks()">
                                        <option value="">Chọn vị trí hiện tại</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}"
                                                {{ request('current_location_id') == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-3 col-lg-2 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc ngày khởi hành:</label>
                                    <input type="text" name="departure_dates" class="form-control pull-right" />
                                </div>

                                <div class="col-12 col-md-3 col-lg-2 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc ngày về kho:</label>
                                    <input type="text" name="arrival_dates" class="form-control pull-right" />
                                </div>

                                <div class="col-12 col-md-3 col-lg-2 hp-flex-none">
                                    <button type="button" class="btn btn-primary"
                                        onclick="exportExcel('xlsx', document.querySelector('.table'));">
                                        Xuất excel
                                    </button>
                                </div>

                                <div class="col-12 col-md-3 col-lg-2 hp-flex-none">
                                    <select class="form-control " name="shipping_method_id" onchange="filterTrucks()">
                                        <option value="">Phương Thức Đi Hàng</option>
                                        @foreach ($shippingMethods as $shippingMethod)
                                            <option value="{{ $shippingMethod->id }}"
                                                {{ request('shipping_method_id') == $shippingMethod->id ? 'selected' : '' }}>
                                                {{ $shippingMethod->name }}
                                            </option>
                                        @endforeach
                                    </select>
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
                                    {{-- <th scope="col"><b>STT</b></th> --}}

                                    <th scope="col"><b>Tên</b></th>

                                    <th scope="col"><b>Vị trí khởi hành</b></th>
                                    <th scope="col"><b>Vị trí hiện tại</b></th>

                                    <th scope="col"><b>Ngày khởi hành</b></th>
                                    <th scope="col"><b>Ngày về kho</b></th>

                                    <th scope="col"><b>Số kg</b></th>

                                    <th scope="col"><b>Số m³</b></th>

                                    {{-- <th scope="col"><b>Công nợ</b></th> --}}

                                    <th scope="col"><b>Hành động</b></th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($trucks as $index => $truck)
                                    <tr>
                                        {{-- <td scope="row">{{ $index + 1 }}</td> --}}

                                        <td>
                                            <a href="{{ route('truck.show', ['truck' => $truck]) }}">
                                                {{ $truck->name }}
                                            </a>
                                        </td>

                                        <td>{{ optional($truck->departureLocation)->name }}</td>
                                        <td>{{ optional($truck->currentLocation)->name }}</td>

                                        <td>{{ $truck->departure_date }}</td>
                                        <td>{{ $truck->arrival_date }}</td>

                                        <td>{{ $truck->orders->sum('weight') }}</td>

                                        <td>{{ $truck->cubic_meters }}</td>

                                        {{-- <td>
                                            {{ number_format($truck->debt, 0, ',', '.') }}
                                </td> --}}

                                        <td class="table-actions">
                                            <div class="d-flex">
                                                @if (optional($truck->currentLocation)->name !== 'Hoàn Thành')
                                                    <a class="btn btn-primary text-nowrap me-6"
                                                        href="{{ route('truck.show', ['truck' => $truck]) }}">Chi Tiết</a>

                                                    @if (!isSeller())
                                                        <form method="POST"
                                                            action="{{ route('truck.delete', ['truck' => $truck]) }}"
                                                            id="frm-{{ $truck->id }}">
                                                            @csrf
                                                        </form>
                                                        <button class="btn btn-danger"
                                                            onclick="deleteTruck({{ $truck->id }})">Xóa</button>
                                                    @endif
                                                @else
                                                    <span style="color: green">Hoàn Thành</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $trucks->appends(request()->all())->links() }}
                    {{-- {{ $paginate->appends(request()->all())->links() }} --}}
                </div>
            </div>
        </div>

        @include('components.modals.create-truck', [
            'locations' => $locations,
        ])
    </div>
@endsection
