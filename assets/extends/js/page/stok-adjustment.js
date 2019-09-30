"use strict";
var KTDatatablesDataSourceHtml = function () {
    var dataJSONArray = JSON.parse(
        '[[1,"10-10-2019", "https://www.sikumis.com/media/frontend/products/Urea-Petro-(Non-Sub).jpg"], [2, "10-10-2019", "https://www.sikumis.com/media/frontend/products/Urea-Petro-(Non-Sub).jpg"], [3, "10-10-2019", "https://www.sikumis.com/media/frontend/products/Urea-Petro-(Non-Sub).jpg"]]');
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
                        <button type = "button" onclick="showme()" class="btn btn-danger btn-elevate btn-icon" data-container="body" data-toggle="kt-tooltip" data-placement="top" title="Hapus"><i class="flaticon-delete"></i> </button>`;
                },
            }, {
                className: 'text-center',
                targets: -2,
                render: function (data, type, full, meta) {
                    var image = '<a class="fancybox" rel="ligthbox" href="' + data + '"><img class="img-responsive" width="100px" src="' + data + '" alt=""></a>';
                    return image;
                },
            }],
            "drawCallback": function (settings) {
                $('[data-toggle="kt-tooltip"]').tooltip();
                $(".fancybox").fancybox({

                    openEffect: "none",

                    closeEffect: "none"

                });
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

function showme() {
    swal.fire({
        title: 'Are you sure?',
        text: "Data yang sudah dihapus tidak bisa dibatalkan.",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus data!'
    }).then(function (result) {
        if (result.value) {
            swal.fire(
                'Berhasil!',
                'Data berhasil dihapus.',
                'success'
            )
        }
    });
}
jQuery(document).ready(function () {
    KTDatatablesDataSourceHtml.init();
});