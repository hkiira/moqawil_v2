<?php   

$this->extend('/Common/crud');

?>

<?php $this->loadHelper('Form', [

    'templates' => 'app_form',

]); 

$this->assign('objet',$this->Form->create($slip, ['class' => 'form-horizontal' ,'id' => 'myform']));

$this->assign('title', 'Valider le bon de '.$slip->sliptype->title.' N° :'.$slip->code);

$this->assign('subtitle', $slip->code);

?>

  <div class="card-body">

    <table class="table table-bordered table-checkable" id="mytable">

      <thead>

        <?php if($slip->sliptype_id==6):?>
        <tr>
          <th>Article</th>
          <th>Quantité à valider</th>
          <th>Stock disponible</th>
          <th>Secteur</th>
        </tr>
        <?php else: ?>
        <tr>
          <th>Article</th>
          <th>Quantité</th>
          <th>Qté par sac/carton</th>
          <th>Qté (Mesure)</th>
          <th>Qté par unité/kg</th>
        </tr>
        <?php endif; ?>
      </thead>

      <tbody>
        <?php if($slip->sliptype_id==6):?>
        <?php foreach ($slippackunties->slipproducts as $key => $slipproduct): ?>
          <tr>
            <td>
            <?= $this->Form->control('slipproducts.'.$slipproduct->id.'.id', ['type' => 'hidden','class' => 'form-control','label' => false, 'value' => $slipproduct->id]); ?>
              <?= $slipproduct->product->title ?><br>
              <?= $slipproduct->productunite->quantity*$slipproduct->product->measurement_quantity ?> <?= $slipproduct->product->measurement_unit->abbreviation ?> par <?= $slipproduct->productunite->unite->abrev ?>
            </td>
            <td>
               <?= $this->Form->control('slipproducts.'.$slipproduct->id.'.1.quantity', ['class' => 'form-control','label' => false, 'value' => $slipproduct->quantity]); ?>
            </td>
            <td>
               <?= $slipproduct->product->whproducts[0]->quantity.' '.$slipproduct->productunite->unite->parentunite->abrev ?>
            </td>
            <td><?= $slipproduct->whnature->title ?></td>
          </tr>

        <?php endforeach ?>
        <?php else: ?>
          <?php foreach ($slippackunties->slipproducts as $key => $slipproduct): ?>
          <tr>
            <td>
            <?= $this->Form->control('slipproducts.'.$slipproduct->id.'.id', ['type' => 'hidden','class' => 'form-control','label' => false, 'value' => $slipproduct->id]); ?>
              <?= $slipproduct->pack->title.$slipproduct->pack->id ?><br>
              <?= $slipproduct->pack->packunites[0]->quantity ?> <?= $slipproduct->pack->packunites[0]->unite->parentunite->abrev ?> par <?= $slipproduct->pack->packunites[0]->unite->abrev ?>
            </td>
            <?php if ($slipproduct->pack->saletype_id !== 4): ?>
            <?php if ($slipproduct->quantity%$slipproduct->pack->packunites[0]->quantity): ?>
                                 <td>
                                     <?php if (intVal($slipproduct->quantity/$slipproduct->pack->packunites[0]->quantity)>0): ?>
                                         <?=  intVal($slipproduct->quantity/$slipproduct->pack->packunites[0]->quantity).' '.$slipproduct->pack->packunites[0]->unite->abrev ?> 
                                         et <?=  $slipproduct->quantity % $slipproduct->pack->packunites[0]->quantity.' '.$slipproduct->pack->packunites[0]->unite->parentunite->abrev ?> </td>
                                                    
                                     <?php else: ?>
                                         <?=  $slipproduct->quantity % $slipproduct->pack->packunites[0]->quantity.' '.$slipproduct->pack->packunites[0]->unite->parentunite->abrev ?> </td>
                                                    
                                     <?php endif ?>
                           <?php else: ?>
                               <td>
                                   <?= intVal($slipproduct->quantity/$slipproduct->pack->packunites[0]->quantity).' '.$slipproduct->pack->packunites[0]->unite->abrev ?>
                               </td>
                            <?php endif ?>
            <?php else: ?>
              <td>
                <?= $slipproduct->quantity ?> <?= ($slipproduct->conversion_factor==1)?$slipproduct->pack->measurement_unit->abbreviation:'Kg' ?>
              </td>
            <?php endif; ?>
            <td>
            <?php if ($slipproduct->pack->saletype_id !== 4): ?>
              <?= $this->Form->control('slipproducts.'.$slipproduct->id.'.0.quantity', ['class' => 'form-control','label' => false, 'value' => intVal($slipproduct->quantity/$slipproduct->pack->packunites[0]->quantity)]); ?>
            <?php else: ?>
              -
            <?php endif; ?>

            </td>

            <td>
            <?php if ($slipproduct->pack->saletype_id == 4): ?>
              <?= $this->Form->control('slipproducts.'.$slipproduct->id.'.2.quantity', ['class' => 'form-control','label' => false, 'value' => $slipproduct->quantity]); ?>
            <?php else: ?>
              -
            <?php endif; ?>
            </td>
              
            <td>
            <?php if ($slipproduct->pack->saletype_id !== 4): ?>
               <?= $this->Form->control('slipproducts.'.$slipproduct->id.'.1.quantity', ['class' => 'form-control','label' => false, 'value' => intVal($slipproduct->quantity%$slipproduct->pack->packunites[0]->quantity)]); ?>
            <?php else: ?>
              -
            <?php endif; ?>
            </td>
          </tr>

        <?php endforeach ?>
        <?php endif; ?>
      </tbody>



    </table>

  </div>



<?= $this->Form->end() ?>

<?= $this->Html->script('/js/jquery.min.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->css('/assets/plugins/custom/datatables/datatables.bundle.css', ['block' => 'css_top']) ?>
<?= $this->Html->script('/assets/plugins/custom/datatables/datatables.bundle.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->script('/js/dataTables.checkboxes.min.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->css('/js/dataTables.checkboxes.css', ['block' => 'css_top']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>

$(document).ready(function(){
   
    var mytable = $("#mytable").DataTable({
      	paging: false,
      	ordering    : false,
    })

});

<?= $this->Html->scriptEnd(); ?>