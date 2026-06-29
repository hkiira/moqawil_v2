<?php $this->extend('/Common/crud'); ?>
<?php $this->loadHelper('Form', [ 'templates' => 'app_form' ]); 
$this->assign('objet', $this->Form->create($measurementUnit, ['id' => 'kt_form_1']));
$this->assign('title', 'Ajouter une Unité de Mesure');
$this->assign('subtitle', 'Vous pouvez ajouter une nouvelle unité de mesure');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-6">
            <?= $this->Form->control('code', ['label' => 'Code', 'class' => 'form-control']); ?>
            <?= $this->Form->control('title', ['label' => 'Nom', 'class' => 'form-control']); ?>
            <?= $this->Form->control('abbreviation', ['label' => 'Abréviation', 'class' => 'form-control']); ?>
        </div>
        <div class="col-xl-6">
            <?php 
                $typeOptions = [
                    'volume' => 'Volume',
                    'weight' => 'Poids',
                    'length' => 'Longueur',
                    'area' => 'Surface',
                    'other' => 'Autre'
                ];
                echo $this->Form->control('type', [
                    'label' => 'Type',
                    'options' => $typeOptions,
                    'class' => 'form-control select2',
                    'empty' => 'Sélectionner un type'
                ]);
            ?>
            <?= $this->Form->control('conversion_factor', [
                'label' => 'Facteur de Conversion',
                'class' => 'form-control',
                'type' => 'number',
                'step' => 'any',
                'help' => 'Facteur de conversion vers l\'unité de base (ex: 0.001 pour mL vers L)'
            ]); ?>
            <?= $this->Form->control('base_unit', [
                'label' => 'Unité de Base',
                'class' => 'form-control',
                'help' => 'Unité de base pour ce type de mesure (ex: L pour volume, kg pour poids)'
            ]); ?>
            <?php 
                $statutOptions = [0 => 'Inactif', 1 => 'Actif'];
                echo $this->Form->control('statut', [
                    'label' => 'Statut',
                    'options' => $statutOptions,
                    'class' => 'form-control select2',
                    'default' => 1
                ]);
            ?>
        </div>
    </div>
</div>

<?= $this->Html->script(['/assets/js/pages/crud/forms/widgets/select2.js'], ['block' => 'script_bottom']) ?>

<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$(document).ready(function() {
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: $(this).find('option[value=""]').text() || 'Sélectionner une option',
    });
});
<?= $this->Html->scriptEnd(); ?> 