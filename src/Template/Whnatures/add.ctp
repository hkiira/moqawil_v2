<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($whnature));
$this->assign('title', 'Ajouter une nouvelle nature de l\'entrepôt');
$this->assign('subtitle', 'vous pouvez ajouter une nouvelle nature de l\'entrepôt');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <?= $this->Form->control('code',['label'=>'Code']); ?>
                <?= $this->Form->control('title',['label'=>'Nom de la nature']); ?>
                <?= $this->element('statut')  ?>
            </div>
        </div>
        <div class="col-xl-2"></div>
    </div>
</div>