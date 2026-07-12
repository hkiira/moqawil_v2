<div class="col-12">
  <div class="card">
    <div class="card-body">
      <table class="table table-bordered table-checkable" id="mytable">
        <thead>
          <tr>
            <th>Article</th>
            <th>Quantité commandé</th>
            <th>Quantité reçu (Carton/Sac)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($productselects as $key => $product): ?>
            <tr>
              <?= $this->Form->control('supporderproducts.' . $product['id'] . '.id', ['type' => 'hidden', 'class' => 'form-control', 'label' => false, 'value' => $product['id']]); ?>
              <?= $this->Form->control('supporderproducts.' . $product['id'] . '.productunite_id', ['type' => 'hidden', 'class' => 'form-control', 'label' => false, 'value' => $product['productunite_id']]); ?>
              <?= $this->Form->control('supporderproducts.' . $product['id'] . '.qtepercs', ['type' => 'hidden', 'class' => 'form-control', 'label' => false, 'value' => $product['qtepercs']]); ?>
              <td style="width: 50%;">
                <?= $product['title'] ?><br>
              </td>
              <td>
                <?= (intVal($product['quantity'])) . ' ' . $product['unit'] ?><br>
              </td>
              <td>
                <?= $this->Form->control('supporderproducts.' . $product['id'] . '.quantity', ['type' => 'number', 'min' => '0', 'class' => 'form-control', 'label' => false, 'value' => 0]); ?>
              </td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>
</div>