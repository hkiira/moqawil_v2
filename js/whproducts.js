"use strict";
var KTDatatablesDataSourceAjaxServer = function() {

	var initTable1 = function() {
		var table = $('#kt_datatable');

		// begin first table
		table.DataTable({
			language: {
                sEmptyTable:     "Aucun article disponible",
                sInfo:           "Affichage des articles _START_ à _END_ sur _TOTAL_ articles",
                sInfoEmpty:      "Affichage des articles 0 à 0 sur 0 unités",
                sInfoFiltered:   "(filtré à partir de _MAX_ articles au total)",
                sInfoPostFix:    "",
                sInfoThousands:  ",",
                sLengthMenu:     "Afficher _MENU_ articles",
                sLoadingRecords: "Chargement...",
                sProcessing:     "Traitement...",
                sSearch:         "Rechercher :",
                sZeroRecords:    "Aucun article correspondant trouvé",
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
				{data: 'Product'},
				{data: 'Quantity'},
				{data: 'Status'},
			]
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
