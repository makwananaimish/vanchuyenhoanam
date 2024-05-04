<div class="modal fade" id="create-order-declaration" tabindex="-1" aria-labelledby="create-order-declaration"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">Kê khai</h5>
                <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px;"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('order_declaration.create') }}" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="order_id" value="{{ old('order_id') }}">

                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label for="" class="col-form-label">Loại sản phẩm:</label>
                                    <div class="row">
                                        <div class="col-12 mt-16">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="type"
                                                    value="{{ \App\OrderDeclaration::TYPE_NORMAL }}"
                                                    id="flexRadioDefault3" checked>
                                                <label class="form-check-label" for="flexRadioDefault3">
                                                    Hàng thường
                                                </label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="type"
                                                    value="{{ \App\OrderDeclaration::TYPE_MACHINE }}"
                                                    id="flexRadioDefault4">
                                                <label class="form-check-label" for="flexRadioDefault4">
                                                    Hàng máy
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Ảnh:</label>
                                    <input type="file"
                                        class="form-control @error('images') is-invalid @enderror @if (count($errors->get('images.*')) > 0) is-invalid @endif"
                                        name="images[]" multiple autofocus>

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
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Tên sản phẩm:</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name') }}" autofocus>

                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Kích thước sản phẩm:</label>
                                    <input type="text" class="form-control @error('size') is-invalid @enderror"
                                        name="size" value="{{ old('size') }}" autofocus>

                                    @error('size')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Thương hiệu, kí hiệu trên sản phẩm:</label>
                                    <input type="text" class="form-control @error('brand') is-invalid @enderror"
                                        name="brand" value="{{ old('brand') }}" autofocus>

                                    @error('brand')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Chất liệu:</label>
                                    <input type="text" class="form-control @error('material') is-invalid @enderror"
                                        name="material" value="{{ old('material') }}" autofocus>

                                    @error('material')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Cân nặng 1 sản phẩm:</label>
                                    <input type="text"
                                        class="form-control @error('weight_per_product') is-invalid @enderror"
                                        name="weight_per_product" value="{{ old('weight_per_product') }}" autofocus>

                                    @error('weight_per_product')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Số sản phẩm/kiện,bao:</label>
                                    <input type="text"
                                        class="form-control @error('quantity_per_pack') is-invalid @enderror"
                                        name="quantity_per_pack" value="{{ old('quantity_per_pack') }}" autofocus>

                                    @error('quantity_per_pack')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Số kiện:</label>
                                    <input type="text"
                                        class="form-control @error('pack_quantity') is-invalid @enderror"
                                        name="pack_quantity" value="{{ old('pack_quantity') }}" autofocus>

                                    @error('pack_quantity')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Tổng số sản phẩm:</label>
                                    <input type="text" class="form-control @error('quantity') is-invalid @enderror"
                                        name="quantity" value="{{ old('quantity') }}" autofocus>

                                    @error('quantity')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Điện áp, công suất, thông số:</label>
                                    <input type="text"
                                        class="form-control @error('voltage_power_parameters') is-invalid @enderror"
                                        name="voltage_power_parameters" value="{{ old('voltage_power_parameters') }}"
                                        autofocus>

                                    @error('voltage_power_parameters')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Cân nặng 1 thùng:</label>
                                    <input type="text"
                                        class="form-control @error('weight_per_box') is-invalid @enderror"
                                        name="weight_per_box" value="{{ old('weight_per_box') }}" autofocus>

                                    @error('weight_per_box')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Kích thước thùng:</label>
                                    <input type="text"
                                        class="form-control @error('box_size') is-invalid @enderror" name="box_size"
                                        value="{{ old('box_size') }}" autofocus>

                                    @error('box_size')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Tổng số m³:</label>
                                    <input type="text"
                                        class="form-control @error('cubic_meters') is-invalid @enderror"
                                        name="cubic_meters" value="{{ old('cubic_meters') }}" autofocus>

                                    @error('cubic_meters')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Tổng số kg:</label>
                                    <input type="text" class="form-control @error('weight') is-invalid @enderror"
                                        name="weight" value="{{ old('weight') }}" autofocus>

                                    @error('weight')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">HS CODE:</label>
                                    <input type="text" class="form-control @error('hs_code') is-invalid @enderror"
                                        name="hs_code" value="{{ old('hs_code') }}" autofocus>

                                    @error('hs_code')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
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
