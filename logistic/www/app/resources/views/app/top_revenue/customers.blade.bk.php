@extends('layouts.app')

@section('title')
    Khách hàng
@endsection

@section('content')
    <div class="div-relationboundingbox">
        <div class="component-1-parent">
            <div class="component-1">
                <b class="danh-sch-khch">DANH SÁCH KHÁCH HÀNG</b>
            </div>
        </div>
        <div class="div-header-parent">
            <div class="div-header">
                <div class="div-name">
                    <div class="relation">Tên khách hàng</div>
                </div>

                <div class="div-sale">
                    <div class="relation1">Ngày khởi tạo</div>
                </div>

                <div class="div-sale1">
                    <div class="relation2">Tình trạng</div>
                </div>

                <div class="div-sale2">
                    <div class="relation3">Doanh số</div>
                </div>
            </div>

            <div class="div-listwrap">
                @foreach ($hunters as $hunter)
                    @if ($hunterId == $hunter->id)
                        @foreach ($hunter->customers as $customer)
                            <div class="div-oneitem2">
                                <div class="div-name">
                                    <div class="relation">{{ $customer->name }}</div>
                                </div>

                                <div class="div-sale">
                                    <div class="relation1">{{ $customer->created_at_formatted }}</div>
                                </div>

                                <div class="div-sale4">
                                    <div class="relation4">●</div>
                                </div>

                                <div class="div-sale5">
                                    <div class="relation7">{{ number_format($customer->revenue, 0, '', '.') }}</div>
                                    <img class="icon-money" alt=""
                                        src="/app-assets/css/top-sale/public/icon-money.svg">
                                </div>
                            </div>
                        @endforeach
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection
