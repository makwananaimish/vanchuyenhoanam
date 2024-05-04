<div class="modal fade" id="merge-orders" tabindex="-1" aria-labelledby="merge-orders" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">Ghép nhiều đơn</h5>
                <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px;"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="col-form-label">Xe:</label>

                    <select
                        class="form-control form-select select2 select2-customer @error('truck_id') is-invalid @enderror"
                        name="truck_id">
                        <option value="">Chọn xe</option>
                        @foreach ($trucks as $truck)
                            <option value="{{ $truck->id }}">{{ $truck->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="col-form-label code-label">Mã vận đơn:</label>

                    <textarea name="order_codes" class="form-control lh-base" cols="30" rows="10" oninput="checkOrderCodes(this)"></textarea>

                    <span class="invalid-feedback d-block" role="alert" style="max-height: 100px; overflow:auto">
                        <strong></strong>
                    </span>
                </div>

                <div class="alert alert-danger d-none"></div>
                <div class="alert alert-success d-none"></div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Đang xử lý"
                    onclick="_bulkMerge(this)">
                    Ghép
                </button>
            </div>
        </div>
    </div>
</div>
