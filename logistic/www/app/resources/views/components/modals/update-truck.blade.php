<div class="modal fade" id="update-truck" tabindex="-1" aria-labelledby="update-truck" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">Cập nhật xe</h5>
                <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px;"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('truck.update', ['truck' => $truck]) }}">
                @csrf

                <div class="modal-body">
                    @if (session('message'))
                        <div class="alert {{ session('alert-class') }}">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="col-12 col-lg-12">
                        <ul class="nav nav-tabs mb-12" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="basic-tab" data-bs-toggle="tab"
                                    data-bs-target="#basic" type="button" role="tab" aria-controls="basic"
                                    aria-selected="true">Thông tin cơ
                                    bản</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cost-tab" data-bs-toggle="tab" data-bs-target="#cost"
                                    type="button" role="tab" aria-controls="cost"
                                    aria-selected="false">Cost</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="basic" role="tabpanel"
                                aria-labelledby="basic">
                                <div class="mb-3">
                                    <label for="" class="col-form-label">Tên:</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ $truck->name }}" required autofocus>

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
                                            <option value="{{ $shippingMethod->id }}"
                                                {{ $shippingMethod->id === $truck->shipping_method_id ? 'selected' : '' }}>
                                                {{ $shippingMethod->name }}
                                            </option>
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
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}"
                                                {{ $location->id === $truck->departure_location_id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('departure_location_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="" class="col-form-label">Vị trí hiện tại:</label>
                                    <select class="form-control @error('current_location_id') is-invalid @enderror"
                                        name="current_location_id" required autofocus>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}"
                                                {{ $location->id === $truck->current_location_id ? 'selected' : '' }}>
                                                {{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('current_location_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="" class="col-form-label">Ngày khởi
                                        hành:</label>
                                    <input type="date"
                                        class="form-control @error('departure_date') is-invalid @enderror"
                                        name="departure_date" value="{{ $truck->departure_date }}" required>
                                    @error('departure_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="" class="col-form-label">Ngày về kho:</label>
                                    <input type="date"
                                        class="form-control @error('arrival_date') is-invalid @enderror"
                                        name="arrival_date" value="{{ $truck->arrival_date }}">
                                    @error('arrival_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="tab-pane fade" id="cost" role="tabpanel" aria-labelledby="cost"
                                style="max-height: 600px; overflow-x: hidden">

                                <div class="row mb-6">
                                    <div class="col-2">
                                        <label for="" class="col-form-label">STT</label>
                                    </div>
                                    <div class="col-4">
                                        <label for="" class="col-form-label">Nội Dung</label>
                                    </div>
                                    <div class="col-3">
                                        <label for="" class="col-form-label">Số Tiền</label>
                                    </div>
                                    <div class="col-3">
                                        <label for="" class="col-form-label">Ghi Chú</label>
                                    </div>
                                </div>

                                @foreach ($truck->cost ?? [] as $index => $cost)
                                    @include('components.cost-row', [
                                        'inputName' => "cost[$index]",
                                        'cost' => $cost,
                                    ])

                                    <div class="row d-flex justify-content-end pl-5 list-costs">
                                        <div class="row mb-6">
                                            <div class="">
                                                <button type="button" class="btn btn-primary"
                                                    onclick="addCostCategoryLevel2(this, {{ $index }})">
                                                    Thêm danh mục
                                                </button>
                                            </div>
                                        </div>

                                        @if (array_key_exists('costs', $cost))
                                            @foreach ($cost['costs'] as $_index => $item)
                                                @include('components.cost-row', [
                                                    'inputName' => "cost[$index][costs][$_index]",
                                                    'cost' => $item,
                                                    'index' => $_index,
                                                ])

                                                <div class="row d-flex justify-content-end pl-5 list-costs">
                                                    <div class="row mb-6">
                                                        <div class="">
                                                            <button type="button" class="btn btn-primary"
                                                                onclick="addCostCategoryLevel3(this, {{ $index }}, {{ $_index }})">
                                                                Thêm danh mục
                                                            </button>
                                                        </div>
                                                    </div>

                                                    @if (array_key_exists('costs', $item))
                                                        @foreach ($item['costs'] as $__index => $itemCost)
                                                            @include('components.cost-row', [
                                                                'inputName' => "cost[$index][costs][$_index][costs][$__index]",
                                                                'cost' => $itemCost,
                                                                'index' => $__index,
                                                            ])
                                                        @endforeach
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                @endforeach

                                <script>
                                    function addCostCategoryLevel1(el) {

                                    }

                                    function addCostCategoryLevel2(el, ordinal) {
                                        const latestSiblingCostRow = $(el).parent().parent().siblings('.cost-row').last();
                                        console.log(latestSiblingCostRow);

                                        let index = 0
                                        if (latestSiblingCostRow.length === 0) {
                                            index = 0
                                        } else {
                                            index = (parseInt(latestSiblingCostRow.attr('data-index')) + 1) ?? 0;
                                        }
                                        console.log(index);

                                        const newEl = $(el).parents('.list-costs').append(`
                                        <div class="row mb-6 cost-row" data-index="${index}">
                                            <div class="col-2">
                                                <input type="text" class="form-control" name="cost[${ordinal}][costs][${index}][ordinal]" value="">
                                            </div>
                                            <div class="col-4">
                                                <input type="text" class="form-control" name="cost[${ordinal}][costs][${index}][content]" value="">
                                            </div>
                                            <div class="col-3">
                                                <input type="text" class="form-control input-currency" name="cost[${ordinal}][costs][${index}][amount]" value="">
                                                <input type="hidden" name="cost[${ordinal}][costs][0][amount]" value="">
                                            </div>
                                            <div class="col-3">
                                                <input type="text" class="form-control" name="cost[${ordinal}][costs][${index}][note]" value="">
                                            </div>
                                        </div>
                                        
                                        <div class="row d-flex justify-content-end pl-5 list-costs">
                                            <div class="row mb-6">
                                                <div class="">
                                                    <button type="button" class="btn btn-primary">
                                                        Thêm danh mục
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        `)

                                        console.log(newEl);
                                    }

                                    function addCostCategoryLevel3(el, ordinal, ordinal2) {
                                        const latestSiblingCostRow = $(el).parent().parent().siblings('.cost-row').last();
                                        console.log(latestSiblingCostRow);

                                        let index = 0
                                        if (latestSiblingCostRow.length === 0) {
                                            index = 0
                                        } else {
                                            index = (parseInt(latestSiblingCostRow.attr('data-index')) + 1) ?? 0;
                                        }
                                        console.log(index);

                                        const newEl = $(el).parents('.list-costs').append(
                                            `<div class="row mb-6 cost-row" data-index="${index}">
                                                <div class="col-2">
                                                    <input type="text" class="form-control" name="cost[${ordinal}][costs][${ordinal2}][costs][${index}][ordinal]" value="">
                                                </div>
                                                <div class="col-4">
                                                    <input type="text" class="form-control" name="cost[${ordinal}][costs][${ordinal2}][costs][${index}][content]" value="">
                                                </div>
                                                <div class="col-3">
                                                    <input type="text" class="form-control input-currency" name="cost[${ordinal}][costs][${ordinal2}][costs][${index}][amount]" value="">
                                                    <input type="hidden" name="cost[${ordinal}][costs][${ordinal2}][costs][${index}][amount]" value="">
                                                </div>
                                                <div class="col-3">
                                                    <input type="text" class="form-control" name="cost[${ordinal}][costs][${ordinal2}][costs][${index}][note]" value="">
                                                </div>
                                            </div>
                                            `
                                        );

                                        console.log(newEl);
                                    }
                                </script>

                                <div class="row mb-6">
                                    <div class="col-6">
                                        <label for="" class="col-form-label">
                                            Tổng cộng chi phí cho lô hàng
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <label for="" class="col-form-label">
                                            {{ number_format($truck->total_cost, 0, '', '.') }}
                                        </label>
                                    </div>
                                </div>

                                <div class="row mb-6">
                                    <div class="col-6">
                                        <label for="" class="col-form-label">
                                            Chi phí trung bình khối
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <label for="" class="col-form-label">
                                            @if ($truck->cubic_meters > 0)
                                                {{ number_format($truck->total_cost / $truck->cubic_meters, 0, '', '.') }}
                                            @else
                                                0
                                            @endif
                                        </label>
                                    </div>
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
                        Cập Nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
