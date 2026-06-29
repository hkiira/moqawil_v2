<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($role));
$this->assign('title', 'Modifier le role '.$role->title);
?>
<div class="card-body">
    <div class="row">
        <div class="col-12">
        <table class="table table-bordered table-hover">
            <tr>
                <th>Controleur</th>
                <th>Action</th>
                <th>On/Off</th>
                <th>Seulement ses actions</th>
            </tr>
            <?php foreach ($accesses as $key => $access): ?>
                <tr>
                    <td><?= $access->controller ?></td>
                    <td><?= $access->action  ?></td>
                    <td>
                        <?php if ($access->statut==1): ?>
                            <?= $this->Form->control('accesroles.'.$access->id.'.statut',['label'=>false,'type'=>'checkbox','checked'=>false]) ?></td>
                        <?php else: ?>
                            <?= $this->Form->control('accesroles.'.$access->id.'.statut',['label'=>false,'type'=>'checkbox','checked'=>false]) ?></td>
                        <?php endif ?>
                    <td><?= $this->Form->control('accesroles.'.$access->id.'.hisown',['label'=>false,'type'=>'checkbox']) ?></td>
                </tr>
            <?php endforeach ?>
        </table>
    </div>
</div>
