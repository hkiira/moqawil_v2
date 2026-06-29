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
                field: 'code',
                width: 100,
                title: 'Code',
            },{
                field: 'user',
                title: 'Vendeur',
                width: 150,
                sortable: 'asc',
            }, {
                field: 'total',
                title: 'Montant Total',
                width: 120,
            }, {
                field: 'payment_methods',
                title: 'Modes de Paiement',
                width: 150,
            }, {
                field: 'customers',
                title: 'Clients',
                width: 200,
            }, {
                field: 'created',
                title: 'Date de création',
                type: 'date',
                textAlign: 'center',
                width: 150,
                format: 'MM/DD/YYYY',
            }, {
                field: 'actions',
                title: 'Actions',
                sortable: false,
                width: 80,
                overflow: 'visible',
                autoHide: false,
                template: function(row) {
                    var actions='<div class="dropdown dropdown-inline">\
                                    <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">\
                                        <i class="la la-cog"></i>\
                                    </a>\
                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">\
                                        <ul class="nav nav-hoverable flex-column">\
                                        <li class="nav-item"><a class="nav-link" href="/payments/edit/' +  row.id + '"><span class="nav-text">Modifier</span></a></li>';
                                actions+='</ul>\
                            </div>\
                        </div>';
                    return actions;
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
            datatable.search($(this).val().toLowerCase(), 'status');
        });

        $('.applyBtn').click(function () {
            var datestart=$('#kt_dashboard_daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD');
            var dateend=$('#kt_dashboard_daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD');
            var date=datestart+";"+dateend;
            datatable.search(date,"date");
        });

        $('#kt_datatable_search_user').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'User');
        });

        $('#kt_datatable_search_status, #kt_datatable_search_user').selectpicker();
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
