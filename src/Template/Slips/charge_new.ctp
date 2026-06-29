<?php
/**
 * ChargeNew - Modern Warehouse Transfer Slip Form
 * Enhanced UI with improved UX and modern design patterns
 */
$this->extend('/Common/crud');

$this->loadHelper('Form', [
    'templates' => 'app_form',
]);

$this->assign('objet', $this->Form->create($slip, [
    'id' => 'charge-new-form',
    'class' => 'needs-validation modern-form'
]));
$this->assign('title', 'Nouveau bon de charge (Version moderne)');
?>

<div class="card-body">
    <!-- Progress Indicator -->
    <div class="progress-steps mb-5">
        <div class="step-container">
            <div class="step active" data-step="1">
                <div class="step-number">1</div>
                <div class="step-label">Nature & Destination</div>
            </div>
            <div class="step-line"></div>
            <div class="step" data-step="2">
                <div class="step-number">2</div>
                <div class="step-label">Sélection Articles</div>
            </div>
            <div class="step-line"></div>
            <div class="step" data-step="3">
                <div class="step-number">3</div>
                <div class="step-label">Validation</div>
            </div>
        </div>
    </div>

    <!-- Step 1: Transfer Information -->
    <div class="form-step" id="step-1">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-gradient-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-exchange-alt me-2"></i>
                    Informations de transfert
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="form-floating">
                            <?= $this->Form->control('whnature_id', [
                                'options' => $whnatures,
                                'class' => 'form-control form-select custom-select',
                                'label' => false,
                                'required' => true,
                                'empty' => '-- Sélectionnez la nature --',
                                'id' => 'whnature-id'
                            ]); ?>
                            <label for="whnature-id">
                                <i class="fas fa-tag text-primary me-2"></i>
                                Nature de transfert *
                            </label>
                        </div>
                        <div class="form-text mt-2">
                            <i class="fas fa-info-circle"></i>
                            Type de mouvement de stock
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="form-floating">
                            <?= $this->Form->control('warehoused', [
                                'type' => 'select',
                                'options' => $warehoused,
                                'class' => 'form-control form-select custom-select',
                                'label' => false,
                                'required' => true,
                                'empty' => '-- Sélectionnez un entrepôt --',
                                'disabled' => true,
                                'id' => 'warehoused'
                            ]); ?>
                            <label for="warehoused">
                                <i class="fas fa-warehouse text-primary me-2"></i>
                                Entrepôt de réception *
                            </label>
                        </div>
                        <div class="form-text mt-2">
                            <i class="fas fa-map-marker-alt"></i>
                            Destination du transfert
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="form-floating">
                            <?= $this->Form->control('raison', [
                                'type' => 'textarea',
                                'class' => 'form-control',
                                'label' => false,
                                'rows' => 3,
                                'placeholder' => 'Décrivez la raison de ce transfert...',
                                'style' => 'height: 100px;',
                                'id' => 'raison'
                            ]); ?>
                            <label for="raison">
                                <i class="fas fa-comment-alt text-primary me-2"></i>
                                Raison du transfert (optionnel)
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-lg btn-primary px-5" id="next-step-1">
                        Suivant
                        <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 2: Stock Selection -->
    <div class="form-step d-none" id="step-2">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-gradient-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-boxes me-2"></i>
                    Articles disponibles
                </h5>
            </div>
            <div class="card-body p-4">
                <!-- Search and Filter Bar -->
                <div class="row mb-4">
                    <div class="col-lg-6">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control" id="search-products" 
                                   placeholder="Rechercher un article...">
                        </div>
                    </div>
                    <div class="col-lg-6 text-end">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary" id="clear-all">
                                <i class="fas fa-eraser"></i> Tout effacer
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="show-selected">
                                <i class="fas fa-filter"></i> Afficher sélectionnés
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stock Items Container -->
                <div class="slips-container" id="slips-container">
                    <div class="alert alert-info text-center py-5">
                        <i class="fas fa-spinner fa-spin fa-3x mb-3"></i>
                        <h5>Chargement des articles disponibles...</h5>
                        <p class="text-muted">Veuillez patienter</p>
                    </div>
                </div>

                <!-- Selected Items Summary -->
                <div class="card bg-light mt-4" id="summary-card" style="display: none;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h6 class="mb-0">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span id="selected-count">0</span> article(s) sélectionné(s)
                                </h6>
                            </div>
                            <div class="col-lg-4 text-end">
                                <button type="button" class="btn btn-outline-danger" id="reset-selection">
                                    <i class="fas fa-undo"></i> Réinitialiser
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-lg btn-outline-secondary px-5" id="prev-step-1">
                        <i class="fas fa-arrow-left me-2"></i>
                        Précédent
                    </button>
                    <button type="button" class="btn btn-lg btn-primary px-5" id="next-step-2" disabled>
                        Suivant
                        <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 3: Validation -->
    <div class="form-step d-none" id="step-3">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-gradient-warning text-white">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-check me-2"></i>
                    Récapitulatif
                </h5>
            </div>
            <div class="card-body p-4">
                <div id="validation-summary">
                    <!-- Summary will be populated by JavaScript -->
                </div>

                <div class="alert alert-warning mt-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Attention:</strong> Veuillez vérifier attentivement les informations avant de valider.
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-lg btn-outline-secondary px-5" id="prev-step-2">
                        <i class="fas fa-arrow-left me-2"></i>
                        Précédent
                    </button>
                    <button type="submit" class="btn btn-lg btn-success px-5" id="submit-form">
                        <i class="fas fa-check-circle me-2"></i>
                        Valider le bon de charge
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Progress Steps */
.progress-steps {
    padding: 20px 0;
}

.step-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 600px;
    margin: 0 auto;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 2;
}

.step-number {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
    transition: all 0.3s ease;
    border: 3px solid #e9ecef;
}

.step.active .step-number {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    transform: scale(1.1);
}

.step.completed .step-number {
    background: #28a745;
    color: white;
    border-color: #28a745;
}

.step-label {
    margin-top: 10px;
    font-size: 0.875rem;
    color: #6c757d;
    font-weight: 500;
    text-align: center;
}

.step.active .step-label {
    color: #667eea;
    font-weight: 600;
}

.step-line {
    flex: 1;
    height: 3px;
    background: #e9ecef;
    margin: 0 10px;
    margin-bottom: 35px;
}

.step.completed ~ .step-line {
    background: #28a745;
}

/* Gradient Backgrounds */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

/* Modern Form Controls */
.form-floating > .form-control,
.form-floating > .form-select {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.form-floating > .form-control:focus,
.form-floating > .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.form-floating > label {
    padding: 1rem 0.75rem;
}

/* Stock Table Enhancements */
.stock-row {
    transition: all 0.3s ease;
}

.stock-row.has-quantity {
    background: linear-gradient(90deg, rgba(102, 126, 234, 0.1) 0%, rgba(102, 126, 234, 0.05) 100%) !important;
    border-left: 4px solid #667eea !important;
}

.stock-row:hover {
    background-color: rgba(102, 126, 234, 0.05) !important;
}

#stock-table {
    border-radius: 10px;
    overflow: hidden;
}

#stock-table thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    border: none !important;
}

.input-group-text {
    background-color: #f8f9fa;
    border: 2px solid #e9ecef;
    font-weight: 600;
}

/* Icon Circle */
.icon-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.icon-circle.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.icon-circle.bg-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

/* Badges */
.badge {
    padding: 0.4rem 0.8rem;
    font-weight: 600;
}

/* Utility */
.fw-bold {
    font-weight: 700 !important;
}

/* Buttons */
.btn {
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
}

.btn-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border: none;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(17, 153, 142, 0.4);
}

/* Cards */
.card {
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    border-bottom: none;
    padding: 1.5rem;
}

/* Loading Animation */
.loading-overlay {
    position: relative;
    pointer-events: none;
    opacity: 0.6;
}

.loading-overlay::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    border: 4px solid #f3f3f3;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
    z-index: 10;
}

@keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .step-label {
        font-size: 0.75rem;
    }
    
    .step-number {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem !important;
        font-size: 1rem;
    }
}

/* Hide/Show Animations */
.fade-in {
    animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<?= $this->Html->scriptStart(['block' => 'script_bottom']) ?>
(function() {
    'use strict';

    // State Management
    const state = {
        currentStep: 1,
        selectedNature: null,
        selectedWarehouse: null,
        selectedProducts: new Map(),
        isLoading: false
    };

    // Configuration
    const CONFIG = {
        ajaxUrl: 'chargestock',
        debounceDelay: 300
    };

    /**
     * Initialize the form
     */
    function init() {
        setupEventListeners();
        updateStepDisplay();
    }

    /**
     * Setup all event listeners
     */
    function setupEventListeners() {
        // Step navigation
        $('#next-step-1').on('click', () => validateAndMoveToStep(2));
        $('#next-step-2').on('click', () => validateAndMoveToStep(3));
        $('#prev-step-1').on('click', () => moveToStep(1));
        $('#prev-step-2').on('click', () => moveToStep(2));

        // Form controls
        $('#whnature-id').on('change', handleNatureChange);
        $('#warehoused').on('change', handleWarehouseChange);
        $('#search-products').on('keyup', debounce(handleSearch, CONFIG.debounceDelay));
        
        // Product actions
        $('#clear-all').on('click', clearAllQuantities);
        $('#show-selected').on('click', filterSelectedProducts);
        $('#reset-selection').on('click', resetSelection);

        // Form submission
        $('#charge-new-form').on('submit', handleFormSubmit);
    }

    /**
     * Update step display
     */
    function updateStepDisplay() {
        // Update progress steps
        $('.step').removeClass('active completed');
        $(`.step[data-step="${state.currentStep}"]`).addClass('active');
        
        for (let i = 1; i < state.currentStep; i++) {
            $(`.step[data-step="${i}"]`).addClass('completed');
        }

        // Show current form step
        $('.form-step').addClass('d-none');
        $(`#step-${state.currentStep}`).removeClass('d-none').addClass('fade-in');
    }

    /**
     * Move to specific step
     */
    function moveToStep(step) {
        state.currentStep = step;
        updateStepDisplay();
        
        if (step === 3) {
            generateValidationSummary();
        }
    }

    /**
     * Validate and move to next step
     */
    function validateAndMoveToStep(nextStep) {
        if (nextStep === 2) {
            const natureId = $('#whnature-id').val();
            const warehouseId = $('#warehoused').val();
            
            if (!natureId || !warehouseId) {
                showAlert('danger', 'Veuillez sélectionner la nature et l\'entrepôt.');
                return;
            }
            
            loadStockData();
        } else if (nextStep === 3) {
            if (state.selectedProducts.size === 0) {
                showAlert('danger', 'Veuillez sélectionner au moins un article.');
                return;
            }
        }
        
        moveToStep(nextStep);
    }

    /**
     * Handle nature selection change
     */
    function handleNatureChange() {
        const natureId = $(this).val();
        state.selectedNature = natureId;
        
        const $warehouse = $('#warehoused');
        if (natureId) {
            $warehouse.prop('disabled', false);
        } else {
            $warehouse.prop('disabled', true).val('');
            state.selectedWarehouse = null;
        }
    }

    /**
     * Handle warehouse selection change
     */
    function handleWarehouseChange() {
        state.selectedWarehouse = $(this).val();
    }

    /**
     * Load stock data via AJAX
     */
    function loadStockData() {
        if (state.isLoading) return;
        
        state.isLoading = true;
        const $container = $('#slips-container');
        $container.addClass('loading-overlay');

        $.ajax({
            method: 'GET',
            url: CONFIG.ajaxUrl,
            data: {
                keyword: state.selectedWarehouse,
                keyword1: state.selectedNature
            },
            timeout: 10000
        })
        .done(function(response) {
            $container.html(response);
            initializeProductCards();
            updateNextButton();
        })
        .fail(function(xhr, status) {
            let errorMessage = 'Erreur lors du chargement des données';
            
            if (status === 'timeout') {
                errorMessage = 'La requête a expiré. Veuillez réessayer.';
            } else if (xhr.status === 404) {
                errorMessage = 'Aucun article disponible.';
            }
            
            $container.html(`
                <div class="alert alert-danger text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h5>${errorMessage}</h5>
                    <button type="button" class="btn btn-outline-danger mt-3" onclick="location.reload()">
                        <i class="fas fa-sync"></i> Recharger
                    </button>
                </div>
            `);
        })
        .always(function() {
            $container.removeClass('loading-overlay');
            state.isLoading = false;
        });
    }

    /**
     * Initialize product cards with event listeners
     */
    function initializeProductCards() {
        // Listen to quantity changes from the loaded chargestock template
        $(document).on('input change', '.package-qty, .unit-qty', function() {
            const $input = $(this);
            const $row = $input.closest('.stock-row');
            const packId = $input.data('pack-id');
            
            // Get both package and unit quantities for this row
            const packageQty = parseInt($row.find('.package-qty').val()) || 0;
            const unitQty = parseInt($row.find('.unit-qty').val()) || 0;
            const qtyPerCs = $input.data('qty-per-cs') || 1;
            
            // Calculate total quantity
            const totalQty = (packageQty * qtyPerCs) + unitQty;
            
            // Update selected products map
            if (totalQty > 0) {
                state.selectedProducts.set(packId, {
                    packages: packageQty,
                    units: unitQty,
                    total: totalQty
                });
                $row.addClass('has-quantity');
            } else {
                state.selectedProducts.delete(packId);
                $row.removeClass('has-quantity');
            }
            
            updateSelectionSummary();
            updateNextButton();
        });
        
        console.log('Product cards initialized');
    }

    /**
     * Handle quantity input change
     */
    function handleQuantityChange() {
        // This is now handled in initializeProductCards
        // Kept for backwards compatibility
    }

    /**
     * Update selection summary
     */
    function updateSelectionSummary() {
        const count = state.selectedProducts.size;
        $('#selected-count').text(count);
        
        if (count > 0) {
            $('#summary-card').fadeIn();
        } else {
            $('#summary-card').fadeOut();
        }
    }

    /**
     * Update next button state
     */
    function updateNextButton() {
        const hasSelection = state.selectedProducts.size > 0;
        $('#next-step-2').prop('disabled', !hasSelection);
    }

    /**
     * Clear all quantities
     */
    function clearAllQuantities() {
        $('.package-qty, .unit-qty').val(0).trigger('change');
        $('.stock-row').removeClass('has-quantity');
    }

    /**
     * Filter to show only selected products
     */
    function filterSelectedProducts() {
        const $btn = $('#show-selected');
        const isFiltered = $btn.hasClass('active');
        
        if (isFiltered) {
            // Show all
            $('.stock-row').show();
            $btn.removeClass('active btn-primary').addClass('btn-outline-primary');
            $btn.html('<i class="fas fa-filter"></i> Afficher sélectionnés');
        } else {
            // Show only selected
            $('.stock-row').each(function() {
                const hasQuantity = $(this).hasClass('has-quantity');
                $(this).toggle(hasQuantity);
            });
            $btn.addClass('active btn-primary').removeClass('btn-outline-primary');
            $btn.html('<i class="fas fa-filter-circle-xmark"></i> Afficher tous');
        }
    }

    /**
     * Reset selection
     */
    function resetSelection() {
        if (confirm('Êtes-vous sûr de vouloir réinitialiser toutes les quantités?')) {
            state.selectedProducts.clear();
            clearAllQuantities();
            $('.stock-row').show();
            $('#show-selected').removeClass('active btn-primary').addClass('btn-outline-primary');
            updateSelectionSummary();
        }
    }

    /**
     * Handle search
     */
    function handleSearch() {
        const searchTerm = $(this).val().toLowerCase();
        
        $('.stock-row').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(searchTerm));
        });
    }

    /**
     * Generate validation summary
     */
    function generateValidationSummary() {
        const natureName = $('#whnature-id option:selected').text();
        const warehouseName = $('#warehoused option:selected').text();
        
        let html = `
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="icon-circle bg-primary">
                                        <i class="fas fa-tag text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted mb-1">Nature de transfert</h6>
                                    <p class="mb-0 fw-bold">${natureName}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="icon-circle bg-success">
                                        <i class="fas fa-warehouse text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted mb-1">Entrepôt de destination</h6>
                                    <p class="mb-0 fw-bold">${warehouseName}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0">
                        <i class="fas fa-list-check me-2"></i>
                        Articles sélectionnés (${state.selectedProducts.size})
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Article</th>
                                    <th class="text-center">Cartons/Sacs</th>
                                    <th class="text-center">Unités</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
        `;
        
        let grandTotal = 0;
        state.selectedProducts.forEach((data, packId) => {
            const $row = $(`.stock-row[data-pack-id="${packId}"]`);
            const productName = $row.find('strong').first().text() || 'Article';
            const unit = $row.find('.input-group-text small').first().text();
            
            html += `
                <tr>
                    <td>
                        <strong>${productName}</strong>
                    </td>
                    <td class="text-center">
                        ${data.packages > 0 ? '<span class="badge bg-primary">' + data.packages + '</span>' : '-'}
                    </td>
                    <td class="text-center">
                        ${data.units > 0 ? '<span class="badge bg-info">' + data.units + '</span>' : '-'}
                    </td>
                    <td class="text-end">
                        <strong>${data.total} ${unit || ''}</strong>
                    </td>
                </tr>
            `;
            grandTotal += data.total;
        });
        
        html += `
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total général:</strong></td>
                                    <td class="text-end">
                                        <h5 class="mb-0 text-primary">${grandTotal}</h5>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        `;
        
        $('#validation-summary').html(html);
    }

    /**
     * Handle form submission
     */
    function handleFormSubmit(e) {
        if (state.selectedProducts.size === 0) {
            e.preventDefault();
            showAlert('danger', 'Aucun article sélectionné.');
            return false;
        }
        
        // Show loading state
        $('#submit-form').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Enregistrement...');
        
        return true;
    }

    /**
     * Show alert message
     */
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="fas fa-${type === 'danger' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('.card-body').first().prepend(alertHtml);
        
        setTimeout(() => {
            $('.alert').fadeOut(() => $(this).remove());
        }, 5000);
    }

    /**
     * Debounce helper
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func.apply(this, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Initialize on document ready
    $(document).ready(init);

})();
<?= $this->Html->scriptEnd(); ?>
