"use strict";
var KTDatatablesDataSourceAjaxServer = function() {

	var initTable1 = function() {
		var table = $('#kt_datatable');

		// begin first table
		table.DataTable({
			language: {
                sEmptyTable:     "Aucun type de remise disponible",
                sInfo:           "Affichage des types de remise _START_ à _END_ sur _TOTAL_ types de remise",
                sInfoEmpty:      "Affichage des types de remise 0 à 0 sur 0 types de remise",
                sInfoFiltered:   "(filtré à partir de _MAX_ types de remise au total)",
                sInfoPostFix:    "",
                sInfoThousands:  ",",
                sLengthMenu:     "Afficher _MENU_ types de remise",
                sLoadingRecords: "Chargement...",
                sProcessing:     "Traitement...",
                sSearch:         "Rechercher :",
                sZeroRecords:    "Aucun types de remise correspondant trouvé",
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
            order: [[ 0, "desc" ]],

			ajax: {
				url: HOST_URL,
				type: 'GET',
			},
			columns: [
				{data: 'Code'},
				{data: 'Title'},
				{data: 'Status'},
				{data: 'Actions', responsivePriority: -1},
			],
			columnDefs: [
				{
					width: '75px',
					targets: -2,
					render: function(data, type, full, meta) {
						var status = {
							1: {'title': 'Actif', 'state': 'success'},
							0: {'title': 'Innactif', 'state': 'danger'},
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
});
