<?php $this->extend('/Common/crud'); ?>
<?php $this->loadHelper('Form', ['templates' => 'app_form']);
$this->assign('objet', $this->Form->create($product, ['type' => 'file', 'id' => 'kt_form_1']));
$this->assign('title', 'Ajouter un Produit');
$this->assign('subtitle', 'Créez un nouveau produit et définissez ses unités de mesure et de vente.');
?>

<div class="card-body">
    <!-- Section: Informations Générales -->
    <div class="card card-custom card-border mb-6">
        <div class="card-header bg-light-primary border-0 min-h-50px px-5">
            <div class="card-title">
                <span class="card-icon">
                    <i class="flaticon2-information text-primary font-size-h5"></i>
                </span>
                <h5 class="card-label text-primary font-weight-bolder font-size-h6 mb-0">Informations Générales</h5>
            </div>
        </div>
        <div class="card-body p-6">
            <div class="row">
                <div class="col-xl-6">
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Nom de l'article <span class="text-danger">*</span></label>
                        <?= $this->Form->control('title', ['label' => false, 'class' => 'form-control form-control-solid', 'placeholder' => 'Entrez le nom du produit']); ?>
                        <?= $this->Form->control('brand_id', ['type' => 'hidden', 'value' => 1]); ?>
                    </div>
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Catégorie <span class="text-danger">*</span></label>
                        <?= $this->Form->control('category_id', ['label' => false, 'options' => $categories, 'class' => 'form-control select2 selectpicker', 'data-live-search' => 'true', 'empty' => 'Sélectionner une Catégorie']); ?>
                    </div>
                    <?= $this->Form->control('gstock', ['type' => 'hidden', 'value' => 1]); ?>
                </div>
                <div class="col-xl-6">
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Photo du produit</label>
                        <div class="custom-file">
                            <?= $this->Form->control('photo.photo', ['label' => false, 'type' => 'file', 'class' => 'custom-file-input', 'id' => 'customFile']); ?>
                            <label class="custom-file-label" for="customFile">Choisir un fichier</label>
                        </div>
                    </div>
                    <?= $this->Form->control('commission', ['class' => 'form-control', 'label' => false, 'type' => 'hidden', 'value' => 0]); ?>
                    <?= $this->Form->control('packtype_id', ['type' => 'hidden', 'value' => 1]); ?>
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Prix d'achat global <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <?= $this->Form->control('buyingprice', ['class' => 'form-control form-control-solid', 'label' => false, 'type' => 'number', 'step' => 'any', 'placeholder' => '0.00', 'required' => 'required']); ?>
                            <div class="input-group-append"><span class="input-group-text">DH</span></div>
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Statut</label>
                        <?php
                        $statutOptions = [0 => 'Inactif', 1 => 'Actif'];
                        echo $this->Form->control('statut', [
                            'label' => false,
                            'options' => $statutOptions,
                            'class' => 'form-control select2',
                            'default' => 1
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= $this->Form->control('measurement_quantity', ['type' => 'hidden', 'value' => 1]); ?>
    <?= $this->Form->control('measurement_unit_id', ['type' => 'hidden', 'value' => 1]); ?>

</div>

<?= $this->Html->script(['/assets/js/pages/crud/forms/widgets/select2.js'], ['block' => 'script_bottom']) ?>

<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$(document).ready(function() {
    // Custom file input name display
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });

    $.fn.select2.defaults.set("width", "100%");
    $('.select2:not(.select2-repeater)').each(function() {
        $(this).select2({
            placeholder: $(this).find('option[value=""]').text() || 'Sélectionner une option',
        });
    });
});
<?= $this->Html->scriptEnd(); ?>
