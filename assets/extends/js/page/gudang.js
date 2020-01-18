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

    // protectNumber("#id_sloc");
    // protectNumber("#id_plant");
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
                "mData": "id"
            }
        ],
        "aaSorting": [
            [4, 'asc']
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
                "aTargets": -2,
                "mData": 'tipe_gudang',
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
                "aTargets": -1,
                "mData": "id",
                "orderable": false,
                render: function (data, type, full, meta) {
                    return `
                        <button class="btn btn-orens btn-elevate btn-elevate-air dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Actions
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="` + ajaxSource + `/stock-adjustment/${full.id}"><i class="flaticon-cogwheel-1"></i> Stok adjustment</a> 
                            <a class="dropdown-item" href="${baseUrl+'list-area/'+full.id}"><i class="flaticon-symbol"></i> List area</a>
                            <a class="dropdown-item" href="${baseUrl+'list-pallet/'+full.id}"><i class="flaticon-layers"></i> List pallet</a>
                            <button class="dropdown-item" onclick="edit(${full.id})" data-toggle="modal" data-target="#kt_modal_1"><i class="flaticon-edit-1"></i> Edit data</button>
                            <a class="dropdown-item" href="` + ajaxSource + `/layout-gudang/${full.id}"><i class="flaticon-app"></i> Layout Gudang</a>
                            <a class="dropdown-item" href="javascript:;" onclick="showModalAktivitasGudang(${full.id})"><i class="flaticon-list"></i> Aktivitas Gudang</a>
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
    $('#start_date').val(helpDateFormat(Date.now(), 'si'));
}

function loadPallet() {
    $.ajax({
        url: ajaxSource+"load-pallet",
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

function loadListAktivitas(id_gudang) {
    $.ajax({
        url: ajaxUrl + '/load-aktivitas-gudang/' + id_gudang,
        success: (response) => {
            const obj = response.data.data;
            $("#label_aktivitas_gudang").html("Aktivitas Gudang <strong>" + response.data.nama_gudang +"</strong>");
            let html = "";
            let link = "";
            obj.forEach(element => {
                link = baseUrl + "master-aktivitas/edit/" + element.id_aktivitas;
                if (element.aktivitas != null) {
                    html += `<tr>
                                <td class="text-left">${element.aktivitas.nama}</td>
                                <td>
                                    <a class="btn btn-primary btn-sm" target="_blank" href="${link}" data-toggle="kt-tooltip" data-placement="top" title="Ke halaman ubah aktivitas"><i class="fa fa-clipboard-list"></i></a>
                                    <button type="button" class="btn btn-danger btn-sm _btnHapus" onclick="removeAktivitas(${element.id_gudang}, ${element.id_aktivitas})" data-toggle="kt-tooltip" data-placement="top" title="Hapus dari daftar"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>`;
                }
            });

            $("#list_aktivitas").html(html);

        },
        error: (response) => {
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

function loadAktivitasGudang(id_gudang) {
    $("#aktivitas_gudang").select2({
        allowClear: true,
        placeholder: 'Ketikkan aktivitas',
        dropdownParent: $("#modalAktivitasGudang"),
        // minimumInputLength: 3,
        delay: 250,
        ajax: {
            url: ajaxUrl + '/get-aktivitas/'+id_gudang,
            dataType: 'json',
            processResults: function (response) {
                /*Tranforms the top-level key of the response object from 'items' to 'results'*/
                return {
                    results: $.map(response.data, function (item) {
                        return {
                            text: item.nama,
                            id: item.id
                        }
                    })
                };
            }
        }
    }).on("select2:select", (q) => {
        const id_plant = q.params.data.id_plant;
        $("#id_plant").val(id_plant);
    });

    loadListAktivitas(id_gudang);
}

function tambahAktivitas() {
    const id_aktivitas = $("#aktivitas_gudang").val();
    const id_gudang = $("#id_gudang").val();
    $.ajax({
        url: ajaxSource+"/select-aktivitas",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method:"post",
        data:{
            id_aktivitas: id_aktivitas,
            id_gudang: id_gudang
        },
        success:(response)=>{
            const obj = response.data;
            let head = 'Pemberitahuan',
                message = response.message,
                type = 'success';

            swal.fire(head, message, type);
            loadListAktivitas(id_gudang);
        },
        error:(response)=>{
            let head = 'Maaf',
                message = 'Terjadi kesalahan koneksi',
                type = 'error';
            window.onbeforeunload = false;

            if (response['status'] == 401 || response['status'] == 419) {
                location.reload();
            } else {
                if (response['status'] != 404 && response['status'] != 500) {
                    let obj = JSON.parse(response['responseText']);
                    console.log(obj.message);
                    if (!$.isEmptyObject(obj.message)) {
                        if (obj.code > 450) {
                            head = 'Maaf';
                            message = obj.message;
                            type = 'error';
                        } else {
                            head = 'Pemberitahuan';
                            type = 'warning';

                            obj = response.responseJSON.errors;
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
                            window.onbeforeunload = false;
                            $('.btn_close_modal').removeClass('hide');
                            $('.se-pre-con').hide();

                            
                        }
                    }
                }

                swal.fire(head, message, type);
            }
        }
    });
}

function removeAktivitas(id_gudang, id_aktivitas) {
    $.ajax({
        url: ajaxSource + "/remove-aktivitas/" + id_gudang + "/" + id_aktivitas,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method:"delete",
        success:(response) =>{
            let head = 'Pemberitahuan',
                message = response.message,
                type = 'success';

            swal.fire(head, message, type);
            loadListAktivitas(id_gudang);
        },
        error:(response) => {
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
    $('#end_date').val('');
    $('#end_date').change();
}
