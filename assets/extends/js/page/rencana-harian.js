"use strict";
let datatable,
    tableTarget = '#kt_table_1',
    ajaxUrl = baseUrl + 'rencana-harian',
    ajaxSource = ajaxUrl,
    laddaButton;

$(document).ready(()=>{
    loadTable();

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

const tambahHouseKeeper = (id_rencana='', id_tkbm='') => {
    const tableId = "table_housekeeper";
    const rows = document.getElementById(tableId).getElementsByTagName("tr").length;
    let html = 
    `<tr class="cap-baris" id="baris-${rows}">
        <td>
            <select class="form-control m-select2 kt_select2_housekeeping pilih_housekeeper" onchange="check(this)" id="housekeeper-${rows}" name="housekeeper[${rows}]" style="width: 100% !important" required >
            </select>
        </td>
        <td>
            <select class="form-control m-select2 kt_select2_area_kerja" id="area-${rows}" name="area[${rows}][]" multiple="multiple" required>
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

    getTkbm(4, "#housekeeper-"+rows, id_rencana, id_tkbm);
    getArea("#area-"+rows);

    if (id_rencana != '') {
        setTimeout(() => {
            getRencanaTkbmArea(id_rencana, id_tkbm, rows);
        }, 2000);
    }
}

const hapus = (id='') => {
    if (id != '') {
        $("#baris-"+id).remove();
    } else {
        $(".cap-baris").remove();
    }
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

const loadTable = function () {
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
                    // let length = draw.settings._iDisplayLength;

                    let counter = (start + 1 + row);

                    return counter;
                }
            },
            {
                "aTargets": -1,
                "mData": "id",
                render: function (data, type, full, meta) {
                    return `
                        <a href="` + ajaxSource + `/realisasi/${full.id}">
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

function check(target) {
    let $lokasi = $('.pilih_housekeeper');
    let chosen = false;

    const id_target = $(target).attr('id').replace('-', '');
    for (var i = 0; i < $lokasi.length; i++) {
        if ($(target).val() != "" && $(target).attr('id') != $($lokasi[i]).attr('id') && $(target).val() == $($lokasi[i]).val()) {
            chosen = true;
        }
    }

    if (chosen == true) {
        $(target).val('').trigger('change.select2');
        swal.fire('Pemberitahuan', 'Housekeeper sudah dipilih. Silahkan Pilih housekeeper lain!', 'error');
        $("#housekeeper-"+ id_target).val("");
    }
}

function getRencanaTkbm(id_job_desk, id_rencana, target) {
    
    $.ajax({
        type: "GET",
        url: ajaxSource + '/get-rencana-tkbm/' + id_job_desk + "/" + id_rencana,
        success: res => {
            const obj = res.data;

            const panjang = obj.length;
            let tampung = [];
            for (let i=0; i<panjang; i++) {
                tampung.push(obj[i].id_tkbm);
            }
            $(target).select2('val', [tampung]);
        },
        error: (err, oo, pp) => {

        }
    });
}

function getRencanaTkbmArea(id_rencana, id_tkbm, count='') {
    // setTimeout(() => {
        // console.log(id_rencana);
        // console.log(id_tkbm);
        $.ajax({
            type: "GET",
            url: ajaxSource + '/get-rencana-tkbm-area/' + id_rencana + "/" + id_tkbm,
            success: res => {
                const obj = res.data;
                const panjang = obj.length;
                let tampung = [];
                for (let i = 0; i < panjang; i++) {
                    if (obj[i].id_tkbm == id_tkbm)
                        tampung.push(obj[i].id_area);
                }
    
                $("#housekeeper-" + count).val(id_tkbm).trigger('change.select2');
                $("#area-"+count).select2('val', [tampung]);
            },
            error: (err, oo, pp) => {
    
            }
        });
    // }, 2000);
}

function getRencanaAlatBerat(id_rencana, target) {
    $.ajax({
        type: "GET",
        url: ajaxSource + '/get-rencana-alat-berat/' + id_rencana,
        success: res => {
            const obj = res.data;
            const panjang = obj.length;
            let tampung = [];
            for (let i = 0; i < panjang; i++) {
                tampung.push(obj[i].id_alat_berat);
            }
            $(target).select2('val', [tampung]);
        },
        error: (err, oo, pp) => {

        }
    });
}

function getHouseKeeper() {
    
}

function getTkbm(id_job_desk, target, id_rencana='', id_tkbm='') {
    $.ajax({
        type: "GET",
        url: ajaxSource + '/get-tkbm/' + id_job_desk,
        success:res=>{
            const obj = res.data;

            let html = `<option value="">Pilih Pegawai</option>`;
            obj.forEach((item, index)=>{
                html += `<option value="${item.id}">${item.nama}</option>`;
            });

            $(target).html(html);
            
            if (id_rencana != '' && id_job_desk != 4) {
                getRencanaTkbm(id_job_desk, id_rencana, target);
                // setTimeout(() => {
                //     console.log(id_tkbm);
                //     getRencanaTkbmArea(id_rencana, id_tkbm);
                // }, 1000);
            }
        },
        error:(err, oo, pp) =>{

        }
    });
}

function getAlatBerat(id_rencana, target) {
    $.ajax({
        type: "GET",
        url: ajaxSource + '/get-alat-berat',
        success: res => {
            const obj = res.data;

            let html = ``;
            obj.forEach((item, index) => {
                html += `<option value="${item.id}">${item.nomor_lambung}</option>`;
            });

            $(target).html(html);
            getRencanaAlatBerat(id_rencana, target);
        },
        error: (err, oo, pp) => {

        }
    });
}

function getArea(target) {
    $.ajax({
        type: "GET",
        url: ajaxSource + '/get-area/',
        success: res => {
            const obj = res.data;

            let html = '';
            obj.forEach((item, index) => {
                html += `<option value="${item.id}">${item.nama}</option>`;
            });

            $(target).html(html);
        },
        error: (response, oo, pp) => {
            const head = 'Pemberitahuan';
            const type = 'warning';
            const obj = response.responseJSON.errors;
            // laddaButton.stop();
            window.onbeforeunload = false;
            $('.btn_close_modal').removeClass('hide');
            $('.se-pre-con').hide();
            
            let message = '';
            if (obj != null) {
                const temp = Object.values(obj);
                temp.forEach(element => {
                    element.forEach(row => {
                        message += row + "<br>"
                    });
                });
            } else {
                message = response.responseJSON.message;
                hapus();
            }
            swal.fire(head, message, type);
        }
    });
}

function getSelectedTkbmArea() {
    $.ajax({
        url: ajaxSource + "",
        success:res=>{

        },
        error:()=>{
            
        }
    });
}


function edit(id = '') {
    $('#action').val('edit');
    $('#btn_save').html('Simpan Data');

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
                $("#id").val(obj.data.id);
                $("#id_shift").val(obj.data.id_shift).trigger('change');

                getTkbm(1, "#admin_loket", obj.data.id);
                getTkbm(2, "#op_alat_berat", obj.data.id);
                getTkbm(3, "#checker", obj.data.id);
                getAlatBerat(obj.data.id, "#alat_berat");
                
                // tambahHouseKeeper(obj.data.id);
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