<?php
    $this->assign('title', 'Détails de l\'emballage produit');
    $this->assign('action', $this->Html->link(__('Nouvel emballage produit'), ['action' => 'add'], ['class' => 'btn btn-primary']));
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= __('Détails de l\'emballage produit') ?></h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th><?= __('Produits') ?></th>
                <td>
                    <?php if (!empty($productPackage->products)): ?>
                        <ul class="list-unstyled">
                            <?php foreach ($productPackage->products as $product): ?>
                                <li><?= h($product->title) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <?= __('Aucun produit associé') ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><?= __('Poids/Taille') ?></th>
                <td><?= h($productPackage->weight) ?></td>
            </tr>
            <tr>
                <th><?= __('Unité') ?></th>
                <td><?= h($productPackage->unit) ?></td>
            </tr>
            <tr>
                <th><?= __('Emballage par défaut') ?></th>
                <td><?= $productPackage->is_default ? __('Oui') : __('Non') ?></td>
            </tr>
            <tr>
                <th><?= __('Statut') ?></th>
                <td><?= $productPackage->statut ? __('Actif') : __('Inactif') ?></td>
            </tr>
            <tr>
                <th><?= __('Date de création') ?></th>
                <td><?= h($productPackage->created) ?></td>
            </tr>
            <tr>
                <th><?= __('Dernière modification') ?></th>
                <td><?= h($productPackage->modified) ?></td>
            </tr>
        </table>
    </div>
    <div class="card-footer">
        <?= $this->Html->link(__('Modifier'), ['action' => 'edit', $productPackage->id], ['class' => 'btn btn-primary']) ?>
        <?= $this->Html->link(__('Retour'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
    </div>
</div> 