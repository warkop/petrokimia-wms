"use strict";
var KTDatatablesDataSourceHtml = function () {
    var dataJSONArray = JSON.parse(
        '[[1,"Gudang A", "10", "5", "100", "100"], [2, "Gudang B", "10", "5", "100", "100"], [3, "Gudang C", "10", "5", "100", "100"], [4,"Gudang D", "10", "5", "100", "100"], [5, "Gudang E", "10", "5", "100", "100"]]');
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
                        <a href="` + baseUrl + `/list-pallet">
                            <button type = "button" class="btn btn-primary btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="List Pallet">
                            <i class="flaticon-layers"></i></button>
                        </a>
                        <a href="` + baseUrl + `/list-alat-berat">
                            <button type = "button" class="btn btn-dark btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="List Alat Berat">
                            <i class="flaticon-truck"></i></button>
                        </a>
                        <a href="` + baseUrl + `/list-tenaga-kerja-nonorganik">
                            <button type = "button" class="btn btn-success btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="List Tenaga Non Organik">
                            <i class="flaticon-users"></i></button>
                        </a>
                        <a href="" data-toggle="modal" data-target="#kt_modal_1">
                            <button type = "button" class="btn btn-orens btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Edit">
                            <i class="flaticon-edit-1"></i></button>
                        </a>`;
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