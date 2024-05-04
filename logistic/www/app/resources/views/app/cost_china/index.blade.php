@extends('layouts.app')

@section('title')
    Chi phí TQ
@endsection

@section('content')
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Home</a>
                </li>

                <li class="breadcrumb-item active">
                    Chi phí TQ
                </li>
            </ol>
        </nav>
    </div>

    <div class="col-12 col-md-6">
        <div class="hp-page-title">
            <h1 class="mb-8 text-uppercase">Chi phí TQ</h1>
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
                        <form id="filter-frm">
                            <div class="row g-16 mb-16">
                                <div class="col-12 col-md-3 col-lg-2 hp-flex-none pt-sm-0 pt-md-42">
                                    <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal"
                                        data-bs-target="#create-cost-china-other">
                                        Các khoản chi khác
                                    </button>
                                </div>

                                <div class="col-12 col-md-3 col-lg-2 hp-flex-none pt-sm-0 pt-md-42">
                                    <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal"
                                        data-bs-target="#create-cost-china-top-up">
                                        Nạp quỹ
                                    </button>
                                </div>

                                <div class="col-12 col-md-3 col-lg-2 hp-flex-none pt-sm-0 pt-md-42">
                                    Số dư : {{ number_format($balance, 0, ',', '.') }}
                                </div>

                                <div class="col-12 col-md-3 col-lg-3 hp-flex-none">
                                    <label for="" class="col-form-label">Từ ngày:</label>
                                    <input type="date" name="from_date" value="{{ request('from_date') }}"
                                        class="form-control" onchange="$('#filter-frm').submit()" />
                                </div>

                                <div class="col-12 col-md-3 col-lg-3 hp-flex-none">
                                    <label for="" class="col-form-label">Đến ngày:</label>
                                    <input type="date" name="to_date" value="{{ request('to_date') }}"
                                        class="form-control" onchange="$('#filter-frm').submit()" />
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
                                    <th scope="col"><b>STT</b></th>
                                    <th scope="col"><b>Ngày phát sinh</b></th>
                                    <th scope="col"><b>Nội dung</b></th>
                                    <th scope="col"><b>Ứng</b></th>
                                    <th scope="col"><b>Kéo</b></th>
                                    <th scope="col"><b>Nạp quỹ</b></th>
                                    <th scope="col"><b>Số dư</b></th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($costs as $index => $cost)
                                    <tr>
                                        <td scope="row">{{ $index + 1 }}</td>
                                        <td>{{ $cost->date_format }}</td>
                                        <td>{{ $cost->content }}</td>
                                        <td>
                                            @if ($cost->type === \App\CostChina::TYPE_OTHER)
                                                {{ $cost->amount_format }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($cost->type === \App\CostChina::TYPE_OTHER)
                                                {{ $cost->amount2_format }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($cost->type !== \App\CostChina::TYPE_OTHER)
                                                {{ $cost->amount_format }}
                                            @endif
                                        </td>
                                        <td>{{ $cost->balance_format }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{ $costs->appends(request()->all())->links() }}
                    </div>
                </div>
            </div>
        </div>

        @include('components.modals.create-cost-china-other', [])
        @include('components.modals.create-cost-china-top-up', [])
    </div>
@endsection
