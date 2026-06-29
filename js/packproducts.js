"use strict";

var KTDatatableRemoteAjaxDemo = function() {

    var demo = function() {

        var datatable = $('#kt_datatable').KTDatatable({
            
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: HOST_URL, // This will be set in the template to packproducts/search
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

            layout: {
                scroll: true,
                footer: true,
            },

            sortable: true,
            pagination: true,

            search: {
                input: $('#kt_datatable_search_query'),
                key: 'generalSearch'
            },

            columns: [
            {
                field: 'pack',
                title: 'Pack',
                sortable: 'asc',
                template: function(row) {
                    // Assuming row.pack_id and row.pack (title) are available
                    // Link to pack view if possible, or just display name
                    return row.pack ? row.pack : 'N/A';
                },
            }, {
                field: 'product',
                title: 'Produit',
                template: function(row) {
                    // Assuming row.product_id and row.product (title) are available
                    return row.product ? row.product : 'N/A';
                },
            }, {
                field: 'quantity',
                title: 'Quantité',
                width: 80,
            }, {
                field: 'status',
                title: 'Statut',
                width: 80,
                autoHide: false,
                template: function(row) {
                    var status = {
                        '-1': { 'title': 'Supprimé', 'class': ' label-light-dark' }, // For soft deleted
                        0: { 'title': 'Innactif', 'class': ' label-light-danger' },
						1: { 'title': 'Actif', 'class': ' label-light-success' }
                    };
                    if (status[row.status]) {
                        return '<span class="label font-weight-bold label-lg ' + status[row.status].class + ' label-inline">' + status[row.status].title + '</span>';
                    }
                    return '<span class="label font-weight-bold label-lg label-light-primary label-inline">N/A</span>';
                },
            }, {
                field: 'company',
                title: 'Société',
                template: function(row) {
                    return row.company ? row.company : 'N/A';
                }
            }, {
                field: 'actions',
                title: 'Actions',
                sortable: false,
                width: 80,
                overflow: 'visible',
                autoHide: false,
                template: function(row) {
                    if(row.edit == 0){ // Assuming 'edit' field indicates permission
                        return '';
                    } else {
                       return '<div class="dropdown dropdown-inline">\
                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">\
                                    <i class="la la-cog"></i>\
                                </a>\
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">\
                                    <ul class="nav nav-hoverable flex-column">\
                                        <li class="nav-item"><a class="nav-link" href="/packproducts/view/'+row.id+'"><i class="nav-icon la la-eye"></i><span class="nav-text">Afficher</span></a></li>\
                                        <li class="nav-item"><a class="nav-link" href="/packproducts/edit/'+row.id+'"><i class="nav-icon la la-edit"></i><span class="nav-text">Modifier</span></a></li>\
                                        <li class="nav-item"><a class="nav-link" href="/packproducts/delete/'+row.id+'" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cet enregistrement?\');"><i class="nav-icon la la-remove"></i><span class="nav-text">Supprimer</span></a></li>\
                                    </ul>\
                                </div>\
                            </div>'; 
                    }
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

        // Filter setup for Packproducts
		$('#kt_datatable_search_pack').on('change', function() {
            datatable.search($(this).val(), 'Pack');
        });
        
		$('#kt_datatable_search_product').on('change', function() {
            datatable.search($(this).val(), 'Product');
        });

        $('#kt_datatable_search_status').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'Status');
        });

        $('#kt_datatable_search_pack, #kt_datatable_search_product, #kt_datatable_search_status').selectpicker();
    };

    return {
        init: function() {
            demo();
        },
    };
}();

jQuery(document).ready(function() {
    KTDatatableRemoteAjaxDemo.init();
});
