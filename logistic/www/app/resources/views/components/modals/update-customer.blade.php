<div class="modal fade" id="update-customer-{{ $customer->id }}" tabindex="-1"
    aria-labelledby="update-customer-{{ $customer->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">Cập nhật khách hàng</h5>
                <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px;"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('customer.update', ['customer' => $customer]) }}">
                @csrf

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="" class="col-form-label">Tên khách hàng:</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                            value="{{ $customer->name }}" required autofocus>

                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="" class="col-form-label">Mã khách hàng:</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" name="code"
                            value="{{ $customer->code }}" required autofocus>

                        @error('code')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="" class="col-form-label">SĐT:</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone"
                            value="{{ $customer->phone }}" required autofocus>

                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="" class="col-form-label">Địa chỉ:</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" name="address"
                            value="{{ $customer->address }}"  autofocus>
                        @error('address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="" class="col-form-label">Mật khẩu:</label>
                        <input type="text" class="form-control @error('password') is-invalid @enderror"
                            name="password" value="123456" required autofocus>

                        @error('password')
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
                        Cập Nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
