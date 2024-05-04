<div class="modal fade" id="create-cost-china-top-up" tabindex="-1" aria-labelledby="create-cost-china-top-up" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">Thêm nạp quỹ</h5>
                <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px;"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('cost_china.create') }}" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="type" value="{{ \App\CostChina::TYPE_TOP_UP }}">

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="col-form-label">Ngày:</label>
                        <input type="date" class="form-control  @error('date') is-invalid @enderror" name="date"
                            value="{{ old('date') }}" required autofocus>

                        @error('date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Nội dung nạp quỹ:</label>
                        <input type="text" class="form-control @error('content') is-invalid @enderror "
                            name="content" value="{{ old('content') }}" autofocus>

                        @error('content')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Số tiền:</label>
                        <input type="text" class="form-control input-currency @error('amount') is-invalid @enderror"
                            name="amount" value="{{ old('amount') ?? 0 }}" required autofocus>

                        @error('amount')
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
                        Xác nhận
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
