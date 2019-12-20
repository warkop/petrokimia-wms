"use strict";
let datatable,
    tableTarget = '#kt_table_1',
    ajaxUrl = baseUrl + 'list-pallet/' + id_gudang,
    ajaxSource = ajaxUrl,
    laddaButton;

$(document).ready(function () {
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

    loadMaterial();
});

const loadMaterial = () => {
    $("#material").select2({
        allowClear: true,
        placeholder: 'Ketikkan nama pallet',
        dropdownParent: $("#modal_form"),
        // minimumInputLength: 3,
        delay: 250,
        ajax: {
            url: baseUrl + 'list-pallet' + '/get-material',
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
    });
}

const load_table = function () {
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
                "mData": null
            },
            {
                "mData": "nama_material"
            },
            {
                "mData": "jumlah"
            },
            {
                "mData": "tipe"
            },
            {
                "mData": "status_pallet"
            },
            {
                "mData": "alasan"
            }
            // {
            //     "mData": "id"
            // }
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
                "aTargets": [1],
                "mData": "tanggal",
                "mRender": function (data, type, full, draw) {
                    const t = helpDateFormat(full.tanggal, 'si');

                    return t;
                }
            },
            {
                "aTargets": [4],
                "mData": null,
                "mRender": function (data, type, full, draw) {
                    let text = "";
                    let klas = "";
                    if (full.tipe == 1) {
                        text = "Mengurangi";
                        klas = "kt-badge kt-badge--warning kt-badge--inline"
                    } else if (full.tipe == 2) {
                        text = "Menambah";
                        klas = "kt-badge kt-badge--danger kt-badge--inline";
                    }

                    const label = `<span class="${klas}">${text}</span>`;

                    return label;
                }
            },
            {
                "aTargets": [5],
                "mRender": function (data, type, full, draw) {
                    let text = "";
                    let klas = "";
                    if (full.status_pallet == 1) {
                        text = "Stok";
                        klas = "kt-badge kt-badge--success kt-badge--inline"
                    } else if (full.status_pallet == 2) {
                        text = "Dipakai";
                        klas = "kt-badge kt-badge--warning kt-badge--inline"
                    } else if (full.status_pallet == 3) {
                        text = "Kosong";
                        klas = "kt-badge kt-badge--danger kt-badge--inline"
                    } else if (full.status_pallet == 4) {
                        text = "Rusak";
                        klas = "kt-badge kt-badge--dark kt-badge--inline"
                        
                    }

                     const label = `<span class="${klas}">${text}</span>`;

                     return label;
                }
            }
            // {
            //     "aTargets": -1,
            //     "mData": "id",
            //     "orderable": false,
            //     render: function (data, type, full, meta) {
            //         return "";
            //         // return `
            //         //         <button type="button" onclick="detail(${full.id})" class="btn btn-orens btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Edit">
            //         //         <i class="flaticon-edit-1"></i> </button>`;
            //     },
            // }
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

function hapus(id) {
    swal.fire({
        title: 'Are you sure?',
        text: "Data yang sudah dihapus tidak bisa dibatalkan.",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus data!'
    }).then(function (result) {
        if (result.value) {
            $.ajax({
                url: baseUrl + 'list-pallet/'+id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "delete",
                success: res => {
                    datatable.api().ajax.reload();
                    swal.fire(
                        'Berhasil!',
                        'Data berhasil dihapus.',
                        'success'
                    );
                },
                error: (err, oo, pp) => {
                    console.log(err);
                    swal.fire(
                        'Berhasil!',
                        'Data berhasil dihapus.',
                        'success'
                    );
                }
            });
        }
    });
}

function tambah() {
    reset_form();
    $('#id').val('');
    $('#action').val('add');
    $('#btn_save').html('Tambah Data');
    $('#modal_form .modal-title').html('Tambah Data List Area');
    $('#modal_form .modal-info').html('Isilah form dibawah ini untuk menambahkan data terkait master List Area.');
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
    $('#modal_form .modal-title').html('Edit Data List Alat Berat');
    $('#modal_form .modal-info').html('Isilah form dibawah ini untuk mengubah data master List Alat Berat sesuai kebutuhan.');
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

            const obj = response;

            if (obj.status == "OK") {
                $('#tanggal').val(helpDateFormat(obj.data['tanggal'], 'si'));
                $('#material').val(obj.data['id_material']);
                $('#jumlah').val(obj.data['jumlah']);
                $('#alasan').val(obj.data['alasan']);
                if (obj.data['tipe'] == 1) {
                    $('#mengurangi').prop('checked', true);
                } else if (obj.data['tipe'] == 2) {
                    $('#menambah').prop('checked', true);
                }
                
                if (obj.data['tipe'] == 1) {
                    $('#stok').prop('checked', true);
                } else if (obj.data['tipe'] == 2) {
                    $('#dipakai').prop('checked', true);
                } else if (obj.data['tipe'] == 3) {
                     $('#kosong').prop('checked', true);
                } else if (obj.data['tipe'] == 4) {
                     $('#rusak').prop('checked', true);
                }
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

function load_selected_id_material(id_material_sap = '') {
    /* Fetch the preselected item, and add to the control */
    var setJalan = $('#material');
    if (id_material_sap != '') {
        $.ajax({
            type: 'GET',
            url: ajaxUrl + '/get-material/' + id_material_sap,
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
            var option = new Option(obj.data.Material_number, obj.data.Material_number, true, true);
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

function detail(id) {
    $.ajax({
        type: 'GET',
        url: ajaxUrl + '/' + id,
        beforeSend: function () {
            preventLeaving();
        },
        success: function (response) {
            window.onbeforeunload = false;

            var obj = response;
            // console.log(obj)
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
        var option = new Option(obj.data.Material_number, obj.data.Material_number, true, true);
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

function simpan() {
    // var file = new FormData($("#form1")[0]);
    const data = $("#form1").serializeArray();
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

            const obj = response;

            if (obj.status == "OK") {
                // datatable.api().ajax.reload();
                swal.fire('Ok', obj.message, 'success').then(()=>{
                    location.reload();
                });
                // $('#modal_form').modal('hide');
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
                            message = '';
                            if (obj == null) {
                                message = response.responseJSON.message;
                            } else {
                                const temp = Object.values(obj);
                                
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
    });
}

function reset_form(method = '') {
    $('#id').val('');
    $('#material').select2("val", "");
    $('#jumlah').val('');
    $('#alasan').val('');
    // $('input[name=tipe]').val(1);
    // $('input[name=jenis]').val(1);
}

var KTDatatablesDataSourceHtml = function () {
    var dataJSONArray = JSON.parse(
        '[[1, "10-10-2019", "3", "Mengurangi", "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout."], [2, "10-10-2019", "3", "Menambah", "Alasan"]]');
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
                        </a>
                        <button type = "button" onclick="showme()" class="btn btn-danger btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Hapus"><i class="flaticon-delete"></i> </button>`;
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

function showme() {
    swal.fire({
        title: 'Are you sure?',
        text: "Data yang sudah dihapus tidak bisa dibatalkan.",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus data!'
    }).then(function (result) {
        if (result.value) {
            swal.fire(
                'Berhasil!',
                'Data berhasil dihapus.',
                'success'
            )
        }
    });
}
jQuery(document).ready(function () {
    // KTDatatablesDataSourceHtml.init();
});