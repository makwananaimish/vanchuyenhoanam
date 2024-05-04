<div class="row justify-content-between">
    <div class="col-12 mt-8 fix-width scroll-inner">
        <table class="table table-bordered">
            <thead>
                <tr class="table-active">
                    <td><b>{{ $title }}</b></td>
                </tr>
                <tr class="table-active">
                    <td></td>
                    <td><b>Ngày trả hàng</b></td>
                    <td><b>Mã vận đơn</b></td>
                    <td><b>Tên sản phẩm</b></td>
                    <td><b>Xe</b></td>
                    <td><b>Vị trí hiện tại</b></td>
                    <td><b>Chi phí Trung Quốc</b></td>
                    <td><b>Tổng kg</b></td>
                    <td><b>Tổng m³</b></td>
                    <td><b>Phí vận chuyển / kg</b></td>
                    <td><b>Phí vận chuyển / m³</b></td>
                    <td><b>Tiền thuế</b></td>
                    <td><b>Chi phí Việt Nam</b></td>

                    <td><b>Số tiền phải thanh toán</b></td>

                    {{-- <td><b>Phải thanh toán</b></td> --}}
                    <td><b>Công nợ</b></td>
                    <td><b>Trạng thái</b></td>
                </tr>
            </thead>

            <tbody>
                @foreach ($orders as $order)
                    <tr class="customer-row" style="cursor: pointer;" data-id="{{ $order->id }}" data-expand="0"
                        data-expandable="1">
                        <td>
                            <i class="fa fa-caret-right customer-row-caret-right "></i>
                        </td>
                        <td>{{ $order->delivery_date_format }}</td>
                        <td>
                            <a href="{{ route('order.show', ['order' => $order]) }}">
                                {{ $order->code }}
                            </a>
                            <button class="btn bg-warning position-relative" data-bs-toggle="modal"
                                data-bs-target="#message-{{ $order->id }}">
                                <div class="position-absolute bg-danger rounded-circle text-white unseen-messages"
                                    style=" width: 15px; height: 15px; top : -9px; right : -9px"
                                    data-unseen-messages="{{ $order->unseen_messages }}">
                                    {{ $order->unseen_messages }}</div>
                            </button>
                        </td>
                        <td>
                            {{ $order->product_name }}
                        </td>
                        <td>
                            @if ($order->truck)
                                <a href="{{ route('truck.show', ['truck' => $order->truck]) }}">
                                    {{ $order->truck->name }}
                                </a>
                            @endif
                        </td>
                        <td>
                            {{ optional(optional($order->truck)->currentLocation)->name }}
                        </td>
                        <td>{{ number_format($order->cost_china_vnd, 0, '', '.') }}</td>
                        <td>{{ number_format($order->weight, 1, ',', '.') }}</td>
                        <td>{{ number_format($order->cubic_meters, 2, ',', '.') }}</td>
                        <td>{{ number_format($order->fare_unit_by_weight, 0, '', '.') }}</td>
                        <td>{{ number_format($order->fare_unit_by_cubic_meters, 0, '', '.') }}</td>
                        <td>{{ number_format($order->taxes, 0, '', '.') }}</td>
                        <td>{{ number_format($order->cost_vietnam, 0, '', '.') }}</td>
                        <td>{{ number_format($order->revenue, 0, '', '.') }}</td>

                        {{-- <td>
                            {{ number_format($order->paid, 0, '', '.') }}
                            </br>

                            @foreach ($order->payments as $payment)
                                <div style="width: 180px">
                                    <form id="frm-{{ $payment->id }}"
                                        action="{{ route('payment.delete', ['payment' => $payment]) }}" method="post">
                                        @csrf
                                    </form>

                                    <a href="{{ $payment->transaction ? route('transaction.show', ['transaction' => $payment->transaction]) : asset('files/' . $payment->image) }}"
                                        target="_blank">{{ number_format($payment->amount, 0, '', '.') }}
                                        <br>({{ $payment->formatted_created_at }})
                                    </a>

                                    <button class="btn btn-danger float-md-end"
                                        onclick="deleteRecord({{ $payment->id }})">Xóa</button>
                                </div>

                                </br>
                            @endforeach

                            @if (!auth()->user()->is_seller)
                                <button class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#create-payment-{{ $order->id }}">
                                    +
                                </button>
                            @endif
                        </td> --}}
                        <td>{{ number_format($order->debt, 0, '', '.') }}</td>
                        <td>
                            {{ $order->status_text }} <br>

                            {{-- @if ($order->status_text === \App\Order::STATUS_TEXT_WAIT_FOR_PAYING && !auth()->user()->is_seller)
                                <button class="btn btn-primary text-nowrap" data-bs-toggle="modal"
                                    data-bs-target="#update-order-{{ $order->id }}">Cập nhật</button>

                                <button class="btn btn-primary text-nowrap" data-bs-toggle="modal"
                                    data-bs-target="#calculate-order-cost-{{ $order->id }}">Sửa
                                    giá</button>
                            @endif

                            @if ($order->status_text === \App\Order::STATUS_TEXT_IS_NOT_CALCULATED_COST && !auth()->user()->is_seller)
                                <button class="btn btn-primary text-nowrap" data-bs-toggle="modal"
                                    data-bs-target="#calculate-order-cost-{{ $order->id }}">Tính
                                    tiền</button>
                            @endif --}}
                        </td>
                    </tr>

                    <tr class="customer-row-header d-none" data-id="{{ $order->id }}">
                        <td>
                        </td>
                        <td><b>Dài</b></td>
                        <td><b>Rộng</b></td>
                        <td><b>Cao</b></td>
                        <td><b>Cân nặng</b></td>
                        <td><b>Trả Hàng</b></td>
                    </tr>

                    @foreach ($order->packs as $pack)
                        <tr class="customer-row-order d-none" data-id="{{ $order->id }}"
                            data-order-id="{{ $order->id }}">
                            <td>
                            </td>
                            <td>{{ $pack->height }}</td>
                            <td>{{ $pack->width }}</td>
                            <td>{{ $pack->depth }}</td>
                            <td>{{ $pack->weight }}</td>
                            <td>
                                @if (auth('customer')->check())
                                    @if ($order->can_delivery)
                                        @if ($pack->status == \App\Pack::DELIVERED)
                                            Đã trả hàng
                                        @else
                                            Đang ở kho Việt Nam
                                        @endif
                                    @endif
                                @else
                                    @if (auth()->check())
                                        @if ($order->can_delivery)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    {{ $pack->status == \App\Pack::DELIVERED ? 'checked' : '' }}
                                                    onclick='updatePackStatus(this, {{ $pack->id }});'>
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>
