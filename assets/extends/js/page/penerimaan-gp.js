"use strict";

let datatable,
    tableTarget = "#kt_table_1",
    ajaxUrl = baseUrl + "penerimaan-gp",
    ajaxSource = ajaxUrl,
    totalFiles = 0,
    completeFiles = 0,
    laddaButton;

$(document).ready(function () {
    loadTable();
});

const loadTable = function () {
    datatable = $(tableTarget);
    // begin first table
    datatable.dataTable({
        bDestroy: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: ajaxSource,
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            }
        },
        sPaginationType: "full_numbers",
        aoColumns: [{
                mData: "id"
            },
            {
                mData: "tanggal"
            },
            {
                mData: "nama_aktivitas"
            },
            {
                mData: "approve"
            },
            {
                mData: "id"
            }
        ],
        aaSorting: [
            [1, "desc"]
        ],
        lengthMenu: [10, 25, 50, 75, 100],
        pageLength: 10,
        aoColumnDefs: [{
                aTargets: [0],
                mData: "id",
                mRender: function (data, type, full, draw) {
                    let row = draw.row;
                    let start = draw.settings._iDisplayStart;
                    let length = draw.settings._iDisplayLength;

                    let counter = start + 1 + row;

                    return counter;
                }
            },
            {
                aTargets: [1],
                mData: "tanggal",
                mRender: function (data, type, full, draw) {
                    return helpDateFormat(full.tanggal, "si");
                }
            },
            {
                className: "text-center",
                targets: -2,
                title: "Status",
                // orderable: false,
                render: function (data, type, full, meta) {
                    if (full.approve == null) {
                        return `<span class="badge badge-warning">Belum Approve</span>`;
                    } else {
                        return `<span class="badge badge-success">Sudah Approve</span>`
                    }
                }
            },
            {
                className: "text-center",
                targets: -1,
                title: "Actions",
                orderable: false,
                render: function (data, type, full, meta) {
                    return `<a href="` + ajaxSource + "/" + full.id + `">
                            <button type = "button" class="btn btn-primary btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Detail Penerimaan GP">
                            <i class="flaticon2-zig-zag-line-sign"></i> </button>
                        </a>`;
                }
            }
        ],
        fnHeaderCallback: function (nHead, aData, iStart, iEnd, aiDisplay) {
            $(nHead)
                .children("th:nth-child(1), th:nth-child(2), th:nth-child(3)")
                .addClass("text-center");
        },
        fnFooterCallback: function (nFoot, aData, iStart, iEnd, aiDisplay) {
            $(nFoot)
                .children("th:nth-child(1), th:nth-child(2), th:nth-child(3)")
                .addClass("text-center");
        },
        fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            $(nRow)
                .children(
                    "td:nth-child(1),td:nth-child(2),td:nth-child(3),td:nth-child(4)"
                )
                .addClass("text-center");
        },
        fnDrawCallback: function (settings) {
            $('[data-toggle="kt-tooltip"]').tooltip();
        }
    });
};

function loadSelectedIdMaterial(id_material_sap = '') {
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

function loadProduk(params) {
    $.ajax({
        url: ajaxSource+"/get-produk",
        success:(res)=>{
            const obj = res.data;
        },
        error:()=>{

        }
    })
}



function approve() {
    let data = $("#form1").serializeArray();
    $.ajax({
        type: "PATCH",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: ajaxUrl + "/" + id,
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
