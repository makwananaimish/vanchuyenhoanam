<div class="modal fade" id="create-user" tabindex="-1" aria-labelledby="create-user" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">
                    Thêm quản trị viên
                </h5>
                <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('user.create') }}">
                @csrf

                <div class="modal-body">
                    <div class="col-12 col-lg-12">
                        <ul class="nav nav-tabs mb-12" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab1"
                                    type="button" role="tab" aria-controls="tab1" aria-selected="true">
                                    Thông tin cơ bản
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab2" type="button"
                                    role="tab" aria-controls="tab2" aria-selected="false">
                                    Phân quyền
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="tab1" role="tabpanel"
                                aria-labelledby="tab1">
                                <div class="mb-3">
                                    <label for="" class="col-form-label">Tên:</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name') }}" required autofocus />
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="" class="col-form-label">Email:</label>
                                    <input type="text" class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email') }}" required autofocus />
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="" class="col-form-label ">Mật
                                        khẩu:</label>
                                    <input type="text" class="form-control @error('password') is-invalid @enderror"
                                        name="password" value="{{ old('password') ?? '123456' }}" required autofocus />
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2">
                                <div class="mb-3">
                                    <label for="" class="col-form-label">Vai trò:</label>
                                    <div class="row">
                                        <div class="col-12 mt-16">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="role"
                                                    value="{{ \App\User::ROLE_ADMIN }}" id="flexRadioDefault1"
                                                    checked="">
                                                <label class="form-check-label" for="flexRadioDefault1">
                                                    {{ __('app.roles.' . \App\User::ROLE_ADMIN) }}
                                                </label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="role"
                                                    value="{{ \App\User::ROLE_SELLER }}" id="flexRadioDefault2">
                                                <label class="form-check-label" for="flexRadioDefault2">
                                                    {{ __('app.roles.' . \App\User::ROLE_SELLER) }}
                                                </label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="role"
                                                    value="{{ \App\User::ROLE_VN_INVENTORY }}"
                                                    id="flexRadioDefault3">
                                                <label class="form-check-label" for="flexRadioDefault3">
                                                    {{ __('app.roles.' . \App\User::ROLE_VN_INVENTORY) }}
                                                </label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="role"
                                                    value="{{ \App\User::ROLE_CN_INVENTORY }}"
                                                    id="flexRadioDefault4">
                                                <label class="form-check-label" for="flexRadioDefault4">
                                                    {{ __('app.roles.' . \App\User::ROLE_CN_INVENTORY) }}
                                                </label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="role"
                                                    value="{{ \App\User::ROLE_ACCOUNTANT }}" id="flexRadioDefault5">
                                                <label class="form-check-label" for="flexRadioDefault5">
                                                    {{ __('app.roles.' . \App\User::ROLE_ACCOUNTANT) }}
                                                </label>
                                            </div>

                                            @foreach ($locationsVN as $location)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="role"
                                                        value="location_vn_{{ $location->id }}"
                                                        id="flexLocationVN{{ $location->id }}">
                                                    <label class="form-check-label"
                                                        for="flexLocationVN{{ $location->id }}">
                                                        {{ __('app.roles.' . \App\User::ROLE_VN_INVENTORY) }} -
                                                        {{ $location->name }}
                                                    </label>
                                                </div>
                                            @endforeach

                                            @foreach ($locationsCN as $location)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="role"
                                                        value="location_cn_{{ $location->id }}"
                                                        id="flexLocationCN{{ $location->id }}">
                                                    <label class="form-check-label"
                                                        for="flexLocationCN{{ $location->id }}">
                                                        {{ __('app.roles.' . \App\User::ROLE_CN_INVENTORY) }} -
                                                        {{ $location->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
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
                        Thêm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
