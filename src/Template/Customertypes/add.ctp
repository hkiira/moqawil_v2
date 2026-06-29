<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($customertype));
$this->assign('title', 'Ajouter un nouveau type du client');
$this->assign('subtitle', 'vous pouvez ajouter un nouveau type du client');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <?php
                echo $this->Form->control('title',['label'=>'Nom du type']);
                ?>
                <?= $this->element('statut')  ?>
            </div>
        </div>
    </div>
    <!--end::Form-->
</div>
