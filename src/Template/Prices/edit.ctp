<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($category,['id'=>'kt_form_1']));
$this->assign('title', 'Modifier les prix de la catégorie '.$category->title);
?>
<div class="card-body">
    <div class="row">
        <div class="col-12">
            <?php $increment=1; ?>
                <?php foreach($category->packs as $key=>$pack): ?>
                <h3 class="display-3" style="text-align: center;"><?= $pack->title ?> </h3>
                <table class="table">
                    <tr>
                        <th>Entrepôt</th>
                        <th>Tarif Promo</th>
                        <th>Tarif normale</th>
                        <th>Type de client</th>
                    </tr>
                            <?php foreach($pack->prices as $key=>$price): ?>
                                <tr> 
                                    <?php if ($increment%2): ?>
                                        <td rowspan="2">
                                            <h5><?= $price->warehouse->title ?></h5>
                                        </td>
                                    <?php endif ?>
                                    <td>
                                        <?= $this->Form->control('prices.'.$increment.'.price',['label'=>false,'min'=>0, 'value'=>$price->price,'required'=>'required']); ?>
                                        <?= $this->Form->control('prices.'.$increment.'.id',['type'=>'hidden','value'=>$price->id]); ?>
                                    </td>
                                    <td>
                                        <?= $price->price ?>
                                    </td>
                                    <td>
                                        <?= $price->customertype->title  ?>
                                    </td>
                                </tr>
                                <?php $increment++; ?>    
                            <?php endforeach; ?>
                </table>
                        <?php endforeach; ?>
        </div>
    </div>
</div>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>

<?= $this->Html->scriptEnd(); ?>
