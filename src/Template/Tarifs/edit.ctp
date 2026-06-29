<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($tarif,['id'=>'kt_form_1']));
$this->assign('title', 'Modifier le tarif '.$tarif->title);
?>
<div class="card-body">
    <div class="row">
        <div class="col-12">
            <?php $increment=0; ?>
            <?php foreach($tarif->tarifcategories as $key=>$tarifcategory): ?>
                <h3 class="display-3" style="text-align: center;"><?= $tarifcategory->category->title ?> </h3>
                <table class="table">
                    <tr>
                        <th>Entrepôt</th>
                        <th>Tarif Promo</th>
                        <th>Tarif normale</th>
                        <th>Type de client</th>
                    </tr>
                        <?php foreach($tarifcategory->category->packs as $key=>$pack): ?>
                            <tr>
                                <th colspan="4">
                                    <h6 class="display-6" style="text-align: center;">
                                            <?= $pack->title ?>
                                    </h6>
                                </th>
                            </tr>
                            <?php if ($pack->prices): ?>
                                
                                <?php foreach($pack->prices as $key=>$price): ?>
                                    <tr>
                                        <td>
                                            <?= $price->warehouse->title ?>
                                        </td>
                                        <td>
                                            <?= $this->Form->control('prices.'.$increment.'.price',['label'=>false,'min'=>0, 'value'=>$price->price,'required'=>'required']); ?>
                                            <?= $this->Form->control('prices.'.$increment.'.id',['type'=>'hidden','value'=>$price->id]); ?>
                                        </td>
                                        <td>
                                            <?= $pricesn[$price->pack_id][$price->warehouse_id][$price->customertype_id]['price'] ?>
                                        </td>
                                        <td>
                                            <?= $price->customertype->title  ?>
                                        </td>
                                    </tr>
                                    <?php $increment++; ?>    
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?php foreach ($pricesn[$pack->id] as $key => $values): ?>
                                <?php foreach ($values as $key1 => $value): ?>

                                    <tr>
                                        <?php if (array_key_first($values)==$key1): ?>
                                          <td rowspan="<?= count($values)  ?>">
                                            <?= $value['warehouse'] ?>
                                        </td>  
                                        <?php endif ?>
                                        
                                        <td>
                                            <?= $this->Form->control('prices.'.$increment.'.price',['label'=>false,'min'=>0, 'value'=>$value['price'],'required'=>'required']); ?>
                                            <?= $this->Form->control('prices.'.$increment.'.pack_id',['type'=>'hidden','value'=>$pack->id]); ?>
                                            <?= $this->Form->control('prices.'.$increment.'.pack_id',['type'=>'hidden','value'=>$pack->id]); ?>
                                            <?= $this->Form->control('prices.'.$increment.'.customertype_id',['type'=>'hidden','value'=>$value['customertypeid']]); ?>
                                            <?= $this->Form->control('prices.'.$increment.'.warehouse_id',['type'=>'hidden','value'=>$value['warehouseid']]); ?>
                                        </td>
                                        <td>
                                            <?= $value['price'] ?>
                                        </td>
                                        <td>
                                            <?= $value['customertype'] ?>
                                        </td>
                                    </tr>
                                    <?php $increment++; ?>    
                                <?php endforeach ?>
                                <?php endforeach ?>
                            <?php endif ?>
                        <?php endforeach; ?>
                </table>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>

<?= $this->Html->scriptEnd(); ?>
