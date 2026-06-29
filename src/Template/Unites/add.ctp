<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
	'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($unite));
$this->assign('title', 'Ajouter une nouvelle unité');
$this->assign('subtitle', 'vous pouvez ajouter une nouvelle unité de mesure');
?>
<div class="card-body">
	<div class="row">
		<div class="col-xl-2"></div>
		<div class="col-xl-8">
			<div class="my-5">
				<?= $this->Form->control('title',['label'=>'Nom']); ?>
				<?= $this->element('statut')  ?>
			</div>
		</div>
		<div class="col-xl-2"></div>
	</div>
</div>