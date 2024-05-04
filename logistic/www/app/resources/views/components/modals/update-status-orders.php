<div class="modal fade" id="update-status-orders" tabindex="-1" aria-labelledby="update-status-orders" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">Trả hàng</h5>
                <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px;"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="col-form-label">Ngày trả hàng:</label>

                    <input class="form-control " type="date" name="" id="delivery_date">
                </div>
                <div class="mb-3">
                    <label class="col-form-label">Số điện thoại lái xe:</label>

                    <input class="form-control " type="text" name="driver_phone">
                </div>
                <div class="mb-3">
                    <label class="col-form-label">Biển số xe của lái xe giao hàng:</label>

                    <input class="form-control " type="text" name="license_plate_number">
                </div>

                <div class="alert alert-danger d-none"></div>
                <div class="alert alert-success d-none"></div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Đang xử lý" onclick="bulkDelivery(this)">
                    Xác nhận trả hàng
                </button>
            </div>
        </div>
    </div>
</div>