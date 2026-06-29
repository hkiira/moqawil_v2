<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($pack,['type'=>'file','id'=>'kt_form_1_edit']));
$this->assign('title', 'Modifier l\'article : '.$pack->title);
$this->assign('subtitle', '');
?>
<div class="card-body">
    <?php if($amodifier==1){ ?>
        <div class="row">
            <div class="col-md-12 mb-5">
                <div class=" border rounded col-md-12 p-5">
                    <h5 class="text-primary">Information du pack</h5>
                    <div class="separator mb-6 separator-solid separator-border-2 separator-primary"></div>
                    <div class="row form-group">
                        <div class="col-xl-6">
                            <?= $this->Form->control('title', ['label' => 'Nom de l\'article', 'class' => 'form-control']); ?>
                            <div class="form-group row mb-0">
                                <label class="col-3">Vendeurs</label>
                                <div class="col-xl-9">
                                    <?php 
                                        $data=[];
                                        if($pack->categoryuserpacks){
                                            foreach($pack->categoryuserpacks as $key=>$categoryuser){
                                                $data[]=$categoryuser->categoryuser_id;
                                            } 
                                        } 
                                    ?>
                                    <?= $this->Form->control('categoryuserpack.categoryuser_id', ['label'=>false,'options' => $categoryusers,'class'=>'select2 form-control','multiple'=>'multiple','value'=>$data]); ?>
                                </div>
                            </div>
                            <?= $this->Form->control('brand_id', ['type' => 'hidden', 'value' => $pack->brand_id]); ?>
                            <?= $this->Form->control('barecode', ['type' => 'hidden', 'label' => 'Code à barre', 'class' => 'form-control']); ?>
                            <?= $this->Form->control('category_id', ['label' => 'Catégorie', 'options' => $categories, 'class' => 'form-control select2', 'empty' => 'Sélectionner Catégorie']); ?>
                            <?= $this->Form->control('saletype_id', ['label' => 'Type de vente', 'options' => $saletypes, 'class' => 'form-control select2', 'empty' => 'Sélectionner un type de vente']); ?>
                
                            <?php
                            $stockOptions = [0 => 'Non', 1 => 'Oui'];
                            echo $this->Form->control('gstock', ['label' => 'Gestion du stock', 'options' => $stockOptions, 'class' => 'form-control select2']);
                            ?> </div>
                        <div class="col-xl-6">
                            <?= $this->Form->control('photo.photo', ['label' => 'Photo', 'type' => 'file', 'class' => 'form-control-file']); ?>
                            <?= $this->Form->control('commission', ['class' => 'form-control', 'label' => 'Commission', 'type' => 'hidden', 'required' => 'required']); ?>
                            <?= $this->Form->control('packtype_id', ['type' => 'hidden', 'value' => $pack->packtype_id]); ?>
                            <?= $this->Form->control('buyingprice', ['class' => 'form-control', 'label' => 'Prix d\'achat global du Pack', 'type' => 'hidden', 'step' => 'any', 'value' => $pack->buyingprice]); ?>
                            <?= $this->Form->control('packtax_id', ['label' => 'TVA', 'options' => $packtaxes, 'empty' => 'Sélectionner TVA', 'class' => 'form-control select2']); ?>
                            <?= $this->Form->control('loyaltypoints', ['class' => 'form-control', 'label' => 'Points de fidélité', 'type' => 'number', 'step' => 'any', 'required' => 'required']); ?>
                            <?php
                            $statutOptions = [0 => 'Inactif', 1 => 'Actif'];
                            echo $this->Form->control('statut', [
                                'label' => 'Statut',
                                'options' => $statutOptions,
                                'class' => 'form-control select2'
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
        <div class="col-md-12 mb-5">
            <div class="p-5 border rounded">
                <h5 class="text-primary">Unité de Mesure</h5>
                <div class="separator mb-6 separator-solid separator-border-2 separator-primary"></div>
                <div class="row">
                    <div class="col-md-6">
                        <?php
                        echo $this->Form->control('measurement_quantity', [
                            'label' => false,
                            'type' => 'number',
                            'class' => 'form-control',
                            'step' => '0.01',
                            'min' => '0.01',
                            'placeholder' => 'Quantité'
                        ]);
                        ?>
                    </div>
                    <div class="col-md-6">
                        <?php
                        echo $this->Form->control('measurement_unit_id', [
                            'label' => false,
                            'options' => $measurementUnits,
                            'class' => 'form-control select2',
                            'empty' => 'Sélectionner une unité de mesure',
                            'placeholder' => 'Unité de mesure'
                        ]);
                        ?>
                    </div>
                </div>
                <small class="form-text text-muted">Exemple: 1.5 Litre (L), 2 Kilogramme (kg), etc.</small>
            </div>
        </div>
    </div>
   
    <?php } elseif($amodifier==2) { ?>
        <div class="product"></div>
    <?php } elseif($amodifier==3) { ?>
        <div class="row">
            <div class="col-xl-2"></div>
            <div class="col-xl-8">
                <?= $this->Form->control('photo.photo',['label'=>'Photo (laisser vide pour ne pas changer)','type'=>'file', 'class' => 'form-control-file' ]); ?>
            </div>
            <div class="col-xl-2"></div>
        </div>
    <?php } else { ?>
        <p>Section de modification non reconnue.</p>
    <?php } ?>
</div>

<?= $this->Html->script(['/assets/js/pages/crud/forms/widgets/select2.js', '/assets/plugins/custom/formrepeater/formrepeater.bundle.js'], ['block' => 'script_bottom']) ?>

<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$(document).ready(function() {
    $.fn.select2.defaults.set("width", "100%");
    $('.select2:not(.select2-repeater)').each(function() {
        $(this).select2({
            placeholder: $(this).find('option[value=""]').text() || 'Sélectionner une option',
        });
    });

    <?php if($amodifier==1): ?>
    var $repeaterEdit = $('#packproducts_repeater_edit').repeater({
        // For existing items, they are already rendered by PHP.
        // The template item is the one with __INDEX__ and style="display:none;"
        initEmpty: false, // Does not create an empty item on init. Existing items are from PHP.
        defaultValues: { 
            'packproducts[__INDEX__][quantity]': 1,
            'packproducts[__INDEX__][statut]': '1'
        },
        show: function () {
            $(this).slideDown();
            var item = $(this);
            // Find the highest existing index to continue numbering
            var highestIndex = -1;
            item.closest('[data-repeater-list]').find('[data-repeater-item]').each(function() {
                $(this).find('[name^="packproducts["]').each(function(){
                    var name = $(this).attr('name');
                    var match = name.match(/packproducts\[(\d+)\]/);
                    if (match && parseInt(match[1]) > highestIndex) {
                        highestIndex = parseInt(match[1]);
                    }
                });
            });
            var index = highestIndex + 1;

            item.find('[name*="__INDEX__"]').each(function() {
                var currentName = $(this).attr('name');
                var newName = currentName.replace('__INDEX__', index);
                $(this).attr('name', newName);
                var newId = newName.replace(/\[/g, '-').replace(/\]/g, '');
                $(this).attr('id', newId);
            });
            
            item.find('.select2-repeater').select2({
                placeholder: 'Sélectionner un produit',
                width: '100%'
            });
        },
        hide: function (deleteElement) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet élément du pack?')) {
                $(this).slideUp(deleteElement, function() { $(this).remove(); });
            }
        },
        // No custom renumbering function needed here if repeater handles indices correctly for new items
        // and existing items are already indexed by PHP.
    });
    // Initialize select2 for already existing items rendered by PHP
    $('#packproducts_repeater_edit [data-repeater-item]').each(function() {
        if ($(this).css('display') !== 'none') { // Exclude the template item
            $(this).find('.select2-repeater').select2({
                placeholder: 'Sélectionner un produit',
                width: '100%'
            });
        }
    });
    <?php endif; ?>

    <?php if($amodifier==2): ?>
    // Original JS for price editing part
    if (typeof searchTags === 'function') { // Check if function exists
        searchTags( 1 );
    }
    $("#packtype-id").change(function(){ // Ensure #packtype-id exists for this case
      var searchkey = $(this).val();
      if (typeof searchTags === 'function') searchTags( searchkey );
    });
    function searchTags( keyword ){ 
      var data = keyword;
      $.ajax({
        method: 'get',
        url : "<?= $this->Url->build( [ 'controller' => 'Packs', 'action' => 'product', $pack->id] ); ?>",
        data: {keyword:data},
        success: function( response ) {       
          $( '.product' ).html(response);
        }
      });
    };
    <?php endif; ?>
});
<?= $this->Html->scriptEnd(); ?>
