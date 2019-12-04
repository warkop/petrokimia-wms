"use strict";
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

jQuery(document).ready(function() {
	KTDatatablesBasicPaginations.init();
});