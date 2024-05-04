@extends('layouts.app')

@section('title')
    Danh Sách Khách Hàng
@endsection

@section('content')
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Home</a>
                </li>

                <li class="breadcrumb-item active">
                    Lịch Sử Chỉnh Sửa
                </li>
            </ol>
        </nav>
    </div>

    <div class="col-12 col-md-6">
        <div class="hp-page-title">
            <h1 class="mb-8 text-uppercase">Lịch Sử Chỉnh Sửa</h1>
        </div>
    </div>

    <div class="col-12 d-none col-md-6 d-md-block">
        <div class="hp-page-title-logo d-flex justify-content-end">
            <img src="{{ asset('app-assets/img/logo/logo2@2x.png') }}">
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-between">
                    <div class="col-12 mt-16">
                        <form class="customer-filter-form">
                            <div class="row g-16 mb-16">
                                <div class="col-12 col-md-6 col-lg-6 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc quản trị viên:</label>
                                    <select class="form-control select2" name="causer_id" onchange="filterCustomer(this)">
                                        <option value="">Tìm quản trị viên</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ request('causer_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6 hp-flex-none">
                                    <label for="" class="col-form-label">Lọc mã vận đơn:</label>
                                    <input type="text" name="order_code" value="{{ request('order_code') }}"
                                        class="form-control">
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
                                    <th scope="col" rowspan="2">#</th>
                                    <th scope="col" rowspan="2">Mô tả</th>
                                    <th scope="col" rowspan="2">Đối tượng</th>
                                    <th scope="col" rowspan="2">Người thay đổi</th>
                                    <th scope="col" colspan="3">Sự thay đổi</th>
                                    <th scope="col" rowspan="2">Thời gian</th>
                                </tr>
                                <tr>
                                    <th scope="col">Tên</th>
                                    <th scope="col">Cũ</th>
                                    <th scope="col">Mới</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($activities as $index => $activity)
                                    <tr>
                                        <td scope="row">{{ $index + 1 }}</td>
                                        <td>{{ __("app.{$activity->description}") }}</td>
                                        <td>{{ __('app.' . optional($activity)->subject_type) }}</td>
                                        <td>{{ optional($activity->causer)->name }}</td>
                                        <td>
                                            @if (array_key_exists('attributes', $activity->changes()->all()))
                                                @foreach ($activity->changes()->all()['attributes'] as $key => $item)
                                                    {{ __('app.attributes.' . $key) }} <br>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            @if ($activity->description !== 'deleted')
                                                @if (array_key_exists('old', $activity->changes()->all()))
                                                    @foreach ($activity->changes()->all()['old'] as $key => $item)
                                                        {{ $item }} <br>
                                                    @endforeach
                                                @endif
                                            @else
                                                @if (array_key_exists('attributes', $activity->changes()->all()))
                                                    @foreach ($activity->changes()->all()['attributes'] as $key => $item)
                                                        {{ $item }} <br>
                                                    @endforeach
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if ($activity->description !== 'deleted')
                                                @if (array_key_exists('attributes', $activity->changes()->all()))
                                                    @foreach ($activity->changes()->all()['attributes'] as $key => $item)
                                                        {{ $item }} <br>
                                                    @endforeach
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                try {
                                                    echo \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $activity->created_at)->format('H:i:s d-m-Y');
                                                } catch (\Exception $e) {
                                                }
                                            @endphp
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{ $activities->appends(request()->all())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
