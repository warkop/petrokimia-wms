"use strict";

let datatable,
    tableTarget = '#kt_table_1',
    ajaxUrl = baseUrl + 'master-pemetaan-sloc',
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
            "mData": "nama"
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
                "aTargets": -1,
                "mData": "id",
                "orderable":false,
                render: function (data, type, full, meta) {
                    return `
                        <button type="button" onclick="edit(${full.id})" class="btn btn-orens btn-elevate btn-icon">
                            <i class="flaticon-edit-1"></i> </button>
                    `;
                },
            }
        ],
        "drawCallback": function (settings) {
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
    $("#modal_form").modal("show");

    reset_form();

}

function edit(id = '') {
    reset_form();
    console.log(id)
    $('#id').val(id);
    $('#action').val('edit');
    $('#btn_save').html('Simpan Data');
    $('#modal_form .modal-title').html('Edit Data Pemetaan');
    $('#modal_form .modal-info').html('Isilah form dibawah ini untuk mengubah data master Pemetaan sesuai kebutuhan.');
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

                let det = obj.data.detail_pemetaan_sloc;
                let no = 1;
                det.forEach(element => {
                    loadSloc(no, element.id_sloc);
                    // no++;
                });

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
    const id = $("#id").val();
    let url = ajaxUrl;
    if (id) {
        type = "PATCH";
        url = ajaxUrl + "/" + id 
    }
    console.log(id)
    $.ajax({
        type: type,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
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

function drawSloc(no) {
    // return 
}

$("#btnTambah").click(function () {
    const tableId = "#inputAdjst";
    const rows = $(tableId).attr("value");
    console.log(rows);
    loadSloc(rows);
});

$("body").on('click', '.button_hapus', function (e) {
    $(this).parent().parent().remove();

    let angka = $("#inputAdjst").attr("value");
    angka--;

    $("#inputAdjst").attr("value", angka);
});

// $('.select2Custom').select2({
//     placeholder: "Pilih Id "
// });

function loadSloc(id, value="") {
    let angka = $("#inputAdjst").attr("value");
    angka++;

    $("#inputAdjst").attr("value", angka);
    id = angka
    var xyz = ` 
        <div class="row sloc_list" id="baris-${id}">
            <div class="col-10 mb1">
                <select class="form-control select2Custom m-select2 pilih_sloc" id="sloc-${id}" name="detail_sloc[]" onchange="checkUnique(this)" style="width: 100%">
                </select>
            </div>
            <div class="col-2">
                <a href="javascript:void(0)" class="btn button_hapus btn-outline-danger btn-sm"><i class="fa fa-trash" style="padding: .9rem;"></i></a>
            </div>
        </div>
        `
    $("#inputAdjst").append(xyz);

   

    $('.select2Custom').select2({
        placeholder: "Pilih Sloc",
        dropdownParent: $("#inputAdjst")
    });
    $.ajax({
        url: ajaxSource + "/load-sloc",
        success: response => {
            const obj = response.data;

            let element = '<option value="" selected>Pilih Sloc</option>';
            const panjang = obj.length;
            for (let i = 0; i < panjang; i++) {
                element += `<option value="${obj[i].id}">${obj[i].id_sloc}</option>`;
            }

            $("#sloc-" + id).html(element);
            $("#sloc-" + id).val(value);
        },
        error: response => {

        }
    })
}

function checkUnique(target) {
    let lokasi = $(".pilih_sloc");
    let chosen = false;

    const id_target = $(target)
        .attr("id")
        .replace("-", "");
    for (let i = 0; i < lokasi.length; i++) {
        if (
            $(target).val() != "" &&
            $(target).attr("id") != $(lokasi[i]).attr("id") &&
            $(target).val() == $(lokasi[i]).val()
        ) {
            chosen = true;
        }
    }

    if (chosen == true) {
        $(target).val("").trigger("change.select2");
        swal.fire(
            "Pemberitahuan",
            "Sloc sudah dipilih. Silahkan Pilih Sloc lain!",
            "error"
        );
        $("#sloc-" + id_target).val("");
    }
}


function reset_form(method = '') {
    $('#id').val('');
    $('#id').change();
    $('#nama').val('');
    $('#inputAdjst').html('');
    $('#inputAdjst').attr("value", 0);
}