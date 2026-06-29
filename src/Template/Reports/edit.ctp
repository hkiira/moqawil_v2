<div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <div class="row">
                <label class="col-3">Livreurs</label>
                <div class="col-9">
                    <?= $this->Form->control('sellerid',['options' => $vendeurs,'class'=>'select2 form-control','label'=>false,'empty'=>false]); ?>
                </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2"></div>
    </div>
<?= $this->Html->scriptStart() ?>
$('#sellerid').select2();
<?= $this->Html->scriptEnd() ?>
