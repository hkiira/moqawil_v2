<?php 
    $this->assign('title', 'Liste des emballages produits');
    $this->assign('action', $this->Html->link(__('Nouvel emballage produit'), ['action' => 'add'], ['class' => 'btn btn-primary']));
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= __('Liste des emballages produits') ?></h3>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" class="form-control" id="kt_datatable_search_query" placeholder="Rechercher...">
            </div>
            <div class="col-md-4">
                <?= $this->Form->select('product_id', $products, [
                    'empty' => __('Tous les produits'),
                    'class' => 'form-control selectpicker',
                    'data-live-search' => 'true'
                ]) ?>
            </div>
            <div class="col-md-4">
                <?= $this->Form->select('status', [
                    '' => __('Tous les statuts'),
                    '1' => __('Actif'),
                    '0' => __('Inactif')
                ], [
                    'class' => 'form-control selectpicker'
                ]) ?>
            </div>
        </div>

        <table class="table table-bordered table-hover" id="kt_datatable">
            <thead>
                <tr>
                    <th class="text-center" width="5%">
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" value="1" />
                        </div>
                    </th>
                    <th><?= __('Produits') ?></th>
                    <th><?= __('Poids/Taille') ?></th>
                    <th><?= __('Unité') ?></th>
                    <th><?= __('Par défaut') ?></th>
                    <th><?= __('Statut') ?></th>
                    <th class="text-center" width="15%"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productPackages as $productPackage): ?>
                <tr>
                    <td class="text-center">
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" value="<?= $productPackage->id ?>" />
                        </div>
                    </td>
                    <td>
                        <?php if (!empty($productPackage->products)): ?>
                            <ul class="list-unstyled mb-0">
                                <?php foreach ($productPackage->products as $product): ?>
                                    <li><?= h($product->title) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <?= __('Aucun produit associé') ?>
                        <?php endif; ?>
                    </td>
                    <td><?= h($productPackage->weight) ?></td>
                    <td><?= h($productPackage->unit) ?></td>
                    <td><?= $productPackage->is_default ? __('Oui') : __('Non') ?></td>
                    <td><?= $productPackage->statut ? __('Actif') : __('Inactif') ?></td>
                    <td class="text-center">
                        <?= $this->Html->link(__('Voir'), ['action' => 'view', $productPackage->id], ['class' => 'btn btn-sm btn-light-primary']) ?>
                        <?= $this->Html->link(__('Modifier'), ['action' => 'edit', $productPackage->id], ['class' => 'btn btn-sm btn-light-warning']) ?>
                        <?= $this->Form->postLink(__('Supprimer'), ['action' => 'delete', $productPackage->id], ['class' => 'btn btn-sm btn-light-danger', 'confirm' => __('Êtes-vous sûr de vouloir supprimer cet emballage produit ?')]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$this->Html->script('bootstrap-select.min', ['block' => true]);
$this->Html->css('bootstrap-select.min', ['block' => true]);
$this->Html->script('product-packages', ['block' => true]);
?>

<?php $this->start('script'); ?>
<script>
    $(document).ready(function() {
        $('.selectpicker').selectpicker();
    });
</script>
<?php $this->end(); ?> 