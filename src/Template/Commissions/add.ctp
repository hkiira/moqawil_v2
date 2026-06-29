<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($commission,['id'=>'kt_form_1']));
$this->assign('title', 'Ajouter un ordre de paiement');
?>
<?php if ($user_id): ?>
    <?= $this->Form->control('user_id',['type'=>'hidden','label'=>false,'value'=>$user_id]); ?>
    <div class="orders"></div>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    searchTags( <?= $user_id  ?>,'/commissions/edit','.orders');
    
    function searchTags( keyword,url ,div){
        var data = keyword;
        $.ajax({
            method: 'get',
            url :url ,
            data: {keyword:data},
            success: function( response )
            {       
               $( div).html(response);
            }
        });
    };
<?= $this->Html->scriptEnd(); ?>
<?php else: ?>
    
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <div class="row">
                <label class="col-3">Vendeurs et conventionnels</label>
                <div class="col-9">
                    <?= $this->Form->control('user_id',['options' => $users,'class'=>'select2 form-control','label'=>false,'empty'=>true]); ?>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="orders"></div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $('.select2').select2();
    
    $("#user-id").change(function(){
        var searchkey = $(this).val();
        searchTags( searchkey,'edit','.orders');
    });

    function searchTags( keyword,url ,div){
        var data = keyword;
        $.ajax({
            method: 'get',
            url :url ,
            data: {keyword:data},
            success: function( response )
            {       
               $( div).html(response);
            }
        });
    };
<?= $this->Html->scriptEnd(); ?>
<?php endif ?>
