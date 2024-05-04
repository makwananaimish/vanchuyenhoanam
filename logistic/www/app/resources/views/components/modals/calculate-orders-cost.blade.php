<div class="modal fade" id="calculate-orders-cost" tabindex="-1" aria-labelledby="calculate-orders-cost" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">Tính tiền</h5>
                <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px;"></i>
                </button>
            </div>

            <form method="POST" id="form-calculate-costs" action="{{ route('order.calculate_costs') }}"
                enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="col-form-label">Tiền Thuế NK/Thuế CBPG/ThuếBVMT:</label>
                        <input type="text"
                            class="form-control input-currency @if (old('order_ids')) @error('taxes1') is-invalid @enderror @endif"
                            name="taxes1" value="" required autofocus>

                        @if (old('order_ids'))
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
                            class="form-control input-currency @if (old('order_ids')) @error('taxes2') is-invalid @enderror @endif"
                            name="taxes2" value="" required autofocus>

                        @if (old('order_ids'))
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
                            value="" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Chi phí Việt Nam(VNĐ):</label>
                        <input type="text"
                            class="form-control input-currency @if (old('order_ids')) @error('cost_vietnam') is-invalid @enderror @endif"
                            name="cost_vietnam" value="" required autofocus>

                        @if (old('order_ids'))
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
                            class="form-control input-currency @if (old('order_ids')) @error('fare_unit_by_weight') is-invalid @enderror @endif"
                            name="fare_unit_by_weight" value="" required autofocus>

                        @if (old('order_ids'))
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
                            class="form-control input-currency @if (old('order_ids')) @error('fare_unit_by_cubic_meters') is-invalid @enderror @endif"
                            name="fare_unit_by_cubic_meters" value="" required
                            autofocus>

                        @if (old('order_ids'))
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

                    <button type="button" class="btn btn-primary" onclick="calculate()">
                        Cập Nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function calculate() {
        var values = $("input[name='order_ids[]']:checked").map(function() {
            return this.value;
        }).get();

        var form = $("#form-calculate-costs");

        $.each(values, function(i, value) {
            $('<input>').attr({
                type: 'hidden',
                name: 'order_ids[]',
                value: value
            }).appendTo(form);
        });

        form.submit();
    }
</script>
