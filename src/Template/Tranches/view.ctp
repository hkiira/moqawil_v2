<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tranch $tranch
 */
?>
<?php 
$this->extend('/Common/crud');
$this->assign('title', 'Détails de la tranche');
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= h($tranch->title) ?></h3>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <tr>
                <th scope="row"><?= __('Titre') ?></th>
                <td><?= h($tranch->title) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Code') ?></th>
                <td><?= h($tranch->code) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Quantité minimale') ?></th>
                <td><?= $this->Number->format($tranch->min) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Quantité maximale') ?></th>
                <td><?= $tranch->max ? $this->Number->format($tranch->max) : '∞' ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Type de remise') ?></th>
                <td>
                    <?php if ($tranch->has('remisetype')): ?>
                        <?= $this->Html->link($tranch->remisetype->title, ['controller' => 'Remisetypes', 'action' => 'view', $tranch->remisetype->id]) ?>
                        <code><?= h($tranch->remisetype->code) ?></code>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?= __('Remise') ?></th>
                <td>
                    <?php
                    $code = strtoupper(trim((string)($tranch->remisetype->code ?? '')));
                    if ($code === 'GRT' && $tranch->pack_id) {
                        echo h($tranch->pack->title) . ' (Qté: ' . h($tranch->remise) . ')';
                    } else {
                        echo $code === '%' ? h($tranch->remise) . '%' : ($code === 'RED' ? h($tranch->remise) . ' DH' : h($tranch->remise));
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?= __('Entreprise') ?></th>
                <td>
                    <?php if ($tranch->has('company')): ?>
                        <?= $this->Html->link($tranch->company->name, ['controller' => 'Companies', 'action' => 'view', $tranch->company->id]) ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?= __('Statut') ?></th>
                <td><?= $tranch->statut ? '<span class="badge badge-success">Actif</span>' : '<span class="badge badge-danger">Inactif</span>' ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Créé') ?></th>
                <td><?= h($tranch->created) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Modifié') ?></th>
                <td><?= h($tranch->modified) ?></td>
            </tr>
        </table>
    </div>
</div>

<?php if (!empty($tranch->trancheprices)): ?>
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Prix associés à cette tranche</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Pack</th>
                    <th>Type Client</th>
                    <th>Entrepôt</th>
                    <th>Prix</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tranch->trancheprices as $tp): ?>
                    <tr>
                        <td><?= h($tp->price->pack->title ?? 'N/A') ?></td>
                        <td><?= h($tp->price->customertype->name ?? 'Tous') ?></td>
                        <td><?= h($tp->price->warehouse->name ?? 'Tous') ?></td>
                        <td><?= $this->Number->format($tp->price->value, ['places' => 2]) ?> DH</td>
                        <td>
                            <?= $this->Html->link(__('Voir'), ['controller' => 'Trancheprices', 'action' => 'view', $tp->id], ['class' => 'btn btn-sm btn-info']) ?>
                            <?= $this->Html->link(__('Modifier'), ['controller' => 'Trancheprices', 'action' => 'edit', $tp->id], ['class' => 'btn btn-sm btn-warning']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<div class="mt-3">
    <?= $this->Html->link(__('Ajouter un prix pour cette tranche'), ['controller' => 'Trancheprices', 'action' => 'add', '?' => ['tranch_id' => $tranch->id]], ['class' => 'btn btn-success btn-sm']) ?>
</div>

<div class="mt-3">
    <?= $this->Html->link(__('Modifier'), ['action' => 'edit', $tranch->id], ['class' => 'btn btn-info']) ?>
    <?= $this->Html->link(__('Retour'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
    <?= $this->Form->postLink(__('Supprimer'), ['action' => 'delete', $tranch->id], ['confirm' => __('Êtes-vous sûr ?'), 'class' => 'btn btn-danger']) ?>
            <td><?= h($tranch->modified) ?></td>
        </tr>
    </table>
</div>
