<div class="modal fade" id="update-pack-{{ $pack->id }}" tabindex="-1"
    aria-labelledby="update-pack-{{ $pack->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">Cập nhật kiện</h5>
                <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px;"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('pack.update', ['pack' => $pack]) }}">
                @csrf

                @isset($truck)
                    <input type="hidden" name="redirect" value="{{ route('truck.show', ['truck' => $truck]) }}">
                @endisset

                @isset($redirect)
                    <input type="hidden" name="redirect" value="{{ $redirect }}">
                @endisset

                <input type="hidden" name="pack_id" value="{{ $pack->id }}">
                <input type="hidden" name="order_id" value="{{ $pack->order_id }}">

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="col-form-label">Số lượng:</label>
                        <input type="text"
                            class="form-control @if (old('pack_id') == $pack->id) @error('quantity') is-invalid @enderror @endif"
                            name="quantity" value="{{ $pack->quantity }}" autofocus>

                        @if (old('pack_id') == $pack->id)
                            @error('quantity')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Dài:</label>
                        <input type="text"
                            class="form-control @if (old('pack_id') == $pack->id) @error('height') is-invalid @enderror @endif"
                            name="height" value="{{ $pack->height }}" autofocus>

                        @if (old('pack_id') == $pack->id)
                            @error('height')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Rộng:</label>
                        <input type="text"
                            class="form-control @if (old('pack_id') == $pack->id) @error('width') is-invalid @enderror @endif"
                            name="width" value="{{ $pack->width }}" autofocus>

                        @if (old('pack_id') == $pack->id)
                            @error('width')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Cao:</label>
                        <input type="text"
                            class="form-control @if (old('pack_id') == $pack->id) @error('depth') is-invalid @enderror @endif"
                            name="depth" value="{{ $pack->depth }}" autofocus>

                        @if (old('pack_id') == $pack->id)
                            @error('depth')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Cân nặng:</label>
                        <input type="text"
                            class="form-control @if (old('pack_id') == $pack->id) @error('weight') is-invalid @enderror @endif"
                            name="weight" value="{{ $pack->getOriginal('weight') }}" autofocus>

                        @if (old('pack_id') == $pack->id)
                            @error('weight')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-text" data-bs-dismiss="modal">
                        Đóng
                    </button>

                    <button type="submit" class="btn btn-primary text-nowrap">
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
