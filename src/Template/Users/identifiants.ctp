<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($user,['id'=>'kt_form_1']));
$this->assign('title', 'Modifer les identifiants du '.$user->role->title.' : '.$user->firstname.' '.$user->lastname );
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-6">
            <?php echo $this->Form->control('username',['label'=>'Identifiant']); ?>
            
        </div>
        <div class="col-xl-6">
            <div class="form-group row fv-plugins-icon-container has-success">
                <label class="col-3">Mot de passe</label>
                <div class="col-9 input-group" id="show_hide_password">
                     <input class="form-control is-valid" type="password" name="password" for="password" required="required" id="password" aria-describedby="basic-addon2" value="<?= $user->password ?>">
                     <div class="input-group-append">
                        <span class="input-group-text">
                            <a href="">
                                <i class="fa fa-eye-slash" aria-hidden="true">
                                </i>
                            </a>
                        </span>
                    </div>
                 </div>
            </div>
        </div>
    </div>
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$(document).ready(function() {
    $("#show_hide_password a").on('click', function(event) {
        event.preventDefault();
        if($('#show_hide_password input').attr("type") == "text"){
            $('#show_hide_password input').attr('type', 'password');
            $('#show_hide_password i').addClass( "fa-eye-slash" );
            $('#show_hide_password i').removeClass( "fa-eye" );
        }else if($('#show_hide_password input').attr("type") == "password"){
            $('#show_hide_password input').attr('type', 'text');
            $('#show_hide_password i').removeClass( "fa-eye-slash" );
            $('#show_hide_password i').addClass( "fa-eye" );
        }
    });
});
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