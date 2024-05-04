<div class="modal fade" id="update-user" tabindex="-1" aria-labelledby="update-user" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">
                    Cập nhật quản trị viên
                </h5>
                <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('user.update', ['user' => $_user]) }}">
                @csrf
                <div class="modal-body">
                    <div class="col-12 col-lg-12">
                        <ul class="nav nav-tabs mb-12" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab3"
                                    type="button" role="tab" aria-controls="tab3" aria-selected="true">
                                    Thông tin cơ bản
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab4" type="button"
                                    role="tab" aria-controls="tab4" aria-selected="false">
                                    Phân quyền
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="tab3" role="tabpanel"
                                aria-labelledby="tab3">
                                <div class="mb-3">
                                    <label for="" class="col-form-label">Tên:</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ $_user->name }}" required autofocus />
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="" class="col-form-label">Email:</label>
                                    <input type="text" class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{ $_user->email }}" required autofocus />
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="" class="col-form-label">Mật khẩu:</label>
                                    <input type="text" class="form-control @error('password') is-invalid @enderror"
                                        name="password" value="{{ old('password') ?? '123456' }}" required autofocus />
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab4" role="tabpanel" aria-labelledby="tab4">
                                <div class="mb-3">
                                    <label for="" class="col-form-label">Vai trò:</label>
                                    <div class="row">
                                        <div class="col-12 mt-16">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="role"
                                                    value="{{ \App\User::ROLE_ADMIN }}" id="flexRadioDefault6"
                                                    {{ \App\User::ROLE_ADMIN === $_user->role ? 'checked' : '' }}>
                                                <label class="form-check-label" for="flexRadioDefault6">
                                                    {{ __('app.roles.' . \App\User::ROLE_ADMIN) }}
                                                </label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="role"
                                                    value="{{ \App\User::ROLE_SELLER }}" id="flexRadioDefault7"
                                                    {{ \App\User::ROLE_SELLER === $_user->role ? 'checked' : '' }}>
                                                <label class="form-check-label" for="flexRadioDefault7">
                                                    {{ __('app.roles.' . \App\User::ROLE_SELLER) }}
                                                </label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="role"
                                                    value="{{ \App\User::ROLE_VN_INVENTORY }}" id="flexRadioDefault8"
                                                    {{ \App\User::ROLE_VN_INVENTORY === $_user->role ? 'checked' : '' }}>
                                                <label class="form-check-label" for="flexRadioDefault8">
                                                    {{ __('app.roles.' . \App\User::ROLE_VN_INVENTORY) }}
                                                </label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="role"
                                                    value="{{ \App\User::ROLE_CN_INVENTORY }}" id="flexRadioDefault9"
                                                    {{ \App\User::ROLE_CN_INVENTORY === $_user->role ? 'checked' : '' }}>
                                                <label class="form-check-label" for="flexRadioDefault9">
                                                    {{ __('app.roles.' . \App\User::ROLE_CN_INVENTORY) }}
                                                </label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="role"
                                                    value="{{ \App\User::ROLE_ACCOUNTANT }}" id="flexRadioDefault10"
                                                    {{ \App\User::ROLE_ACCOUNTANT === $_user->role ? 'checked' : '' }}>
                                                <label class="form-check-label" for="flexRadioDefault10">
                                                    {{ __('app.roles.' . \App\User::ROLE_ACCOUNTANT) }}
                                                </label>
                                            </div>

                                            @foreach ($locationsVN as $location)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="role"
                                                        value="location_vn_{{ $location->id }}"
                                                        id="_flexLocationVN{{ $location->id }}"
                                                        {{ \App\User::ROLE_VN_INVENTORY === $_user->role && $_user->location_id === $location->id ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="_flexLocationVN{{ $location->id }}">
                                                        {{ __('app.roles.' . \App\User::ROLE_VN_INVENTORY) }} -
                                                        {{ $location->name }}
                                                    </label>
                                                </div>
                                            @endforeach

                                            @foreach ($locationsCN as $location)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="role"
                                                        value="location_cn_{{ $location->id }}"
                                                        id="_flexLocationCN{{ $location->id }}"
                                                        {{ \App\User::ROLE_CN_INVENTORY === $_user->role && $_user->location_id === $location->id ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="_flexLocationCN{{ $location->id }}">
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
                        Cập Nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
