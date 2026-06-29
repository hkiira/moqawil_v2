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
            columns: [ {
                field: 'code',
                width: 80,
                title: 'Code',
            }, {
                field: 'warehouse',
                title: 'Du',
                width: 80,
            }, {
                field: 'warehoused',
                title: 'Vers',
                width: 80,
            }, {
                field: 'products',
                title: 'Articles',
                width: 80,
            },{
                field: 'date',
                title: 'Date',
                width: 100,
            }, {
                field: 'user',
                title: 'Par',
                width: 180,
                // callback function support for column rendering
                template: function(row) {
                    return '<span class="label label-primary label-dot mr-2"></span><span class="font-weight-bold text-primary">Pour ' +
                        row.user + '</span><br><span class="label label-success label-dot mr-2"></span><span class="font-weight-bold text-success">Validé par ' +
                        row.validate + '</span>';
                },
            },{
                field: 'status',
                title: 'Statut',
                width: 90,
                autoHide: false,
                // callback function support for column rendering
                template: function(row) {
                    var status = {
                        1: {
                            'title': 'confirmé',
                            'class': ' label-light-primary'
                        },
                        2: {
                            'title': 'En attente',
                            'class': ' label-light-warning'
                        },
                        3: {
                            'title': 'Validé',
                            'class': ' label-light-success'
                        },
                        4: {
                            'title': 'Récupéré',
                            'class': ' label-light-warning'
                        },
                        5: {
                            'title': 'Encaissé',
                            'class': ' label-light-danger'
                        },
                        6: {
                            'title': 'Encaissé',
                            'class': ' label-light-danger'
                        },
                        7: {
                            'title': 'Encaissé',
                            'class': ' label-light-danger'
                        },
                        8: {
                            'title': 'Encaissé',
                            'class': ' label-light-danger'
                        },
                        9: {
                            'title': 'Encaissé',
                            'class': ' label-light-danger'
                        },
                        10: {
                            'title': 'Encaissé',
                            'class': ' label-light-danger'
                        },
                        11: {
                            'title': 'Encaissé',
                            'class': ' label-light-danger'
                        },
                        12: {
                            'title': 'Encaissé',
                            'class': ' label-light-danger'
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
                    var actions='<div class="dropdown dropdown-inline">\
                                    <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">\
                                        <i class="la la-cog"></i>\
                                    </a>\
                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">\
                                        <ul class="nav nav-hoverable flex-column">';
                            if(row.status==2){
                                actions+='<li class="nav-item"><a class="nav-link" href="/slips/print/' +  row.id + '.pdf" target="_blank""><span class="nav-text">Imprimer</span></a></li>';
                                actions+='<li class="nav-item"><a class="nav-link" href="/slips/validation/' +  row.id + '"><span class="nav-text">Valider</span></a></li>';
                                actions+='<li class="nav-item"><a class="nav-link" href="/slips/delete/' +  row.id + '"><span class="nav-text">Supprimer</span></a></li>';
                            }else{
                                actions+='<li class="nav-item"><a class="nav-link" href="/slips/print/' +  row.id + '.pdf" target="_blank""><span class="nav-text">Imprimer</span></a></li>';
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
