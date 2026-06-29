"use strict";
var data=[
				{data: 'Code'},
				{data: 'Automobile'},
				{data: 'Warehouse'},
				{data: 'Products'},
				{data: 'Status'},
				{data: 'Actions', responsivePriority: -1},
			];


var KTDatatablesDataSourceAjaxServer = function() {

	var initTable1 = function() {
		var table = $('#kt_datatable');

		// begin first table
		table.DataTable({
			language: {
                sEmptyTable:     "Aucun point de vente disponible",
                sInfo:           "Affichage des points de vente _START_ à _END_ sur _TOTAL_ points de vente",
                sInfoEmpty:      "Affichage des points de vente 0 à 0 sur 0 points de vente",
                sInfoFiltered:   "(filtré à partir de _MAX_ points de vente au total)",
                sInfoPostFix:    "",
                sInfoThousands:  ",",
                sLengthMenu:     "Afficher _MENU_ points de vente",
                sLoadingRecords: "Chargement...",
                sProcessing:     "Traitement...",
                sSearch:         "Rechercher :",
                sZeroRecords:    "Aucun point de vente correspondant trouvé",
                oPaginate: {
                    sFirst:    "Premier",
                    sLast:     "Dernier",
                    sNext:     "Suivant",
                    sPrevious: "Précédent"
                },
            },
            responsive: true,
            pageLength: 50,
			searchDelay: 500,
			processing: true,
			serverSide: true,
            order: [[ 0, "desc" ]],
			ajax: {
				url: HOST_URL+"/"+var1,
				type: 'GET',
			},
			columns: data,
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
