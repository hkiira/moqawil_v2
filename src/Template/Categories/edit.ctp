<?php   
$this->extend('/Common/crud');
?>
<?php 
    $this->loadHelper('Form', [
        'templates' => 'app_form',
    ]); 
    $isSub = !empty($category->category_id);
    $categorie = $isSub ? "catégorie" : "famille";
    $this->assign('objet', $this->Form->create($category, ['type' => 'file', 'id' => 'kt_form_1_edit']));
    $this->assign('title', 'Modifier la ' . $categorie . ' : ' . h($category->title));
    $this->assign('subtitle', 'Mettez à jour les paramètres de la ' . $categorie);
?>

<div class="card-body p-6">
    <?php if ($image == "image"): ?>
        <div class="card card-custom card-border mb-6">
            <div class="card-header bg-light-primary border-0 min-h-50px px-5">
                <div class="card-title">
                    <span class="card-icon">
                        <i class="flaticon2-image-file text-primary font-size-h5"></i>
                    </span>
                    <h5 class="card-label text-primary font-weight-bolder font-size-h6 mb-0">Modifier l'Image de la Catégorie</h5>
                </div>
            </div>
            <div class="card-body p-6">
                <div class="form-group mb-0">
                    <label class="font-weight-bolder text-dark mb-3">Photo de couverture</label>
                    <div class="custom-file">
                        <?= $this->Form->control('photo.photo', [
                            'label' => false,
                            'type' => 'file',
                            'class' => 'custom-file-input',
                            'id' => 'customFileEdit'
                        ]); ?>
                        <label class="custom-file-label" for="customFileEdit">Choisir une image...</label>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Section 1: Type Information Badge -->
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
                <div class="d-flex align-items-center">
                    <?php if ($isSub): ?>
                        <span class="badge badge-light-success font-weight-bolder font-size-h6 p-4">
                            <i class="flaticon2-tag text-success font-size-h4 mr-3"></i> Sous-Famille / Catégorie (Rattachée à une famille)
                        </span>
                    <?php else: ?>
                        <span class="badge badge-light-primary font-weight-bolder font-size-h6 p-4">
                            <i class="flaticon2-folder text-primary font-size-h4 mr-3"></i> Famille Principale (Catégorie racine)
                        </span>
                    <?php endif ?>
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
                            <label class="font-weight-bolder text-dark">Nom de la <?= $categorie ?> <span class="text-danger">*</span></label>
                            <?= $this->Form->control('title', [
                                'label' => false,
                                'class' => 'form-control form-control-solid form-control-lg',
                                'placeholder' => 'Nom'
                            ]); ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <?php if ($isSub): ?>
                            <div class="form-group mb-4">
                                <label class="font-weight-bolder text-dark">Famille Parente <span class="text-danger">*</span></label>
                                <?= $this->Form->control('category_id', [
                                    'label' => false,
                                    'options' => $categories,
                                    'empty' => 'Sélectionner une famille parente',
                                    'class' => 'form-control select2 form-control-solid',
                                    'id' => 'parent-category-select'
                                ]); ?>
                            </div>
                        <?php endif ?>
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
    <?php endif ?>
</div>

<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$(document).ready(function() {
    $.fn.select2.defaults.set("width", "100%");
    
    $('.select2').select2({
        placeholder: 'Sélectionner une catégorie',
        width: '100%'
    });

    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
});
<?= $this->Html->scriptEnd(); ?>