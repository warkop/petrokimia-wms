"use strict";

let datatable,
    tableTarget = '#kt_table_1',
    ajaxUrl = baseUrl + 'master-tenaga-kerja-nonorganik',
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
    protectNumber("#nik", 20);
    protectNumber("#nomor_hp", 10);
    protectNumber("#nomor_bpjs", 10);

    // $('#nomor_hp').autoNumeric('init', {
    //     minimumValue: '0',
    //     maximumValue: '999999999',
    //     decimalPlaces: 0,
    //     digitGroupSeparator: '',
    // });

    // $('#nomor_bpjs').autoNumeric('init', {
    //     aSep: '.',
    //     aDec: ',',
    //     aSign: ''
    // });
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
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        },
        "sPaginationType": "full_numbers",
        "aoColumns": [{
                "mData": "id"
            },
            {
                "mData": "nama"
            },
            {
                "mData": "no_hp"
            },
            {
                "mData": "pekerjaan"
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
                render: function (data, type, full, meta) {
                    return `
                    <a href="" data-toggle="modal" data-target="#kt_modal_1">
                        <button type = "button" onclick="edit(${full.id})" class="btn btn-orens btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Ubah Data">
                        <i class="flaticon-edit-1"></i> </button>
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
    $('#id').val('');
    $('#action').val('add');
    $('#btn_save').html('Tambah Data');
    $('#modal_form .modal-title').html('Tambah Data Tenaga Kerja Non Organik');
    $('#modal_form .modal-info').html('Isilah form dibawah ini untuk menambahkan data terkait master Tenaga Kerja Non Organik.');
    $('#modal_form').modal({
        backdrop: 'static',
        keyboard: false
    }, 'show');
}

function edit(id = '') {
    reset_form();
    $('#id').val(id);
    $('#action').val('edit');
    $('#btn_save').html('Simpan Data');
    $('#modal_form .modal-title').html('Edit Data Tenaga Kerja Non Organik');
    $('#modal_form .modal-info').html('Isilah form dibawah ini untuk mengubah data master Tenaga Kerja Non Organik sesuai kebutuhan.');
    $('#modal_form').modal({
        backdrop: 'static',
        keyboard: false
    }, 'show');

    $.ajax({
        type: "GET",
        url: ajaxUrl + "/" + id,
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
                $('#nama').val(obj.data['nama']);
                $('#nik').val(obj.data['nik']);
                $('#nomor_hp').val(obj.data['nomor_hp']);
                $('#nomor_bpjs').val(obj.data['nomor_bpjs']);
                $("#job_desk_id").val(obj.data['job_desk_id']).change();
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

function simpan() {
    let data = $("#form1").serializeArray();
    let type = "PUT";
    $.ajax({
        type: type,
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
            $("#btn_save").prop("disabled", false);
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
                            laddaButton.stop();
                            window.onbeforeunload = false;
                            $('.btn_close_modal').removeClass('hide');
                            $('.se-pre-con').hide();

                            const temp = Object.values(obj);
                            message = '';
                            temp.forEach(element => {
                                element.forEach(row => {
                                    message += row + "<br>"
                                });
                            });
                        }
                    }
                }

                swal.fire(head, message, type);
            }
        }
    });
}

function reset_form(method = '') {
    $('#id').val('');
    $('#id').change();
    $('#nama').val('');
    $('#nama').change();
    $('#nik').val('');
    $('#nik').change();
    $('#nomor_hp').val('');
    $('#nomor_hp').change();
    $('#job_desk_id').val('');
    $('#job_desk_id').change();
    $('#nomor_bpjs').val('');
    $('#nomor_bpjs').change();
    // $('#start_date').val('');
    // $('#start_date').change();
    $('#end_date').val('');
    $('#end_date').change();
}
