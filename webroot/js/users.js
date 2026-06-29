"use strict";

var KTDatatableRemoteAjaxDemo = function() {

    var demo = function() {

        var datatable = $('#kt_datatable').KTDatatable({
            
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: HOST_URL,
                        map: function(raw) {
                            var dataSet = raw;
                            if (typeof raw.data !== 'undefined') {
                                dataSet = raw.data;
                            }
                            return dataSet;
                        },
                    },
                },
                pageSize: 10,
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true,
            },

            // layout definition
            layout: {
                scroll: true,
                footer: true,
            },

            // column sorting
            sortable: true,

            pagination: true,

            search: {
                input: $('#kt_datatable_search_query'),
                key: 'generalSearch'
            },


            columns: [
            {
                field: 'name',
                title: 'Nom',
                width: 250,
                sortable: 'asc',
                template: function(row) {
                	return '<span style="width: 250px;">\
                				<div class="d-flex align-items-center">\
									<div class="symbol symbol-40 flex-shrink-0">\
										<div class="symbol-label font-size-h4 font-weight-bold" >'+row.name.substr(0, 1)+'</div>\
									</div>\
									<div class="ml-2">\
										<div class="text-dark-75 font-weight-bold line-height-sm">' +row.code+' - '+row.name + '</div>\
									</div>\
								</div>\
							</span>';
                },
            }, {
                field: 'secteur',
                title: 'Secteurs',
                width: 180,
            },  {
                field: 'date',
                title: 'Date',
                width: 120,
            }, {
                field: 'status',
                title: 'Statut',
                width: 90,
                autoHide: false,
                // callback function support for column rendering
                template: function(row) {
                    var status = {
                        0: {
                            'title': 'Innactif',
                            'class': ' label-light-danger'
                        },
						1: {
                            'title': 'Actif',
                            'class': ' label-light-success'
                        },
                    };
                    return '<span class="label font-weight-bold label-lg ' + status[row.status].class + ' label-inline">' + status[row.status].title + '</span>';
                },
            }, {
                field: 'actions',
                title: 'Actions',
                sortable: false,
                width: 80,
                overflow: 'visible',
                autoHide: false,
                template: function(row) {
                    return '<div class="dropdown dropdown-inline">\
                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">\
                                    <i class="la la-cog"></i>\
                                </a>\
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">\
                                    <ul class="nav nav-hoverable flex-column">\
                                        <li class="nav-item"><a class="nav-link" href="/users/edit/'+row.id+'"><i class="nav-icon la la-edit"></i><span class="nav-text">Modifier l\'utlisateur</span></a></li>\
                                        <li class="nav-item"><a class="nav-link" href="/users/identifiants/'+row.id+'"><i class="nav-icon la la-edit"></i><span class="nav-text">Modifier les identifiants</span></a></li>\
                                    </ul>\
                                </div>\
                            </div>';
                },
            }],
            translate: {
                records: {
                    processing: 'Chargement...',
                    noRecords: 'Aucun enregistrement trouvé',
                },
                toolbar: {
                    pagination: {
                        items: {
                            default: {
                                first: 'Premier',
                                prev: 'Précédent',
                                next: 'Suivant',
                                last: 'Dernière',
                                more: 'Plus de pages',
                                input: 'N° de page',
                                select: 'Sélectionnez la taille de la page',
                            },
                            info: 'Vue {{start}} - {{end}} de {{total}} enregistrements',
                        },
                    },
                },
            },
        });

		$('#kt_datatable_search_status').on('change', function() {
            datatable.search($(this).val(), 'Status');
        });


        $('#kt_datatable_search_status').selectpicker();
    };

    return {
        // public functions
        init: function() {
            demo();
        },
    };
}();

jQuery(document).ready(function() {
    KTDatatableRemoteAjaxDemo.init();
});
