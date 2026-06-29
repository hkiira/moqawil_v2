<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($pofsale,['id'=>'kt_form_1']));
?>
<?php if ($pofstype){
    $this->assign('title', 'Ajouter une nouvelle '.$pofstype->title);
} ?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <?php
                    echo $this->Form->control('title',['label'=>'Matricule']);
                    echo $this->Form->control('parentwarehouse_id', ['label'=>'Entrepôt principale','options' => $warehouses,'class'=>'select2','empty'=>true]);
                ?>
                <div class="matricule"></div>
                <?= $this->element('statut')  ?>
            </div>
        </div>
        <div class="col-xl-2"></div>
    </div>
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2({
        placeholder: 'Sélectionner un élément',
    });
      var searchkey = <?= $pofstype->id  ?>;
      searchTags( searchkey );
    function searchTags( keyword ){
      var data = keyword;
      $.ajax({
        method: 'get',
        url : "<?php echo $this->Url->build( ['action' => 'matricule'] ); ?>",
        data: {keyword:data},
        success: function( response )
        {       
          $( '.matricule' ).html(response);
        }
      });
    };
    
<?= $this->Html->scriptEnd(); ?>    