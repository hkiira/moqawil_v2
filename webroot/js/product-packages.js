"use strict";

// Class definition
var KTProductPackages = function () {
    // Shared variables
    var table;
    var datatable;

    // Private functions
    var initDatatable = function () {
        // Set date data order
        const tableRows = table.querySelectorAll('tbody tr');

        tableRows.forEach(row => {
            const dateRow = row.querySelectorAll('td');
            const realDate = moment(dateRow[3].innerHTML, "DD MMM YYYY, LT").format(); // select date from 4th column in table
            dateRow[3].setAttribute('data-order', realDate);
        });

        // Init datatable --- more info on datatables: https://datatables.net/manual/
        datatable = $(table).DataTable({
            "info": false,
            'order': [],
            'pageLength': 10,
            'columnDefs': [
                { orderable: false, targets: 0 }, // Disable ordering on column 0 (checkbox)
                { orderable: false, targets: 6 }, // Disable ordering on column 6 (actions)
            ]
        });

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        datatable.on('draw', function () {
            initToggleToolbar();
            toggleToolbars();
        });
    }

    // Search Datatable --- official docs: https://datatables.net/reference/api/search()
    var handleSearchDatatable = function () {
        const filterSearch = document.querySelector('[data-kt-datatable-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    }

    // Filter Datatable
    var handleFilterDatatable = function () {
        // Select filter options
        const filterForm = document.querySelector('[data-kt-datatable-table-filter="form"]');
        const filterButton = filterForm.querySelector('[data-kt-datatable-table-filter="filter"]');
        const selectOptions = filterForm.querySelectorAll('select');

        // Filter datatable on submit
        filterButton.addEventListener('click', function () {
            var filterString = '';

            // Get filter values
            selectOptions.forEach((item, index) => {
                if (item.value && item.value !== '') {
                    if (index !== 0) {
                        filterString += ' ';
                    }

                    filterString += item.value;
                }
            });

            // Filter datatable --- official docs: https://datatables.net/reference/api/search()
            datatable.search(filterString).draw();
        });
    }

    // Reset Filter
    var handleResetForm = function () {
        // Select reset button
        const resetButton = document.querySelector('[data-kt-datatable-table-filter="reset"]');

        // Reset datatable
        resetButton.addEventListener('click', function () {
            // Reset form
            document.querySelector('[data-kt-datatable-table-filter="form"]').reset();

            // Reset datatable --- official docs: https://datatables.net/reference/api/search()
            datatable.search('').draw();

            // Trigger select2 to update
            selectOptions.forEach((item) => {
                $(item).selectpicker('refresh');
            });
        });
    }

    // Toggle toolbars
    var toggleToolbars = function () {
        // Select toolbars
        const toolbarBase = document.querySelector('[data-kt-datatable-toolbar="base"]');
        const toolbarSelected = document.querySelector('[data-kt-datatable-toolbar="selected"]');
        const selectedCount = document.querySelector('[data-kt-datatable-select="selected_count"]');

        // Select checked boxes
        const checkboxes = table.querySelectorAll('tbody [type="checkbox"]');

        // Detect checked checkboxes
        let count = 0;

        checkboxes.forEach((item) => {
            if (item.checked) {
                count++;
            }
        });

        // Toggle toolbars
        if (count > 0) {
            selectedCount.innerHTML = count;
            toolbarBase.classList.add('d-none');
            toolbarSelected.classList.remove('d-none');
        } else {
            toolbarBase.classList.remove('d-none');
            toolbarSelected.classList.add('d-none');
        }
    }

    // Toggle delete all
    var handleDeleteRows = () => {
        // Select delete button
        const deleteButtons = document.querySelectorAll('[data-kt-datatable-table-select="delete_selected"]');

        deleteButtons.forEach(d => {
            d.addEventListener('click', function () {
                // Select checked boxes
                const checkboxes = table.querySelectorAll('tbody [type="checkbox"]');
                const selectedIds = [];

                // Detect checked checkboxes
                checkboxes.forEach((item) => {
                    if (item.checked) {
                        selectedIds.push(item.value);
                    }
                });

                // Send delete request
                if (selectedIds.length > 0) {
                    if (confirm('Êtes-vous sûr de vouloir supprimer les emballages sélectionnés ?')) {
                        $.ajax({
                            url: '/product-packages/delete/' + selectedIds.join(','),
                            type: 'POST',
                            success: function(response) {
                                // Remove selected rows
                                datatable.rows('.selected').nodes().each(function () {
                                    $(this).remove();
                                });

                                // Remove header checked box
                                const headerCheckbox = table.querySelectorAll('thead [type="checkbox"]')[0];
                                headerCheckbox.checked = false;

                                // Trigger toolbars
                                toggleToolbars();

                                // Show popup confirmation
                                Swal.fire({
                                    text: "Les emballages ont été supprimés avec succès!",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    text: "Une erreur s'est produite lors de la suppression des emballages.",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                });
                            }
                        });
                    }
                }
            });
        });
    }

    // Export buttons
    var handleExportButtons = () => {
        document.querySelectorAll('[data-kt-datatable-table-select="export_print"]').forEach(function (d) {
            d.addEventListener('click', function (e) {
                e.preventDefault();
                datatable.button(0).trigger();
            });
        });

        document.querySelectorAll('[data-kt-datatable-table-select="export_copy"]').forEach(function (d) {
            d.addEventListener('click', function (e) {
                e.preventDefault();
                datatable.button(1).trigger();
            });
        });

        document.querySelectorAll('[data-kt-datatable-table-select="export_excel"]').forEach(function (d) {
            d.addEventListener('click', function (e) {
                e.preventDefault();
                datatable.button(2).trigger();
            });
        });

        document.querySelectorAll('[data-kt-datatable-table-select="export_csv"]').forEach(function (d) {
            d.addEventListener('click', function (e) {
                e.preventDefault();
                datatable.button(3).trigger();
            });
        });

        document.querySelectorAll('[data-kt-datatable-table-select="export_pdf"]').forEach(function (d) {
            d.addEventListener('click', function (e) {
                e.preventDefault();
                datatable.button(4).trigger();
            });
        });
    }

    // Hook export buttons
    var handleExportButtons = () => {
        const documentTitle = 'Product Packages Report';
        var buttons = new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    extend: 'print',
                    title: function() {
                        return documentTitle
                    }
                },
                {
                    extend: 'copyHtml5',
                    title: function() {
                        return documentTitle
                    }
                },
                {
                    extend: 'excelHtml5',
                    title: function() {
                        return documentTitle
                    }
                },
                {
                    extend: 'csvHtml5',
                    title: function() {
                        return documentTitle
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: function() {
                        return documentTitle
                    }
                }
            ]
        }).container().appendTo($('#kt_datatable_packages_buttons'));

        // Hook dropdown menu click event to datatable export buttons
        const exportButtons = document.querySelectorAll('#kt_datatable_packages_export_menu [data-kt-export]');
        exportButtons.forEach(exportButton => {
            exportButton.addEventListener('click', e => {
                e.preventDefault();

                // Get clicked export value
                const exportValue = e.target.getAttribute('data-kt-export');
                const target = document.querySelector('.dt-buttons .buttons-' + exportValue);

                // Trigger click event on hidden datatable export buttons
                target.click();
            });
        });
    }

    // Toggle toolbars
    var initToggleToolbar = () => {
        // Toggle selected action toolbar
        const headCheckbox = table.querySelectorAll('thead [type="checkbox"]')[0];
        const bodyCheckboxes = table.querySelectorAll('tbody [type="checkbox"]');

        // Select all checkboxes
        headCheckbox.addEventListener('click', function () {
            // Get checkboxes
            bodyCheckboxes.forEach(item => {
                // Set checked state
                item.checked = headCheckbox.checked;
            });
        });

        // Checkbox actions
        bodyCheckboxes.forEach(item => {
            item.addEventListener('click', function () {
                // Next checkbox
                const nextCheckbox = item.closest('tr').querySelectorAll('[type="checkbox"]')[0];

                // Detect checked state
                if (nextCheckbox.checked) {
                    // Uncheck header checkbox
                    headCheckbox.checked = false;
                }
            });
        });
    }

    // Public methods
    return {
        init: function () {
            table = document.querySelector('#kt_datatable_packages');

            if (!table) {
                return;
            }

            initDatatable();
            handleSearchDatatable();
            handleFilterDatatable();
            handleResetForm();
            handleDeleteRows();
            handleExportButtons();
            initToggleToolbar();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTProductPackages.init();
}); 