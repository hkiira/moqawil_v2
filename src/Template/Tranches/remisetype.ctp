<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]);
?>

<?php if ($inputType === 'pack'): ?>
    <?php echo $this->Form->control('pack_id', ['options' => $packs, 'label' => 'Sélectionner l\'article cadeau', 'class' => 'select2']); ?>
    <?php echo $this->Form->control('remise', ['label' => 'Quantité de packs cadeaux', 'type' => 'number', 'min' => 1]); ?>
<?php elseif ($inputType === 'percent'): ?>
    <?php echo $this->Form->control('remise', ['label' => 'Pourcentage de remise (%)', 'type' => 'number', 'min' => 0, 'max' => 100, 'step' => 0.01, 'placeholder' => 'Ex: 5 pour 5%']); ?>
<?php elseif ($inputType === 'fixed'): ?>
    <?php echo $this->Form->control('remise', ['label' => 'Montant de remise (DH)', 'type' => 'number', 'min' => 0, 'step' => 0.01, 'placeholder' => 'Ex: 10 pour 10 DH']); ?>
<?php else: ?>
    <?php echo $this->Form->control('remise', ['label' => 'Remise', 'type' => 'number']); ?>
<?php endif ?>

<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js') ?>
<?= $this->Html->scriptStart() ?>
$.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
    placeholder: 'Sélectionner l\'article',
});
<?= $this->Html->scriptEnd(); ?>
