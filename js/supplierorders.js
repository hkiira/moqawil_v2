"use strict";

var KTDatatableRemoteAjaxDemo = function () {

    var demo = function () {

        var datatable = $('#kt_datatable').KTDatatable({

            data: {
                type: 'remote',
                source: {
                    read: {
                        url: HOST_URL,
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


            columns: [
                {
                    field: 'user',
                    title: 'par',
                    width: 120,
                }, {
                    field: 'code',
                    title: 'Code',
                    width: 80,
                }, {
                    field: 'products',
                    sortable: false,
                    title: 'Nombre de produis',
                    width: 80,
                }, {
                    field: 'created',
                    title: 'Date',
                    width: 120,
                }, {
                    field: 'status',
                    title: 'Statut',
                    width: 180,
                    autoHide: false,
                    // callback function support for column rendering
                    template: function (row) {
                        var status = {
                            0: {
                                'title': 'En attente de confirmation',
                                'class': ' label-light-primary'
                            },
                            1: {
                                'title': 'En attente de réception',
                                'class': ' label-light-info'
                            },
                            2: {
                                'title': 'Réceptionné partiellement',
                                'class': ' label-light-warning'
                            },
                            3: {
                                'title': 'Réceptionné totalement',
                                'class': ' label-light-success'
                            },
                            4: {
                                'title': 'Annulée',
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
                    template: function (row) {
                        var action = '';

                        if (row.status == 1) {
                            action = '<div class="dropdown dropdown-inline">\
		                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">\
		                                    <i class="la la-cog"></i>\
		                                </a>\
		                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">\
		                                    <ul class="nav nav-hoverable flex-column">\
		                                        <li class="nav-item"><a class="nav-link" href="/supplierorders/view/'+ row.id + '/1"><i class="nav-icon la la-edit"></i><span class="nav-text">Afficher</span></a></li>\
		                                        <li class="nav-item"><a class="nav-link" href="/supplierorders/print/'+ row.id + '.pdf" target="_black"><i class="nav-icon la la-edit"></i><span class="nav-text">Imprimer</span></a></li>\
		                                    </ul>\
		                                </div>\
		                            </div>';
                        } else if (row.status == 2) {
                            action = '<div class="dropdown dropdown-inline">\
		                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">\
		                                    <i class="la la-cog"></i>\
		                                </a>\
		                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">\
		                                    <ul class="nav nav-hoverable flex-column">\
		                                        <li class="nav-item"><a class="nav-link" href="/supplierorders/view/'+ row.id + '/1"><i class="nav-icon la la-edit"></i><span class="nav-text">Afficher</span></a></li>\
		                                        <li class="nav-item"><a class="nav-link" href="/supplierorders/print/'+ row.id + '.pdf" target="_black"><i class="nav-icon la la-edit"></i><span class="nav-text">Imprimer</span></a></li>\
		                                    </ul>\
		                                </div>\
		                            </div>';
                        } else if (row.status == 4) {
                            action = '<div class="dropdown dropdown-inline">\
		                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">\
		                                    <i class="la la-cog"></i>\
		                                </a>\
		                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">\
		                                    <ul class="nav nav-hoverable flex-column">\
		                                        <li class="nav-item"><a class="nav-link" href="/supplierorders/view/'+ row.id + '/1"><i class="nav-icon la la-edit"></i><span class="nav-text">Afficher</span></a></li>\
		                                    </ul>\
		                                </div>\
		                            </div>';
                        } else {
                            action = '<div class="dropdown dropdown-inline">\
		                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">\
		                                    <i class="la la-cog"></i>\
		                                </a>\
		                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">\
		                                    <ul class="nav nav-hoverable flex-column">\
		                                        <li class="nav-item"><a class="nav-link" href="/supplierorders/view/'+ row.id + '/1"><i class="nav-icon la la-edit"></i><span class="nav-text">Afficher</span></a></li>\
		                                        <li class="nav-item"><a class="nav-link" href="/supplierorders/print/'+ row.id + '.pdf" target="_black"><i class="nav-icon la la-edit"></i><span class="nav-text">Imprimer</span></a></li>\
		                                    </ul>\
		                                </div>\
		                            </div>';
                        }
                        return action;
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

        $('#kt_datatable_search_user').on('change', function () {
            datatable.search($(this).val(), 'User');
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
        $('#kt_datatable_search_user, #kt_datatable_search_status').selectpicker();
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
