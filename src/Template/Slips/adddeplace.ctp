<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($slip));
$this->assign('title', 'Ajouter un nouveau bon de déplacement');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-6">
			<?= $this->Form->control('whnature_id',['options' => $whnatures,'class'=>'select2 form-control','empty'=>true,'label'=>'Nature de déplacement']); ?>
        </div>
        <div class="col-xl-6">
			<div class="whnatured">
				<?= $this->Form->control('whnatured',['type'=>'select','options' => null,'class'=>'select2 form-control','label'=>'Nature de réception']); ?>
            </div>
		</div>
        <div class="col-xl-6">
            <?= $this->Form->control('raison',['label'=>'Raison']); ?>
        </div>
    </div>
    <div class="slips"></div>
</div>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2();
        $("#whnature-id").change(function(){
	        var searchkey = $(this).val();
            searchTags( searchkey ,searchkey,'warehoused/3','.whnatured');
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