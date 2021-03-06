"use strict";
let datatable,
    tableTarget = "#kt_table_1",
    ajaxUrl = baseUrl + "log-aktivitas",
    ajaxSource = ajaxUrl,
    totalFiles = 0,
    completeFiles = 0,
    laddaButton;

$(document).ready(function () {
    load_table();
    KTSelect2.init();
});

const load_table = function (data="") {
    datatable = $(tableTarget);
    // begin first table
    datatable.dataTable({
        bDestroy: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: ajaxSource,
            data:{
                gudang: data.gudang,
                produk: data.produk,
                shift: data.shift,
                start_date: data.start_date,
                end_date: data.end_date
            },
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
                mData: "checker"
            },
            {
                mData: "nama_aktivitas"
            },
            {
                mData: null
            },
            {
                mData: null
            },
            {
                mData: "nama_gudang"
            },
            {
                mData: "nama_shift"
            },
            {
                mData: "nopol"
            },
            {
                mData: "driver"
            },
            {
                mData: "posto"
            },
            {
                mData: "id"
            }
        ],
        aaSorting: [
            [1, "asc"]
        ],
        lengthMenu: [10, 25, 50, 75, 100],
        pageLength: 10,
        aoColumnDefs: [{
                aTargets: [0],
                mData: "id",
                mRender: function (data, type, full, draw) {
                    let row = draw.row;
                    let start = draw.settings._iDisplayStart;

                    let counter = start + 1 + row;

                    return counter;
                }
            },
            {
                aTargets: [1],
                mData: "tanggal",
                mRender: function (data, type, full, draw) {
                    return helpDateFormat(full.tanggal, "si")+" "+helpTime(full.tanggal);
                }
            },
            {
                aTargets: [4],
                orderable:false,
                mRender: function (data) {
                    let materials = ""
                    data.material_trans.forEach(element => {
                        if (element.material && element.material.kategori == 1) {
                            materials += element.material.nama +"<br>";
                        }
                    });

                    if (materials == "") {
                        return "Tidak ada produk"
                    }
                    return materials
                }
            },
            {
                aTargets: [5],
                mData: "material_trans",
                orderable:false,
                mRender: function (data) {
                    let kuantum = ""
                    data.material_trans.forEach(element => {
                        if (element.material && element.material.kategori == 1) {
                            kuantum += element.jumlah+"<br>";
                        }
                    })
                    if (kuantum == "") {
                        return "Tidak ada produk"
                    }
                    return kuantum
                }
            },
            {
                className: "text-center",
                targets: -1,
                title: "Actions",
                orderable: false,
                render: function (data, type, full, meta) {
                   return `<a href="` + ajaxSource+"/"+full.id+`">
                            <button type = "button" class="btn btn-primary btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Detail Log Aktivitas">
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

function pilih() {
    const gudang = $("#gudang").val();
    const produk = $("#produk").val();
    const shift = $("#shift").val();
    const start_date = $("#start_date").val();
    const end_date = $("#end_date").val();
    const data = {
        "gudang": gudang,
        "shift": shift,
        "start_date": start_date,
        "end_date": end_date,
        "produk": produk
    }
    load_table(data);
}

var KTDatatablesDataSourceHtml = function () {
    var dataJSONArray = JSON.parse(
        '[[1,"12/09/2019", "Pengiriman Pupuk ZA-X001", "Gudang Petrokimia 1", "Shift 1"]]');
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
                        <a href="` + baseUrl + `log-aktivitas/detail">
                            <button type = "button" class="btn btn-primary btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Detail Log Aktivitas">
                            <i class="flaticon2-zig-zag-line-sign"></i> </button>
                        </a>
                        `;
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


