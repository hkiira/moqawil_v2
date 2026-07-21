"use strict";

var KTDatatableRemoteAjaxDemo = function () {

    var formatChildCategories = function(data) {
        if (!data || data.length === 0) {
            return '<div class="p-4 text-muted text-center font-weight-bold">Aucune sous-catégorie rattachée à cette famille</div>';
        }

        var html = '<div class="card card-custom p-4 my-2" style="background-color: #fcfcfd; border: 1px dashed #3699ff;">';
        html += '<h6 class="font-weight-bolder text-primary mb-3"><i class="flaticon2-tag text-primary mr-2"></i>Sous-Familles & Catégories de cette famille</h6>';
        html += '<table class="table table-bordered table-head-custom mb-0">';
        html += '<thead><tr><th>Code</th><th>Nom de la sous-catégorie</th><th>Statut</th><th class="text-right">Actions</th></tr></thead>';
        html += '<tbody>';

        $.each(data, function(index, cat) {
            var statusHtml = '';
            if (cat.Status == 1) {
                statusHtml = '<span class="label label-inline label-light-success font-weight-bold">Actif</span>';
            } else {
                statusHtml = '<span class="label label-inline label-light-danger font-weight-bold">Inactif</span>';
            }

            html += '<tr>';
            html += '<td><span class="font-weight-bolder text-dark">' + cat.Code + '</span></td>';
            html += '<td>' + cat.Title + '</td>';
            html += '<td>' + statusHtml + '</td>';
            html += '<td class="text-right">' + cat.Actions + '</td>';
            html += '</tr>';
        });

        html += '</tbody></table></div>';
        return html;
    };

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
                    field: 'RecordID',
                    title: '',
                    sortable: false,
                    width: 30,
                    template: function(row) {
                        if (row.parentcategory == "aucune") {
                            return '<i class="fa fa-chevron-right text-muted cursor-pointer toggle-details" data-id="' + row.id + '" style="font-size: 0.9rem; cursor: pointer;"></i>';
                        }
                        return '';
                    }
                },
                {
                    field: 'name',
                    title: 'Nom',
                    sortable: 'asc',
                    template: function (row) {
                        return '<span style="width: 250px;">\
                                <div class="d-flex align-items-center">\
                                    <div class="symbol symbol-40 flex-shrink-0">\
                                        <div class="symbol-label" style="background-image:url(' + row.img + ')"></div>\
                                    </div>\
                                    <div class="ml-2">\
                                        <div class="text-dark-75 font-weight-bold line-height-sm">' + row.name + '</div>\
                                        <span class="font-size-sm text-muted">' + row.code + '</span>\
                                    </div>\
                                </div>\
                            </span>';
                    },
                }, {
                    field: 'category',
                    title: 'Catégorie Parente',
                }, {
                    field: 'status',
                    title: 'Statut',
                    autoHide: false,
                    template: function (row) {
                        var status = {
                            0: {
                                'title': 'Inactif',
                                'class': ' label-light-danger'
                            },
                            1: {
                                'title': 'Actif',
                                'class': ' label-light-success'
                            },
                        };
                        var st = status[row.status] || status[0];
                        return '<span class="label font-weight-bold label-lg ' + st.class + ' label-inline">' + st.title + '</span>';
                    },
                }, {
                    field: 'actions',
                    title: 'Actions',
                    sortable: false,
                    overflow: 'visible',
                    autoHide: false,
                    template: function (row) {
                        if (row.parentcategory == "aucune") {
                            return '<div class="dropdown dropdown-inline">\
                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">\
                                    <i class="la la-cog"></i>\
                                </a>\
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">\
                                    <ul class="nav nav-hoverable flex-column">\
                                        <li class="nav-item"><a class="nav-link" href="/categories/edit/'+ row.id + '"><i class="nav-icon la la-edit"></i><span class="nav-text">Modifier</span></a></li>\
                                        <li class="nav-item"><a class="nav-link" href="/categories/edit/'+ row.id + '/image"><i class="nav-icon la la-image"></i><span class="nav-text">Modifier l\'image</span></a></li>\
                                        <li class="nav-item"><a class="nav-link" href="/categories/index/2/'+ row.id + '"><i class="nav-icon la la-list"></i><span class="nav-text">Liste des sous-catégories</span></a></li>\
                                    </ul>\
                                </div>\
                            </div>';
                        } else {
                            return '<div class="dropdown dropdown-inline">\
                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">\
                                    <i class="la la-cog"></i>\
                                </a>\
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">\
                                    <ul class="nav nav-hoverable flex-column">\
                                        <li class="nav-item"><a class="nav-link" href="/categories/edit/'+ row.id + '"><i class="nav-icon la la-edit"></i><span class="nav-text">Modifier</span></a></li>\
                                        <li class="nav-item"><a class="nav-link" href="/categories/edit/'+ row.id + '/image"><i class="nav-icon la la-image"></i><span class="nav-text">Modifier l\'image</span></a></li>\
                                        <li class="nav-item"><a class="nav-link" href="/categories/view/'+ row.id + '"><i class="nav-icon la la-eye"></i><span class="nav-text">Liste des articles</span></a></li>\
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

        $('#kt_datatable_search_status').on('change', function () {
            datatable.search($(this).val().toLowerCase(), 'Status');
        });

        // Add event listener for opening and closing nested sub-categories (like Zones)
        $('#kt_datatable').on('click', '.toggle-details', function () {
            var icon = $(this);
            var parentId = icon.attr('data-id');
            var tr = icon.closest('tr');
            var detailRow = tr.next('.child-category-row');

            if (detailRow.length > 0) {
                detailRow.toggle();
                if (detailRow.is(':visible')) {
                    icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
                } else {
                    icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
                }
            } else {
                icon.removeClass('fa-chevron-right').addClass('fa-spinner fa-spin');

                $.ajax({
                    url: '/categories/child-categories/' + parentId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        icon.removeClass('fa-spinner fa-spin').addClass('fa-chevron-down');
                        var subTableHtml = '<tr class="child-category-row"><td colspan="5" class="p-0">' + formatChildCategories(data) + '</td></tr>';
                        tr.after(subTableHtml);
                    },
                    error: function() {
                        icon.removeClass('fa-spinner fa-spin').addClass('fa-chevron-right');
                    }
                });
            }
        });
    };

    return {
        init: function () {
            demo();
        },
    };
}();

jQuery(document).ready(function () {
    KTDatatableRemoteAjaxDemo.init();
});
