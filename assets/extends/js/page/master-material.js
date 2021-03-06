"use strict";

let datatable,
    tableTarget = '#kt_table_1',
    ajaxUrl = baseUrl + 'master-material',
    ajaxSource = ajaxUrl,
    laddaButton;

jQuery(document).ready(function () {
    load_table();

    if (typeof datatable !== 'undefined') {
        datatable.on('draw.dt', function () {
            $('[data-toggle=kt-tooltip]').tooltip({
                html: true
            });
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
    

    $("#id_material_sap").select2({
        allowClear: true,
        placeholder: 'Ketikkan id material',
        dropdownParent: $("#modal_form"),
        // minimumInputLength: 3,
        delay: 250,
        ajax: {
            url: ajaxUrl + '/sap',
            dataType: 'json',
            processResults: function (response) {
                /*Tranforms the top-level key of the response object from 'items' to 'results'*/
                return {
                    results: $.map(response.data, function (item) {
                        return {
                            text: item.MaterialNumber,
                            id: item.MaterialNumber
                            // material_desc: item.MaterialDesc
                        }
                    })
                };
            }
        }
    }).on("select2:select", (q)=>{
        // const material_desc = q.params.data.material_desc;
    });
});

let load_table = function () {
    datatable = $(tableTarget);
    // begin first table
    datatable.dataTable({
        "bDestroy": true,
        "processing": true,
        "serverSide": true,
        "scrollX":true,
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
                "mData": "id_material_sap"
            },
            {
                "mData": "nama"
            },
            {
                "mData": "kategori"
            },
            {
                "mData": "berat"
            },
            {
                "mData": "koefisien_pallet"
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
        "aoColumnDefs": [
            {
                "aTargets": [0],
                "mData": "id",
                "mRender": function (data, type, full, draw) {
                    let row = draw.row;
                    let start = draw.settings._iDisplayStart;
                    let length = draw.settings._iDisplayLength;

                    let counter = (start + 1 + row);

                    return counter;
                }
            }, {
                "aTargets": [3],
                "mData": "id",
                "mRender": function (data, type, full, draw) {
                    if (full.kategori == 1) {
                        return `<span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill kt-badge--rounded" data-toggle="kt-tooltip" data-placement="top" title="Material kategori Produk">Produk</span>`;
                    } else if (full.kategori == 2) {
                        return `<span class="kt-badge kt-badge--info kt-badge--inline kt-badge--pill kt-badge--rounded" data-toggle="kt-tooltip" data-placement="top" title="Material kategori Pallet">Pallet</span>`;
                    } else {
                        return `<span class="kt-badge kt-badge--dark kt-badge--inline kt-badge--pill kt-badge--rounded" data-toggle="kt-tooltip" data-placement="top" title="Material kategori Lain-lain">Lain-lain</span>`;
                    }
                }
            },
            {
                "aTargets": -1,
                "mData": "id",
                render: function (data, type, full, meta) {
                    return `
                        <button type = "button" onclick="edit(${full.id})" class="btn btn-orens btn-elevate btn-icon" data-container="body" data-togle="x-tooltip" data-placement="left" title="Ubah Data">
                        <i class="flaticon-edit-1"></i> </button>
                    `;
                },
            }
        ],
        "drawCallback": function( settings ) {
            $('[data-togle="x-tooltip"]').tooltip({
                boundary: "window",
                container: "body",
                trigger: "hover"
            });
        },
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
    $('#modal_form .modal-title').html('Tambah Data Material');
    $('#modal_form .modal-info').html('Isilah form dibawah ini untuk menambahkan data terkait master Material.');
    $('#modal_form').modal({
        backdrop: 'static',
        keyboard: false
    }, 'show');
    $('#start_date').val(helpDateFormat(Date.now(), 'si'));
}

function pilihKategori(target) {
    $("#berat").val("");
    $("#koefisien_pallet").val("");
    if ($(target).val() == 1) {
        $("#berat").prop("disabled", false);
        $("#koefisien_pallet").prop("disabled", false);
    } else {
        $("#berat").prop("disabled", true);
        $("#koefisien_pallet").prop("disabled", true);
    }
}

function edit(id = '') {
    reset_form();
    $('#id').val(id);
    $('#action').val('edit');
    $('#btn_save').html('Simpan Data');
    $('#btn_save').prop('disabled', true);
    $('#modal_form .modal-title').html('Edit Data Material');
    $('#modal_form .modal-info').html('Isilah form dibawah ini untuk mengubah data master Material sesuai kebutuhan.');
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
            $('#btn_save').prop('disabled', false);
            window.onbeforeunload = false;
            $('.btn_close_modal').removeClass('hide');
            $('.se-pre-con').hide();

            let obj = response;
            if (obj.status == "OK") {
                $('#id_material_sap').val(obj.data['id_material_sap']);
                $('#nama').val(obj.data['nama']);
                $('#kategori').val(obj.data['kategori']).change();
                $('#berat').val(obj.data['berat']);
                $('#koefisien_pallet').val(obj.data['koefisien_pallet']);
                if (obj.data['start_date'] != null) {
                    $('#start_date').val(helpDateFormat(obj.data['start_date'], 'si'));
                }

                if (obj.data['end_date'] != null) {
                    $('#end_date').val(helpDateFormat(obj.data['end_date'], 'si'));
                }

                if (obj.data.sap != null) {
                    load_selected_id_material(obj.data.sap['MaterialNumber']);

                    if (obj.data.sap['MaterialNumber'] != null) {
                        $('#id_material_sap').val(obj.data.sap['MaterialNumber']);
                        $('#id_material_sap').html(obj.data.sap['MaterialNumber']); 
                    }
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

function load_selected_id_material(id_material_sap = '') {
    /* Fetch the preselected item, and add to the control */
    var setJalan = $('#id_material_sap');
    if (id_material_sap != '') {
        $.ajax({
            type: 'GET',
            url: ajaxUrl + '/get-material-sap/' + id_material_sap,
            beforeSend: function () {
                preventLeaving();
            },
            success: function (response) {
                window.onbeforeunload = false;

                var obj = response;

                if (obj.status == "OK") {
                    /*OK*/
                } else {
                    swal.fire('Pemberitahuan', obj.message, 'warning');
                }
            },
            error: function (response) {
                var head = 'Maaf',
                    message = 'Terjadi kesalahan koneksi',
                    type = 'error';
                window.onbeforeunload = false;

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
        }).then(function (response) {
            var obj = response;
            /* create the option and append to Select2 */
            var option = new Option(obj.data.MaterialNumber, obj.data.MaterialNumber, true, true);
            setJalan.append(option).trigger('change');

            /* manually trigger the `select2:select` event */
            setJalan.trigger({
                type: 'select2:select',
                params: {
                    data: obj.data
                }
            });
        });
    }
}

function simpan() {
    let data = $("#form1").serializeArray();
    $('#btn_save').prop('disabled', true);
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
    $('#id_material_sap').val('');
    $('#id_material_sap').change();
    $('#nama').val('');
    $('#nama').change();
    $('#kategori').val('');
    $('#kategori').change();
    $('#berat').val('');
    $('#berat').change();
    $('#koefisien_pallet').val('');
    $('#koefisien_pallet').change();
    $('#end_date').val('');
    $('#end_date').change();
}
