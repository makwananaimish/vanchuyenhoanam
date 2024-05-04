$("#form-create .qr").show();
$("#form-create .amount").hide();
$("#form-create .submit").hide();

$("#form-create .form-check-input").change(function () {
    const val = $(this).val();

    if (val === "deposit") {
        $("#form-create .qr").show();
        $("#form-create .amount").hide();
        $("#form-create .submit").hide();
    } else if (val === "withdrawal") {
        $("#form-create .qr").hide();
        $("#form-create .amount").show();
        $("#form-create .submit").show();
    }
});
