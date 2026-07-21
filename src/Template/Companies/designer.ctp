<?php
$this->assign('title', 'Concepteur de documents PDF');
?>

<div class="d-flex flex-column flex-lg-row">
    <!-- Left panel: Draggable elements -->
    <div class="flex-column flex-lg-row-auto w-100 w-lg-300px mb-10 mb-lg-0 mr-lg-8">
        <div class="card card-custom card-stretch">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label font-weight-bolder text-dark">Éléments PDF</h3>
                </div>
            </div>
            <div class="card-body pt-4">
                <p class="text-muted font-size-sm">Faites glisser les composants ci-dessous sur la zone de travail centrale A4 pour concevoir votre modèle de document.</p>
                
                <div id="designer-toolbox" class="d-flex flex-column gap-4">
                    <div class="toolbox-item card card-custom bg-light-primary border-primary border-dashed mb-3 p-4 cursor-grab" draggable="true" data-type="header">
                        <div class="d-flex align-items-center">
                            <span class="symbol symbol-30 mr-3">
                                <span class="symbol-label bg-primary text-white"><i class="flaticon-buildings font-size-h5"></i></span>
                            </span>
                            <div>
                                <h6 class="font-weight-bolder text-dark mb-0">En-tête Société</h6>
                                <small class="text-muted">Logo, nom, adresse</small>
                            </div>
                        </div>
                    </div>

                    <div class="toolbox-item card card-custom bg-light-success border-success border-dashed mb-3 p-4 cursor-grab" draggable="true" data-type="client">
                        <div class="d-flex align-items-center">
                            <span class="symbol symbol-30 mr-3">
                                <span class="symbol-label bg-success text-white"><i class="flaticon-users font-size-h5"></i></span>
                            </span>
                            <div>
                                <h6 class="font-weight-bolder text-dark mb-0">Infos Client</h6>
                                <small class="text-muted">Nom, adresse client</small>
                            </div>
                        </div>
                    </div>

                    <div class="toolbox-item card card-custom bg-light-warning border-warning border-dashed mb-3 p-4 cursor-grab" draggable="true" data-type="meta">
                        <div class="d-flex align-items-center">
                            <span class="symbol symbol-30 mr-3">
                                <span class="symbol-label bg-warning text-white"><i class="flaticon2-document font-size-h5"></i></span>
                            </span>
                            <div>
                                <h6 class="font-weight-bolder text-dark mb-0">Détails Document</h6>
                                <small class="text-muted">Code, date, échéance</small>
                            </div>
                        </div>
                    </div>

                    <div class="toolbox-item card card-custom bg-light-info border-info border-dashed mb-3 p-4 cursor-grab" draggable="true" data-type="table">
                        <div class="d-flex align-items-center">
                            <span class="symbol symbol-30 mr-3">
                                <span class="symbol-label bg-info text-white"><i class="flaticon-list font-size-h5"></i></span>
                            </span>
                            <div>
                                <h6 class="font-weight-bolder text-dark mb-0">Tableau Articles</h6>
                                <small class="text-muted">Liste des produits, Qté, Prix</small>
                            </div>
                        </div>
                    </div>

                    <div class="toolbox-item card card-custom bg-light-danger border-danger border-dashed mb-3 p-4 cursor-grab" draggable="true" data-type="totals">
                        <div class="d-flex align-items-center">
                            <span class="symbol symbol-30 mr-3">
                                <span class="symbol-label bg-danger text-white"><i class="flaticon-price-tag font-size-h5"></i></span>
                            </span>
                            <div>
                                <h6 class="font-weight-bolder text-dark mb-0">Calcul Totaux</h6>
                                <small class="text-muted">HT, TVA, Remises, TTC</small>
                            </div>
                        </div>
                    </div>

                    <div class="toolbox-item card card-custom bg-light-dark border-dark border-dashed mb-3 p-4 cursor-grab" draggable="true" data-type="text">
                        <div class="d-flex align-items-center">
                            <span class="symbol symbol-30 mr-3">
                                <span class="symbol-label bg-dark text-white"><i class="flaticon2-edit font-size-h5"></i></span>
                            </span>
                            <div>
                                <h6 class="font-weight-bolder text-dark mb-0">Zone de Texte Libre</h6>
                                <small class="text-muted">Notes, RIB, remerciements</small>
                            </div>
                        </div>
                    </div>

                    <div class="toolbox-item card card-custom bg-light-primary border-primary border-dashed mb-3 p-4 cursor-grab" draggable="true" data-type="signatures">
                        <div class="d-flex align-items-center">
                            <span class="symbol symbol-30 mr-3">
                                <span class="symbol-label bg-primary text-white"><i class="flaticon-edit font-size-h5"></i></span>
                            </span>
                            <div>
                                <h6 class="font-weight-bolder text-dark mb-0">Signatures</h6>
                                <small class="text-muted">Signature société & client</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Center panel: A4 Canvas -->
    <div class="flex-row-fluid">
        <div class="card card-custom card-stretch">
            <div class="card-header align-items-center py-5">
                <div class="card-title">
                    <h3 class="card-label font-weight-bolder text-dark">Zone de Travail A4</h3>
                    <div class="ml-4">
                        <select class="form-control form-control-sm font-weight-bold" id="select-document-type" style="width: 250px;">
                            <option value="facture" <?= $documentType === 'facture' ? 'selected' : '' ?>>Facture (Invoices)</option>
                            <option value="livraison" <?= $documentType === 'livraison' ? 'selected' : '' ?>>Bon de Livraison (Shippings)</option>
                            <option value="sortie" <?= $documentType === 'sortie' ? 'selected' : '' ?>>Bon de Sortie (Exitslips)</option>
                            <option value="commande" <?= $documentType === 'commande' ? 'selected' : '' ?>>Bon de Commande Fournisseur (Supplierorders)</option>
                            <option value="commande_client" <?= $documentType === 'commande_client' ? 'selected' : '' ?>>Bon de Commande Client (Orders)</option>
                            <option value="catalogue" <?= $documentType === 'catalogue' ? 'selected' : '' ?>>Catalogue Articles (Products)</option>
                            <option value="fiche" <?= $documentType === 'fiche' ? 'selected' : '' ?>>Fiche de Stock / État (Slips)</option>
                            <option value="inventaire" <?= $documentType === 'inventaire' ? 'selected' : '' ?>>Inventaire (Inventories)</option>
                        </select>
                    </div>
                </div>
                <div class="card-toolbar gap-2">
                    <button type="button" class="btn btn-light-danger btn-sm font-weight-bold mr-2" id="btn-clear-canvas">
                        <i class="la la-trash-restore"></i> Réinitialiser
                    </button>
                    <button type="button" class="btn btn-primary btn-sm font-weight-bold" id="btn-save-template">
                        <i class="la la-save"></i> Enregistrer le modèle
                    </button>
                </div>
            </div>
            <div class="card-body bg-light-o-50 d-flex justify-content-center pt-8 overflow-auto" style="min-height: 700px;">
                <div id="pdf-a4-canvas" class="bg-white rounded shadow-lg p-10 position-relative" style="width: 210mm; min-height: 297mm; border: 1px solid #ddd; background-image: radial-gradient(#ddd 1px, transparent 0); background-size: 20px 20px;">
                    <div id="canvas-dropzone" class="w-100 h-100 d-flex flex-column gap-3" style="min-height: 270mm;">
                        <!-- Canvas elements will be injected here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right panel: Inspector -->
    <div class="flex-column flex-lg-row-auto w-100 w-lg-300px mb-10 mb-lg-0 ml-lg-8">
        <div class="card card-custom card-stretch">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label font-weight-bolder text-dark">Propriétés</h3>
                </div>
            </div>
            <div class="card-body pt-4" id="inspector-panel">
                <div id="inspector-empty" class="text-center py-10 text-muted">
                    <i class="flaticon-search font-size-h1 text-muted opacity-40 mb-3 d-block"></i>
                    <p class="font-size-sm">Cliquez sur un élément de la zone de travail pour le configurer.</p>
                </div>
                
                <div id="inspector-form" class="d-none">
                    <input type="hidden" id="elem-id" />
                    
                    <div class="form-group mb-5">
                        <label class="font-weight-bolder font-size-sm">Marge supérieure (px)</label>
                        <input type="number" class="form-control form-control-sm" id="elem-margin-top" value="10" />
                    </div>

                    <div class="form-group mb-5">
                        <label class="font-weight-bolder font-size-sm">Marge inférieure (px)</label>
                        <input type="number" class="form-control form-control-sm" id="elem-margin-bottom" value="10" />
                    </div>

                    <div class="form-group mb-5">
                        <label class="font-weight-bolder font-size-sm">Alignement du texte</label>
                        <select class="form-control form-control-sm" id="elem-align">
                            <option value="left">Gauche</option>
                            <option value="center">Centre</option>
                            <option value="right">Droite</option>
                        </select>
                    </div>

                    <div class="form-group mb-5 d-none" id="group-custom-text">
                        <label class="font-weight-bolder font-size-sm">Texte personnalisé</label>
                        <textarea class="form-control form-control-sm" rows="5" id="elem-custom-text"></textarea>
                    </div>

                    <div class="form-group mb-5">
                        <label class="font-weight-bolder font-size-sm">Taille de police</label>
                        <select class="form-control form-control-sm" id="elem-font-size">
                            <option value="11px">Petite (11px)</option>
                            <option value="13px">Normal (13px)</option>
                            <option value="16px">Grande (16px)</option>
                        </select>
                    </div>

                    <button type="button" class="btn btn-light-danger btn-sm btn-block font-weight-bold" id="btn-delete-element">
                        <i class="la la-trash"></i> Supprimer cet élément
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.cursor-grab {
    cursor: grab;
}
.cursor-grab:active {
    cursor: grabbing;
}
.canvas-item {
    border: 1px dashed transparent;
    transition: all 0.2s ease;
    position: relative;
}
.canvas-item:hover {
    border-color: #3699FF;
    background-color: rgba(54, 153, 255, 0.03);
}
.canvas-item.selected {
    border: 1px solid #3699FF;
    background-color: rgba(54, 153, 255, 0.05);
}
.canvas-item .actions-overlay {
    position: absolute;
    top: 5px;
    right: 5px;
    display: none;
}
.canvas-item:hover .actions-overlay {
    display: block;
}
</style>

<?= $this->Html->scriptStart(['block' => 'script_bottom']) ?>
$(document).ready(function() {
    var canvasElements = [];
    var selectedElementId = null;
    var loadedConfig = <?= !empty($templateConfig) ? $templateConfig : '[]' ?>;

    // Document type switcher
    $('#select-document-type').on('change', function() {
        var docType = $(this).val();
        window.location.href = '<?= $this->Url->build(["action" => "designer"]) ?>/' + docType;
    });

    // Load initial layout if exists, or set default layout
    if (loadedConfig.length > 0) {
        canvasElements = loadedConfig;
        renderCanvas();
    } else {
        // Setup a beautiful default template
        canvasElements = [
            { id: generateId(), type: 'header', marginTop: 10, marginBottom: 15, align: 'left', fontSize: '13px', customText: '' },
            { id: generateId(), type: 'meta', marginTop: 10, marginBottom: 15, align: 'right', fontSize: '13px', customText: '' },
            { id: generateId(), type: 'client', marginTop: 10, marginBottom: 15, align: 'left', fontSize: '13px', customText: '' },
            { id: generateId(), type: 'table', marginTop: 15, marginBottom: 15, align: 'left', fontSize: '13px', customText: '' },
            { id: generateId(), type: 'totals', marginTop: 15, marginBottom: 15, align: 'right', fontSize: '13px', customText: '' }
        ];
        renderCanvas();
    }

    // Drag-and-drop toolbox handlers
    var toolboxItems = document.querySelectorAll('.toolbox-item');
    toolboxItems.forEach(function(item) {
        item.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('text/plain', this.dataset.type);
        });
    });

    var dropzone = document.getElementById('canvas-dropzone');
    dropzone.addEventListener('dragover', function(e) {
        e.preventDefault();
    });

    dropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        var type = e.dataTransfer.getData('text/plain');
        if (type) {
            var newElement = {
                id: generateId(),
                type: type,
                marginTop: 10,
                marginBottom: 10,
                align: (type === 'totals' || type === 'meta') ? 'right' : 'left',
                fontSize: '13px',
                customText: type === 'text' ? 'Saisissez votre texte personnalisé ici...' : ''
            };
            canvasElements.push(newElement);
            renderCanvas();
            selectElement(newElement.id);
        }
    });

    function generateId() {
        return 'elem_' + Math.random().toString(36).substr(2, 9);
    }

    function renderCanvas() {
        var dropzone = $('#canvas-dropzone');
        dropzone.empty();

        if (canvasElements.length === 0) {
            dropzone.append('<div class="d-flex align-items-center justify-content-center h-100 text-muted"><p>La zone de travail est vide. Glissez des éléments ici.</p></div>');
            return;
        }

        canvasElements.forEach(function(elem, index) {
            var contentHtml = '';
            
            switch (elem.type) {
                case 'header':
                    contentHtml = `
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-4">
                            <div>
                                <h4 class="font-weight-bold text-primary mb-1"><?= h($company->name) ?></h4>
                                <p class="text-muted font-size-sm mb-0"><?= h($company->adresse) ?><br><?= h($company->city) ?> | Tél: <?= h($company->phone) ?></p>
                            </div>
                            <div class="bg-light-primary rounded p-4 text-center" style="width: 120px; border: 1px dashed #3699FF;">
                                <span class="font-size-sm text-primary font-weight-bold">LOGO SOCIÉTÉ</span>
                            </div>
                        </div>`;
                    break;
                case 'client':
                    contentHtml = `
                        <div class="card bg-light-success p-4 rounded" style="max-width: 350px;">
                            <span class="text-success font-weight-bold font-size-sm mb-2">Destinataire / Client</span>
                            <h6 class="font-weight-bold mb-1">M. LE CLIENT DEMO</h6>
                            <p class="text-muted font-size-xs mb-0">123 Rue de la République<br>Casablanca, Maroc</p>
                        </div>`;
                    break;
                case 'meta':
                    var docLabel = 'FACTURE';
                    var docCode = '#FAC-2026-001';
                    if ('<?= $documentType ?>' === 'livraison') {
                        docLabel = 'BON DE LIVRAISON';
                        docCode = '#BL-2026-001';
                    } else if ('<?= $documentType ?>' === 'sortie') {
                        docLabel = 'BON DE SORTIE';
                        docCode = '#BS-2026-001';
                    } else if ('<?= $documentType ?>' === 'commande') {
                        docLabel = 'BON DE COMMANDE FOURNISSEUR';
                        docCode = '#BCF-2026-001';
                    } else if ('<?= $documentType ?>' === 'commande_client') {
                        docLabel = 'BON DE COMMANDE CLIENT';
                        docCode = '#BCC-2026-001';
                    } else if ('<?= $documentType ?>' === 'catalogue') {
                        docLabel = 'CATALOGUE PRODUITS';
                        docCode = '#CAT-2026-001';
                    } else if ('<?= $documentType ?>' === 'fiche') {
                        docLabel = 'FICHE DE STOCK / ÉTAT';
                        docCode = '#FSH-2026-001';
                    } else if ('<?= $documentType ?>' === 'inventaire') {
                        docLabel = 'INVENTAIRE DE STOCK';
                        docCode = '#INV-2026-001';
                    }
                    contentHtml = `
                        <div>
                            <span class="label label-inline label-lg label-light-warning font-weight-bold mb-2">${docLabel} ${docCode}</span>
                            <p class="text-muted font-size-xs mb-0">Date de création: 21/07/2026<br>Statut: Brouillon</p>
                        </div>`;
                    break;
                case 'table':
                    contentHtml = `
                        <table class="table table-bordered table-sm font-size-sm">
                            <thead>
                                <tr class="bg-light-info text-info">
                                    <th>Description / Produit</th>
                                    <th class="text-center" style="width: 80px;">Qté</th>
                                    <th class="text-right" style="width: 100px;">P.U (DH)</th>
                                    <th class="text-right" style="width: 100px;">Total (DH)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Article de démonstration A</td>
                                    <td class="text-center">2</td>
                                    <td class="text-right">150,00</td>
                                    <td class="text-right">300,00</td>
                                </tr>
                                <tr>
                                    <td>Article de démonstration B</td>
                                    <td class="text-center">1</td>
                                    <td class="text-right">500,00</td>
                                    <td class="text-right">500,00</td>
                                </tr>
                            </tbody>
                        </table>`;
                    break;
                case 'totals':
                    contentHtml = `
                        <div class="d-inline-block bg-light-danger p-4 rounded text-right" style="min-width: 250px;">
                            <div class="d-flex justify-content-between font-size-xs mb-1">
                                <span>Sous-Total HT</span>
                                <span class="font-weight-bold">800,00 DH</span>
                            </div>
                            <div class="d-flex justify-content-between font-size-xs mb-1">
                                <span>TVA (20%)</span>
                                <span class="font-weight-bold">160,00 DH</span>
                            </div>
                            <div class="d-flex justify-content-between font-size-sm border-top pt-2 mt-2">
                                <span class="font-weight-bolder text-danger">Total TTC</span>
                                <span class="font-weight-bolder text-danger">960,00 DH</span>
                            </div>
                        </div>`;
                    break;
                case 'text':
                    contentHtml = `
                        <div class="p-3 border rounded bg-light-light text-muted font-size-sm" style="white-space: pre-wrap;">${elem.customText}</div>`;
                    break;
                case 'signatures':
                    contentHtml = `
                        <div class="d-flex justify-content-between pt-6">
                            <div class="text-center" style="width: 200px; border-top: 1px solid #ddd; padding-top: 10px;">
                                <span class="font-size-xs text-muted">Signature & Cachet Société</span>
                            </div>
                            <div class="text-center" style="width: 200px; border-top: 1px solid #ddd; padding-top: 10px;">
                                <span class="font-size-xs text-muted">Signature Client</span>
                            </div>
                        </div>`;
                    break;
            }

            var itemDiv = $(`
                <div class="canvas-item p-3 mb-2 rounded" data-id="${elem.id}" style="margin-top: ${elem.marginTop}px; margin-bottom: ${elem.marginBottom}px; text-align: ${elem.align}; font-size: ${elem.fontSize};">
                    ${contentHtml}
                    <div class="actions-overlay">
                        <button type="button" class="btn btn-xs btn-icon btn-light-primary btn-move-up" data-index="${index}"><i class="la la-arrow-up"></i></button>
                        <button type="button" class="btn btn-xs btn-icon btn-light-primary btn-move-down" data-index="${index}"><i class="la la-arrow-down"></i></button>
                    </div>
                </div>
            `);

            if (selectedElementId === elem.id) {
                itemDiv.addClass('selected');
            }

            // Click to select
            itemDiv.on('click', function(e) {
                if ($(e.target).closest('.actions-overlay').length > 0) return;
                selectElement($(this).data('id'));
            });

            dropzone.append(itemDiv);
        });

        // Reordering buttons handler
        $('.btn-move-up').on('click', function() {
            var index = parseInt($(this).data('index'));
            if (index > 0) {
                var temp = canvasElements[index];
                canvasElements[index] = canvasElements[index - 1];
                canvasElements[index - 1] = temp;
                renderCanvas();
            }
        });

        $('.btn-move-down').on('click', function() {
            var index = parseInt($(this).data('index'));
            if (index < canvasElements.length - 1) {
                var temp = canvasElements[index];
                canvasElements[index] = canvasElements[index + 1];
                canvasElements[index + 1] = temp;
                renderCanvas();
            }
        });
    }

    function selectElement(id) {
        selectedElementId = id;
        $('.canvas-item').removeClass('selected');
        $(`.canvas-item[data-id="${id}"]`).addClass('selected');

        var elem = canvasElements.find(function(el) { return el.id === id; });
        if (elem) {
            $('#inspector-empty').addClass('d-none');
            $('#inspector-form').removeClass('d-none');

            $('#elem-id').val(elem.id);
            $('#elem-margin-top').val(elem.marginTop);
            $('#elem-margin-bottom').val(elem.marginBottom);
            $('#elem-align').val(elem.align);
            $('#elem-font-size').val(elem.fontSize);

            if (elem.type === 'text') {
                $('#group-custom-text').removeClass('d-none');
                $('#elem-custom-text').val(elem.customText);
            } else {
                $('#group-custom-text').addClass('d-none');
            }
        }
    }

    // Inspector input change listeners
    $('#elem-margin-top').on('input change', function() {
        var id = $('#elem-id').val();
        var val = parseInt($(this).val()) || 0;
        var elem = canvasElements.find(function(el) { return el.id === id; });
        if (elem) {
            elem.marginTop = val;
            $(`.canvas-item[data-id="${id}"]`).css('margin-top', val + 'px');
        }
    });

    $('#elem-margin-bottom').on('input change', function() {
        var id = $('#elem-id').val();
        var val = parseInt($(this).val()) || 0;
        var elem = canvasElements.find(function(el) { return el.id === id; });
        if (elem) {
            elem.marginBottom = val;
            $(`.canvas-item[data-id="${id}"]`).css('margin-bottom', val + 'px');
        }
    });

    $('#elem-align').on('change', function() {
        var id = $('#elem-id').val();
        var val = $(this).val();
        var elem = canvasElements.find(function(el) { return el.id === id; });
        if (elem) {
            elem.align = val;
            $(`.canvas-item[data-id="${id}"]`).css('text-align', val);
        }
    });

    $('#elem-font-size').on('change', function() {
        var id = $('#elem-id').val();
        var val = $(this).val();
        var elem = canvasElements.find(function(el) { return el.id === id; });
        if (elem) {
            elem.fontSize = val;
            $(`.canvas-item[data-id="${id}"]`).css('font-size', val);
        }
    });

    $('#elem-custom-text').on('input change', function() {
        var id = $('#elem-id').val();
        var val = $(this).val();
        var elem = canvasElements.find(function(el) { return el.id === id; });
        if (elem) {
            elem.customText = val;
            $(`.canvas-item[data-id="${id}"] .bg-light-light`).text(val);
        }
    });

    // Delete elements
    $('#btn-delete-element').on('click', function() {
        var id = $('#elem-id').val();
        canvasElements = canvasElements.filter(function(el) { return el.id !== id; });
        selectedElementId = null;
        $('#inspector-form').addClass('d-none');
        $('#inspector-empty').removeClass('d-none');
        renderCanvas();
    });

    // Clear Canvas
    $('#btn-clear-canvas').on('click', function() {
        if (confirm('Voulez-vous vraiment vider la zone de travail ?')) {
            canvasElements = [];
            selectedElementId = null;
            $('#inspector-form').addClass('d-none');
            $('#inspector-empty').removeClass('d-none');
            renderCanvas();
        }
    });

    // Save layout config
    $('#btn-save-template').on('click', function() {
        var button = $(this);
        button.addClass('spinner spinner-white spinner-right').attr('disabled', true);

        $.ajax({
            url: '<?= $this->Url->build(['action' => 'saveTemplate', $documentType]) ?>',
            type: 'POST',
            data: {
                layout: JSON.stringify(canvasElements)
            },
            dataType: 'json',
            success: function(response) {
                button.removeClass('spinner spinner-white spinner-right').attr('disabled', false);
                if (response.success) {
                    Swal.fire({
                        text: "Votre modèle a été enregistré avec succès !",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Super !",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            },
            error: function() {
                button.removeClass('spinner spinner-white spinner-right').attr('disabled', false);
                Swal.fire({
                    text: "Une erreur est survenue lors de l'enregistrement.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "OK",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
            }
        });
    });
});
<?= $this->Html->scriptEnd(); ?>
