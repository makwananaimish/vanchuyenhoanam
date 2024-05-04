<div class="modal fade" id="update-order-{{ $order->id }}" tabindex="-1"
    aria-labelledby="update-order-{{ $order->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">Cập nhật vận đơn</h5>
                <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px;"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('order.update', ['order' => $order]) }}"
                enctype="multipart/form-data">
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
                        <label class="col-form-label">Khách hàng:</label>

                        <select
                            class="form-control form-select select2 select2-customer @if (old('order_id')) @error('customer_id') is-invalid @enderror @endif"
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

                        @if (old('order_id'))
                            @error('customer_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Mã vận đơn:</label>
                        <input type="text"
                            class="form-control @if (old('order_id') == $order->id) @error('code') is-invalid @enderror @endif"
                            name="code" value="{{ $order->code }}" required autofocus>

                        @if (old('order_id') == $order->id)
                            @error('code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        @endif

                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Bill gốc:</label>
                        <input type="text"
                            class="form-control @if (old('order_id') == $order->id) @error('bill') is-invalid @enderror @endif"
                            name="bill" value="{{ $order->bill }}" required autofocus>

                        @if (old('order_id') == $order->id)
                            @error('bill')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Tên hàng:</label>
                        <input type="text"
                            class="form-control @if (old('order_id') == $order->id) @error('product_name') is-invalid @enderror @endif"
                            name="product_name" value="{{ $order->product_name }}" autofocus>

                        @if (old('order_id') == $order->id)
                            @error('product_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <label class="col-form-label">Ảnh:</label>
                        <input type="file"
                            class="form-control @if (old('order_id') == $order->id) @error('images') is-invalid @enderror @endif @if(count($errors->get('images.*')) > 0) is-invalid @endif"
                            name="images[]" multiple autofocus>

                        @if (is_array($order->images))
                            <div class="d-flex">
                                @foreach ($order->images as $image)
                                    @if (is_string($image))
                                        <div class="mt-6 mr-2">
                                            <img class="lazy" height="50px" data-src="{{ asset('files/' . $image) }}">
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        @if (old('order_id') == $order->id)
                            @error('images')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            @foreach ($errors->get('images.*') as $e)
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $e[0] }}</strong>
                                </span>
                            @endforeach
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Cân nặng(kg):</label>
                        <input type="text"
                            class="form-control @if (old('order_id') == $order->id) @error('weight') is-invalid @enderror @endif"
                            name="weight" value="{{ $order->getOriginal('weight') }}" autofocus>

                        @if (old('order_id') == $order->id)
                            @error('weight')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Chi phí Trung Quốc(Tệ) - Ứng:</label>
                        <input type="text"
                            class="form-control input-currency @if (old('order_id') == $order->id) @error('cost_china1') is-invalid @enderror @endif"
                            name="cost_china1" value="{{ $order->cost_china1 ?? 0 }}" required autofocus>

                        @if (old('order_id') == $order->id)
                            @error('cost_china1')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Chi phí Trung Quốc(Tệ) - Kéo:</label>
                        <input type="text"
                            class="form-control input-currency @if (old('order_id') == $order->id) @error('cost_china2') is-invalid @enderror @endif"
                            name="cost_china2" value="{{ $order->cost_china2 ?? 0 }}" required autofocus>

                        @if (old('order_id') == $order->id)
                            @error('cost_china2')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Ghi chú:</label>
                        <input type="text"
                            class="form-control @if (old('order_id') == $order->id) @error('note') is-invalid @enderror @endif"
                            name="note" value="{{ $order->note }}" autofocus>

                        @if (old('order_id') == $order->id)
                            @error('note')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Tỷ giá Tệ - VND:</label>
                        <input type="text"
                            class="form-control input-currency @if (old('order_id') == $order->id) @error('rmb_to_vnd') is-invalid @enderror @endif"
                            name="rmb_to_vnd" value="{{ $order->rmb_to_vnd }}" required autofocus>

                        @if (old('order_id') == $order->id)
                            @error('rmb_to_vnd')
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
