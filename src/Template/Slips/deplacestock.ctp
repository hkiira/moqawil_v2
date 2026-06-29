  <div class="col-12">
    <table class="table table-bordered table-checkable" id="mytable">
      <thead>
        <tr>
          <th>Article</th>
          <th>Quantité Disponible</th>
          <th>Quantité à décharger (Carton/Sac)</th>
          <th>Quantité à décharger (Unité)</th>
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
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>