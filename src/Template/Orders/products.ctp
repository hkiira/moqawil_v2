
<select class="form-control select2 produit" name="produit">

      <option></option>

  <?php foreach ($packselected as $key => $pack): ?>
	<?php foreach ($pack as $key1 => $packunite): ?>
   	 	<option value="<?= $packunite['id'].$key1 ?>" data-id="<?= $packunite['id'].$key1 ?>"><?= $packunite['title'] ?></option>

  <?php endforeach ?>
	<?php endforeach ?>

</select>

<?= $this->Html->scriptStart() ?>

  	$('.produit').select2({

    placeholder: 'Selectionnez un article',

  });

  	$('document').ready(function(){

         $(".produit").change(function(){

            var packid = $(this).val();

            var customerid = $('#customer-id').val();

            var pofsaleid = $('#pofsale').val();

            searchTags( packid,customerid,pofsaleid );

         });

        function searchTags( packid,customerid,pofsaleid ){

        var pack = packid;
        var packid = packid.substring(0, packid.length-1);

        var customer = customerid;

        var pofsale = pofsaleid;

          $('#example2 tr:last').after('<tr id="product'+packid+'"></tr>');

        $.ajax({

            method: 'get',

            url : "<?php echo $this->Url->build( [ 'controller' => 'Orders', 'action' => 'product',$avoir] ); ?>",

            data: {packid:pack,customerid:customer,pofsaleid:pofsale},

            success: function( response )

            {       

               $( '#product'+packid  ).html(response);

            }

        });

      };

    });

<?= $this->Html->scriptEnd(); ?>

