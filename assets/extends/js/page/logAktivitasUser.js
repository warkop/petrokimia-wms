"use strict";

let datatable,
	tableTarget = "#kt_table_1",
	ajaxUrl = baseUrl + "log-aktivitas-user",
	ajaxSource = ajaxUrl,
	totalFiles = 0,
	completeFiles = 0,
	laddaButton;

$(document).ready(function () {
	load_table();
});

const load_table = function (start_date='', end_date='') {
	datatable = $(tableTarget);
	// begin first table
	datatable.dataTable({
		bDestroy: true,
		processing: true,
		serverSide: true,
		ajax: {
			url: ajaxSource,
			data:{
                start_date: start_date,
                end_date: end_date
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
			mData: 'username'
		},
		{
			mData: "aktivitas"
		},
		{
			mData: "created_at"
		},
		],
		aaSorting: [
			[3, "desc"]
		],
		lengthMenu: [10, 25, 50, 75, 100],
		pageLength: 10,
		aoColumnDefs: [{
			aTargets: [0],
			mData: "id",
			mRender: function (data, type, full, draw) {
				let row = draw.row;
				let start = draw.settings._iDisplayStart;
				let length = draw.settings._iDisplayLength;

				let counter = start + 1 + row;

				return counter;
			}
		}, {
				aTargets: -1,
				mData: "created_at",
				mRender: function (data, type, full, draw) {
					const temp = '';

					return helpDateFormat(full.created_at, 'si') + " " + helpTime(full.created_at);
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

var KTDatatablesBasicPaginations = function() {

    var dataJSONArray = JSON.parse(
        '[[1, "Ivan", "Login", "12-12-2019 (08:00)"],[2, "Gian", "Login", "12-12-2019 (13:00)"]]');

	var initTable1 = function() {
		var table = $('#logAktivUser');

		// begin first table
		table.DataTable({
			responsive: true,
            pagingType: 'full_numbers',
            data: dataJSONArray,
			columnDefs: [
			],
		});
	};

	return {

		//main function to initiate the module
		init: function() {
			initTable1();
		},

	};

}();

function pilih() {
    const start_date = $("#start_date").val();
    const end_date = $("#end_date").val();
    load_table(start_date, end_date);
}

// jQuery(document).ready(function() {
// 	KTDatatablesBasicPaginations.init();
// });