<div class="modal fade" id="create-location" tabindex="-1" aria-labelledby="create-location" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">Tạo kho</h5>
                <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px;"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('location.create') }}">
                @csrf

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="" class="col-form-label">Loại:</label>
                        <div class="row">
                            <div class="col-12 mt-16">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type"
                                        value="{{ \App\Location::IN_CHINA }}" id="flexRadioDefault3"
                                        {{ old('type') == \App\Location::IN_CHINA ? 'checked' : '' }}
                                        {{ !old('type') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="flexRadioDefault3">
                                        Trung Quốc
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type"
                                        value="{{ \App\Location::IN_VIETNAM }}" id="flexRadioDefault4"
                                        {{ old('type') == \App\Location::IN_VIETNAM ? 'checked' : '' }}>
                                    <label class="form-check-label" for="flexRadioDefault4">
                                        Việt Nam
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type"
                                        value="{{ \App\Location::TRANSSHIPMENT }}" id="flexRadioDefault5"
                                        {{ old('type') == \App\Location::TRANSSHIPMENT ? 'checked' : '' }}>
                                    <label class="form-check-label" for="flexRadioDefault5">
                                        Điểm trung chuyển
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="" class="col-form-label">Tên:</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                            value="{{ old('name') }}" required autofocus>
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
                        Tạo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
