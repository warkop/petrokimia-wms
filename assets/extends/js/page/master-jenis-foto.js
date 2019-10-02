"use strict";
var KTDatatablesDataSourceHtml = function () {
    var dataJSONArray = JSON.parse(
        '[[1, "Foto Tampak Atas", "10-12-2019", "15-12-2019"], [2, "Foto Tampak Bawah", "11-12-2019", "19-12-2019"], [3, "Foto Tampak Depan", "11-12-2019", "19-12-2019"], [4, "Foto Tampak Belakang", "11-12-2019", "19-12-2019"], [5, "Foto Tampak Kiri", "11-12-2019", "19-12-2019"], [6, "Foto Tampak Kanan", "11-12-2019", "19-12-2019"]]');
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
jQuery(document).ready(function () {
    KTDatatablesDataSourceHtml.init();
});

//<a href="" data-toggle="modal" data-target="#kt_modal_2"><button type = "button" class="btn btn-primary btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Edit"><i class="flaticon-edit-1"></i> </button></a>