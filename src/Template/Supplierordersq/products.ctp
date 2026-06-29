<select class="form-control select2 produit" name="produit">

  <option></option>

  <?php foreach ($products as $key => $product): ?>

    <option value="<?= $product->id ?>" data-id="<?= $product->id ?>"><?= $product->code ?> - <?= $product->title ?></option>

  <?php endforeach ?>

</select>

<script type="text/javascript">
  $('.produit').select2({

    placeholder: 'Selectionner un article',

  });

  $('document').ready(function() {

    $(".produit").change(function() {



      var searchkey = $(this).val();

      searchTags(searchkey);

    });

    function searchTags(keyword) {

      var data = keyword;

      $('#example2 tr:last').after('<tr id="product' + data + '"></tr>');

      $.ajax({

        method: 'get',

        url: "<?php echo $this->Url->build(['controller' => 'Supplierorders', 'action' => 'product']); ?>",

        data: {
          keyword: data
        },

        success: function(response)

        {

          $('#product' + data).html(response);

        }

      });

    };

  });
</script>