function modalChangePassword(id) {
    $("#modal_form_ganti_password").modal("show");
    $("#id_user").val(id);
    $("#old_password").val('');
    $("#new_password").val('');
    $("#new_password_confirmation").val('');
}
$('#btn_change').on('click', function (e) {
    e.preventDefault();
    laddaButton = Ladda.create(this);
    laddaButton.start();
    changePassword();
});

$('.input-enter').on("keyup", function (event) {
    event.preventDefault();
    if (event.keyCode === 13) {
        $("#btn_save").click();
    }
});

$('.input-enter-change').on("keyup", function (event) {
    event.preventDefault();
    if (event.keyCode === 13) {
        $("#btn_change").click();
    }
});
function changePassword() {
    const id = $("#id_user").val();
    let data = $("#form2").serializeArray();
    $.ajax({
        url: baseUrl + "master-user/change-password-general/"+id,
        type:"PATCH",
        data: data,
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:(response) => {
            laddaButton.stop();
            window.onbeforeunload = false;
            $('.btn_close_modal').removeClass('hide');
            $('.se-pre-con').hide();

            let obj = response;

            if (obj.status == "OK") {
                swal.fire('Ok', obj.message, 'success');
                $('#modal_form_ganti_password').modal('hide');
            } else {
                swal.fire('Pemberitahuan', obj.message, 'warning');
            }
        },
        error:response => {
            $("#btn_change").prop("disabled", false);
            let head = 'Maaf',
                message = 'Terjadi kesalahan koneksi',
                type = 'error';
            laddaButton.stop();
            window.onbeforeunload = false;
            $('.btn_close_modal').removeClass('hide');
            $('.se-pre-con').hide();

            if (response['status'] == 401 || response['status'] == 419) {
                location.reload();
            } else {
                if (response['status'] != 404 && response['status'] != 500) {
                    let obj = JSON.parse(response['responseText']);

                    if (!$.isEmptyObject(obj.message)) {
                        if (obj.code > 450) {
                            head = 'Maaf';
                            message = obj.message;
                            type = 'error';
                        } else {
                            head = 'Pemberitahuan';
                            type = 'warning';
                            obj = response.responseJSON.errors;
                            message = '';
                            if (obj == null) {
                                message = response.responseJSON.message;
                            } else {
                                const temp = Object.values(obj);
                                message = '';
                                temp.forEach(element => {
                                    element.forEach(row => {
                                        message += row + "<br>"
                                    });
                                });
                            }

                            laddaButton.stop();
                            window.onbeforeunload = false;
                            $('.btn_close_modal').removeClass('hide');
                            $('.se-pre-con').hide();
                        }
                    }
                }

                swal.fire(head, message, type);
            }
        }
    })
}
$(".reveal").on('click',function() {
    var $text = $(this).children("#basic-addon2");
    if ($("#"+$(this).attr('for')).attr('type') === 'password') {
        $("#"+$(this).attr('for')).attr('type', 'text');
        $text.html('Hide');
    } else {
        $("#"+$(this).attr('for')).attr('type', 'password');
        $text.html('Show');
    }
});