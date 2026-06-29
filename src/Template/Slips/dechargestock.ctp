  <?php if ($users): ?>
    <div class="row pb-5">
      <label class="col-3">
        Vendeur
      </label>
      <div class="col-9">
          <?= $this->Form->control('user_id',['options' => $users,'class'=>'select2 form-control','label'=>false,'empty'=>true]); ?>
        </div>
      </div>
  <?php endif ?>
<div class="col-12 userproducts">
        
    <table class="table table-bordered table-checkable" id="mytable">
      <thead>
        <tr>
          <th width="45%">Article</th>
          <th width="10%">Qté Disponible</th>
          <th width="10%">Qté (Carton/Sac)</th>
          <th width="10%">Qté (Unité)</th>
          <th width="10%">Prix</th>
          <th width="15%">Raison</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($packselects as $key => $pack): ?>
          <tr>
            <?= $this->Form->control('slipproducts.'.$pack['id'].'.pack_id', ['type' => 'hidden','class' => 'form-control','label' => false, 'value' => $pack['id']]); ?>
            <?= $this->Form->control('slipproducts.'.$pack['id'].'.qtepercs', ['type' => 'hidden','class' => 'form-control','label' => false, 'value' => $pack['qtepercs']]); ?>
            <td style="width: 50%;">
              <?=$pack['title']  ?><br>
              <?=$pack['qtepercs'].' '.$pack['piecekg'].' par '.$pack['carsac'] ?>
            </td>
            <td>
              <?=(intVal($pack['quantity']/$pack['qtepercs'])).' '.$pack['carsac']   ?><br>
              <?= ($pack['quantity']).' '.$pack['piecekg']  ?>
            </td>
            <td>
              <?= $this->Form->control('slipproducts.'.$pack['id'].'.0.quantity', ['type' => 'number','min'=>'0' ,'class' => 'form-control','label' => false, 'value' => 0]); ?>
            </td>
            <td>
              <?= $this->Form->control('slipproducts.'.$pack['id'].'.1.quantity', ['type' => 'number','min'=>'0' ,'class' => 'form-control','label' => false, 'value' => 0]); ?>
            </td>
            <td>
              <?= $this->Form->control('slipproducts.'.$pack['id'].'.1.price', ['type' => 'number' ,'class' => 'form-control','step'=>'any','label' => false, 'value' => $pack['price']]); ?>
            </td>
            <td>
              <?= $this->Form->control('slipproducts.'.$pack['id'].'.whnature_id',['options' => $whnatures,'class'=>'form-control','label'=>false]); ?>
                
            </td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
  <script type="text/javascript">
    $('.select2').select2({
      placeholder: 'Selectionnez un vendeur',
    });
    $("#user-id").change(function(){
        var searchkey = $("#warehoused").val();
        var searchkey1 = $("#whnature-id").val();
        var searchkey2 = $("#user-id").val();
        searchTags( searchkey ,searchkey1,searchkey2,'instanceord','.userproducts');
    });
  </script>