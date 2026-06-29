<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($tranch));
$this->assign('title', 'Ajouter une nouvelle tranche');
$this->assign('subtitle', 'Définissez une tranche de prix avec remise basée sur la quantité commandée');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-6">
            <?php
            echo $this->Form->control('title',['label'=>'Nom de la tranche','placeholder'=>'Ex: Tranche 10-19 kg']);
            echo $this->Form->control('apply_type', [
                'type' => 'select',
                'options' => ['QUANTITY' => 'Basée sur la quantité', 'AMOUNT' => 'Basée sur le montant'],
                'label' => 'Type d\'application de la tranche',
                'value' => 'QUANTITY',
                'class' => 'form-control select2',
                'id' => 'apply-type-select'
            ]);
            ?>
            <div id="quantity-unit-type-container">
            <?php
            echo $this->Form->control('quantity_unit_type', [
                'type' => 'select',
                'options' => [
                    'UNITS' => 'Par unités (pièces individuelles)',
                    'PACKAGE' => 'Par colis/paquet',
                    'MEASUREMENT' => 'Par poids/volume (KG, L)'
                ],
                'label' => 'Unité de comptage',
                'value' => 'UNITS',
                'class' => 'form-control select2',
                'id' => 'quantity-unit-type-select'
            ]);
            ?>
            </div>
            <?php
            echo $this->Form->control('min',['label'=>'Quantité/Montant minimal','type'=>'number','min'=>0,'placeholder'=>'Ex: 10 (quantité) ou 1000 (DH)']);
            echo $this->Form->control('max',['label'=>'Quantité/Montant maximal (optionnel)','type'=>'number','min'=>0,'placeholder'=>'Ex: 19, laisser vide pour sans limite']);
            ?>
        </div>
        <div class="col-xl-6">
            <?php
            echo $this->Form->control('remisetype_id', ['options' => $remisetypes,'label'=>'Type de remise','class'=>'select2','required'=>true]);
            ?>
            <div class="remisetype"></div>
            <div class="alert alert-info mt-3" role="alert">
                <strong>Types de remise disponibles:</strong>
                <ul>
                    <li><strong>% (Pourcentage):</strong> Applique un pourcentage de réduction (Ex: 5% de remise)</li>
                    <li><strong>RED (Remise fixe):</strong> Applique une somme fixe de remise en DH (Ex: 10 DH de remise)</li>
                    <li><strong>GRT (Cadeaux):</strong> Ajoute gratuitement un article cadeau à la commande</li>
                </ul>
            </div>
            <div class="alert alert-warning mt-3" role="alert">
                <strong>Type d'application:</strong>
                <ul>
                    <li><strong>Basée sur la quantité:</strong> La tranche s'applique selon la quantité commandée
                        <ul class="ml-3 mt-2">
                            <li><strong>Par unités:</strong> Compte les pièces individuelles (Ex: 100 articles)</li>
                            <li><strong>Par colis/paquet:</strong> Compte les paquets complets (Ex: 10 colis de 12 unités chacun)</li>
                            <li><strong>Par poids/volume:</strong> Compte en KG ou litres (Ex: 50 KG de produit)</li>
                        </ul>
                    </li>
                    <li class="mt-2"><strong>Basée sur le montant:</strong> La tranche s'applique si le montant total de la commande atteint les limites définies (en DH)</li>
                </ul>
            </div>
            <?= $this->element('statut')  ?>
        </div>

    </div>
    <!--end::Form-->
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
    placeholder: 'Sélectionner le type de remise',
});

// Toggle quantity unit type visibility based on apply_type
function toggleQuantityUnitType() {
    var applyType = $('#apply-type-select').val();
    if (applyType === 'QUANTITY') {
        $('#quantity-unit-type-container').show();
    } else {
        $('#quantity-unit-type-container').hide();
    }
}

// Initialize on page load
toggleQuantityUnitType();

// Handle apply_type change
$('#apply-type-select').change(function() {
    toggleQuantityUnitType();
});

$("#remisetype-id").change(function(){
      var searchkey = $(this).val();
      searchTags( searchkey );
    });
    function searchTags( keyword ){
      var data = keyword;
      $.ajax({
        method: 'get',
        url : "<?php echo $this->Url->build( ['action' => 'remisetype'] ); ?>",
        data: {keyword:data},
        success: function( response )
        {       
          $( '.remisetype' ).html(response);
        }
      });
    };
<?= $this->Html->scriptEnd(); ?>
