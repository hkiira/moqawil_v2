"use strict";

var KTDatatablePaymentMethods = function() {
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

            layout: {
                scroll: false,
                footer: false,
            },

            sortable: true,
            pagination: true,

            search: {
                input: $('#kt_datatable_search_query'),
                key: 'generalSearch'
            },

            columns: [{
                field: 'name',
                title: 'Name',
                sortable: 'asc',
                width: 200,
                type: 'string',
                selector: false,
                textAlign: 'left',
            }, {
                field: 'code',
                title: 'Code',
                sortable: 'asc',
                width: 150,
                type: 'string',
                selector: false,
                textAlign: 'left',
            }, {
                field: 'requires_cheque_date',
                title: 'Requires Cheque Date',
                sortable: false,
                width: 150,
                type: 'number',
                selector: false,
                textAlign: 'center',
                template: function(row) {
                    return row.requires_cheque_date ? '<span class="label label-lg label-light-success label-inline">Yes</span>' : '<span class="label label-lg label-light-danger label-inline">No</span>';
                }
            }, {
                field: 'active',
                title: 'Active',
                sortable: false,
                width: 150,
                type: 'number',
                selector: false,
                textAlign: 'center',
                template: function(row) {
                    return row.active ? '<span class="label label-lg label-light-success label-inline">Active</span>' : '<span class="label label-lg label-light-danger label-inline">Inactive</span>';
                }
            }, {
                field: 'Actions',
                title: 'Actions',
                sortable: false,
                width: 150,
                overflow: 'visible',
                autoHide: false,
                template: function(row) {
                    return '\
                        <a href="/payment-methods/view/' + row.id + '" class="btn btn-sm btn-clean btn-icon mr-2" title="View">\
                            <span class="svg-icon svg-icon-md">\
                                <i class="la la-eye"></i>\
                            </span>\
                        </a>\
                        <a href="/payment-methods/edit/' + row.id + '" class="btn btn-sm btn-clean btn-icon mr-2" title="Edit">\
                            <span class="svg-icon svg-icon-md">\
                                <i class="la la-edit"></i>\
                            </span>\
                        </a>\
                        <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" title="Delete" data-id="' + row.id + '">\
                            <span class="svg-icon svg-icon-md">\
                                <i class="la la-trash"></i>\
                            </span>\
                        </a>\
                    ';
                }
            }]
        });

        // Handle delete button click
        $('#kt_datatable').on('click', 'a[title="Delete"]', function() {
            var id = $(this).data('id');
            if (confirm('Are you sure you want to delete this payment method?')) {
                $.ajax({
                    url: '/payment-methods/delete/' + id,
                    type: 'POST',
                    success: function(response) {
                        datatable.reload();
                    },
                    error: function(xhr, status, error) {
                        alert('Error deleting payment method');
                    }
                });
            }
        });
    };

    return {
        init: function() {
            demo();
        }
    };
}();

jQuery(document).ready(function() {
    KTDatatablePaymentMethods.init();
}); 