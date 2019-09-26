"use strict";
var KTDatatablesDataSourceHtml = function () {
    var dataJSONArray = JSON.parse(
        '[[1, "Pallet", "Pengurangan", "1", "10-12-2019", "15-12-2019"], [2, "Produk", "Penambahan", "2", "10-12-2019", "15-12-2019"]]');
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
                        </a>`;
                    },
                },
                {
                    targets: -5,
                    render: function (data, type, full, meta) {
                        var foto = {
                            1: {
                                'title': 'Aktif',
                                'state': 'primary'
                            },
                            2: {
                                'title': 'Tidak aktif',
                                'state': 'danger'
                            }
                        };
                        if (typeof foto[data] === 'undefined') {
                            return data;
                        }
                        return '<span class="kt-badge kt-badge--' + foto[data].state + ' kt-badge--dot"></span>&nbsp;' +
                            '<span class="kt-font-bold kt-font-' + foto[data].state + '">' + foto[data].title + '</span>';
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