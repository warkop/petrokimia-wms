"use strict";
var KTDatatablesDataSourceHtml = function () {
    var dataJSONArray = JSON.parse(
        '[[1,"Forklift", "100.000", "10-12-2019", "15-12-2019"], [2, "Excavator", "100.000", "10-12-2019", "15-12-2019"], [3, "Alat Pengangkut (Truk)", "100.000", "10-12-2019", "15-12-2019"]]');
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
                        <a href="` + baseUrl + `master-alat-berat/list-alat-berat" class="btn btn-primary btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="List alat berat">
                            <i class="flaticon-truck"></i> </button>
                        `;
                },
            }, ],
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