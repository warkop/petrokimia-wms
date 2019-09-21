"use strict";
var KTDatatablesDataSourceHtml = function () {
    var dataJSONArray = JSON.parse(
        '[[1,"Admin", "10-12-2019", "15-12-2019"], [2, "Checker", "10-12-2019", "15-12-2019"], [3, "Loket", "10-12-2019", "15-12-2019"]]');
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
                            <button type = "button" class="btn btn-success btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Edit">
                            <i class="flaticon-edit-1"></i> </button>
                        </a>`;
                    },
                },
                {
                    className: 'text-center',
                    targets: -2,
                    title: 'Status',
                    orderable: false,
                    render: function () {
                        return `
                    <span class="kt-switch kt-switch--primary kt-switch--icon">
                        <label>
                            <input type="checkbox" checked="checked" name="">
                            <span></span>
                        </label>
                    </span>`;
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