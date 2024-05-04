<div class="modal fade" id="notify-address" tabindex="-1" aria-labelledby="notify-address" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">Báo địa chỉ</h5>
                <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px;"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('customer.orders.notify_address', ['customer' => $customer]) }}">
                @csrf

                <input type="hidden" name="ids" id="ids" value="">

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="col-form-label">Ngày giờ:</label>
                        <input type="text" class="form-control" name="date" autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Địa chỉ:</label>
                        <input type="text" class="form-control" name="address" autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Số điện thoại:</label>
                        <input type="text" class="form-control" name="phone" autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Tên người nhận:</label>
                        <input type="text" class="form-control" name="name" autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Ghi chú:</label>
                        <input type="text" class="form-control" name="note" autofocus>
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
