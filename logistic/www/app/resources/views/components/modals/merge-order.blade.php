<div class="modal fade" id="merge-order-{{ $order->id }}" tabindex="-1"
    aria-labelledby="merge-order-{{ $order->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">Ghép đơn</h5>
                <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px;"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('order.merge', ['order' => $order]) }}">
                @csrf

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="col-form-label">Khách hàng:</label>

                        <select
                            class="form-control form-select select2 select2-customer @error('customer_id') is-invalid @enderror"
                            name="customer_id">
                            <option value="">Chọn khách hàng</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    {{ $customer->id === $order->customer_id ? 'selected' : '' }}>
                                    {{ $customer->code }} - {{ $customer->name }} -
                                    {{ $customer->phone }}
                                </option>
                            @endforeach
                        </select>

                        @error('customer_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-text" data-bs-dismiss="modal">
                        Đóng
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Ghép
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
