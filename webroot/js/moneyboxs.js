jQuery(document).ready(function() {

    var table = $('#kt_datatable').DataTable({
		        language: {
               sEmptyTable:     "Aucun employée disponible",
               sInfo:           "Affichage des employées _START_ à _END_ sur _TOTAL_ employées",
               sInfoEmpty:      "Affichage des employées 0 à 0 sur 0 rapports",
               sInfoFiltered:   "(filtré à partir de _MAX_ employées au total)",
               sInfoPostFix:    "",
               sInfoThousands:  ",",
               sLengthMenu:     "Afficher _MENU_ employées",
               sLoadingRecords: "Chargement...",
               sProcessing:     "Traitement...",
               sSearch:         "Rechercher :",
               sZeroRecords:    "Aucun employée correspondant trouvé",
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
      				url: HOST_URL,
      				type: 'GET',
      			},
      			columns: [
      				{data: 'User'},
      	 			{data: 'Chiffre'},
              {data: 'Regles'},
              {data: 'Impaye'},
      			],
			
		});

			$('.applyBtn').click(function () {
                var datestart=$('#kt_dashboard_daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD');

                var dateend=$('#kt_dashboard_daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD');
        alert(HOST_URL+'/'+datestart+'/'+dateend );

            	table.clear();
            	table.ajax.url( HOST_URL+'/'+datestart+'/'+dateend ).load();

            });

});

