"use strict";
let datatable,
    tableTarget = '#kt_table_1',
    ajaxUrl = baseUrl + 'rencana-harian',
    ajaxSource = ajaxUrl,
    laddaButton;

$(document).ready(()=>{
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
});

const tambahHouseKeeper = () => {
    const tableId = "table_housekeeper";
    const rows = document.getElementById(tableId).getElementsByTagName("tr").length;
    let html = 
    `<tr id="baris-${rows}">
        <td>
            <select class="form-control m-select2 kt_select2_housekeeping" name="housekeeper" style="width: 100% !important" name="param" multiple="multiple" >
                <option value="AK">Suryati</option>
                <option value="HI">Maya</option>
            </select>
        </td>
        <td>
            <select class="form-control m-select2 kt_select2_area_kerja" name="area" multiple="multiple">
                <option>Area A</option>
                <option>Area B</option>
            </select>
        </td>
        <td>
            <button class="btn btn-danger btn-sm btn-block" onclick="hapus(${rows})"><i class="fa fa-trash"></i> Hapus</button>
        </td>
    </tr>`;

    $("#table_housekeeper tbody").append(html);
    $('.kt_select2_housekeeping').select2({
        placeholder: "Pilih Housekeeper"
    });
    $('.kt_select2_area_kerja').select2({
        placeholder: "Pilih Area Kerja",
    });
}

const hapus = (id) => {
    $("#baris-"+id).remove();
}

const simpan = () => {
    $("#btn_save").prop("disabled", true);
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
                swal.fire('Ok', obj.message, 'success').then(() => {
                    window.location = ajaxSource;
                }).catch(() => {

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
                "mData": "tanggal"
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
            },
            {
                "aTargets": -1,
                "mData": "id",
                render: function (data, type, full, meta) {
                    return `
                        <a href="` + ajaxSource + `/realisasi">
                            <button type = "button" class="btn btn-primary btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Realisasi">
                            <i class="flaticon-interface-5"></i> </button>
                        </a>
                        <a href="` + ajaxSource + `/ubah/${full.id}">
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

function getTkbm(id) {
    $.ajax({
        type: "GET",
        url: ajaxSource + '/get_tkbm/' + id,
        success:res=>{

        },
        error:(err, oo, pp) =>{

        }
    });
}

function edit(id = '') {
    // reset_form();
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
            
            if (obj.status == "OK") {
                $("#id_shift").val(obj.data.id_shift).trigger('change.select2');
                // $("#admin_loket").val(obj)
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

var KTDatatablesDataSourceHtml = function () {
    var dataJSONArray = JSON.parse(
        '[[1,"12/09/2019", "Shift 1"]]');
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
                        <a href="` + baseUrl + `realisasi">
                            <button type = "button" class="btn btn-primary btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Realisasi">
                            <i class="flaticon-interface-5"></i> </button>
                        </a>
                        <a href="` + baseUrl + `/add-rencana-harian">
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


// Class definition
var KTSelect2 = function () {
    // Private functions
    var demos = function () {
        // basic
        $('#kt_select2_1, #kt_select2_1_validate').select2({
            placeholder: "Select a state"
        });

        // nested
        $('#kt_select2_2, #kt_select2_2_validate').select2({
            placeholder: "Select a state"
        });

        // multi select
        $('#kt_select2_3, #kt_select2_3_validate').select2({
            placeholder: "Select a state",
        });

        // basic
        $('#kt_select2_4').select2({
            placeholder: "Select a state",
            allowClear: true
        });

        // loading data from array
        var data = [{
            id: 0,
            text: 'Enhancement'
        }, {
            id: 1,
            text: 'Bug'
        }, {
            id: 2,
            text: 'Duplicate'
        }, {
            id: 3,
            text: 'Invalid'
        }, {
            id: 4,
            text: 'Wontfix'
        }];

        $('#kt_select2_5').select2({
            placeholder: "Select a value",
            data: data
        });

        // loading remote data

        function formatRepo(repo) {
            if (repo.loading) return repo.text;
            var markup = "<div class='select2-result-repository clearfix'>" +
                "<div class='select2-result-repository__meta'>" +
                "<div class='select2-result-repository__title'>" + repo.full_name + "</div>";
            if (repo.description) {
                markup += "<div class='select2-result-repository__description'>" + repo.description + "</div>";
            }
            markup += "<div class='select2-result-repository__statistics'>" +
                "<div class='select2-result-repository__forks'><i class='fa fa-flash'></i> " + repo.forks_count + " Forks</div>" +
                "<div class='select2-result-repository__stargazers'><i class='fa fa-star'></i> " + repo.stargazers_count + " Stars</div>" +
                "<div class='select2-result-repository__watchers'><i class='fa fa-eye'></i> " + repo.watchers_count + " Watchers</div>" +
                "</div>" +
                "</div></div>";
            return markup;
        }

        function formatRepoSelection(repo) {
            return repo.full_name || repo.text;
        }

        $("#kt_select2_6").select2({
            placeholder: "Search for git repositories",
            allowClear: true,
            ajax: {
                url: "https://api.github.com/search/repositories",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 1,
            templateResult: formatRepo, // omitted for brevity, see the source of this page
            templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
        });

    }

    var modalDemos = function () {
        $('#kt_modal_1').on('shown.bs.modal', function () {
            // basic
            $('#kt_admin_gudang').select2({
                placeholder: "Pilih Admin Gudang"
            });

            $('#kt_loket').select2({
                placeholder: "Pilih Loket"
            });

            $('#kt_operator').select2({
                placeholder: "Pilih Operator"
            });

            $('#kt_housekeeping').select2({
                placeholder: "Pilih House Keeping"
            });

            $('#kt_checker').select2({
                placeholder: "Pilih Checker"
            });

        });
    }

    // Public functions
    return {
        init: function () {
            modalDemos();
        }
    };
}();


jQuery(document).ready(function () {
    // KTDatatablesDataSourceHtml.init();
    // KTSelect2.init();
});