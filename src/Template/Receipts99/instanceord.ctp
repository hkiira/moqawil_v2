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
                      <?php foreach ($packselects as $key => $pack): ?>
                            <tr>
                              <?= $this->Form->control('supporderproducts.'.$pack['id'].'.id', ['type' => 'hidden','class' => 'form-control','label' => false, 'value' => $pack['id']]); ?>
                              <?= $this->Form->control('supporderproducts.'.$pack['id'].'.qtepercs', ['type' => 'hidden','class' => 'form-control','label' => false, 'value' => $pack['qtepercs']]); ?>
                              <td style="width: 50%;">
                                <?=$pack['title']  ?><br>
                                <?=$pack['qtepercs'].' '.$pack['piecekg'].' par '.$pack['carsac'] ?>
                              </td>
                              <td>
                                <?=(intVal($pack['quantity']/$pack['qtepercs'])).' '.$pack['carsac']   ?><br>
                                <?= ($pack['quantity']).' '.$pack['piecekg']  ?>
                              </td>
                              <td>
                                  <?= $this->Form->control('supporderproducts.'.$pack['id'].'.quantity', ['type' => 'number','min'=>'0' ,'class' => 'form-control','label' => false, 'value' => 0]); ?>
                              </td>
                            </tr>
                      <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>