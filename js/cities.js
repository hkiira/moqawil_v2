"use strict";
var KTDatatablesDataSourceAjaxServer = function() {

	var initTable1 = function() {
		var table = $('#kt_datatable');

		// begin first table
		table.DataTable({
			language: {
                sEmptyTable:     "Aucune ville disponible",
                sInfo:           "Affichage des villes _START_ à _END_ sur _TOTAL_ villes",
                sInfoEmpty:      "Affichage des villes 0 à 0 sur 0 villes",
                sInfoFiltered:   "(filtré à partir de _MAX_ villes au total)",
                sInfoPostFix:    "",
                sInfoThousands:  ",",
                sLengthMenu:     "Afficher _MENU_ villes",
                sLoadingRecords: "Chargement...",
                sProcessing:     "Traitement...",
                sSearch:         "Rechercher :",
                sZeroRecords:    "Aucune ville correspondant trouvé",
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

			ajax: {
				url: HOST_URL,
				type: 'GET',
			},
			columns: [
				{data: 'Title'},
				{data: 'Region'},
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
