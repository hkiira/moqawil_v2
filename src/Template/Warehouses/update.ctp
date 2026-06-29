<?php   
  $this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($warehouse,['id'=>'kt_form_1']));
$this->assign('title', 'Modifier le stock');
?>
<div class="row">
  <div class="col-12">
        <div class="card">
            <div class="card-body">
                  <table class="table table-bordered table-checkable" id="mytable">
                    <thead>
                      <tr>
                        <th>Article</th>
                        <th>Quantité disponible</th>
                        <th>Quantité (Carton/Sac)</th>
                        <th>Quantité (Kg/unité)</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($packselects as $key => $packselect): ?>
                        <?php if ($packselect['packs']) { ?>
                          <tr><td><h6><?= $packselect['category']  ?></h6></td><td></td><td></td><td></td><td></td></tr>
                          <?php foreach ($packselect['packs'] as $key1 => $pack): ?>
                            <tr>
                              <?= $this->Form->control('whproducts.'.$pack['id'].'.id', ['type' => 'hidden','class' => 'form-control','label' => false, 'value' => $pack['id']]); ?>
                              <?= $this->Form->control('whproducts.'.$pack['id'].'.qtpersac', ['type' => 'hidden','class' => 'form-control','label' => false, 'value' => $pack['qtepercs']]); ?>
                              <td style="width: 50%;">
                                <?=$pack['title']  ?><br>
                                <?=$pack['qtepercs'].' '.$pack['piecekg'].' par '.$pack['carsac'] ?>
                              </td>
                              <td>
                                <?=(intVal($pack['quantity']/$pack['qtepercs'])).' '.$pack['carsac']   ?><br>
                                <?= ($pack['quantity']).' '.$pack['piecekg']  ?>
                              </td>
                              <td>
                                  <?= $this->Form->control('whproducts.'.$pack['id'].'.0.quantity', ['type' => 'number','min'=>'0','max'=>$pack['quantity'] ,'class' => 'form-control','label' => false, 'value' => intVal($pack['quantity']/$pack['qtepercs'])]); ?>
                              </td>
                              <td>
                                  <?= $this->Form->control('whproducts.'.$pack['id'].'.1.quantity', ['type' => 'number','min'=>'0','max'=>$pack['quantity'] ,'class' => 'form-control','label' => false, 'value' => intVal($pack['quantity']%$pack['qtepercs'])]); ?>
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
  

  $('#supplier-id').select2({
    placeholder: 'code ou nom du fournisseur ',
    ajax: {
      url : "<?php echo $this->Url->build( [ 'controller' => 'Supplierorders', 'action' => 'suppliers'] ); ?>",
      dataType: 'json',
      delay: 500,
      processResults: function (data) {
        return {
          results: data
        };
      },
      cache: true
    }
  });
    
  $('.groups').select2({
    placeholder: 'Selectionnez une catégorie'
  });

  $('.produit').select2({
    placeholder: 'Selectionnez un article',
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
    $('form').on('keypress','input',function(e){
        var eClassName = this.className,
            index = $(this).index('.' + eClassName) + 1;
        if (e.which === 13){
            e.preventDefault();
            $('input.' + eClassName).eq(index).focus();
        }
    });

    var mytable = $("#mytable").DataTable({
    
        
        paging: false,
        ordering    : false,
        

    })

    $("#myform").on('submit', function(e){
        var form = this
        var rowsel = mytable.column(0).checkboxes.selected();
        mytable.rows().invalidate().draw();
        $.each(rowsel, function(index, rowId){
          $(form).append(
              $('<input>').attr('type','hidden').attr('name','id[]').val(rowId)
          )
        })
    })
});


<?= $this->Html->scriptEnd(); ?>



