"use strict";

var KTDatatablesDataSourceAjaxServer = function() {
	var initTable1 = function() {
		var table = $('#kt_datatable');
		table.DataTable({
			language: {
                sEmptyTable:     "Aucun bon de livraison disponible",
                sInfo:           "Affichage des bons de livraison _START_ à _END_ sur _TOTAL_ bons de livraison",
                sInfoEmpty:      "Affichage des bons de livraison 0 à 0 sur 0 bons de livraison",
                sInfoFiltered:   "(filtré à partir de _MAX_ bons de livraison au total)",
                sInfoPostFix:    "",
                sInfoThousands:  ",",
                sLengthMenu:     "Afficher _MENU_ bons de livraison",
                sLoadingRecords: "Chargement...",
                sProcessing:     "Traitement...",
                sSearch:         "Rechercher :",
                sZeroRecords:    "Aucun bon de livraison correspondant trouvé",
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
            order: [[ 4, "desc" ]],
			ajax: {
				url: HOST_URL,
				type: 'GET',
			},

			columns: [
				{data: 'User'},
				{data: 'Code'},
				{data: 'Customer'},
				{data: 'Orders'},
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
							1: {'title': 'Confirmé', 'state': 'primary'},
							2: {'title': 'Validée', 'state': 'info'},
							3: {'title': 'En cours', 'state': 'warning'},
							4: {'title': 'Livrée', 'state': 'success'},
							5: {'title': 'Encaissée', 'state': 'dark'},
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
		init: function() {
			initTable1();
		},
	};
}();



jQuery(document).ready(function() {
	KTDatatablesDataSourceAjaxServer.init();
});
