<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($slip));
$this->assign('title', 'Ajouter un nouveau bon de décharge');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-12">
          <?= $this->Form->control('warehoused',['type'=>'select','options' => $warehoused,'class'=>'select2 form-control','label'=>'Entrepôt d\'envoi','empty'=>true]); ?>
        </div>
        <div class="col-xl-12">
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
        searchTags( searchkey ,searchkey,searchkey,'warehoused/2','.warehoused');
    });
    $('#warehoused').select2({
            placeholder: 'Selectionnez un entrepôt',
          });
          $('.whnature').select2({
            placeholder: 'Selectionnez la nature',
          });
          $("#warehoused").change(function(){
              var searchkey = $(this).val();
              var searchkey1 = $("#whnature-id").val();
              searchTags( searchkey,searchkey1,searchkey1,'dechargestock','.slips');
          });
    function searchTags( keyword ,keyword1 ,keyword2 ,url,div){
        var data = keyword;
        var data1 = keyword1;
        var data2 = keyword2;
        $.ajax({
	         method: 'get',
	         url : url,
	         data: {keyword:data,keyword1:data1,keyword2:data2},
	         success: function( response )
	         {       
	            $(div).html(response);
	         }
        });
    };
<?= $this->Html->scriptEnd(); ?>