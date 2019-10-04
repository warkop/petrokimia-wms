"use strict";

let datatable,
    tableTarget = '#kt_table_1',
    ajaxUrl = baseUrl + 'master-user',
    ajaxSource = ajaxUrl,
    laddaButton;

jQuery(document).ready(function () {
    load_table();

    if (typeof datatable !== 'undefined') {
        datatable.on('draw.dt', function () {
            $('[data-toggle=kt-tooltip]').tooltip();
        });
    }

    $('#btn_save').on('click', function (e) {
        e.preventDefault();
        laddaButton = Ladda.create(this);
        laddaButton.start();
        simpan();
    });

    $('.input-enter').on("keyup", function (event) {
        event.preventDefault();
        if (event.keyCode === 13) {
            $("#btn_save").click();
        }
    });
});

let load_table = function () {
    datatable = $(tableTarget);
    // begin first table
    datatable.dataTable({
        "bDestroy": true,
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: ajaxSource,
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        },
        "sPaginationType": "full_numbers",
        "aoColumns": [{
                "mData": "id"
            },
            {
                "mData": "email"
            },
            {
                "mData": "nama"
            },
            {
                "mData": "role_name"
            },
            {
                "mData": "start_date"
            },
            {
                "mData": "end_date"
            },
            {
                "mData": "id"
            }
        ],
        "aaSorting": [
            [1, 'asc']
        ],
        "lengthMenu": [10, 25, 50, 75, 100],
        "pageLength": 10,
        "aoColumnDefs": [{
                "aTargets": [0],
                "mData": "id",
                "mRender": function (data, type, full, draw) {
                    let row = draw.row;
                    let start = draw.settings._iDisplayStart;
                    let length = draw.settings._iDisplayLength;

                    let counter = (start + 1 + row);

                    return counter;
                }
            },
            {
                "aTargets": [6],
                "mData": "id",
                "aaSorting":false,
                render: function (data, type, full, meta) {
                    return `
                    <a href="" data-toggle="modal" data-target="#kt_modal_1">
                        <button type = "button" onclick="edit(${full.id})" class="btn btn-orens btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Ubah Data">
                        <i class="flaticon-edit-1"></i> </button>
                    </a> <a href="" data-toggle="modal" data-target="#kt_modal_1">
                        <button type = "button" onclick="gantiPassword(${full.id})" class="btn btn-info btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Reset Password">
                        <i class="flaticon2-refresh"></i> </button>
                    </a>`;
                },
            }
        ],
        "fnHeaderCallback": function (nHead, aData, iStart, iEnd, aiDisplay) {
            $(nHead).children('th:nth-child(1), th:nth-child(2), th:nth-child(3)').addClass('text-center');
        },
        "fnFooterCallback": function (nFoot, aData, iStart, iEnd, aiDisplay) {
            $(nFoot).children('th:nth-child(1), th:nth-child(2), th:nth-child(3)').addClass('text-center');
        },
        "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            $(nRow).children('td:nth-child(1),td:nth-child(2),td:nth-child(3),td:nth-child(4)').addClass('text-center');
        }
    });
};

function tambah() {
    reset_form();
    $('#user_id').val('');
    $('#action').val('add');
    $('#btn_save').html('Tambah Data');
    $('#modal_form .modal-title').html('Tambah Data Shift Kerja');
    $('#modal_form .modal-info').html('Isilah form dibawah ini untuk menambahkan data terkait master Shift Kerja.');
    $('#modal_form').modal({
        backdrop: 'static',
        keyboard: false
    }, 'show');
}

function edit(user_id = '') {
    reset_form();
    $('#user_id').val(user_id);
    $('#action').val('edit');
    $('#btn_save').html('Simpan Data');
    $('#modal_form .modal-title').html('Edit Data Shift Kerja');
    $('#modal_form .modal-info').html('Isilah form dibawah ini untuk mengubah data master Shift Kerja sesuai kebutuhan.');
    $('#modal_form').modal({
        backdrop: 'static',
        keyboard: false
    }, 'show');

    $.ajax({
        type: "GET",
        url: ajaxUrl + "/" + user_id,
        beforeSend: function () {
            preventLeaving();
            $('.btn_close_modal').addClass('hide');
            $('.se-pre-con').show();
        },
        success: function (response) {
            window.onbeforeunload = false;
            $('.btn_close_modal').removeClass('hide');
            $('.se-pre-con').hide();

            let obj = response;

            if (obj.status == "OK") {
                $('#username').val(obj.data['username']);
                $('#email').val(obj.data['email']);
                $("#radio"+obj.data['role_id']).prop("checked",true);
                if (obj.data['start_date'] != null) {
                    $('#start_date').val(helpDateFormat(obj.data['start_date'], 'si'));
                }

                if (obj.data['end_date'] != null) {
                    $('#end_date').val(helpDateFormat(obj.data['end_date'], 'si'));
                }
            } else {
                swal.fire('Pemberitahuan', obj.message, 'warning');
            }

        },
        error: function (response) {
            let head = 'Maaf',
                message = 'Terjadi kesalahan koneksi',
                type = 'error';
            window.onbeforeunload = false;
            $('.btn_close_modal').removeClass('hide');
            $('.se-pre-con').hide();

            if (response['status'] == 401 || response['status'] == 419) {
                location.reload();
            } else {
                if (response['status'] != 404 && response['status'] != 500) {
                    let obj = JSON.parse(response['responseText']);

                    if (!$.isEmptyObject(obj.message)) {
                        if (obj.code > 400) {
                            head = 'Maaf';
                            message = obj.message;
                            type = 'error';
                        } else {
                            head = 'Pemberitahuan';
                            message = obj.message;
                            type = 'warning';
                        }
                    }
                }

                swal.fire(head, message, type);
            }
        }
    });
}

function gantiPassword(user_id) {
    swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Password akan direset menjadi petrokimia123!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak'
    }).then((result) => {
        if (result.value) {
            // Swal.fire(
            //     'Deleted!',
            //     'Your file has been deleted.',
            //     'success'
            // )
            $.ajax({
                type: "PATCH",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: ajaxSource + "/" + user_id,
                success: response => {
                    $('.se-pre-con').hide();

                    let obj = response;
                    console.log(obj)
                    if (obj.status == "OK") {
                        swal.fire('Ok', obj.message, 'success');
                    } else {
                        swal.fire('Pemberitahuan', obj.message, 'warning');
                    }
                },
                error: (response, oo, pp) => {
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
                                if (obj.code > 400) {
                                    head = 'Maaf';
                                    message = obj.message;
                                    type = 'error';
                                } else {
                                    head = 'Pemberitahuan';
                                    message = obj.message;
                                    type = 'warning';
                                }
                            }
                        }

                        swal.fire(head, message, type);
                    }
                }
            });
        }
    })
}

function simpan() {
    let data = $("#form1").serializeArray();
    $.ajax({
        type: "PUT",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: ajaxUrl,
        data: data,
        beforeSend: function () {
            preventLeaving();
            $('.btn_close_modal').addClass('hide');
            $('.se-pre-con').show();
        },
        success: function (response) {
            laddaButton.stop();
            window.onbeforeunload = false;
            $('.btn_close_modal').removeClass('hide');
            $('.se-pre-con').hide();

            let obj = response;

            if (obj.status == "OK") {
                datatable.api().ajax.reload();
                swal.fire('Ok', obj.message, 'success');
                $('#modal_form').modal('hide');
            } else {
                swal.fire('Pemberitahuan', obj.message, 'warning');
            }

        },
        error: function (response) {
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
                        if (obj.code > 400) {
                            head = 'Maaf';
                            message = obj.message;
                            type = 'error';
                        } else {
                            head = 'Pemberitahuan';
                            message = obj.message;
                            type = 'warning';
                        }
                    }
                }

                swal.fire(head, message, type);
            }
        }
    });
}

function reset_form(method = '') {
    $('#user_id').val('');
    $('#user_id').change();
    $('#username').val('');
    $('#username').change();
    $('#email').val('');
    $('#email').change();
    $('#password').val('');
    $('#password').change();
    $('#start_date').val('');
    $('#start_date').change();
    $('#end_date').val('');
    $('#end_date').change();
}
