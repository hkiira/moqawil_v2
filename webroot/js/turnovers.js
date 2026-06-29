"use strict";
var KTDatatablesDataSourceAjaxServer = function () {

	var initTable1 = function () {
		var table = $('#kt_datatable');

		// begin first table
		table.DataTable({
			language: {
				sEmptyTable: "Aucun chiffre disponible",
				sInfo: "Affichage des chiffres _START_ à _END_ sur _TOTAL_ chiffres",
				sInfoEmpty: "Affichage des chiffres 0 à 0 sur 0 chiffres",
				sInfoFiltered: "(filtré à partir de _MAX_ chiffres au total)",
				sInfoPostFix: "",
				sInfoThousands: ",",
				sLengthMenu: "Afficher _MENU_ chiffres",
				sLoadingRecords: "Chargement...",
				sProcessing: "Traitement...",
				sSearch: "Rechercher :",
				sZeroRecords: "Aucun chiffres correspondant trouvé",
				oPaginate: {
					sFirst: "Premier",
					sLast: "Dernier",
					sNext: "Suivant",
					sPrevious: "Précédent"
				},
			},
			responsive: true,
			pageLength: 50,
			searchDelay: 500,
			processing: true,
			serverSide: true,
			order: [[0, "asc"]],

			ajax: {
				url: HOST_URL,
				type: 'GET',
			},
			columns: [
				{ data: 'Title' },
				{ data: 'Code' },
				{ data: 'Status' },
				{ data: 'Actions', responsivePriority: -1 },
			],
			columnDefs: [
				{
					width: '75px',
					targets: -2,
					render: function (data, type, full, meta) {
						var status = {
							1: { 'title': 'Actif', 'state': 'success' },
							0: { 'title': 'Innactif', 'state': 'danger' },
						};
						if (typeof status[data] === 'undefined') {
							return data;
						}
						return '<span class="label label-' + status[data].state + ' label-dot mr-2"></span>' +
							'<span class="font-weight-bold text-' + status[data].state + '">' + status[data].title + '</span>';
					},
				},
			],
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
	KTDatatablesDataSourceAjaxServer.init();
});
