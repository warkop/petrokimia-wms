"use strict";

let datatable,
    tableTarget = '#kt_table_1',
    ajaxUrl = baseUrl + 'gudang',
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

    protectNumber("#id_sloc");
    protectNumber("#id_plant");
});

var load_table = function () {
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
                "mData": "id_sloc"
            },
            {
                "mData": "id_plant"
            },
            {
                "mData": "nama"
            },
            {
                "mData": "tipe_gudang"
            },
            {
                "mData": "jumlah"
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
                "aTargets": [4],
                // title: 'Jumlah Pupuk',
                "orderable": true,
                render: function (data, type, full, meta) {
                    let result = '';
                    if (full.tipe_gudang == 1) {
                        result = "Internal";
                    } else {
                        result = "Eksternal";
                    }
                    // var result = '<a href="" data-toggle="modal" data-target="#kt_modal_pupuk">' + data + '</a>';
                    return result;
                },
            },
            {
                "aTargets": [6],
                "mData": "id",
                "orderable": false,
                render: function (data, type, full, meta) {
                    return `
                        <button class="btn btn-orens btn-elevate btn-elevate-air dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Actions
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="` + baseUrl + `stok-adjustment"><i class="flaticon-cogwheel-1"></i> Stok adjustment</a> 
                            <a class="dropdown-item" href="${baseUrl+'list-area/'+full.id}"><i class="flaticon-symbol"></i> List area</a>
                            <a class="dropdown-item" href="` + baseUrl + `list-pallet"><i class="flaticon-layers"></i> List pallet</a>
                            <button class="dropdown-item" onclick="edit(${full.id})" data-toggle="modal" data-target="#kt_modal_1"><i class="flaticon-edit-1"></i> Edit data</button>
                        </div>`;
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
        },
        "fnDrawCallback": function (settings) {
            $('[data-toggle="kt-tooltip"]').tooltip();
        }
    });
};

function tambah() {
    reset_form();
    $('#id').val('');
    $('#action').val('add');
    $('#btn_save').html('Tambah Data');
    $('#modal_form .modal-title').html('Tambah Data Gudang');
    $('#modal_form .modal-info').html('Isilah form dibawah ini untuk menambahkan data terkait Gudang.');
    $('#modal_form').modal({
        backdrop: 'static',
        keyboard: false
    }, 'show');
}

function loadPallet() {
    $.ajax({
        url: ajaxSource+"load_pallet",
        dataType: 'json',
        contentType: 'application/json',
        success: res =>{
            panjang = res.length;
            for (let i=0; i<panjang; i++) {
                
            }
        },
        error:() =>{

        }
    });
}

function edit(id = '') {
    reset_form();
    $('#id').val(id);
    $('#action').val('edit');
    $('#btn_save').html('Simpan Data');
    $('#modal_form .modal-title').html('Edit Data Gudang');
    $('#modal_form .modal-info').html('Isilah form dibawah ini untuk mengubah data Gudang sesuai kebutuhan.');
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
                $('#id_sloc').val(obj.data['id_sloc']);
                $('#id_plant').val(obj.data['id_plant']);
                $('#tipe_gudang').val(obj.data['tipe_gudang']).change();
                $('#id_karu').val(obj.data['id_karu']).trigger('change.select2');
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

    $.ajax({
        type: "GET",
        url: ajaxUrl + "/load-material/" + id,
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
                const panjang = obj.data.length;
                for (let i=0; i<panjang; i++) {
                    // $('#id-material-'+obj.data[i]['id_material']).val(obj.data[i]['id_material']);
                    $('#stok-min-'+obj.data[i]['id_material']).val(obj.data[i]['stok_min']);
                    console.log(obj.data[i]['stok_min'])
                }
                // $('#id_plant').val(obj.data['id_plant']);
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
    $('#id').val('');
    $('#id').change();
    $('#nama').val('');
    $('#nama').change();
    $('#id_sloc').val('');
    $('#id_sloc').change();
    $('#id_plant').val('');
    $('#id_plant').change();
    $('#tipe_gudang').val('');
    $('#tipe_gudang').change();
    $('#id_karu').val('');
    $('#id_karu').change();
    $('.material').val('');
    $('.material').change();
    $('#start_date').val('');
    $('#start_date').change();
    $('#end_date').val('');
    $('#end_date').change();
}
