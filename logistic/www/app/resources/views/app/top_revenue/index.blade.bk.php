@extends('layouts.app')

@section('title')
    Top doanh thu
@endsection

@section('content')
    <div class="col-12">
        @if (session('message'))
            <div class="alert {{ session('alert-class') }}">
                {{ session('message') }}
            </div>
        @endif
    </div>

    <div class="row g-32">
        
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="component-11" style="height: auto">
                        <div class="div-title1">
                            <b class="top-doanh-thu1">TOP DOANH THU</b>

                            <div class="div-title2">
                                <form id="filter-frm">
                                    <input type="month" name="month" value="{{ request('month') }}" class="form-control"
                                        onchange="$('#filter-frm').submit()" />
                                </form>
                            </div>
                        </div>

                        {{-- <div class="div-latestaccess">
                        <div class="div-relationboundingbox1">
                            <div class="component-1-wrapper">
                                <div class="component-12">
                                    <b class="top-hunter-bonus">TOP HUNTER BONUS</b>
                                </div>
                            </div>

                            <div class="div-header-group">
                                <div class="div-header1">
                                    <div class="div-stt">
                                        <div class="relation60">STT</div>
                                    </div>
                                    <div class="div-name15">
                                        <div class="relation61">Tên</div>
                                    </div>
                                    <div class="div-sale45">
                                        <div class="relation62">Doanh số</div>
                                    </div>
                                </div>

                                <div class="div-listwrap1">
                                    @php
                                        $index = 0;
                                    @endphp

                                    @foreach ($hunters as $hunter)
                                        <div class="div-oneitem">
                                            <div class="div-stt-wrapper">
                                                @if ($index === 0)
                                                    <img src="/assets/img/1.png" class="div-stt-icon">
                                                @endif

                                                @if ($index === 1)
                                                    <img src="/assets/img/gems.png" class="div-stt-icon">
                                                @endif

                                                @if ($index === 2)
                                                    <img src="/assets/img/oval.png" class="div-stt-icon">
                                                @endif

                                                @if ($index > 2)
                                                    <div class="div-stt-icon" style="text-align:center">
                                                        <b>{{ $index + 1 }}</b>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="div-name16">
                                                <div class="relation63">
                                                    <a
                                                        href="{{ route('top_revenue.customers', ['hunterId' => $hunter->id]) }}?month={{ request('month') }}">{{ $hunter->name }}</a>
                                                </div>
                                            </div>
                                            <div class="div-sale46">
                                                <div class="relation64">{{ number_format($hunter->revenue, 0, '', '.') }}
                                                </div>
                                            </div>
                                            <img class="icon-money14" alt=""
                                                src="/app-assets/css/top-sale/public/icon-money1.svg" />
                                        </div>

                                        @php
                                            $index++;
                                        @endphp
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="div-relationboundingbox1">
                            <div class="component-1-wrapper">
                                <div class="component-12">
                                    <b class="top-hunter-bonus">TOP SALES</b>
                                </div>
                            </div>

                            <div class="div-header-group">
                                <div class="div-header1">
                                    <div class="div-stt">
                                        <div class="relation60">STT</div>
                                    </div>
                                    <div class="div-name15">
                                        <div class="relation61">Tên</div>
                                    </div>
                                    <div class="div-sale45">
                                        <div class="relation62">Doanh số</div>
                                    </div>
                                </div>

                                <div class="div-listwrap1">
                                    @php
                                        $index = 0;
                                    @endphp

                                    @foreach ($topSale as $seller)
                                        <div class="div-oneitem">
                                            <div class="div-stt-wrapper">
                                                @if ($index === 0)
                                                    <img src="/assets/img/1.png" class="div-stt-icon">
                                                @endif

                                                @if ($index === 1)
                                                    <img src="/assets/img/gems.png" class="div-stt-icon">
                                                @endif

                                                @if ($index === 2)
                                                    <img src="/assets/img/oval.png" class="div-stt-icon">
                                                @endif

                                                @if ($index > 2)
                                                    <div class="div-stt-icon" style="text-align:center">
                                                        <b>{{ $index + 1 }}</b>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="div-name16">
                                                <div class="relation63">{{ $seller->name }}</div>
                                            </div>
                                            <div class="div-sale46">
                                                <div class="relation64">{{ number_format($seller->revenue, 0, '', '.') }}
                                                </div>
                                            </div>
                                            <img class="icon-money14" alt=""
                                                src="/app-assets/css/top-sale/public/icon-money1.svg" />
                                        </div>
                                        @php
                                            $index++;
                                        @endphp
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between">
                        <canvas id="top-hunter-bonus-chart">
                        </canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between">
                        <canvas id="top-sale-chart">
                        </canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

<script>
    const hunters = {!! json_encode($hunters, JSON_PRETTY_PRINT) !!};
    const topSale = {!! json_encode($topSale, JSON_PRETTY_PRINT) !!};
</script>
