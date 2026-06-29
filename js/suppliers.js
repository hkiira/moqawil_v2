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
                title: 'Fournisseur',
                width: 300,
                sortable: 'asc',
                template: function(row) {
                	return '<span style="width: 250px;">\
                				<div class="d-flex align-items-center">\
									<div class="symbol symbol-40 flex-shrink-0">\
										<div class="symbol-label" style="background-image:url(' + row.img + ')"></div>\
									</div>\
									<div class="ml-2">\
										<div class="text-dark-75 font-weight-bold line-height-sm">' +row.code+' -'+row.name + '</div>\
										<a href="#" class="font-size-sm text-dark-50 text-hover-primary">' + row.phone + '</a>\
									</div>\
								</div>\
							</span>';
                },
            }, {
                field: 'adresse',
                title: 'Adresse',
                width: 200,
            }, {
                field: 'status',
                title: 'Statut',
                width: 80,
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
                width: 80,
                sortable: false,
                overflow: 'visible',
                autoHide: false,
                template: function(row) {
                    return '<div class="dropdown dropdown-inline">\
                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">\
                                    <i class="la la-cog"></i>\
                                </a>\
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">\
                                    <ul class="nav nav-hoverable flex-column">\
                                        <li class="nav-item"><a class="nav-link" href="/suppliers/edit/'+row.id+'/1"><i class="nav-icon la la-edit"></i><span class="nav-text">Modifier les infos</span></a></li>\
                                        <li class="nav-item"><a class="nav-link" href="/suppliers/edit/'+row.id+'/2"><i class="nav-icon la la-edit"></i><span class="nav-text">Modifier la photo</span></a></li>\
                                        <li class="nav-item"><a class="nav-link" href="/adresses/edit/suppliers/'+row.id+'"><i class="nav-icon la la-edit"></i><span class="nav-text">Modifier l\'adresse</span></a></li>\
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
            datatable.search($(this).val().toLowerCase(), 'Status');
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
