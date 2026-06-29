<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 

$this->assign('objet',$this->Form->create($brand,['type'=>'file']));
$this->assign('title', 'Modifier la marque : '.$brand->title);
$this->assign('subtitle', '');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <?php if ($image=="image"): ?>
                    <div class="row">
                        <div class="col-xl-2"></div>
                            <div class="col-xl-8">
                                <?= $this->Form->control('photo.photo',['label'=>'Photo','type'=>'file' ]); ?>
                            </div>
                        <div class="col-xl-2"></div>
                    </div>
                <?php else: ?>
                    <?= $this->Form->control('title',['label'=>'Nom de la marque']); ?>
                    <?= $this->element('statut')  ?>
                <?php endif ?>
            </div>
        </div>
    </div>
    <!--end::Form-->
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
  FormValidation.formValidation(
        document.getElementById('kt_form_1'),
        {
            fields: {
                'title': {
                    validators: {
                        notEmpty: {
                            message: 'Le nom de la marque est obligatoire'
                        },
                        stringLength: {
                            min:4,
                            message: 'le nom de la marque doit contenir plus de 4 caractére '
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