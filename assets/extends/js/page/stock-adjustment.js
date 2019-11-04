"use strict";
let datatable,
    tableTarget = '#kt_table_1',
    ajaxUrl = baseUrl + 'gudang',
    ajaxSource = ajaxUrl,
    totalFiles = 0,
    completeFiles = 0,
    laddaButton;

function __addedFile() {
    totalFiles++;
}

function __completeFiles() {
    completeFiles++;
}

Dropzone.autoDiscover = false;

const dropzoneOptions = {
    url: ajaxSource + '/stock_adjustment/upload-file',
    params: {
        _token: "{{ csrf_token() }}",
    },
    parallelUploads: 1000,
    // maxFiles: 1,
    addRemoveLinks: true,
    dictDefaultMessage: 'Seret File atau klik disini untuk mengunggah',
    acceptedFiles: ".jpg,.png,.jpeg,.gif",
    autoProcessQueue: false,
    init: function () {
        this.on("addedfile", function (file) {
            __addedFile(); 
            if (!file.type.match('image.*')) {
                // alert("Upload Image Only!");

                // return false;
            }
        });
        this.on("success", function (file) {
            __completeFiles();
            if (completeFiles === totalFiles) {
                /* window["myDropzone"+i+"_"+val+"_1"].removeAllFiles(); */
            }
        });
    }
};

const myDropzone = new Dropzone('#m-dropzone-one', dropzoneOptions);

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

    protectNumber(".produk_jumlah");
    protectNumber(".pallet_jumlah");
});

const load_table = function () {
    datatable = $(tableTarget);
    // begin first table
    datatable.dataTable({
        "bDestroy": true,
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: ajaxSource + "/stock-adjustment/" + id_gudang,
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
            "mData": "tanggal"
        },
        {
            "mData": null
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
                className: 'text-center',
                targets: -1,
                title: 'Actions',
                orderable: false,
                render: function (data, type, full, meta) {
                    return `
                            <button type = "button" class="btn btn-orens btn-elevate btn-icon" onclick="edit(${full.id})" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Edit">
                            <i class="flaticon-edit-1"></i> </button>
                        <button type = "button" onclick="showme()" class="btn btn-danger btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Hapus"><i class="flaticon-delete"></i> </button>`;
                },
            }, {
                className: 'text-center',
                targets: -2,
                render: function (data, type, full, meta) {
                    var image = '<a class="fancybox" rel="ligthbox" href="' + data + '"><img class="img-responsive" width="100px" src="' + data + '" alt=""></a>';
                    return image;
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

function tambahProduk() {
    const tableId = "table_produk";
    const rows = document.getElementById(tableId).getElementsByTagName("tr").length;

    let html = `<tr class="produk_baris" id="baris-produk-${rows}">
                    <td>${rows}</td>
                    <td>
                        <select class="form-control m-select2 pilih_produk" id="produk-${rows}" name="produk[]" onchange="checkProduk(this)" aria-placeholder="Pilih Produk" style="width: 100%;">
                            
                        </select>
                    </td>
                    <td>
                        <select class="form-control kt-selectpicker" name="action_produk[]" id="produk-status-${rows}" style="width: 100%;">
                            <option value="1">Menambah</option>
                            <option value="2">Mengurangi</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="produk_jumlah[]" id="produk-jumlah-${rows}" class="form-control" placeholder="Masukkan jumlah">
                    </td>
                    <td>
                        <button class="btn btn-danger btn-elevate btn-icon btn-sm" onclick="hapusProduk(${rows})"><i class="la la-trash"></i></button>
                    </td>
                </tr>`;
    $("#table_produk tbody").append(html);
    $('#produk-' + rows).select2({
        placeholder: "Pilih Produk"
    });
    protectNumber(`#produk-jumlah-${rows}`, 10);

    getProduk(`#produk-${rows}`);
    $(`#produk-${rows}`).val(id);
}

function hapusProduk(id) {
    if (id != '') {
        $("#baris-produk-" + id).remove();
    } else {
        $(".produk_baris").remove();
    }
}

function tambahPallet(id='') {
    const tableId = "table_pallet";
    const rows = document.getElementById(tableId).getElementsByTagName("tr").length;

    let html = `<tr class="pallet_baris" id="baris-pallet-${rows}">
                    <td>${rows}</td>
                    <td>
                        <select class="form-control m-select2 pilih_pallet" id="pallet-${rows}" name="pallet[]" onchange="checkPallet(this)" aria-placeholder="Pilih Pallet" style="width: 100%;">
                            
                        </select>
                    </td>
                    <td>
                        <select class="form-control kt-selectpicker" name="action_pallet[]" id="pallet-status-${rows}" style="width: 100%;">
                            <option value="1">Menambah</option>
                            <option value="2">Mengurangi</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="pallet_jumlah[]" id="pallet-jumlah-${rows}" class="form-control" placeholder="Masukkan jumlah">
                    </td>
                    <td>
                        <button class="btn btn-danger btn-elevate btn-icon btn-sm" onclick="hapusPallet(${rows})"><i class="la la-trash"></i></button>
                    </td>
                </tr>`;
    $("#table_pallet tbody").append(html);
    $('#pallet-' + rows).select2({
        placeholder: "Pilih Pallet"
    });
    protectNumber(`#pallet-jumlah-${rows}`, 10);

    getPallet(`#pallet-${rows}`);
}

function hapusPallet(id) {
    if (id != '') {
        $("#baris-pallet-" + id).remove();
    } else {
        $(".pallet_baris").remove();
    }
}

function getProduk(target) {
    $.ajax({
        url:ajaxSource+"/"+"get-produk",
        success:(res)=>{
            const obj = res.data;

            let html = `<option value="">Pilih Produk</option>`;
            obj.forEach((item, index) => {
                html += `<option value="${item.id}">${item.nama}</option>`;
            });

            $(target).html(html);
        },
        error:()=>{
            
        }
    });
}

function getPallet(target) {
    $.ajax({
        url: ajaxSource+"/"+"get-pallet",
        success:(res)=>{
            const obj = res.data;

            let html = `<option value="">Pilih Pallet</option>`;
            obj.forEach((item, index) => {
                html += `<option value="${item.id}">${item.nama}</option>`;
            });

            $(target).html(html);
        },
        error:()=>{
            
        }
    });
}

function checkProduk(target) {
    let lokasi = $('.pilih_produk');
    let chosen = false;

    const id_target = $(target).attr('id').replace('-', '');
    for (let i = 0; i < lokasi.length; i++) {
        if ($(target).val() != "" && $(target).attr('id') != $(lokasi[i]).attr('id') && $(target).val() == $(lokasi[i]).val()) {
            chosen = true;
        }
    }



    if (chosen == true) {
        $(target).val('').trigger('change.select2');
        swal.fire('Pemberitahuan', 'Produk sudah dipilih. Silahkan Pilih produk lain!', 'error');
        $("#produk-" + id_target).val("");
    }
}

function checkPallet(target) {
    let lokasi = $('.pilih_pallet');
    let chosen = false;

    const id_target = $(target).attr('id').replace('-', '');
    for (let i = 0; i < lokasi.length; i++) {
        if ($(target).val() != "" && $(target).attr('id') != $(lokasi[i]).attr('id') && $(target).val() == $(lokasi[i]).val()) {
            chosen = true;
        }
    }
    if (chosen == true) {
        $(target).val('').trigger('change.select2');
        swal.fire('Pemberitahuan', 'Pallet sudah dipilih. Silahkan Pilih pallet lain!', 'error');
        $("#pallet-" + id_target).val("");
    }
}

function edit(id = '') {
    $('#id').val(id);
    $('#action').val('edit');
    $('#btn_save').html('Simpan Data');
    $('#modal_form .modal-title').html('Edit Data Kategori Alat Berat');
    $('#modal_form .modal-info').html('Isilah form dibawah ini untuk mengubah data master Kategori Alat Berat sesuai kebutuhan.');
    $('#modal_form').modal({
        backdrop: 'static',
        keyboard: false
    }, 'show');

    $.ajax({
        type: "GET",
        url: ajaxUrl + "/stock-adjustment/"+id_gudang + "/" + id,
        beforeSend: function () {
            preventLeaving();
            $('.btn_close_modal').addClass('hide');
            $('.se-pre-con').show();
        },
        success: function (response) {
            window.onbeforeunload = false;
            $('.btn_close_modal').removeClass('hide');
            $('.se-pre-con').hide();

            let obj_adjustment = response.data.material_adjustment;
            let obj_trans = response.data.material_trans;

            console.log(obj_adjustment)
            console.log(obj_trans)
            if (obj_adjustment.tanggal != null) {
                $('#tanggal').val(helpDateFormat(obj_adjustment.tanggal, 'si'));
            }

            obj_trans.forEach(element => {
                tambahProduk();
            });
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
        url: ajaxUrl +"/stock-adjustment/"+id_gudang,
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
                console.log('moden');
                // datatable.api().ajax.reload();
                swal.fire('Ok', "Data berhasil disimpan", 'success');
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

function tambah() {
    reset_form();
    $('#id').val('');
    $('#action').val('add');
    $('#btn_save').html('Tambah Data');
    $('#modal_form .modal-title').html('Tambah Data Stock Adjustment');
    $('#modal_form .modal-info').html('Isilah form dibawah ini untuk menambahkan data terkait stock adjustment.');
    $('#modal_form').modal({
        backdrop: 'static',
        keyboard: false
    }, 'show');
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

var KTDatatablesDataSourceHtml = function () {
    var dataJSONArray = JSON.parse(
        '[[1,"10-10-2019", "https://www.sikumis.com/media/frontend/products/Urea-Petro-(Non-Sub).jpg"], [2, "10-10-2019", "https://www.sikumis.com/media/frontend/products/Urea-Petro-(Non-Sub).jpg"], [3, "10-10-2019", "https://www.sikumis.com/media/frontend/products/Urea-Petro-(Non-Sub).jpg"]]');
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
            }, {
                className: 'text-center',
                targets: -2,
                render: function (data, type, full, meta) {
                    var image = '<a class="fancybox" rel="ligthbox" href="' + data + '"><img class="img-responsive" width="100px" src="' + data + '" alt=""></a>';
                    return image;
                },
            }],
            "drawCallback": function (settings) {
                $('[data-toggle="kt-tooltip"]').tooltip();
                $(".fancybox").fancybox({

                    openEffect: "none",

                    closeEffect: "none"

                });
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
// jQuery(document).ready(function () {
//     KTDatatablesDataSourceHtml.init();
// });