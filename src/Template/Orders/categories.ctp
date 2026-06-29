<?= $this->Form->control('category', ['class'=>'category','type' => 'select','empty'=>true,'options'=>$categories,'label' => false]) ?>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js') ?>
<?= $this->Html->scriptStart() ?>
  $('.category').select2({
    placeholder: 'Selectionnez une catégories',
  });
  
  $('.produit').select2({
    placeholder: 'Selectionnez un produit',
  });

  $('document').ready(function(){
    $(".category").change(function(){
      var category = $(this).val();
      var pofsale = $('#pofsale').val();
      searchTags( category,pofsale );
    });
    function searchTags( category,pofsale ){
      var category = category;
      var pofsale = pofsale;
      $.ajax({
        method: 'get',
        url : "<?php echo $this->Url->build( [ 'controller' => 'Orders', 'action' => 'products' ] ); ?>",
        data: {category:category,pofsale:pofsale},
        success: function( response )
        {       
           $( '.products').html(response);
        }
      });
    };
  });  
<?= $this->Html->scriptEnd(); ?>
