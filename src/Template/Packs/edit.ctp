<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($pack,['type'=>'file','id'=>'kt_form_1_edit']));
$this->assign('title', 'Modifier l\'article : '.$pack->title);
$this->assign('subtitle', 'Mettez à jour les informations du pack, sa tarification et ses unités.');
?>

<div class="card-body p-6">
    <?php if($amodifier == 1): ?>
        <!-- Section 1: Information Générale du Pack -->
        <div class="card card-custom card-border mb-6">
            <div class="card-header bg-light-primary border-0 min-h-50px px-5">
                <div class="card-title">
                    <span class="card-icon">
                        <i class="flaticon2-box-1 text-primary font-size-h5"></i>
                    </span>
                    <h5 class="card-label text-primary font-weight-bolder font-size-h6 mb-0">1. Information Générale du Pack</h5>
                </div>
            </div>
            <div class="card-body p-6">
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <div class="form-group mb-0">
                            <label class="font-weight-bolder text-dark">Nom de l'article <span class="text-danger">*</span></label>
                            <?= $this->Form->control('title', [
                                'label' => false,
                                'class' => 'form-control form-control-solid form-control-lg',
                                'placeholder' => 'Nom de l\'article'
                            ]); ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label class="font-weight-bolder text-dark">Catégories Vendeurs</label>
                            <?php 
                                $data = [];
                                if($pack->categoryuserpacks){
                                    foreach($pack->categoryuserpacks as $key => $categoryuser){
                                        $data[] = $categoryuser->categoryuser_id;
                                    } 
                                } 
                            ?>
                            <?= $this->Form->control('categoryuserpack.categoryuser_id', [
                                'label' => false,
                                'options' => $categoryusers,
                                'class' => 'select2 form-control form-control-solid',
                                'multiple' => 'multiple',
                                'value' => $data
                            ]); ?>
                        </div>

                        <?= $this->Form->control('brand_id', ['type' => 'hidden', 'value' => $pack->brand_id]); ?>
                        <?= $this->Form->control('barecode', ['type' => 'hidden']); ?>

                        <div class="form-group mb-4">
                            <label class="font-weight-bolder text-dark">Catégorie</label>
                            <?= $this->Form->control('category_id', [
                                'label' => false,
                                'options' => $categories,
                                'class' => 'form-control select2 form-control-solid',
                                'empty' => 'Sélectionner Catégorie'
                            ]); ?>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bolder text-dark">Type de Vente</label>
                            <?= $this->Form->control('saletype_id', [
                                'label' => false,
                                'options' => $saletypes,
                                'class' => 'form-control select2 form-control-solid',
                                'empty' => 'Sélectionner un type de vente'
                            ]); ?>
                        </div>

                        <div class="form-group mb-4 mb-md-0">
                            <label class="font-weight-bolder text-dark">Gestion du Stock</label>
                            <?php
                            $stockOptions = [0 => 'Non', 1 => 'Oui'];
                            echo $this->Form->control('gstock', [
                                'label' => false,
                                'options' => $stockOptions,
                                'class' => 'form-control select2 form-control-solid'
                            ]);
                            ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label class="font-weight-bolder text-dark">Photo du Produit</label>
                            <div class="custom-file">
                                <?= $this->Form->control('photo.photo', [
                                    'label' => false,
                                    'type' => 'file',
                                    'class' => 'custom-file-input',
                                    'id' => 'customFile'
                                ]); ?>
                                <label class="custom-file-label" for="customFile">Modifier la photo...</label>
                            </div>
                        </div>

                        <?= $this->Form->control('commission', ['type' => 'hidden']); ?>
                        <?= $this->Form->control('packtype_id', ['type' => 'hidden', 'value' => $pack->packtype_id]); ?>
                        <?= $this->Form->control('buyingprice', ['type' => 'hidden', 'value' => $pack->buyingprice]); ?>

                        <div class="form-group mb-4">
                            <label class="font-weight-bolder text-dark">Taux de TVA</label>
                            <?= $this->Form->control('packtax_id', [
                                'label' => false,
                                'options' => $packtaxes,
                                'empty' => 'Sélectionner TVA',
                                'class' => 'form-control select2 form-control-solid'
                            ]); ?>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bolder text-dark">Points de Fidélité <span class="text-danger">*</span></label>
                            <?= $this->Form->control('loyaltypoints', [
                                'label' => false,
                                'class' => 'form-control form-control-solid',
                                'type' => 'number',
                                'step' => 'any',
                                'required' => 'required'
                            ]); ?>
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bolder text-dark">Statut</label>
                            <?php
                            $statutOptions = [0 => 'Inactif', 1 => 'Actif'];
                            echo $this->Form->control('statut', [
                                'label' => false,
                                'options' => $statutOptions,
                                'class' => 'form-control select2 form-control-solid'
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Unité de Mesure -->
        <div class="card card-custom card-border mb-6">
            <div class="card-header bg-light-warning border-0 min-h-50px px-5">
                <div class="card-title">
                    <span class="card-icon">
                        <i class="flaticon2-ruler text-warning font-size-h5"></i>
                    </span>
                    <h5 class="card-label text-warning font-weight-bolder font-size-h6 mb-0">2. Unité de Mesure</h5>
                </div>
            </div>
            <div class="card-body p-6">
                <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="font-weight-bolder text-dark">Quantité de Mesure</label>
                        <?= $this->Form->control('measurement_quantity', [
                            'label' => false,
                            'type' => 'number',
                            'class' => 'form-control form-control-solid',
                            'step' => '0.01',
                            'min' => '0.01',
                            'placeholder' => 'Quantité'
                        ]); ?>
                    </div>
                    <div class="col-md-6">
                        <label class="font-weight-bolder text-dark">Unité</label>
                        <?= $this->Form->control('measurement_unit_id', [
                            'label' => false,
                            'options' => $measurementUnits,
                            'class' => 'form-control select2 form-control-solid',
                            'empty' => 'Sélectionner une unité de mesure'
                        ]); ?>
                    </div>
                </div>
                <span class="form-text text-muted mt-3">Exemple de mesure : 1.5 Litre (L), 2 Kilogrammes (kg), etc.</span>
            </div>
        </div>

    <?php elseif($amodifier == 2): ?>
        <div class="product"></div>
    <?php elseif($amodifier == 3): ?>
        <div class="card card-custom card-border mb-6">
            <div class="card-header bg-light-primary border-0 min-h-50px px-5">
                <div class="card-title">
                    <span class="card-icon">
                        <i class="flaticon2-image-file text-primary font-size-h5"></i>
                    </span>
                    <h5 class="card-label text-primary font-weight-bolder font-size-h6 mb-0">Modifier la Photo du Pack</h5>
                </div>
            </div>
            <div class="card-body p-6">
                <div class="form-group mb-0">
                    <label class="font-weight-bolder text-dark mb-3">Nouvelle Photo (laisser vide pour conserver la photo actuelle)</label>
                    <div class="custom-file">
                        <?= $this->Form->control('photo.photo', [
                            'label' => false,
                            'type' => 'file',
                            'class' => 'custom-file-input',
                            'id' => 'customFileEdit'
                        ]); ?>
                        <label class="custom-file-label" for="customFileEdit">Choisir un fichier image...</label>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-custom alert-light-danger p-4 mb-0" role="alert">
            <div class="alert-text font-size-sm">Section de modification non reconnue.</div>
        </div>
    <?php endif ?>
</div>

<?= $this->Html->script(['/assets/js/pages/crud/forms/widgets/select2.js', '/assets/plugins/custom/formrepeater/formrepeater.bundle.js'], ['block' => 'script_bottom']) ?>

<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$(document).ready(function() {
    // Custom file input display name
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });

    $.fn.select2.defaults.set("width", "100%");
    $('.select2:not(.select2-repeater)').each(function() {
        $(this).select2({
            placeholder: $(this).find('option[value=""]').text() || 'Sélectionner une option',
            width: '100%'
        });
    });

    <?php if($amodifier==2): ?>
    if (typeof searchTags === 'function') {
        searchTags(1);
    }
    $("#packtype-id").change(function(){
      var searchkey = $(this).val();
      if (typeof searchTags === 'function') searchTags(searchkey);
    });
    function searchTags(keyword){ 
      var data = keyword;
      $.ajax({
        method: 'get',
        url : "<?= $this->Url->build(['controller' => 'Packs', 'action' => 'product', $pack->id]); ?>",
        data: {keyword:data},
        success: function(response) {       
          $('.product').html(response);
        }
      });
    };
    <?php endif; ?>
});
<?= $this->Html->scriptEnd(); ?>
