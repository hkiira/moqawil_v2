<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create(null, ['url' => ['action' => 'assign'], 'type' => 'post']));
$this->assign('title', 'Assigner une tranche aux prix');
$this->assign('subtitle', 'Liez une tranche aux prix selon des critères (produits, catégorie, type client)');
?>
<div class="card-body">
    <div class="alert alert-custom alert-light-info fade show mb-5" role="alert">
        <div class="alert-icon"><i class="flaticon-info"></i></div>
        <div class="alert-text">
            Liez une tranche aux prix correspondant aux critères sélectionnés. Vous pouvez sélectionner uniquement des catégories ou des types de clients sans sélectionner de produits spécifiques. Si aucun critère n'est sélectionné, la tranche sera considérée comme globale.
        </div>
    </div>

    <div class="alert alert-custom alert-light-primary fade show mb-5" role="alert" id="current-assignment-alert" style="display:none;">
        <div class="alert-icon"><i class="flaticon-list"></i></div>
        <div class="alert-text">
            <strong>Données actuellement liées à cette tranche:</strong>
            <div id="current-assignment-details"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <?php
            echo $this->Form->control('tranche_id', [
                'label' => 'Tranche',
                'type' => 'select',
                'options' => $trancheOptions,
                'empty' => 'Sélectionner une tranche',
                'required' => true,
                'class' => 'select2',
            ]);
            echo $this->Form->control('pack_ids', [
                'label' => 'Produits (optionnel)',
                'type' => 'select',
                'multiple' => 'multiple',
                'options' => $packOptions,
                'empty' => false,
                'class' => 'select2',
            ]);
            echo $this->Form->control('category_ids', [
                'label' => 'Catégories',
                'type' => 'select',
                'multiple' => 'multiple',
                'options' => $categoryOptions,
                'empty' => false,
                'class' => 'select2',
            ]);
            ?>
        </div>
        <div class="col-xl-6">
            <?php
            echo $this->Form->control('customertype_id', [
                'label' => 'Type de client (optionnel)',
                'type' => 'select',
                'options' => $customertypeOptions,
                'empty' => 'Tous les types de clients',
                'class' => 'select2',
            ]);
            ?>
            <div class="alert alert-custom alert-light-warning fade show mt-5" role="alert">
                <div class="alert-icon"><i class="flaticon-warning"></i></div>
                <div class="alert-text">
                    <strong>Important:</strong> Une tranche sans prix liés est considérée comme globale et s'applique à tous les produits.
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$.fn.select2.defaults.set("width", "100%");
$('.select2').select2({
    placeholder: 'Sélectionner une option',
});

// Load existing tranche data when tranche is selected
$('#tranche-id').on('change', function() {
    var trancheId = $(this).val();
    if (!trancheId) {
        $('#current-assignment-alert').hide();
        $('#pack-ids').val(null).trigger('change');
        $('#category-ids').val(null).trigger('change');
        $('#customertype-id').val('').trigger('change');
        return;
    }

    $.ajax({
        url: '<?= $this->Url->build(['action' => 'gettranchedata']) ?>',
        type: 'GET',
        data: { tranche_id: trancheId },
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                console.error(data.error);
                return;
            }

            // Show current assignment details
            var details = [];
            if (data.pack_ids && data.pack_ids.length > 0) {
                details.push('<strong>Produits:</strong> ' + data.pack_ids.length + ' produit(s) lié(s)');
                $('#pack-ids').val(data.pack_ids).trigger('change');
            } else {
                $('#pack-ids').val(null).trigger('change');
            }

            if (data.category_ids && data.category_ids.length > 0) {
                details.push('<strong>Catégories:</strong> ' + data.category_ids.length + ' catégorie(s) liée(s)');
                $('#category-ids').val(data.category_ids).trigger('change');
            } else {
                $('#category-ids').val(null).trigger('change');
            }

            if (data.customertype_ids && data.customertype_ids.length > 0) {
                details.push('<strong>Types de clients:</strong> ' + data.customertype_ids.length + ' type(s) détecté(s)');
                // If only one customer type, auto-select it
                if (data.customertype_ids.length === 1) {
                    $('#customertype-id').val(data.customertype_ids[0]).trigger('change');
                }
            } else {
                $('#customertype-id').val('').trigger('change');
            }

            if (details.length > 0) {
                $('#current-assignment-details').html(details.join('<br>'));
                $('#current-assignment-alert').show();
            } else {
                $('#current-assignment-details').html('<em>Aucune donnée liée (tranche globale)</em>');
                $('#current-assignment-alert').show();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading tranche data:', error);
        }
    });
});
<?= $this->Html->scriptEnd(); ?>
