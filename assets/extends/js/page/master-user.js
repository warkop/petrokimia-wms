"use strict";
var KTDatatablesDataSourceHtml = function () {
    var dataJSONArray = JSON.parse(
        '[[1,"administrator@gmail.com", "administrator", 1, "10-12-2019", "15-12-2019"], [2,"ulfa123@gmail.com", "ulfa123", 3, "10-12-2019", "15-12-2019"], [3,"ulfa123@gmail.com", "ulfa123", 3, "10-12-2019", "15-12-2019"], [4,"ulfa123@gmail.com", "ulfa123", 2, "10-12-2019", "15-12-2019"], [5,"ulfa123@gmail.com", "ulfa123", 4, "10-12-2019", "15-12-2019"]]');
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
                    targets: -5,
                    render: function (data, type, full, meta) {
                        var status = {
                            1: {
                                'title': 'Administrator',
                                'state': 'primary'
                            },
                            2: {
                                'title': 'Checker',
                                'state': 'warning'
                            },
                            3: {
                                'title': 'Departemen',
                                'state': 'success'
                            },
                            4: {
                                'title': 'Loket',
                                'state': 'danger'
                            },
                        };
                        if (typeof status[data] === 'undefined') {
                            return data;
                        }
                        return '<span class="kt-badge kt-badge--' + status[data].state + ' kt-badge--dot"></span>&nbsp;' +
                            '<span class="kt-font-bold kt-font-' + status[data].state + '">' + status[data].title + '</span>';
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