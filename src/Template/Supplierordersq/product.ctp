<td>

  <?= h($products->code) ?>

  - <?= h($products->title) ?>

  <?= $this->Form->control('supporderproducts.' . $products->id . '.pack_id', ['type' => 'hidden', 'class' => 'form-control', 'label' => false, 'value' => $products->id]); ?>



</td>

<td width="20%">

  <?= $this->Form->control('supporderproducts.' . $products->id . '.quantity', ['type' => 'number', 'class' => 'form-control', 'label' => false, 'value' => 1]); ?>

</td>

<td>

  <?php echo $this->Form->control('supporderproducts.' . $products->id . '.price', ['type' => 'number', 'step' => 'any', 'class' => 'form-control', 'label' => false, 'value' => 0, 'required' => 'required']); ?>

</td>

<td>

  <b class='btn btn-delete btn-danger'>-</a>

</td>

<script>
  $('table').on('click', '.btn-delete', function() {

    tableID = '#' + $(this).closest('table').attr('id');

    $(this).closest('tr').remove();

    renumber_table(tableID);

  });

  function log(name, evt) {

    if (!evt) {

      var args = "{}";

    } else {

      var args = JSON.stringify(evt.params, function(key, value) {

        if (value && value.nodeName) return "[DOM node]";

        if (value instanceof $.Event) return "[$.Event]";

        return value;

      });

    }

  }



  function formatResultData(data) {

    if (!data.id) return data.text;

    if (data.element.selected) return

    return data.text;

  };



  $('.size').select2();
</script>