"use strict";

// HOST_URL must be defined globally in the HTML template before this script.
// e.g., <script>var HOST_URL = "<?= $this->Url->build(['action' => 'search']); ?>";</script>

var MeasurementUnitsDataTable = function() {
    var table; // Reference to the DataTable instance

    var initTable = function() {
        table = $('#kt_datatable').KTDatatable({
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: HOST_URL,
                        method: 'POST',
                        map: function(raw) {
                            var dataSet = raw;
                            if (typeof raw.data !== 'undefined') {
                                dataSet = raw.data;
                            }
                            return dataSet;
                        },
                        params: {
                            query: {
                                Type: function() {
                                    return $('#kt_datatable_search_type').val();
                                },
                                Status: function() {
                                    return $('#kt_datatable_search_status').val();
                                }
                            }
                        }
                    }
                },
                pageSize: 10,
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true
            },
            layout: {
                scroll: false,
                footer: false
            },
            sortable: true,
            pagination: true,
            search: {
                input: $('#kt_datatable_search_query')
            },
            columns: [
                {
                    field: 'title',
                    title: 'Nom',
                    template: function(row) {
                        return '<div class="d-flex align-items-center">\
                                    <div class="ml-3">\
                                        <span class="text-dark-75 font-weight-bold line-height-sm d-block pb-2">' + row.title + '</span>\
                                        <span class="text-muted font-size-sm">' + row.abbreviation + '</span>\
                                    </div>\
                                </div>';
                    }
                },
                {
                    field: 'code',
                    title: 'Code'
                },
                {
                    field: 'type',
                    title: 'Type',
                    template: function(row) {
                        var typeLabels = {
                            'volume': 'Volume',
                            'weight': 'Poids',
                            'length': 'Longueur',
                            'area': 'Surface',
                            'other': 'Autre'
                        };
                        return typeLabels[row.type] || row.type;
                    }
                },
                {
                    field: 'conversion_factor',
                    title: 'Facteur de Conversion',
                    template: function(row) {
                        return row.conversion_factor ? row.conversion_factor : '-';
                    }
                },
                {
                    field: 'base_unit',
                    title: 'Unité de Base',
                    template: function(row) {
                        return row.base_unit ? row.base_unit : '-';
                    }
                },
                {
                    field: 'statut',
                    title: 'Statut',
                    template: function(row) {
                        return row.statut == 1 ? 
                            '<span class="label label-lg label-light-success label-inline">Actif</span>' : 
                            '<span class="label label-lg label-light-danger label-inline">Inactif</span>';
                    }
                },
                {
                    field: 'actions',
                    title: 'Actions',
                    sortable: false,
                    width: 125,
                    overflow: 'visible',
                    autoHide: false,
                    template: function(row) {
                        return '<a href="/measurement-units/edit/' + row.id + '" class="btn btn-sm btn-clean btn-icon mr-2" title="Modifier">\
                                    <span class="svg-icon svg-icon-md">\
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">\
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\
                                                <rect x="0" y="0" width="24" height="24"/>\
                                                <path d="M8,17.9148182 L8,5.96685884 C8,5.01591705 8.8954305,4.5 9.77777778,4.5 L15.2222222,4.5 C16.1045695,4.5 17,5.01591705 17,5.96685884 L17,17.9148182 C17,18.8657569 16.1045695,19.5 15.2222222,19.5 L9.77777778,19.5 C8.8954305,19.5 8,18.8657569 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.5, 12) rotate(-45) translate(-12.5, -12) "/>\
                                                <path d="M12,22 C13.1045695,22 14,21.1045695 14,20 C14,18.8954305 13.1045695,18 12,18 C10.8954305,18 10,18.8954305 10,20 C10,21.1045695 10.8954305,22 12,22 Z" fill="#000000" opacity="0.3"/>\
                                            </g>\
                                        </svg>\
                                    </span>\
                                </a>\
                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" title="Supprimer" onclick="deleteMeasurementUnit(' + row.id + ')">\
                                    <span class="svg-icon svg-icon-md">\
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">\
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\
                                                <rect x="0" y="0" width="24" height="24"/>\
                                                <path d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z" fill="#000000" fill-rule="nonzero"/>\
                                                <path d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>\
                                            </g>\
                                        </svg>\
                                    </span>\
                                </a>';
                    }
                }
            ]
        });

        // Custom filter dropdowns
        $('#kt_datatable_search_type, #kt_datatable_search_status').on('change', function() {
            table.reload();
        });
    };

    return {
        init: function() {
            initTable();
        }
    };
}();

jQuery(document).ready(function() {
    MeasurementUnitsDataTable.init();
});

// Delete measurement unit function
function deleteMeasurementUnit(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette unité de mesure ?')) {
        $.ajax({
            url: '/measurement-units/delete/' + id,
            type: 'POST',
            success: function(response) {
                if (response.success) {
                    toastr.success('Unité de mesure supprimée avec succès');
                    $('#kt_datatable').KTDatatable().reload();
                } else {
                    toastr.error('Erreur lors de la suppression de l\'unité de mesure');
                }
            },
            error: function() {
                toastr.error('Erreur lors de la suppression de l\'unité de mesure');
            }
        });
    }
} 