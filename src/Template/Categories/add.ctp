<?php   
$this->extend('/Common/crud');
?>
<?php 
    $this->loadHelper('Form', [
        'templates' => 'app_form',
    ]); 
    $this->assign('objet', $this->Form->create($category, ['id' => 'kt_form_1']));
    $this->assign('title', 'Ajouter une catégorie ou une famille');
    $this->assign('subtitle', 'Définissez la hiérarchie des familles de produits et sous-catégories.');
?>

<style>
.option-card {
    border: 2px solid #ebedf3;
    border-radius: 0.85rem;
    padding: 1.25rem;
    cursor: pointer;
    transition: all 0.25s ease;
    background-color: #ffffff;
    height: 100%;
}
.option-card:hover {
    border-color: #3699ff;
    box-shadow: 0px 0px 15px rgba(54, 153, 255, 0.15);
}
.option-card.active {
    border-color: #3699ff;
    background-color: #f3f6f9;
}
.option-card input[type="radio"] {
    display: none;
}
</style>

<div class="card-body p-6">
    <!-- Section 1: Type Selection (Option Cards) -->
    <div class="card card-custom card-border mb-6">
        <div class="card-header bg-light-primary border-0 min-h-50px px-5">
            <div class="card-title">
                <span class="card-icon">
                    <i class="flaticon2-layers text-primary font-size-h5"></i>
                </span>
                <h5 class="card-label text-primary font-weight-bolder font-size-h6 mb-0">1. Type de Catégorie</h5>
            </div>
        </div>
        <div class="card-body p-6">
            <div class="row">
                <div class="col-md-6 mb-4 mb-md-0">
                    <label class="option-card <?= ($id != 2) ? 'active' : '' ?> d-flex align-items-center" id="card-famille">
                        <input type="radio" name="type_cat" value="famille" <?= ($id != 2) ? 'checked="checked"' : '' ?> id="type-famille"/>
                        <div class="symbol symbol-45 symbol-light-primary mr-4">
                            <span class="symbol-label">
                                <i class="flaticon2-folder text-primary font-size-h3"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="font-weight-bolder text-dark mb-1">Famille Principale</h6>
                            <span class="text-muted font-size-sm">Catégorie de haut niveau (ex: Boissons, Épicerie)</span>
                        </div>
                    </label>
                </div>

                <div class="col-md-6">
                    <label class="option-card <?= ($id == 2) ? 'active' : '' ?> d-flex align-items-center" id="card-subcategory">
                        <input type="radio" name="type_cat" value="sub_category" <?= ($id == 2) ? 'checked="checked"' : '' ?> id="type-subcategory"/>
                        <div class="symbol symbol-45 symbol-light-success mr-4">
                            <span class="symbol-label">
                                <i class="flaticon2-tag text-success font-size-h3"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="font-weight-bolder text-dark mb-1">Sous-Famille / Catégorie</h6>
                            <span class="text-muted font-size-sm">Sous-division rattachée à une Famille Principale</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Informations Générales -->
    <div class="card card-custom card-border mb-6">
        <div class="card-header bg-light-primary border-0 min-h-50px px-5">
            <div class="card-title">
                <span class="card-icon">
                    <i class="flaticon2-file text-primary font-size-h5"></i>
                </span>
                <h5 class="card-label text-primary font-weight-bolder font-size-h6 mb-0">2. Informations Générales</h5>
            </div>
        </div>
        <div class="card-body p-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-4">
                        <label class="font-weight-bolder text-dark">Nom de la catégorie <span class="text-danger">*</span></label>
                        <?= $this->Form->control('title', [
                            'label' => false,
                            'class' => 'form-control form-control-solid form-control-lg',
                            'placeholder' => 'Saisir le nom'
                        ]); ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div id="parent-category-container" class="form-group mb-4" style="<?= ($id == 2) ? '' : 'display: none;' ?>">
                        <label class="font-weight-bolder text-dark">Famille Parente <span class="text-danger">*</span></label>
                        <?= $this->Form->control('category_id', [
                            'label' => false,
                            'options' => $categories,
                            'class' => 'form-control select2 form-control-solid',
                            'id' => 'parent-category-select',
                            'empty' => 'Sélectionner une famille parente'
                        ]); ?>
                    </div>
                </div>

                <div class="col-md-6 mt-2">
                    <div class="form-group mb-0">
                        <label class="font-weight-bolder text-dark mb-3">Statut d'activité</label>
                        <?= $this->element('statut')  ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$(document).ready(function() {
    $.fn.select2.defaults.set("width", "100%");
    
    $('#parent-category-select').select2({
        placeholder: 'Sélectionner une famille parente',
        width: '100%'
    });

    $('input[name="type_cat"]').change(function() {
        var val = $(this).val();
        $('.option-card').removeClass('active');
        if (val === 'famille') {
            $('#card-famille').addClass('active');
            $('#parent-category-container').hide();
            $('#parent-category-select').val('').trigger('change');
        } else {
            $('#card-subcategory').addClass('active');
            $('#parent-category-container').show();
            $('#parent-category-select').select2({ placeholder: 'Sélectionner une famille parente', width: '100%' });
        }
    });
});
<?= $this->Html->scriptEnd(); ?>