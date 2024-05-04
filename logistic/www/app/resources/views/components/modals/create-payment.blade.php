<div class="modal fade" id="create-payment-{{ $order->id }}" tabindex="-1" aria-labelledby="create-payment"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">Thêm thanh
                    toán</h5>
                <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px;"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('payment.create') }}" enctype="multipart/form-data">
                @csrf

                @isset($truck)
                    <input type="hidden" name="redirect" value="{{ route('truck.show', ['truck' => $truck]) }}">
                @endisset

                @isset($redirect)
                    <input type="hidden" name="redirect" value="{{ $redirect }}">
                @endisset

                <input type="hidden" name="order_id" value="{{ $order->id }}">

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="col-form-label">Số tiền:</label>
                        <input type="text"
                            class="form-control input-currency @if (old('order_id') == $order->id) @error('amount') is-invalid @enderror @endif"
                            name="amount" value="{{ old('order_id') == $order->id ? old('amount') : '' }}" required
                            autofocus>

                        @if (old('order_id') == $order->id)
                            @error('amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Ảnh:</label>
                        <input type="file"
                            class="form-control @if (old('order_id') == $order->id) @error('image') is-invalid @enderror @endif"
                            name="image" value="{{ old('order_id') == $order->id ? old('image') : '' }}" required
                            autofocus>

                        @if (old('order_id') == $order->id)
                            @error('image')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        @endif
                    </div>

                    @error('order_id')
                        <div class="mt-16">
                            <div class="alert alert-danger">
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            </div>
                        </div>
                    @enderror
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-text" data-bs-dismiss="modal">
                        Đóng
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Thêm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
