<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$categorie ="famille";
if ($id==2) {
    $categorie ="catégorie";
}
$this->assign('objet',$this->Form->create($category,['id'=>'kt_form_1']));
$this->assign('title', 'Ajouter une nouvelle '.$categorie);
$this->assign('subtitle', 'vous pouvez ajouter une nouvelle '.$categorie);

?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <?php
                    echo $this->Form->control('title',['label'=>'Nom de la '.$categorie]);
                    if ($id==2) {
                        echo $this->Form->control('category_id', ['label'=>'Categorie parente','options' => $categories, 'class'=>'select2' ]);
                    }
                ?>
                <?= $this->element('statut')  ?>
            </div>
        </div>
    </div>
    <!--end::Form-->
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: 'Sélectionner une categorie',
    });
    FormValidation.formValidation(
    	document.getElementById('kt_form_1'),
    	{
    		fields: {
    			'title': {
    				validators: {
    					notEmpty: {
    						message: 'Le nom de la catégorie est obligatoire'
    					},
    					stringLength: {
                            min:4,
                            message: 'le nom de la catégorie doit contenir plus de 4 caractére '
                        }
    				}
    			},
    			
    		},
    
    		plugins: {
    		    trigger: new FormValidation.plugins.Trigger(),
			    bootstrap: new FormValidation.plugins.Bootstrap(),
			    submitButton: new FormValidation.plugins.SubmitButton(),
			    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
    		}
    	}
    );
<?= $this->Html->scriptEnd(); ?>