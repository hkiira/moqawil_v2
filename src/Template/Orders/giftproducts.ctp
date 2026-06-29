  <div class="row mx-5">
    <?php if ($userinfos->customertype_id==4): ?>
        <div class="col-6 pt-5">
              <?= $this->Form->control('comment', ['type'=>'text','class'=>'form-control','label' => 'Livreur']) ?>
        </div>
        <div class="col-6 pt-5">
              <?= $this->Form->control('pofsale', ['type'=>'select','class'=>'form-control','options'=>$pofsales,'label' => 'Entrepôt']) ?>
        </div>
    <?php endif ?>
      <div class="col-12 pt-5">
        <?php if ($userinfos->customertype_id!==4): ?>
            <?= $this->Form->control('pofsale', ['type'=>'hidden','value'=>$pofsale->id,'label' => false]) ?>
        <?php endif ?>
                  <table class="table table-bordered table-checkable" id="mytable">
                    <thead>
                      <tr>
                        <th>Article</th>
                        <th>Quantité disponible</th>
                        <th>Quantité</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($packselects as $key => $packselect): ?>
                        <?php if ($packselect['packs']) { ?>
                          <tr><td><h6><?= $packselect['category']  ?></h6></td><td></td><td></td><td></td><td></td></tr>
                          <?php foreach ($packselect['packs'] as $key1 => $pack): ?>
                            <tr>
                              <?= $this->Form->control('orderpacks.'.$pack['id'].'.pack_id', ['type' => 'hidden','class' => 'form-control','label' => false, 'value' => $pack['id']]); ?>
                              <td style="width: 50%;">
                                <?=$pack['title']  ?><br>
                                <?=$pack['loyaltypoints']  ?> points de fidélité
                              </td>
                              <td>
                                <?= ($pack['quantity']).' '.$pack['piecekg']  ?>
                              </td>
                              <td>
                                <?php if (isset($pack[0]['price'])): ?>
                                  <?= $this->Form->control('orderpacks.'.$pack['id'].'.quantity', ['type' => 'number','min'=>'0','max'=>intVal($max/$pack['loyaltypoints']),'class' => 'form-control','label' => false, 'value' => 0]); ?>
                                <?php endif ?>
                              </td>
                            </tr>
                          <?php endforeach ?>
                       <?php } ?>
                      <?php endforeach ?>
                    </tbody>
                </table>
    </div> 
  </div> 

<?= $this->Html->script('/js/jquery.min.js') ?>
<?= $this->Html->css('/assets/plugins/custom/datatables/datatables.bundle.css') ?>
<?= $this->Html->script('/assets/plugins/custom/datatables/datatables.bundle.js') ?>
<?= $this->Html->script('/js/dataTables.checkboxes.min.js') ?>
<?= $this->Html->css('/js/dataTables.checkboxes.css') ?>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js') ?>

<?= $this->Html->scriptStart() ?>
    
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


<?= $this->Html->scriptEnd(); ?>



