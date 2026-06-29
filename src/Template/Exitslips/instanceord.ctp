 <?php $this->loadHelper('Form', [

    'templates' => 'app_form',

]);

?>
                <div class="row">
                <label class="col-3">Secteurs</label>
                <div class="col-9">
                    <?= $this->Form->control('zoneusers',['options' => $zoneuserdatas,'class'=>'select2 form-control','label'=>false,'empty'=>true,'multiple'=>'multiple']); ?>
                </div>
                </div>
<?= $this->Html->scriptStart() ?>
$('.select2').select2({
        placeholder: 'Selectionnez le livreur',
    }
    );
<?= $this->Html->scriptEnd(); ?>
