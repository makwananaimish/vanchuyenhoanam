@extends('layouts.app')

@section('title')
    Top doanh thu
@endsection

@section('content')
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Home</a>
                </li>

                <li class="breadcrumb-item">
                    <a href="{{ route('report.index') }}">Top doanh thu</a>
                </li>
            </ol>
        </nav>
    </div>

    <div class="col-12 col-md-6">
        <div class="hp-page-title">
            <h1 class="mb-8 text-uppercase">TOP DOANH THU</h1>
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
                                    <label for="" class="col-form-label">Tháng:</label>
                                    <input type="month" name="month" value="{{ request('month') }}" class="form-control"
                                        onchange="$('#filter-frm').submit()" />
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
                                    <th colspan="3" class="text-center">
                                        <b>
                                            Top Hunter Bonus
                                        </b>
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col" style="width: 70px"><b>STT</b></th>

                                    <th scope="col"><b>Tên</b></th>

                                    <th scope="col"><b>Doanh số</b></th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    $index = 0;
                                @endphp

                                @foreach ($hunters as $hunter)
                                    <tr class="pe-auto" data-bs-toggle="collapse"
                                        data-bs-target="#collapseExample-{{ $hunter->id }}" aria-expanded="false"
                                        aria-controls="collapseExample-{{ $hunter->id }}" style="cursor: pointer;">
                                        <th scope="col" class="text-center" style="width: 70px">
                                            @if ($index === 0)
                                                <img src="/assets/img/diamond.png" width="60px">
                                            @endif

                                            @if ($index === 1)
                                                <img src="/assets/img/gems.png" width="40px">
                                            @endif

                                            @if ($index === 2)
                                                <img src="/assets/img/oval.png" width="30px">
                                            @endif

                                            @if ($index > 2)
                                                <b>{{ $index + 1 }}</b>
                                            @endif
                                        </th>

                                        <th scope="col">
                                            <a href="#">
                                                <b>{{ $hunter->name }}</b>
                                            </a>
                                        </th>

                                        <th scope="col">
                                            <b>{{ number_format($hunter->revenue, 0, '', '.') }}</b>
                                        </th>
                                    </tr>

                                    <tr class="collapse" id="collapseExample-{{ $hunter->id }}">
                                        <th scope="col"></th>

                                        <th scope="col">
                                            <b>Tên khách hàng</b>
                                        </th>

                                        <th scope="col">
                                            <b>Ngày tạo</b>
                                        </th>
                                    </tr>

                                    @foreach ($hunter->customers as $customer)
                                        <tr class="collapse" id="collapseExample-{{ $hunter->id }}">
                                            <th scope="col"></th>

                                            <th scope="col">
                                                <a href="">
                                                    <b>{{ $customer->name }}</b>
                                                </a>
                                            </th>

                                            <th scope="col">
                                                <b>{{ $customer->created_at_formatted }}</b>
                                            </th>
                                        </tr>
                                    @endforeach

                                    @php
                                        $index++;
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-12 mt-16 fix-width scroll-inner">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="3" class="text-center" style="width: 70px">
                                        <b>
                                            Top sale
                                        </b>
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col"><b>STT</b></th>

                                    <th scope="col"><b>Tên</b></th>

                                    <th scope="col"><b>Doanh số</b></th>
                                </tr>
                            </thead>

                            <tbody>
                                {{-- @foreach ($topSellers as $index => $seller)
                                    <tr>
                                        <th scope="col" class="text-center" style="width: 70px">
                                            @if ($index === 0)
                                                <img src="/assets/img/diamond.png" width="60px">
                                            @endif

                                            @if ($index === 1)
                                                <img src="/assets/img/gems.png" width="40px">
                                            @endif

                                            @if ($index === 2)
                                                <img src="/assets/img/oval.png" width="30px">
                                            @endif

                                            @if ($index > 2)
                                                <b>{{ $index + 1 }}</b>
                                            @endif
                                        </th>

                                        <th scope="col">
                                            <a href="#">
                                                <b>{{ $seller->user->name }}</b>
                                            </a>
                                        </th>

                                        <th scope="col">
                                            <b>
                                                {{ number_format($seller->commission, 0, '', '.') }}
                                            </b>
                                        </th>
                                    </tr>
                                @endforeach --}}
                                @php
                                    $index = 0;
                                @endphp
                                @foreach ($topSale as $seller)
                                    <tr>
                                        <th scope="col" class="text-center" style="width: 70px">
                                            @if ($index === 0)
                                                <img src="/assets/img/diamond.png" width="60px">
                                            @endif

                                            @if ($index === 1)
                                                <img src="/assets/img/gems.png" width="40px">
                                            @endif

                                            @if ($index === 2)
                                                <img src="/assets/img/oval.png" width="30px">
                                            @endif

                                            @if ($index > 2)
                                                <b>{{ $index + 1 }}</b>
                                            @endif
                                        </th>

                                        <th scope="col">
                                            <a href="#">
                                                <b>{{ $seller->name }}</b>
                                            </a>
                                        </th>

                                        <th scope="col">
                                            <b>
                                                {{ number_format($seller->revenue, 0, '', '.') }}
                                            </b>
                                        </th>
                                    </tr>

                                    @php
                                        $index++;
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>
        </div>
    </div>
@endsection
