<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($slip));
$this->assign('title', 'Ajouter un nouveau bon de transfért');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
      				<?= $this->Form->control('warehouse_id',['options' => $warehouses,'class'=>'select2 form-control','label'=>'Entrepôt d\'envoi','empty'=>true]); ?>
      				<div class="warehoused">
      				    <?= $this->Form->control('whnature_id',['options' => null,'class'=>'select2 form-control','label'=>'Nature de transfért']); ?>
      					<?= $this->Form->control('warehoused',['type'=>'select','options' => null,'class'=>'select2 form-control','label'=>'Entrepôt de réception']); ?>
      				</div>
              <?= $this->Form->control('raison',['label'=>'Raison']); ?>
            </div>
        </div>
        <div class="col-xl-2"></div>
    </div>
    <div class="slips"></div>
</div>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2();

        $("#warehouse-id").change(function(){
          	var searchkey = $(this).val();
            searchTags( searchkey ,searchkey,'warehoused/4','.warehoused');
        });

        
    function searchTags( keyword ,keyword1 ,url,div){
        var data = keyword;
        var data1 = keyword1;
        $.ajax({
             method: 'get',
             url : url,
             data: {keyword:data,keyword1:data1},
             success: function( response )
             {       
                $(div).html(response);
             }
        });
    };
<?= $this->Html->scriptEnd(); ?>

