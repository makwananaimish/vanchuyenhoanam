@extends('layouts.app')

@section('title')
    Địa Chỉ Trả Hàng
@endsection

@section('content')
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Home</a>
                </li>

                <li class="breadcrumb-item">
                    <a href="{{ route('order.address') }}">Địa chỉ trả hàng</a>
                </li>
            </ol>
        </nav>
    </div>

    <div class="col-12 col-md-6">
        <div class="hp-page-title">
            <h1 class="mb-8 text-uppercase">Địa chỉ trả hàng</h1>
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
                    <div class="col-12 mt-16 fix-width scroll-inner">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col"><b>STT</b></th>
                                    <th scope="col"><b>Mã khách hàng</b></th>

                                    <th scope="col"><b>Trạng thái</b></th>

                                    <th scope="col"><b>Ngày tạo</b></th>

                                    <th scope="col"><b>Mã vận đơn</b></th>

                                    <th scope="col"><b>Số kg</b></th>
                                    <th scope="col"><b>Số m3</b></th>

                                    <th scope="col"><b>Ngày khai báo</b></th>
                                    <th scope="col"><b>Địa chỉ</b></th>
                                    <th scope="col"><b>Số điện thoại</b></th>
                                    <th scope="col"><b>Tên người nhận</b></th>
                                    <th scope="col"><b>Ghi chú</b></th>

                                    <th scope="col"><b>Số dư</b></th>
                                    <th scope="col"><b>Tổng số nợ</b></th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($addresses as $index => $address)
                                    <tr>
                                        <td scope="row">{{ $index + 1 }}</td>
                                        <td>
                                            <a
                                                href="{{ route('customer.show', ['customer' => optional($address->order)->customer]) }}">
                                                {{ optional(optional($address->order)->customer)->code }}
                                            </a>
                                        </td>

                                        <td>{{ optional($address->order)->status_text }}</td>

                                        <td>{{ $address->created_at }}</td>

                                        <td>
                                            <a
                                                href="{{ route('order.show', ['order' => optional($address->order)->id]) }}">
                                                {{ optional($address->order)->code }}
                                            </a>
                                        </td>

                                        <td>{{ optional($address->order)->weight }}</td>
                                        <td>{{ optional($address->order)->cubic_meters }}</td>

                                        <td>{{ $address->date }}</td>
                                        <td>{{ $address->address }}</td>
                                        <td>{{ $address->phone }}</td>
                                        <td>{{ $address->name }}</td>
                                        <td>{{ $address->note }}</td>

                                        <td>{{ optional(optional($address->order))->customer->balance_formatted }}</td>
                                        <td>
                                            {{ number_format(optional($address->order)->customer->debt, 0, '', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- {{ $addresses->appends(request()->all())->links() }} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
