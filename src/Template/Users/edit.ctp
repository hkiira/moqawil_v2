<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($user,['id'=>'kt_form_1']));
$this->assign('title', 'Modifer le '.$role->title.' : '.$user->firstname.' '.$user->lastname );
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-6">
            <?=  $this->Form->control('firstname',['label'=>'Nom']); ?>
            <?php if($user->role_id==5 || $user->role_id==6 || $user->role_id==3){ ?>
            
                <div class="form-group row fv-plugins-icon-container has-success">
                    <label class="col-3">Secteurs</label>
                    <div class="col-xl-9">
                        <?php 
                            $data=[];
                            if($user->zoneusers){
                                foreach($user->zoneusers as $key=>$zoneuser){
                                    $data[]=$zoneuser->zone_id;
                                } 
                            } 
                        ?>

                        <?= $this->Form->control('zoneusers.zone_id', ['label'=>false,'options' => $zones,'class'=>'select2 form-control','multiple'=>'multiple','value'=>$data]); ?>
                    </div>
                </div>
               
            <?php }?>
            <div class="form-group row fv-plugins-icon-container has-success">
                <label class="col-3">Secteurs</label>
                <div class="col-xl-9">
                    <?php 
                        $data=[];
                        if($user->whusers){
                            foreach($user->whusers as $key=>$whuser){
                                $data[]=$whuser->warehouse_id;
                            } 
                        } 
                    ?>

                    <?= $this->Form->control('whusers.warehouse_id', ['label'=>false,'options' => $warehouses,'class'=>'select2 form-control','multiple'=>'multiple','value'=>$data]); ?>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <?= $this->Form->control('lastname',['label'=>'Prénom']); ?>
                <?= $this->element('statut')  ?>
            <?php if($user->role_id==5 || $user->role_id==3): ?>
            <?= $this->Form->control('grpassword',['label'=>'Mot de passe des clients']); ?>
            <?= $this->Form->control('categoryuser_id', ['label'=>'Catégorie','options' => $categoryusers,'class'=>'select2 form-control']); ?>
            <?php endif ?>
        </div>
    </div>
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: 'Sélectionner une categorie',
    });
    $("input#username").on({
  keydown: function(e) {
    if (e.which === 32)
      return false;
  },
  change: function() {
    this.value = this.value.replace(/\s/g, "");
  }
});

    FormValidation.formValidation(
    	document.getElementById('kt_form_1'),
    	{
    		fields: {
    			'firstname': {
    				validators: {
    					notEmpty: {
    						message: 'Le nom est obligatoire'
    					},
    					stringLength: {
                            min:4,
                            max:15,
                            message: 'Veuillez entrer un nom dans la plage de longueur de texte 4 et 15 '
                        }
    				}
    			},
    			'lastname': {
    				validators: {
    					notEmpty: {
    						message: 'Le prénom est obligatoire'
    					},
    					stringLength: {
                            min:4,
                            max:15,
                            message: 'Veuillez entrer un prénom dans la plage de longueur de texte 4 et 15 '
                        }
    				}
    			},
    			'username': {
    				validators: {
    					notEmpty: {
    						message: 'Le nom d\'utilsateur est obligatoire'
    					},
    					stringLength: {
                            min:4,
                            max:15,
                            message: 'Veuillez entrer un nom d\'utilsateur dans la plage de longueur de texte 4 et 15 '
                        }
    				}
    			},
    			'password': {
    				validators: {
    					notEmpty: {
    						message: 'Le mot de passe est obligatoire'
    					},
    					stringLength: {
                            min:8,
                            max:20,
                            message: 'le mot de passe doit contenir plus de 8 caractére '
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