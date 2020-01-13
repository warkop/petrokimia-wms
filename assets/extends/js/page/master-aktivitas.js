"use strict";

let datatable,
    tableTarget = '#kt_table_1',
    ajaxUrl = baseUrl + 'master-aktivitas',
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

    // $('.anggaran').AutoNumeric('init', {
    //     aSep: '.',
    //     aDec: ',',
    //     aSign: '',
    //     minimumValue: '0',
    //     maximumValue: '999999999',
    //     decimalPlaces: 0,
    //     allowDecimalPadding: false,
    //     digitGroupSeparator : ''
    // });

    // $('#anggaran_tkbm').autoNumeric('init', {
    //     allowDecimalPadding: false
    // });

    // $("#anggaran_tkbm").val(($("#anggaran_tkbm").val()/1000).toFixed(3))
    // const numeric = new AutoNumeric('.anggaran', { allowDecimalPadding: false });
});

jQuery(function ($) {
    const numeric = new AutoNumeric.multiple('.anggaran', { 
        decimalCharacter: ',',
        digitGroupSeparator:'.',
        allowDecimalPadding: false 
    });

    // const numeric2 = new AutoNumeric('#anggaran_tkbm', {
    //     decimalCharacter: ',',
    //     digitGroupSeparator: '.',
    //     allowDecimalPadding: false
    // });
    // console.log(numeric);
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
                "mData": "kode_aktivitas"
            },
            {
                "mData": "nama"
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
                "aTargets": -1,
                "mData": "id",
                render: function (data, type, full, meta) {
                    return `
                    <a href="${ajaxSource+'/edit/'+full.id}" class="btn btn-orens btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Ubah Data">
                        <i class="flaticon-edit-1"></i> </button>
                    </a> `;
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
    $('#modal_form .modal-title').html('Tambah Data Kategori Alat Berat');
    $('#modal_form .modal-info').html('Isilah form dibawah ini untuk menambahkan data terkait master Kategori Alat Berat.');
    $('#modal_form').modal({
        backdrop: 'static',
        keyboard: false
    }, 'show');
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

            $('#kode_aktivitas').val(obj.data['kode_aktivitas']);
            $('#nama').val(obj.data['nama']);
            if (obj.data['produk_stok'] != null) {
                // $("#produk_rusak").attr('disabled', true);
                // $("#produk_rusak").selectpicker('refresh');

                $("#produk_stok").attr('disabled', false);
                $("#produk_stok").selectpicker('refresh');
                $('#selector_produk_stok').prop('checked', true);
                $('#produk_stok').val(obj.data['produk_stok']).change();
            }

            if (obj.data['produk_rusak'] != null) {
                // $("#produk_stok").attr('disabled', true);
                // $("#produk_stok").selectpicker('refresh');

                $("#produk_rusak").attr('disabled', false);
                $("#produk_rusak").selectpicker('refresh');
                $('#selector_produk_rusak').prop('checked', true);
                $('#produk_rusak').val(obj.data['produk_rusak']).change();
            }

            if (obj.data['pallet_stok'] != null) {
                $("#pallet_stok").attr('disabled', false);
                $("#pallet_stok").selectpicker('refresh');
                $('#selector_pallet_stok').prop('checked', true);
                $('#pallet_stok').val(obj.data['pallet_stok']).change();
            }
            if (obj.data['pallet_dipakai'] != null) {
                $("#pallet_dipakai").attr('disabled', false);
                $("#pallet_dipakai").selectpicker('refresh');
                $('#selector_pallet_dipakai').prop('checked', true);
                $('#pallet_dipakai').val(obj.data['pallet_dipakai']).change();
            }
            if (obj.data['pallet_kosong'] != null) {
                $("#pallet_kosong").attr('disabled', false);
                $("#pallet_kosong").selectpicker('refresh');
                $('#selector_pallet_kosong').prop('checked', true);
                $('#pallet_kosong').val(obj.data['pallet_kosong']).change();
            }
            
            if (obj.data['pallet_rusak'] != null) {
                $("#pallet_rusak").attr('disabled', false);
                $("#pallet_rusak").selectpicker('refresh');
                $('#selector_pallet_rusak').prop('checked', true);
                $('#pallet_rusak').val(obj.data['pallet_rusak']).change();
            }

            if (obj.data['upload_foto'] != null) {
                $('#upload_foto').prop('checked', true);
            }
            if (obj.data['connect_sistro'] != null) {
                $('#connect_sistro').prop('checked', true);
            }
            if (obj.data['pengiriman'] != null) {
                $('#pengiriman').prop('checked', true);
            }
            if (obj.data['fifo'] != null) {
                $('#fifo').prop('checked', true);
            }
            if (obj.data['pengaruh_tgl_produksi'] != null) {
                $('#pengaruh_tgl_produksi').prop('checked', true);
            } else {
                $('#pengaruh_tgl_produksi').prop('checked', false);
            }
            
            if (obj.data['internal_gudang'] != null) {
                $('#internal_gudang').prop('checked', true);
            }
            
            if (obj.data['butuh_alat_berat'] != null) {
                $('#butuh_alat_berat').prop('checked', true);
            }
            
            if (obj.data['butuh_tkbm'] != null) {
                $('#butuh_tkbm').prop('checked', true);
            }
            
            if (obj.data['tanda_tangan'] != null) {
                $('#tanda_tangan').prop('checked', true);
            }

            if (obj.data['butuh_approval'] != null) {
                $('#butuh_approval').prop('checked', true);
            }

            if (obj.data['internal_gudang'] != null || obj.data['pengiriman'] != null) {
                $('#butuh_approval').prop('disabled', false);
            }

            if (obj.data['pengaruh_tgl_produksi'] != null) {
                $('#fifo').prop('disabled', false);
            }

            if (obj.data['butuh_biaya'] != null) {
                $('#butuh_biaya').prop('checked', true);
            }
            if (obj.data['kelayakan'] != null) {
                $('#kelayakan').prop('checked', true);
            }
            if (obj.data['peminjaman'] != null) {
                $('#peminjaman').prop('checked', true);
            }

            if (obj.data['pindah_area'] != null) {
                $('#pindah_area').prop('checked', true);
            }

            if (obj.data['penerimaan_gi'] != null) {
                $('#penerimaan_gi').prop('checked', true);
            }

            if (obj.data['so'] != null) {
                $('#so').prop('checked', true);
            }

            if (obj.data['penyusutan'] != null) {
                $('#penyusutan').prop('checked', true);
            }
            
            if (obj.data['start_date'] != null) {
                $('#start_date').val(helpDateFormat(obj.data['start_date'], 'si'));
            }

            if (obj.data['end_date'] != null) {
                $('#end_date').val(helpDateFormat(obj.data['end_date'], 'si'));
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
    $("#btn_save").prop("disabled", true);
    let data = $("#form1").serializeArray();
    let type = "PUT";
    const id = $("#id").val();
    if (id) {
        type = "PATCH";
    }
    $.ajax({
        type: type,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: ajaxUrl + "/" +id,
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
                swal.fire('Ok', obj.message, 'success').then(()=>{
                    window.location = ajaxSource;
                }).catch(()=>{

                });
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
    $('#end_date').val('');
    $('#end_date').change();
}
