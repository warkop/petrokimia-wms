"use strict";
<<<<<<< HEAD
var KTDatatablesDataSourceHtml = function () {
    var dataJSONArray = JSON.parse(
        '[[1, "Foto Tampak Atas", "10-12-2019", "15-12-2019"], [2, "Foto Tampak Bawah", "11-12-2019", "19-12-2019"], [3, "Foto Tampak Depan", "11-12-2019", "19-12-2019"], [4, "Foto Tampak Belakang", "11-12-2019", "19-12-2019"], [5, "Foto Tampak Kiri", "11-12-2019", "19-12-2019"], [6, "Foto Tampak Kanan", "11-12-2019", "19-12-2019"]]');
    var initTable1 = function () {
        var table = $('#kt_table_1');
        // begin first table
        table.DataTable({
            responsive: true,
            data: dataJSONArray,
            columnDefs: [{
                className: 'text-center',
                targets: -1,
                title: 'Actions',
                orderable: false,
                render: function (data, type, full, meta) {
                    return `
                        <a href="" data-toggle="modal" data-target="#kt_modal_1">
                            <button type = "button" class="btn btn-orens btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Edit">
                            <i class="flaticon-edit-1"></i> </button>
                        </a>`;
                },
            }],
            "drawCallback": function (settings) {
                $('[data-toggle="kt-tooltip"]').tooltip();
            }
        });
    };
    return {
        //main function to initiate the module
        init: function () {
            initTable1();
        },
    };
}();
jQuery(document).ready(function () {
    KTDatatablesDataSourceHtml.init();
});

//<a href="" data-toggle="modal" data-target="#kt_modal_2"><button type = "button" class="btn btn-primary btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Edit"><i class="flaticon-edit-1"></i> </button></a>
=======

var datatable,
    tableTarget = '#kt_table_1',
    ajaxUrl = baseUrl + '/master-jenis-foto/',
    ajaxSource = ajaxUrl + 'json',
    laddaButton;

jQuery(document).ready(function () {
    load_table();

    if (typeof datatable !== 'undefined') {
        datatable.on('draw.dt', function () {
            $('[data-toggle=tooltip]').tooltip();
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

var load_table = function () {
    datatable = $(tableTarget);
    // begin first table
    datatable.DataTable({
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
            "mData": "nama"
        },
        {
            "mData": "from_date"
        },
        {
            "mData": "end_date"
        },
        {
            "mData": "id"
        }],
        "aaSorting": [
            [1, 'asc']
        ],
        "lengthMenu": [10, 25, 50, 75, 100],
        "pageLength": 10,
        "aoColumnDefs": [{
            "aTargets": [0],
            "mData": "id",
            "mRender": function (data, type, full, draw) {
                var row = draw.row;
                var start = draw.settings._iDisplayStart;
                var length = draw.settings._iDisplayLength;

                var counter = (start + 1 + row);

                return counter;
            }
        },
        {
            "aTargets": [4],
            "mData": "id",
            render: function (data, type, full, meta) {
                return `
                    <a href="" data-toggle="modal" data-target="#kt_modal_1">
                        <button type = "button" class="btn btn-orens btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Edit">
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
    // return {
    //     //main function to initiate the module
    //     init: function () {
    //         initTable1();
    //     },
    // };


function tambah() {
    reset_form();
    $('#id_guna').val('');
    $('#action').val('add');
    $('#btn_save').html('Tambah Data');
    $('#modal_form .modal-title').html('Tambah Data Guna');
    $('#modal_form .modal-info').html('Isilah form dibawah ini untuk menambahkan data terkait master Guna.');
    $('#modal_form').modal({
        backdrop: 'static',
        keyboard: false
    }, 'show');
}

function edit(jenis_foto_id = '') {
    reset_form();
    $('#jenis_foto_id').val(jenis_foto_id);
    $('#action').val('edit');
    $('#btn_save').html('Simpan Data');
    $('#modal_form .modal-title').html('Edit Data Guna');
    $('#modal_form .modal-info').html('Isilah form dibawah ini untuk mengubah data master Guna sesuai kebutuhan.');
    $('#modal_form').modal({
        backdrop: 'static',
        keyboard: false
    }, 'show');

    $.ajax({
        type: "GET",
        url: ajaxUrl + "show/" + jenis_foto_id,
        beforeSend: function () {
            preventLeaving();
            $('.btn_close_modal').addClass('hide');
            $('.se-pre-con').show();
        },
        success: function (response) {
            window.onbeforeunload = false;
            $('.btn_close_modal').removeClass('hide');
            $('.se-pre-con').hide();

            var obj = response;

            if (obj.status == "OK") {
                $('#nama_jenis_foto').val(obj.data['nama_jenis_foto']);
            } else {
                swal.fire('Pemberitahuan', obj.message, 'warning');
            }

        },
        error: function (response) {
            var head = 'Maaf',
                message = 'Terjadi kesalahan koneksi',
                type = 'error';
            window.onbeforeunload = false;
            $('.btn_close_modal').removeClass('hide');
            $('.se-pre-con').hide();

            if (response['status'] == 401 || response['status'] == 419) {
                location.reload();
            } else {
                if (response['status'] != 404 && response['status'] != 500) {
                    var obj = JSON.parse(response['responseText']);

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
    // var file = new FormData($("#form1")[0]);
    var data = $("#form1").serializeArray();
    $.ajax({
        type: "PUT",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: ajaxUrl + "save",
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

            var obj = response;

            if (obj.status == "OK") {
                datatable.ajax.reload();
                swal.fire('Ok', obj.message, 'success');
                $('#modal_form').modal('hide');
            } else {
                swal.fire('Pemberitahuan', obj.message, 'warning');
            }

        },
        error: function (response) {
            var head = 'Maaf',
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
                    var obj = JSON.parse(response['responseText']);

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
    $('#jenis_foto_id').val('');
    $('#jenis_foto_id').change();
    $('#nama_jenis').val('');
    $('#nama_jenis').change();
}
>>>>>>> 5591f9adceeea4567c71ff9aa625db866fafd2ef
