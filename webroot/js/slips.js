"use strict";

if (var1==1 || var1==5) {

			var data=[

				{data: 'Code'},

				{data: 'Warehouse'},

				{data: 'Warehoused'},

				{data: 'Products'},
				
				{data: 'Total'},

				{data: 'User'},

				{data: 'Validate'},

				{data: 'Status'},

				{data: 'Actions', responsivePriority: -1},

			];



}else if(var1==2){

            var data=[

				{data: 'Code'},

				{data: 'Warehoused'},

				{data: 'Warehouse'},

				{data: 'Products'},
				
				{data: 'Total'},

				{data: 'User'},

				{data: 'Validate'},

				{data: 'Status'},

				{data: 'Actions', responsivePriority: -1},

			];

}else if(var1==3){

			var data=[

				{data: 'Code'},

				{data: 'Warehouse'},

				{data: 'Whnature'},

				{data: 'Whnatured'},

				{data: 'Products'},
				
				{data: 'Total'},

				{data: 'User'},

				{data: 'Validate'},

				{data: 'Status'},

				{data: 'Actions', responsivePriority: -1},

			];



}else if(var1==4){

			var data=[

				{data: 'Code'},

				{data: 'Warehouse'},

				{data: 'Warehoused'},

				{data: 'Whnature'},

				{data: 'Products'},
				
				{data: 'Total'},

				{data: 'User'},

				{data: 'Validate'},

				{data: 'Status'},

				{data: 'Actions', responsivePriority: -1},

			];

}else if(var1==99){

			var data=[

				{data: 'Code'},

				{data: 'Warehouse'},

				{data: 'Warehoused'},

				{data: 'Whnature'},

				{data: 'Whnatured'},

				{data: 'Products'},
				
				{data: 'Total'},

				{data: 'User'},

				{data: 'Validate'},

				{data: 'Sliptype'},

				{data: 'Status'},

				{data: 'Actions', responsivePriority: -1},

			];

}

jQuery(document).ready(function() {



		var table = $('#kt_datatable').DataTable({

			language: {

                sEmptyTable:     "Aucun bon disponible",

                sInfo:           "Affichage des bons _START_ à _END_ sur _TOTAL_ bons",

                sInfoEmpty:      "Affichage des bons 0 à 0 sur 0 bons",

                sInfoFiltered:   "(filtré à partir de _MAX_ bons au total)",

                sInfoPostFix:    "",

                sInfoThousands:  ",",

                sLengthMenu:     "Afficher _MENU_ bons",

                sLoadingRecords: "Chargement...",

                sProcessing:     "Traitement...",

                sSearch:         "Rechercher :",

                sZeroRecords:    "Aucun bon correspondant trouvé",

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

			columns: data,

			'fnDrawCallback': function(oSettings){

                $('.print').click(function(){

                    table.DataTable().clear();

                    table.DataTable().ajax.url( "/slips/search/1" ).load();

                });

            },

			columnDefs: [

				{

					width: '75px',

					targets: -2,

					render: function(data, type, full, meta) {

						var status = {

							1: {'title': 'En attente de confirmation', 'state': 'danger'},

							2: {'title': 'En attente de validation', 'state': 'warning'},

							3: {'title': 'Validé', 'state': 'success'},

							5: {'title': 'Livré', 'state': 'danger'},

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

		$('.user').change(function () {
         	var datestart=$('#kt_dashboard_daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD');
         	var dateend=$('#kt_dashboard_daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD');
        	var user=$('.user').val();
            table.clear();
            table.ajax.url( HOST_URL+'/'+user+'/'+datestart+'/'+dateend ).load();
   	 	});
   	 	
        $('.applyBtn').click(function () {
	   	 	var datestart=$('#kt_dashboard_daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD');
	        var dateend=$('#kt_dashboard_daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD');
	        var user=$('.user').val();
            table.clear();
            table.ajax.url( HOST_URL+'/'+user+'/'+datestart+'/'+dateend ).load();
   	 	});
});
