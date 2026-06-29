<?php
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
  'templates' => 'app_form',
]);
$this->assign('objet', $this->Form->create($supplierorder));
$this->assign('title', 'Ajouter un nouveau réceapitulatif');
?>
<div class="row">
  <?php foreach ($orderpackids as $pid) {
    echo $this->Form->control('orderpacks[' . $pid . ']', ['type' => 'hidden', 'class' => 'form-control', 'label' => false, 'value' => $pid]);
  }
  ?>
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <table class="table table-bordered table-checkable" id="mytable">
          <thead>
            <tr>
              <th>Article</th>
              <th>Quantité (Carton/Sac)</th>
              <th>Quantité (Kg/unité)</th>
              <th>Prix</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($packselects as $key => $packselect): ?>
              <?php if ($packselect['packs']) { ?>
                <tr>
                  <td>
                    <h6><?= $packselect['category']  ?></h6>
                  </td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>
                <?php foreach ($packselect['packs'] as $key1 => $pack): ?>
                  <tr>

                    <?= $this->Form->control('supporderproducts.' . $pack['id'] . '.pack_id', ['type' => 'hidden', 'class' => 'form-control', 'label' => false, 'value' => $pack['id']]); ?>
                    <td style="width: 50%;">
                      <?= $pack['title']  ?><br>
                      <?= $pack['qtepercs'] . ' ' . $pack['piecekg'] . ' par ' . $pack['carsac'] ?>
                    </td>
                    <td>
                      <?php if (isset($pack[0]['price'])): ?>
                        <?= $this->Form->control('supporderproducts.' . $pack['id'] . '.0.quantity', ['type' => 'number', 'min' => '0', 'class' => 'form-control', 'label' => false, 'value' => $pack[0]['quantity']]); ?>
                      <?php endif ?>
                    </td>
                    <td>
                      <?php if (isset($pack[1]['price'])): ?>
                        <?= $this->Form->control('supporderproducts.' . $pack['id'] . '.1.quantity', ['type' => 'number', 'min' => '0', 'class' => 'form-control', 'label' => false, 'value' => $pack[1]['quantity']]); ?>
                      <?php endif ?>
                    </td>
                    <td>
                      <?= $this->Form->control('supporderproducts.' . $pack['id'] . '.price', ['type' => 'number', 'class' => 'form-control', 'label' => false, 'value' => $pack[0]['price']]); ?>
                    </td>
                  </tr>
                <?php endforeach ?>
              <?php } ?>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>


<?= $this->Html->scriptStart(['block' => 'script_bottom']) ?>
$('.select2').select2();
$.fn.select2.defaults.set("width", "100%");

$('.supplier_id').select2({
placeholder: 'Selectionnez le fournisseur'
});

FormValidation.formValidation(
document.getElementById('kt_form_1'),
{
fields: {
'supplier_id': {
validators: {
notEmpty: {
message: 'Merci de mentionner le fournisseur avant de valider la commande'
}
}
},

},

plugins: {
trigger: new FormValidation.plugins.Trigger(),
bootstrap: new FormValidation.plugins.Bootstrap(),
submitButton: new FormValidation.plugins.SubmitButton(),
defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
}
}
);

<?= $this->Html->scriptEnd(); ?>
<?= $this->Html->css('/assets/plugins/custom/datatables/datatables.bundle.css', ['block' => 'css_top']) ?>
<?= $this->Html->script('/assets/plugins/custom/datatables/datatables.bundle.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->script('/js/dataTables.checkboxes.min.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->css('/js/dataTables.checkboxes.css', ['block' => 'css_top']) ?>

<?= $this->Html->scriptStart(['block' => 'script_bottom']) ?>
$(document).ready(function(){
var mytable = $("#mytable").DataTable({
paging: false,
ordering : false,
})

$('form').on('submit', function(e){
var form = this;
var params = mytable.$('input,select,textarea').serializeArray();
$.each(params, function(){
if(!$.contains(document, form[this.name])){
$(form).append(
$('<input>')
.attr('type', 'hidden')
.attr('name', this.name)
.val(this.value)
);
}
});
});
});


<?= $this->Html->scriptEnd(); ?>