<?php   
  $this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($slip));
$this->assign('title', 'Ajouter un nouveau bon de conditionnement');
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
          <div class="card-body p-0">
              <div class="row">
                  <div class="col-xl-2"></div>
                  <div class="col-xl-8">
                      <div class="my-5">
                        <?= $this->Form->control('warehouse_id', ['class'=>'select2 form-control','options' => $warehouses,'label' => 'Entrepôt']) ?>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
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
                      <?php foreach ($productselects as $key => $productselect): ?>
                        <?php if ($productselect['products']) { ?>
                          <tr><td><h6><?= $productselect['category']  ?></h6></td><td></td><td></td><td></td></tr>
                          <?php foreach ($productselect['products'] as $key1 => $product): ?>
                            <tr>
                              <?= $this->Form->control('slipproducts.'.$product['id'].'.item_id', ['type' => 'hidden','class' => 'form-control','label' => false, 'value' => $product['product_id']]); ?>
                              <?= $this->Form->control('slipproducts.'.$product['id'].'.unity_id', ['type' => 'hidden','class' => 'form-control','label' => false, 'value' => $product['id']]); ?>
                              <td style="width: 50%;">
                                <?=$product['title']  ?><br>
                                <?=$product['qtepercs'].' '.$product['piecekg'].' par '.$product['carsac'] ?>
                              </td>
                              <td>
                                <?php if (isset($product[0]['price'])): ?>
                                  <?= $this->Form->control('slipproducts.'.$product['id'].'.0.quantity', ['type' => 'number','min'=>'0' ,'class' => 'form-control','label' => false, 'value' => 0]); ?>
                                <?php endif ?>
                              </td>
                              <td>
                                <?php if (isset($product[1]['price'])): ?>
                                  <?= $this->Form->control('slipproducts.'.$product['id'].'.1.quantity', ['type' => 'number','min'=>'0','class' => 'form-control','label' => false, 'value' => 0]); ?>
                                <?php endif ?>
                              </td>
                              <td>
                                <?= $this->Form->control('slipproducts.'.$product['id'].'.price', ['type' => 'number','class' => 'form-control','label' => false, 'value' => $product[0]['price']]); ?>
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


<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
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
<?= $this->Html->css('/assets/plugins/custom/datatables/datatables.bundle.css',['block'=>'css_top']) ?>
<?= $this->Html->script('/assets/plugins/custom/datatables/datatables.bundle.js',['block'=>'script_bottom']) ?>
<?= $this->Html->script('/js/dataTables.checkboxes.min.js',['block'=>'script_bottom']) ?>
<?= $this->Html->css('/js/dataTables.checkboxes.css',['block'=>'css_top']) ?>

<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
  $(document).ready(function(){
    var mytable = $("#mytable").DataTable({
        paging: false,
        ordering    : false,
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



