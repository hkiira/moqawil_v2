"use strict";

var KTDatatableRemoteAjaxDemo = function () {

    var demo = function () {

        var datatable = $('#kt_datatable').KTDatatable({

            data: {
                type: 'remote',
                source: {
                    read: {
                        url: HOST_URL,
                        cache: false,
                        map: function (raw) {
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
                width: 100,
                sortable: 'asc',
            }, {
                field: 'code',
                width: 100,
                title: 'Code',
            }, {
                field: 'customer',
                width: 150,
                title: 'Client',
            }, {
                field: 'total',
                sortable: false,
                width: 100,
                title: 'total',
            }, {
                field: 'loyaltypoints',
                sortable: false,
                width: 120,
                title: 'Points fidelite',
            }, {
                field: 'created',
                title: 'Date',
                type: 'date',
                textAlign: 'center',
                width: 90,
                format: 'MM/DD/YYYY',
            }, {
                field: 'status',
                title: 'Statut',
                sortable: false,
                width: 80,
                autoHide: false,
                // callback function support for column rendering
                template: function (row) {
                    var status = {
                        1: {
                            'title': 'En attente',
                            'class': ' label-light-primary'
                        },
                        2: {
                            'title': 'Confirmée',
                            'class': ' label-light-danger'
                        },
                        3: {
                            'title': 'Validée',
                            'class': ' label-light-primary'
                        },
                        4: {
                            'title': 'En attente de livraison',
                            'class': ' label-light-warning'
                        },
                        5: {
                            'title': 'En cours',
                            'class': ' label-light-info'
                        },
                        6: {
                            'title': 'Livrée',
                            'class': ' label-light-success'
                        },
                        7: {
                            'title': 'Livrée',
                            'class': ' label-light-success'
                        },
                        8: {
                            'title': 'Annulée',
                            'class': ' label-light-warning'
                        },
                        9: {
                            'title': 'Livrée',
                            'class': ' label-light-success'
                        },
                        10: {
                            'title': 'Livrée',
                            'class': ' label-light-success'
                        },
                        11: {
                            'title': 'Livrée',
                            'class': ' label-light-success'
                        },
                        12: {
                            'title': 'Livrée',
                            'class': ' label-light-success'
                        },
                    };
                    return '<span class="label font-weight-bold label-lg ' + status[row.status].class + ' label-inline">' + status[row.status].title + '</span>';
                },
            }, {
                field: 'type',
                title: 'Type',
                width: 80,
                // callback function support for column rendering
                template: function (row) {
                    var status = {
                        4: {
                            'title': 'Vente magasin',
                            'state': 'danger'
                        },
                        1: {
                            'title': 'Conventionnel',
                            'state': 'primary'
                        },
                        3: {
                            'title': 'Prévente',
                            'state': 'success'
                        },
                    };
                    return '<span class="label label-' + status[row.type].state + ' label-dot mr-2"></span><span class="font-weight-bold text-' + status[row.type].state + '">' +
                        status[row.type].title + '</span>';
                },
            }, {
                field: 'actions',
                title: 'Actions',
                sortable: false,
                width: 80,
                overflow: 'visible',
                autoHide: false,
                template: function (row) {
                    var actions = '<div class="dropdown dropdown-inline">\
                            <a href="javascript:;" class="btn btn-sm btn-clean btn-icon mr-2" data-toggle="dropdown">\
                                <span class="svg-icon svg-icon-md">\
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">\
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\
                                            <rect x="0" y="0" width="24" height="24"/>\
                                            <path d="M5,8.6862915 L5,5 L8.6862915,5 L11.5857864,2.10050506 L14.4852814,5 L19,5 L19,9.51471863 L21.4852814,12 L19,14.4852814 L19,19 L14.4852814,19 L11.5857864,21.8994949 L8.6862915,19 L5,19 L5,15.3137085 L1.6862915,12 L5,8.6862915 Z M12,15 C13.6568542,15 15,13.6568542 15,12 C15,10.3431458 13.6568542,9 12,9 C10.3431458,9 9,10.3431458 9,12 C9,13.6568542 10.3431458,15 12,15 Z" fill="#000000"/>\
                                        </g>\
                                    </svg>\
                                </span>\
                            </a>\
                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">\
                                <ul class="navi flex-column navi-hover py-2">\
                                    <li class="navi-header font-weight-bolder text-uppercase font-size-xs text-primary pb-2">\
                                        Choisissez une action:\
                                    </li>\
                                    <li class="navi-item">\
                                            <a href="/orders/edit/' + row.id + '" class="navi-link">\
                                                <span class="navi-icon"><i class="la la-pencil"></i></span>\
                                                <span class="navi-text">Modifier</span>\
                                            </a>\
                                        </li>\
                                    <li class="navi-item">\
                                        <a href="/orders/print/' + row.id + '.pdf" class="navi-link" target="_black">\
                                            <span class="navi-icon"><i class="la la-print"></i></span>\
                                            <span class="navi-text">Imprimer</span>\
                                        </a>\
                                    </li>\
                                    <li class="navi-item">\
                                        <a href="/orders/view/' + row.id + '" class="navi-link">\
                                            <span class="navi-icon"><i class="la la-eye"></i></span>\
                                            <span class="navi-text">Afficher</span>\
                                        </a>\
                                    </li>';
                    if (row.status == 1) {
                        actions += '<li class="navi-item">\
                                            <a href="/orders/delete/' + row.id + '" class="navi-link">\
                                                <span class="navi-icon"><i class="la la-trash"></i></span>\
                                                <span class="navi-text">Supprimer</span>\
                                            </a>\
                                        </li>';
                    }
                    if (row.status == 2) {
                        actions += '<li class="navi-item">\
                                            <a href="/orders/delete/' + row.id + '" class="navi-link">\
                                                <span class="navi-icon"><i class="la la-trash"></i></span>\
                                                <span class="navi-text">Supprimer</span>\
                                            </a>\
                                        </li>';
                    }
                    if (row.status == 6) {
                        actions += '<li class="navi-item">\
                                            <a href="/orders/inventory/' + row.id + '.pdf" class="navi-link" target="_black">\
                                                <span class="navi-icon"><i class="la la-trash"></i></span>\
                                                <span class="navi-text">Mouvement</span>\
                                            </a>\
                                        </li>';
                    }
                    actions += '</ul>\
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

        $('#kt_datatable_search_status').on('change', function () {
            datatable.search($(this).val().toLowerCase(), 'status');
        });

        $('.applyBtn').click(function () {
            var datestart = $('#kt_dashboard_daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD');
            var dateend = $('#kt_dashboard_daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD');
            var date = datestart + ";" + dateend;
            datatable.search(date, "date");
        });

        $('#kt_datatable_search_user').on('change', function () {
            datatable.search($(this).val().toLowerCase(), 'User');
        });

        $('#kt_datatable_search_status, #kt_datatable_search_user').selectpicker();
    };

    return {
        // public functions
        init: function () {
            demo();
        },
    };
}();

jQuery(document).ready(function () {
    KTDatatableRemoteAjaxDemo.init();
});
