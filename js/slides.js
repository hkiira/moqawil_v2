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

            // columns definition
            columns: [{
                field: 'photo',
                title: 'Photo',
                sortable: 'asc',
                template: function(row) {
                    return '<div class="symbol symbol-50 flex-shrink-0">\
		                <img src="'+row.photo+'" alt="photo">\
		            </div>';
                }
            },{
                field: 'title',
                title: 'Nom du slide',
                sortable: 'asc',
            }, {
                field: 'actions',
                title: 'Actions',
                sortable: false,
                width: 80,
                overflow: 'visible',
                autoHide: false,
                template: function(row) {
                    return '<a href="/Slides/delete/'+row.id+'" class="btn btn-outline-danger btn-icon btn-sm">\
                                <i class="la la-trash"></i>\
                            </a>';
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
