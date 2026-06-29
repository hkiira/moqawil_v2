<?php
$this->extend('/Common/crud');
?>
<?php
$this->loadHelper('Form', [
    'templates' => 'app_form',
]);
$this->assign('objet', $this->Form->create($turnover));
$this->assign('title', 'Ajouter un nouveau chiffre');
$this->assign('subtitle', 'vous pouvez ajouter un nouveau chiffre');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <?php
                echo $this->Form->control('title', ['label' => 'Nom du chiffre']);
                echo $this->Form->control('commission', ['label' => 'Valeur du chiffre', 'type' => 'number']);
                ?>
                <?= $this->element('statut')  ?>
            </div>
        </div>
    </div>
</div>