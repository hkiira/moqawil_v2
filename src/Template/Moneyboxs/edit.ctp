<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 

$this->assign('objet',$this->Form->create($moneybox));
$this->assign('title', 'Modifier la caisse du rapport : '.$moneybox->report->code);
$this->assign('subtitle', '');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <div class="form-group row">
                    <label class="col-3">Total à encaisser</label>
                    <div class="col-9">
                        <h2><?= $moneybox->total  ?> Dh</h2>
                    </div>
                </div>
                <?= $this->Form->control('received',['label'=>'Montant reçu']); ?>
                <?= $this->Form->control('report.charges.0.valeur',['label'=>'Charges ']); ?>
                <?= $this->Form->control('report.charges.0.motif',['label'=>'Motif']); ?>
            </div>
        </div>
    </div>
</div>