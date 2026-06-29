<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($pofstype));
$this->assign('title', 'Ajouter un nouveau type de point');
$this->assign('subtitle', 'vous pouvez un nouveau type de point');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <?php
                    echo $this->Form->control('code',['label'=>'Code']);
                    echo $this->Form->control('title',['label'=>'Nom du type']);
                ?>
                <?= $this->element('statut')  ?>
            </div>
        </div>
        <div class="col-xl-2"></div>
    </div>
</div>