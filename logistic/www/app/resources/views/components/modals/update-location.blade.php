<div class="modal fade" id="update-location-{{ $location->id }}" tabindex="-1"
    aria-labelledby="update-location-{{ $location->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">Cập nhật kho</h5>
                <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px;"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('location.update', ['location' => $location]) }}">
                @csrf

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="" class="col-form-label">Tên:</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                            value="{{ $location->name }}" required autofocus>
                        @error('name')
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
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
