<div class="modal fade message-modal" id="message-{{ $order->id }}" data-id="{{ $order->id }}" tabindex="-1"
    aria-labelledby="message" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">Ghi chú</h5>
                <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px;"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="col-form-label code-label">Lịch sử:</label>
                    {{-- <textarea class="form-control lh-base" id="messages-{{ $order->id }}" cols="30" rows="10" disabled></textarea> --}}

                    <div id="messages-{{ $order->id }}" >

                    </div>
                </div>

                <div class="mb-3">
                    <label class="col-form-label">Nội dung:</label>
                    <input type="hidden" name="" id="order-id-{{ $order->id }}" value="{{ $order->id }}">
                    <input class="form-control " type="text" name="" id="content-{{ $order->id }}">
                </div>

                <div class="alert alert-danger d-none"></div>
                <div class="alert alert-success d-none"></div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Đang gửi"
                    onclick="_send(this, $('#order-id-{{ $order->id }}').val(), $('#content-{{ $order->id }}').val() )">
                    Gửi
                </button>
            </div>
        </div>
    </div>
</div>
