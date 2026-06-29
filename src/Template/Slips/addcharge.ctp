<?php
$this->extend('/Common/crud');

$this->loadHelper('Form', [
    'templates' => 'app_form',
]);

$this->assign('objet', $this->Form->create($slip));
$this->assign('title', 'Ajouter un nouveau bon de charge');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-6">
            <?= $this->Form->control('whnature_id', [
                'options' => $whnatures,
                'class' => 'select2 form-control',
                'label' => 'Nature de transfert'
            ]); ?>
        </div>
        <div class="col-xl-6">
            <?= $this->Form->control('warehoused', [
                'type' => 'select',
                'options' => $warehoused,
                'class' => 'select2 form-control',
                'label' => 'Entrepôt de réception',
                'empty' => true
            ]); ?>
        </div>
        <div class="col-xl-12">
            <?= $this->Form->control('raison', [
                'label' => 'Raison'
            ]); ?>
        </div>
    </div>
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
                          <tr><td><h6><?= $packselect['category']  ?></h6></td><td></td><td></td><td></td></tr>
                          <?php foreach ($packselect['packs'] as $key1 => $pack): ?>
                            <tr>
                              <?= $this->Form->control('slipproducts.'.$pack['id'].'.item_id', ['type' => 'hidden','class' => 'form-control','label' => false, 'value' => $pack['pack_id']]); ?>
                              <?= $this->Form->control('slipproducts.'.$pack['id'].'.unity_id', ['type' => 'hidden','class' => 'form-control','label' => false, 'value' => $pack['id']]); ?>
                              <td style="width: 50%;">
                                <?=$pack['title']  ?><br>
                                <?=$pack['qtepercs'].' '.$pack['piecekg'].' par '.$pack['carsac'] ?>
                              </td>
                              <td>
                                <?php if (isset($pack[0]['price'])): ?>
                                  <?= $this->Form->control('slipproducts.'.$pack['id'].'.0.quantity', ['type' => 'number','min'=>'0' ,'class' => 'form-control','label' => false, 'value' => 0]); ?>
                                <?php endif ?>
                              </td>
                              <td>
                                <?php if (isset($pack[1]['price'])): ?>
                                  <?= $this->Form->control('slipproducts.'.$pack['id'].'.1.quantity', ['type' => 'number','min'=>'0','class' => 'form-control','label' => false, 'value' => 0]); ?>
                                <?php endif ?>
                              </td>
                              <td>
                                <?= $this->Form->control('slipproducts.'.$pack['id'].'.price', ['type' => 'number','class' => 'form-control','label' => false, 'value' => $pack[0]['price']]); ?>
                              </td>
                            </tr>
                          <?php endforeach ?>
                       <?php } ?>
                      <?php endforeach ?>
                    </tbody>
                </table>
</div>
<?= $this->Html->scriptStart(['block' => 'script_bottom']) ?>
    $.fn.select2.defaults.set("width", "100%");
    // Initialize select2 for warehouse selection
    $('#warehoused').select2({
        placeholder: 'Sélectionnez un entrepôt'
    });
    $('.select2').select2();


<?= $this->Html->scriptEnd(); ?>

