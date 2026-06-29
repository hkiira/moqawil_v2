<?= $this->Form->create(null, [ 'url' => ['controller' => 'Tranches', 'action' => 'add']]) ?>
        <div class="card-body">
            <div class="row">
                <div class="col-xl-6">
                    <?php
                    echo $this->Form->control('min',['label'=>'Quantité minimale','type'=>'number']);
                    echo $this->Form->control('max',['label'=>'Quantité maximale','type'=>'number']);
                    ?>
                </div>
                <div class="col-xl-6">
                    <?php
                    echo $this->Form->control('remisetype_id', ['options' => $remisetypes,'label'=>'Type de remise','class'=>'select2']);
                    ?>
                    <div class="remisetype"></div>
                    <?= $this->element('statut')  ?>
                </div>
                <button type="submit" class="btn btn-lg btn-success  m-t-30 float-right">Ajouter l'offre</button>

        
            </div>

        </div>
                                    <?= $this->Form->end() ?>