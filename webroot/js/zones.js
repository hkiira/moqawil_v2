"use strict";
var KTDatatablesDataSourceAjaxServer = function() {

	var formatChildZones = function(data) {
		if (!data || data.length === 0) {
			return '<div class="p-3 text-muted text-center">Aucune zone sous ce secteur</div>';
		}

		var html = '<div class="card card-custom p-4 my-2" style="background-color: #fcfcfd; border: 1px dashed #ebedf3;">';
		html += '<h6 class="font-weight-bold text-dark mb-3">Liste des zones de ce secteur</h6>';
		html += '<table class="table table-bordered table-head-custom">';
		html += '<thead><tr><th>Code</th><th>Nom de la zone</th><th>Ville</th><th>Statut</th><th>Actions</th></tr></thead>';
		html += '<tbody>';

		$.each(data, function(index, zone) {
			var statusHtml = '';
			if (zone.Status == 1) {
				statusHtml = '<span class="label label-success label-dot mr-2"></span><span class="font-weight-bold text-success">Actif</span>';
			} else {
				statusHtml = '<span class="label label-danger label-dot mr-2"></span><span class="font-weight-bold text-danger">Innactif</span>';
			}

			html += '<tr>';
			html += '<td>' + zone.Code + '</td>';
			html += '<td>' + zone.Title + '</td>';
			html += '<td>' + zone.City + '</td>';
			html += '<td>' + statusHtml + '</td>';
			html += '<td>' + zone.Actions + '</td>';
			html += '</tr>';
		});

		html += '</tbody></table></div>';
		return html;
	};

	var initTable1 = function() {
		var tableEl = $('#kt_datatable');

		// begin first table
		var table = tableEl.DataTable({
			language: {
                sEmptyTable:     "Aucune zone disponible",
                sInfo:           "Affichage des zones _START_ à _END_ sur _TOTAL_ zones",
                sInfoEmpty:      "Affichage des zones 0 à 0 sur 0 zones",
                sInfoFiltered:   "(filtré à partir de _MAX_ zones au total)",
                sInfoPostFix:    "",
                sInfoThousands:  ",",
                sLengthMenu:     "Afficher _MENU_ zones",
                sLoadingRecords: "Chargement...",
                sProcessing:     "Traitement...",
                sSearch:         "Rechercher :",
                sZeroRecords:    "Aucune zone correspondant trouvé",
                oPaginate: {
                    sFirst:    "Premier",
                    sLast:     "Dernier",
                    sNext:     "Suivant",
                    sPrevious: "Précédent"
                },
            },
            responsive: false, // Turn off responsive so details-control layout stays consistent
			searchDelay: 500,
            pageLength: 50,
			processing: true,
			serverSide: true,

			ajax: {
				url: HOST_URL,
				type: 'GET',
			},
			columns: [
				{
					className: 'details-control',
					orderable: false,
					data: null,
					render: function(data, type, full, meta) {
						return '<i class="fa fa-chevron-right text-muted cursor-pointer toggle-details" style="font-size: 0.9rem;"></i>';
					},
					width: '30px'
				},
				{data: 'Code'},
				{data: 'Title'},
				{data: 'City'},
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

		// Add event listener for opening and closing details
		tableEl.find('tbody').on('click', 'td.details-control', function () {
			var tr = $(this).closest('tr');
			var row = table.row( tr );
			var icon = $(this).find('i.toggle-details');

			if ( row.child.isShown() ) {
				// This row is already open - close it
				row.child.hide();
				tr.removeClass('shown');
				icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
			}
			else {
				// Open this row
				var parentId = row.data().Id; // Get sector ID
				icon.removeClass('fa-chevron-right').addClass('fa-spinner fa-spin'); // show spinner

				var url = HOST_URL;
				if (url.indexOf('/search/secteurs') !== -1) {
					url = url.replace('/search/secteurs', '/child-zones');
				} else {
					url = url.replace('/search', '/child-zones');
				}
				url = url + '/' + parentId;

				$.ajax({
					url: url,
					type: 'GET',
					dataType: 'json',
					success: function(data) {
						icon.removeClass('fa-spinner fa-spin').addClass('fa-chevron-down');
						row.child( formatChildZones(data) ).show();
						tr.addClass('shown');
					},
					error: function() {
						icon.removeClass('fa-spinner fa-spin').addClass('fa-chevron-right');
						toastr.error('Erreur lors du chargement des zones.');
					}
				});
			}
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
