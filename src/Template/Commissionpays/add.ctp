<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($commissionpay));
$this->assign('title', 'Ajouter un nouveau ordre de paiement');
?>
<div class="card-body">
    
    <div class="form-group row">
     <label class="col-form-label col-lg-3 col-sm-12">Prévendeurs *</label>
     <div class="col-lg-9 col-md-9 col-sm-12">
        <div class=" checkbox-list">
            
                    <?php foreach ($users as $key => $user): ?>
                         <label class="checkbox checkbox-outline">
                                 <input type="checkbox" name="user_id<?= $user->id ?>" value=<?= $user->id ?> checked>
                                 <span></span>
                                 <?= $user->firstname ?> <?= $user->lastname ?> 
                            </label> 
                    <?php endforeach ?>
               
        </div>
    </div>
</div>
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: 'Sélectionner un vendeur',
    });
<?= $this->Html->scriptEnd(); ?>
