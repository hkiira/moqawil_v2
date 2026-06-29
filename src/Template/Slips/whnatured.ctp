<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]);
?>
    <?= $this->Form->control('whnatured',['options' => $whnatured,'class'=>'select2 form-control','label'=>'Dépôt de réception']); ?>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js') ?>

<script type="text/javascript">
  	$('#whnatured').select2({
    	placeholder: 'Selectionnez un dépôt',
  	});
</script>
