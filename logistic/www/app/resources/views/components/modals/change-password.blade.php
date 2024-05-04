<style>
    .form-class {
        margin: auto;
        text-align: center;
    }

    #pf_foto {
        background-image: url('/assets/img/user.png');
        background-size: cover;
        width: 120px;
        height: 120px;
        margin: 0 auto;
        border-radius: 100px;
        background-position: center;
    }

    #uploadButton {
        margin: 15px auto;
        width: 135px;
        border-radius: 20px;
        padding: 8px 0px;
        border-width: 4px;
        border-style: none;
        font-size: 10px;
        color: white;
        background-color: #F05D17;
        display: inline-block;
        cursor: pointer;
        text-align: center;
    }
</style>

<div class="modal fade" id="change-password" tabindex="-1" aria-labelledby="change-password" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">Đổi mật khẩu</h5>
                <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px;"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('user.change_password') }}">
                @csrf

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="col-form-label">Mật khẩu:</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" value="{{ old('password') }}" required autofocus>

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Xác nhận mật khẩu:</label>
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                            name="password_confirmation" value="{{ old('password_confirmation') }}" required autofocus>

                        @error('password_confirmation')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="col-form-label">Upload avatar:</label>

                        <div class="form-class">
                            <div id="pf_foto"></div>

                            <input type='file' id='verborgen_file' />
                            <input type="hidden" id="avatar" name="avatar">
                            <input type="button" value="Upload" id="uploadButton" />
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

<script>
    $("#verborgen_file").hide();
    $("#uploadButton").on("click", function() {
        $("#verborgen_file").click();
    });

    $("#verborgen_file").change(function() {
        var file = this.files[0];
        var reader = new FileReader();
        reader.onloadend = function() {
            $('#avatar').val(reader.result.replace(/data:(.*)base64,/, ``))
            $("#pf_foto").css("background-image", 'url("' + reader.result + '")');
        };
        if (file) {
            reader.readAsDataURL(file);
        } else {}
    });
</script>
