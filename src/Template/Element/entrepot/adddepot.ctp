<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($warehouse));
$this->assign('title', 'Ajouter un nouveau dépôt');
$this->assign('subtitle', 'vous pouvez ajouter un nouveau dépôt');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-4">
        	<div class="card card-custom gutter-b card-stretch">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 mr-4 symbol symbol-65 symbol-circle">
                            <?= $this->Html->image('/assets/media/e-commerce/warehouse.png') ?>
                        </div>
                        <div class="d-flex flex-column mr-auto">
                            <?= $this->Html->link(__($iswarehouse->code.' - '.$iswarehouse->title), ['controller'=>'Products','action' => 'view', $iswarehouse->id],['class'=>'card-title text-hover-primary font-weight-bolder font-size-h5 text-dark mb-1']) ?>
                            <span class="text-muted font-weight-bold">
                                <?=  $iswarehouse->adress->title.' - '.$iswarehouse->adress->city->title ?>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap mt-4">
                        <div class="col-md-12 d-flex flex-column mb-7">
                            <span class="font-weight-bolder mb-4">Date de création</span>
                            <span class="btn btn-light-primary btn-sm font-weight-bold btn-upper btn-text">
                                <?= $iswarehouse->created->nice('Europe/Paris', 'fr-FR') ?>
                            </span>
                        </div>
                    </div>
	                <h3 class="card-title font-weight-bolder text-dark">Dépôts affectés</h3>
	                 <?php foreach ($depots as $key => $depot): ?>
	                    <div class="d-flex align-items-center mt-6">
	            			<span class="bullet bullet-bar bg-success align-self-stretch"></span>
				            <label class="checkbox checkbox-lg checkbox-light-success checkbox-inline flex-shrink-0 m-0 mx-4">
				                <input type="checkbox" name="select" value="1">
				                <span></span>
				            </label>
				            <div class="d-flex flex-column flex-grow-1">
				                <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-lg mb-1">
				                    	<?php echo $depot->title ?>
				                </a>
				            </div>
	       				 </div>
	                <?php endforeach ?>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="my-5">
                <?php
                    echo $this->Form->control('title',['label'=>'Nom']);
                    echo $this->Form->control('whnature_id',['label'=>'Nature du dépôt','class'=>'select2','options'=>$whnatures]);
                ?>
                <?= $this->element('statut')  ?>
            </div>
        </div>
    </div>
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: 'Sélectionner un élément',
    });
    
<?= $this->Html->scriptEnd(); ?>    