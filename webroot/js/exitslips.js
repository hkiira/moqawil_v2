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
                field: 'user',
                title: 'Par',
                width: 180,
                sortable: 'asc',
            }, {
                field: 'code',
                width: 100,
                title: 'Code',
            }, {
                field: 'created',
                title: 'Date',
                type: 'date',
                textAlign: 'center',
                width: 120,
                format: 'MM/DD/YYYY',
            }, {
                field: 'shipping',
                title: 'Bons de livraison',
                width: 120,
            },{
                field: 'status',
                title: 'Statut',
                width: 150,
                // callback function support for column rendering
                template: function(row) {
                    var status = {
                        1: {
                            'title': 'En préparation',
                            'class': ' label-light-warning'
                        },
                        2: {
                            'title': 'Validé',
                            'class': ' label-light-success'
                        },
                    };
                    return '<span class="label font-weight-bold label-lg ' + status[row.status].class + ' label-inline">' + status[row.status].title + '</span>';
                },
            },  {
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
                                        <li class="nav-item"><a class="nav-link" href="/exitslips/print/' +  row.id + '.pdf" target="_blank"><span class="nav-text">Imprimer</span></a></li>\
                                        <li class="nav-item"><a class="nav-link" href="/exitslips/thermalprint/' +  row.id + '" target="_blank"><span class="nav-text">Impression thermique</span></a></li>\
                                        <li class="nav-item"><a class="nav-link" href="/shippings/index/' +  row.id + '"><span class="nav-text">Bons de livraison</span></a></li>';
                                    if(row.inventory==1){
                                        actions+='<li class="nav-item"><a class="nav-link" href="/exitslips/inventoryprint/'+  row.id + '.pdf" target="_blank"><span class="nav-text">Historique</span></a></li>';
                                    }
                                    
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
