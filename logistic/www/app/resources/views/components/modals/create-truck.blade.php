<div class="modal fade" id="create-truck" tabindex="-1" aria-labelledby="create-truck" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">
                    Thêm xe
                </h5>
                <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('truck.create') }}">
                @csrf
                <div class="modal-body">
                    @if (session('message'))
                        <div class="alert {{ session('alert-class') }}">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="" class="col-form-label">Tên:</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                            value="{{ old('name') }}" required autofocus />
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="" class="col-form-label">Phương Thức Đi Hàng:</label>
                        <select class="form-control @error('shipping_method_id') is-invalid @enderror"
                            name="shipping_method_id" autofocus>
                            <option value="">Chọn Phương Thức Đi Hàng</option>
                            @foreach ($shippingMethods as $shippingMethod)
                                <option value="{{ $shippingMethod->id }}">{{ $shippingMethod->name }}</option>
                            @endforeach
                        </select>
                        @error('shipping_method_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="" class="col-form-label">Vị trí khởi hành:</label>
                        <select class="form-control @error('departure_location_id') is-invalid @enderror"
                            name="departure_location_id" required autofocus>
                            @foreach ($withoutTransshipmentPoints as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                        @error('departure_location_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="" class="col-form-label">Ngày khởi hành:</label>
                        <input type="date" class="form-control @error('departure_date') is-invalid @enderror"
                            name="departure_date" value="{{ old('departure_date') }}" required autofocus />
                        @error('departure_date')
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
                        Thêm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
