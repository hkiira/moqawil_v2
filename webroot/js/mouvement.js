"use strict";
var KTDatatablesDataSourceAjaxServer = function() {

	var initTable1 = function() {
		var tables = $('#receipt_table');

		// begin first table
		tables.DataTable({
			language: {
                sEmptyTable:     "Aucun bon de réception disponible",
                sInfo:           "Affichage des bons de réception _START_ à _END_ sur _TOTAL_ bons de réception",
                sInfoEmpty:      "Affichage des bons de réception 0 à 0 sur 0 bons de réception",
                sInfoFiltered:   "(filtré à partir de _MAX_ bons de réception au total)",
                sInfoPostFix:    "",
                sInfoThousands:  ",",
                sLengthMenu:     "Afficher _MENU_ bons de réception",
                sLoadingRecords: "Chargement...",
                sProcessing:     "Traitement...",
                sSearch:         "Rechercher :",
                sZeroRecords:    "Aucun bon de réception correspondant trouvé",
                oPaginate: {
                    sFirst:    "Premier",
                    sLast:     "Dernier",
                    sNext:     "Suivant",
                    sPrevious: "Précédent"
                },
            },
            responsive: true,
			searchDelay: 500,
            pageLength: 50,
			processing: true,
			serverSide: true,
            order: [[ 5, "desc" ]],

			ajax: {
				url: HOST_URL1,
				type: 'GET',
			},
			columns: [
				{data: 'User'},
				{data: 'Code'},
				{data: 'Supplier'},
				{data: 'Total'},
				{data: 'Warehouse'},
				{data: 'Created'},
				{data: 'Status'},
				{data: 'Actions', responsivePriority: -1},
			],
			columnDefs: [
				{
					width: '75px',
					targets: -2,
					render: function(data, type, full, meta) {
						var status = {
							1: {'title': 'En attente de validation', 'state': 'warning'},
							3: {'title': 'Validée', 'state': 'success'},
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
		init: function() {
			initTable1();
		},

	};

}();
var KTDatatablesDataSource = function() {

	var initTable1 = function() {
		var table = $('#exitslip_table');

		// begin first table
		table.DataTable({
			language: {
                sEmptyTable:     "Aucun bon de sortie disponible",
                sInfo:           "Affichage des bons de sortie _START_ à _END_ sur _TOTAL_ bons de sortie",
                sInfoEmpty:      "Affichage des bons de sortie 0 à 0 sur 0 bons de sortie",
                sInfoFiltered:   "(filtré à partir de _MAX_ bons de sortie au total)",
                sInfoPostFix:    "",
                sInfoThousands:  ",",
                sLengthMenu:     "Afficher _MENU_ bons de sortie",
                sLoadingRecords: "Chargement...",
                sProcessing:     "Traitement...",
                sSearch:         "Rechercher :",
                sZeroRecords:    "Aucun bon de sortie correspondant trouvé",
                oPaginate: {
                    sFirst:    "Premier",
                    sLast:     "Dernier",
                    sNext:     "Suivant",
                    sPrevious: "Précédent"
                },
            },
            responsive: true,
			searchDelay: 500,
            pageLength: 50,
			processing: true,
			serverSide: true,
            order: [[ 3, "desc" ]],

			ajax: {
				url: HOST_URL2,
				type: 'GET',
			},
			columns: [
				{data: 'User'},
				{data: 'Code'},
				{data: 'Shipping'},
				{data: 'Created'},
				{data: 'Status'},
				{data: 'Actions', responsivePriority: -1},
			],
			columnDefs: [
				{
					width: '75px',
					targets: -2,
					render: function(data, type, full, meta) {
						var status = {
							1: {'title': 'En attente', 'state': 'secondary'},
							2: {'title': 'En cours', 'state': 'warning'},
							3: {'title': 'Livrée', 'state': 'success'},
							4: {'title': 'Encaissée', 'state': 'dark'},
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
		init: function() {
			initTable1();
		},

	};

}();
jQuery(document).ready(function() {
	KTDatatablesDataSourceAjaxServer.init();
	KTDatatablesDataSource.init();
});
