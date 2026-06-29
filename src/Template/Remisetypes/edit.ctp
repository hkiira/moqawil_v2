<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($remisetype));
$this->assign('title', 'Modifier le type de remise '.$remisetype->title);
$this->assign('subtitle', 'vous pouvez modifier le type de remise');
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
    </div>
    <!--end::Form-->
</div>
