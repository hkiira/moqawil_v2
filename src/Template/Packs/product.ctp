<?php $increment=0; ?>
    <h3 class="display-3">Tarif normale</h3>
<div class="row">
    <?php foreach ($customertypes as $key => $customertype): ?>
        <div class="col-6 pb-4 bg-light-success">
            <h4 class="display-5 p-6 "><?= $customertype ?></h4>
            <div class="separator mb-6 separator-solid separator-border-2 separator-success"></div>
            <?php foreach ($prices as $key1 => $price): ?>
                <?php if ($price->customertype_id==$key): ?>
                    <?= $this->Form->control('prices.'.$increment.'.id',['type'=>'hidden','label' => 'false','value'=>$price->id]);?>
                    <?= $this->Form->control('prices.'.$increment.'.price',['class'=>'form-control ','type'=>'number','label' => 'Prix '.$price->warehouse->title,'step'=>'any','required'=>'required','value'=>$price->price]);?>
                    <?= $this->Form->control('prices.'.$increment.'.minp',['class'=>'form-control ','type'=>'number','label' => 'Prix Min','step'=>'any','required'=>'required','value'=>($price->minp>0)?$price->minp:$price->price]);?>
                    <?= $this->Form->control('prices.'.$increment.'.maxp',['class'=>'form-control ','type'=>'number','label' => 'Prix Max','step'=>'any','required'=>'required','value'=>($price->maxp>0)?$price->maxp:$price->price]);?>
                        <?php $increment++; ?>
                <?php endif ?>
            <?php endforeach ?>
        </div>
    <?php endforeach ?>
</div>