<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($slip,['id'=>'kt_form_1']));
$this->assign('title', 'Ajouter un nouveau bon de préparation');
$this->assign('subtitle', 'vous pouvez ajouter un bon de préparation');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                    <?= $this->Form->control('livreur',['class'=>'form-control','label'=>'Livreurs dépositaire']); ?>
                <div class="row">
                <label class="col-3">Livreurs</label>
                <div class="col-9">
                    <?= $this->Form->control('user_id',['options' => $users,'class'=>'select2 form-control','label'=>false,'empty'=>true]); ?>
                </div>
                </div>
                <div class="userzones"></div>
            </div>
        </div>
    </div>
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $('.select2').select2({
        placeholder: 'Selectionnez le livreur',
    }
    );
    $("#user-id").change(function(){
        var searchkey = $(this).val();
        searchTags( searchkey,'secteurs','userzones');
    });

    function searchTags( keyword,url ,div){
        var data = keyword;
        $.ajax({
            method: 'get',
            url :url ,
            data: {keyword:data},
            success: function( response )
            {       
               $( '.'+div).html(response);
            }
        });
    };

<?= $this->Html->scriptEnd(); ?>
