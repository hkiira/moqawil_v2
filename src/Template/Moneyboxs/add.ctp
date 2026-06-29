<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 

$this->assign('objet',$this->Form->create($moneybox));
$this->assign('title', 'ajouter des montant la caisse du rapport : ');
$this->assign('subtitle', '');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <div class="form-group row">
                    <label class="col-3">Total à encaisser</label>
                    <div class="col-9" id="totalorders">
                        <?= $this->Form->control('total',['type'=>'hidden','value'=>($total)]); ?>
                        <?= number_format($total, 2, '.', ' ')  ?> Dh
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-3">Encaisser</label>
                    <div class="col-9" id="price">
                        <?= $this->Form->control('encaisser',['type'=>'hidden','value'=>($encaisser)]); ?>
                        <?= number_format($encaisser, 2, '.', ' ')  ?> Dh
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-3">Crédit</label>
                    <div class="col-9" id="totalslips">
                        <?= number_format(($total-$encaisser), 2, '.', ' ')  ?> Dh
                    </div>
                </div>
                <?= $this->Form->control('received',['label'=>'Montant à encaissé','value'=>(number_format(($total-$encaisser), 2, '.', ''))]); ?>
            </div>
        </div>
    </div>
</div>
<style type="text/css">
    div#totalorders {
        font-size: 30px;
        font-weight: bolder;
        color: #1bc5bd;
    }
    div#totalslips {
        font-size: 30px;
        font-weight: bolder;
        color: #f64e60;
    }
    div#price {
        font-size: 30px;
        font-weight: bolder;
        color: #1b6fc5;
    }
    input#received {
        font-size: 30px;
        font-weight: bolder;
    }
</style>