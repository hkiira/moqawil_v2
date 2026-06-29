<?= $this->Form->control('produit', ['class'=>'produit','options'=>$products,'label' => false,'type' => 'select']) ?>
	<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js') ?>
	<?= $this->Html->scriptStart() ?>
$.fn.select2.defaults.set("width", "100%");
	$('.produit').select2({
	    placeholder: 'Selectionnez le produit',
	  });
	 $('document').ready(function(){
         $(".produit").change(function(){
            var searchkey = $(this).val();
            searchTags( searchkey );
         });
        function searchTags( keyword){
        var data = keyword;
          $('#example2 tr:last').after('<tr id="product'+data+'"></tr>');
        $.ajax({
            method: 'get',
            url : "<?php echo $this->Url->build( ['action' => 'selectedpack'] ); ?>",
            data: {keyword:data},
            success: function( response )
            {       
               $( '#product'+data  ).html(response);
            }
        });
      };
    });
	<?= $this->Html->scriptEnd(); ?>
