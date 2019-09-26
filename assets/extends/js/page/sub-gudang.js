"use strict";
var KTDatatablesDataSourceHtml = function () {
    var dataJSONArray = JSON.parse(
        '[[1,"Sub Gudang A", "10", "5", "100", "100"], [2, "Sub Gudang B", "10", "5", "100", "100"]]');
    var initTable1 = function () {
        var table = $('#kt_table_1');
        // begin first table
        table.DataTable({
            responsive: true,
            data: dataJSONArray,
            columnDefs: [{
                    className: 'text-center',
                    targets: -1,
                    title: '#',
                    orderable: false,
                    render: function (data, type, full, meta) {
                        return `
                        <button class="btn btn-orens btn-elevate btn-elevate-air dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Actions
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="` + baseUrl + `/list-pallet"><i class="flaticon-layers"></i> List pallet</a>
                            <a class="dropdown-item" href="` + baseUrl + `/list-alat-berat"><i class="flaticon-truck"></i> List alat berat</a>
                            <a class="dropdown-item" href="` + baseUrl + `/list-tenaga-kerja-nonorganik"><i class="flaticon-users"></i> List tenaga non-organik</a>
                            <a class="dropdown-item" href="" data-toggle="modal" data-target="#kt_modal_1"><i class="flaticon-edit-1"></i> Edit data</a>
                        </div>`;
                    },
                },
                {
                    className: 'text-center',
                    targets: -2,
                    title: 'Jumlah Alat Berat',
                    orderable: true,
                    render: function (data, type, full, meta) {
                        var result = '<a href="" data-toggle="modal" data-target="#kt_modal_alat">' + data + '</a>';
                        return result;
                    },
                }, {
                    className: 'text-center',
                    targets: -3,
                    title: 'Jumlah Pupuk',
                    orderable: true,
                    render: function (data, type, full, meta) {
                        var result = '<a href="" data-toggle="modal" data-target="#kt_modal_pupuk">' + data + '</a>';
                        return result;
                    },
                }
            ],
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
jQuery(document).ready(function () {
    KTDatatablesDataSourceHtml.init();
});