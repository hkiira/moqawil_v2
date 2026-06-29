<?php   
	$this->extend('/Common/crud');
	$this->loadHelper('Form', [ 'templates' => 'app_form']); 
	$this->assign('objet',$this->Form->create(null,['type'=>'file']));
	$this->assign('title', 'Mise à jour des commandes');
?>
<div class="card-body">
    <?= $this->Form->control('file',['class'=>'form-control','type'=>'file','label'=>'Importer le fichier']); ?>
</div>