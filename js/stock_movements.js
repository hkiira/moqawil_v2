"use strict";

var KTDatatableStockMovements = function() {

    var demo = function() {

        var datatable = $('#kt_datatable_stock_movements').KTDatatable({
            
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: HOST_URL_STOCK_MOVEMENTS_SEARCH, // This will be set in the template
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
                saveState: { // Save datatable state (paging, sorting, etc) in localStorage
                    cookie: true,
                    webstorage: true,
                },
            },

            layout: {
                scroll: false, // Can be true if many columns
                footer: true,
            },

            sortable: true,
            pagination: true,

            search: {
                input: $('#kt_datatable_search_query_stock_movements'),
                key: 'generalSearch'
            },

            columns: [
            {
                field: 'created',
                title: 'Date',
                type: 'datetime',
                format: 'YYYY-MM-DD HH:mm:ss',
                width: 130,
            }, {
                field: 'item_type',
                title: 'Type Article',
                width: 80,
            }, {
                field: 'item_name',
                title: 'Nom Article',
                width: 200,
                template: function(row) {
                    // Potentially link to item view page if URLs are available/constructible
                    return row.item_name;
                }
            }, {
                field: 'warehouse',
                title: 'Entrepôt',
                width: 120,
            }, {
                field: 'quantity_change',
                title: 'Qté Changée',
                width: 100,
                textAlign: 'right',
                 template: function(row) {
                    var q = parseFloat(row.quantity_change);
                    var color = q > 0 ? 'text-success' : (q < 0 ? 'text-danger' : 'text-muted');
                    return '<span class="font-weight-bold ' + color + '">' + q + '</span>';
                }
            }, {
                field: 'balance_after_movement',
                title: 'Solde Après',
                width: 100,
                textAlign: 'right',
            }, {
                field: 'movement_type',
                title: 'Type Mouvement',
                width: 150,
            }, {
                field: 'user',
                title: 'Utilisateur',
                width: 100,
            }, {
                field: 'notes',
                title: 'Notes',
                width: 200,
                sortable: false
            }
            // No 'Actions' column for logs typically, but can be added if view/details needed
            ],
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

        // Filters
		$('#kt_datatable_search_item_type').on('change', function() {
            datatable.search($(this).val(), 'ItemType');
        });
        $('#kt_datatable_search_warehouse').on('change', function() {
            datatable.search($(this).val(), 'Warehouse');
        });
        $('#kt_datatable_search_user').on('change', function() {
            datatable.search($(this).val(), 'User');
        });
        $('#kt_datatable_search_movement_type').on('change', function() {
            datatable.search($(this).val(), 'MovementType');
        });
        // Add date range filter JS if implementing date filters

        $('#kt_datatable_search_item_type, #kt_datatable_search_warehouse, #kt_datatable_search_user, #kt_datatable_search_movement_type').selectpicker();
    };

    return {
        init: function() {
            demo();
        },
    };
}();

jQuery(document).ready(function() {
    KTDatatableStockMovements.init();
});
