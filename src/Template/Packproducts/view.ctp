<?php $this->extend('/Common/crud'); ?>
<?php $this->loadHelper('Form', [ 'templates' => 'app_form' ]); 

$this->assign('title', 'Détails de la Liaison Pack-Produit');
$editButton = $this->Html->link('Modifier cette liaison', ['action' => 'edit', $packproduct->id], ['class' => 'btn btn-success font-weight-bold']);
$this->assign('edit', $editButton);
?>
<div class="card-body">
    <div class="row pb-4">
        <div class="col-lg-12">
            <h3>Liaison: <?= h($packproduct->pack->title) ?> &rarr; <?= h($packproduct->product->title) ?></h3>
            <table class="table table-bordered table-hover">
                <tr>
                    <th scope="row" style="width: 20%;"><?= __('Pack') ?></th>
                    <td><?= $packproduct->has('pack') ? $this->Html->link($packproduct->pack->title, ['controller' => 'Packs', 'action' => 'view', $packproduct->pack->id]) : '' ?></td>
                </tr>
                <tr>
                    <th scope="row"><?= __('Produit') ?></th>
                    <td><?= $packproduct->has('product') ? $this->Html->link($packproduct->product->title, ['controller' => 'Products', 'action' => 'view', $packproduct->product->id]) : '' ?></td>
                </tr>
                <tr>
                    <th scope="row"><?= __('Quantité dans le pack') ?></th>
                    <td><?= $this->Number->format($packproduct->quantity) ?></td>
                </tr>
                <tr>
                    <th scope="row"><?= __('Statut') ?></th>
                    <td>
                        <?php 
                            $statusMap = [-1 => 'Supprimé', 0 => 'Innactif', 1 => 'Actif'];
                            echo isset($statusMap[$packproduct->statut]) ? $statusMap[$packproduct->statut] : 'N/A';
                        ?>
                    </td>
                </tr>
                <?php if ($packproduct->has('company')): ?>
                <tr>
                    <th scope="row"><?= __('Société') ?></th>
                    <td><?= $this->Html->link($packproduct->company->name, ['controller' => 'Companies', 'action' => 'view', $packproduct->company->id]) ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th scope="row"><?= __('Créé le') ?></th>
                    <td><?= h($packproduct->created) ?></td>
                </tr>
                <tr>
                    <th scope="row"><?= __('Modifié le') ?></th>
                    <td><?= h($packproduct->modified) ?></td>
                </tr>
            </table>
        </div>
    </div>
    <?php // Add any related data display if necessary ?>
</div>
