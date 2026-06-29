<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 

$this->assign('objet',$this->Form->create($slide,['type'=>'file']));
$this->assign('title', 'Ajouter un nouveau slide ');
$this->assign('subtitle', '');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <div class="row">
                    <div class="col-xl-2"></div>
                        <div class="col-xl-8">
                            <?= $this->Form->control('photo',['label'=>'Photo','type'=>'file' ]); ?>
                        </div>
                    <div class="col-xl-2"></div>
                </div>
                
            </div>
        </div>
    </div>
    <!--end::Form-->
</div>