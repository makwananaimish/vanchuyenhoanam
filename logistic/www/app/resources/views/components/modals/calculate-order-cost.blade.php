<div class="modal fade" id="calculate-order-cost-{{ $order->id }}" tabindex="-1"
    aria-labelledby="calculate-order-cost-{{ $order->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">Tính tiền</h5>
                <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px;"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('order.calculate_cost', ['order' => $order]) }}"
                enctype="multipart/form-data">
                @csrf

                @isset($redirect)
                    <input type="hidden" name="redirect" value="{{ $redirect }}">
                @endisset

                <input type="hidden" name="order_id" value="{{ $order->id }}">

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="col-form-label">Tiền Thuế NK/Thuế CBPG/ThuếBVMT:</label>
                        <input type="text"
                            class="form-control input-currency @if (old('order_id') == $order->id) @error('taxes1') is-invalid @enderror @endif"
                            name="taxes1" value="{{ $order->taxes1 }}" required autofocus>

                        @if (old('order_id') == $order->id)
                            @error('taxes1')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Thuế VAT:</label>
                        <input type="text"
                            class="form-control input-currency @if (old('order_id') == $order->id) @error('taxes2') is-invalid @enderror @endif"
                            name="taxes2" value="{{ $order->taxes2 }}" required autofocus>

                        @if (old('order_id') == $order->id)
                            @error('taxes2')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Tiền thuế:</label>
                        <input type="text" class="form-control input-currency " name="taxes"
                            value="{{ $order->taxes }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Chi phí Việt Nam(VNĐ):</label>
                        <input type="text"
                            class="form-control input-currency @if (old('order_id') == $order->id) @error('cost_vietnam') is-invalid @enderror @endif"
                            name="cost_vietnam" value="{{ $order->cost_vietnam ?? 0 }}" required autofocus>

                        @if (old('order_id') == $order->id)
                            @error('cost_vietnam')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Phí vận chuyển / kg:</label>
                        <input type="text"
                            class="form-control input-currency @if (old('order_id') == $order->id) @error('fare_unit_by_weight') is-invalid @enderror @endif"
                            name="fare_unit_by_weight" value="{{ $order->fare_unit_by_weight }}" required autofocus>

                        @if (old('order_id') == $order->id)
                            @error('fare_unit_by_weight')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Phí vận chuyển / m³:</label>
                        <input type="text"
                            class="form-control input-currency @if (old('order_id') == $order->id) @error('fare_unit_by_cubic_meters') is-invalid @enderror @endif"
                            name="fare_unit_by_cubic_meters" value="{{ $order->fare_unit_by_cubic_meters }}" required
                            autofocus>

                        @if (old('order_id') == $order->id)
                            @error('fare_unit_by_cubic_meters')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-text" data-bs-dismiss="modal">
                        Đóng
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Cập Nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
