"use strict";

var KTDatatablesDataSourceAjaxServer = function() {



	var initTable1 = function() {

		var table = $('#kt_datatable');



		// begin first table

		table.DataTable({

			language: {

                sEmptyTable:     "Aucun Inventaire disponible",

                sInfo:           "Affichage des Inventaires _START_ à _END_ sur _TOTAL_ Inventaires",

                sInfoEmpty:      "Affichage des Inventaires 0 à 0 sur 0 Inventaires",

                sInfoFiltered:   "(filtré à partir de _MAX_ Inventaires au total)",

                sInfoPostFix:    "",

                sInfoThousands:  ",",

                sLengthMenu:     "Afficher _MENU_ Inventaires",

                sLoadingRecords: "Chargement...",

                sProcessing:     "Traitement...",

                sSearch:         "Rechercher :",

                sZeroRecords:    "Aucun Inventaire correspondant trouvé",

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

			order: [[ 4, "desc" ]],

			ajax: {

				url: HOST_URL,

				type: 'GET',

			},

			columns: [

				{data: 'User'},

				{data: 'Code'},

				{data: 'Warehouse'},

				{data: 'Whnature'},

				{data: 'Products'},

				{data: 'Created'},

				{data: 'Actions', responsivePriority: -1},

			],

			columnDefs: [

				{

					width: '75px',

					targets: -2,

					render: function(data, type, full, meta) {

						var status = {

							1: {'title': 'En attente', 'state': 'warning'},

							2: {'title': 'Validée', 'state': 'success'},

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

