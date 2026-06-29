<?php 
    $this->assign('title', 'Paliers de Commission');
    $test= '
            <a href="/commission-tiers/add" class="btn btn-primary font-weight-bolder">
                <span class="svg-icon svg-icon-md">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24" />
                        <circle fill="#000000" cx="9" cy="15" r="6" />
                        <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3" />
                    </g>
                </svg>
            </span>Nouveau Palier</a>';

    $this->assign('actionsubh', $test);
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th><?= $this->Paginator->sort('id', '#') ?></th>
                                <th><?= $this->Paginator->sort('name', __('Nom')) ?></th>
                                <th><?= $this->Paginator->sort('apply_type', __('Mode')) ?></th>
                                <th><?= __('Packs') ?></th>
                                <th><?= $this->Paginator->sort('min_quantity', __('Quantité Min')) ?></th>
                                <th><?= $this->Paginator->sort('max_quantity', __('Quantité Max')) ?></th>
                                <th><?= $this->Paginator->sort('commission_type', __('Type')) ?></th>
                                <th><?= $this->Paginator->sort('commission_value', __('Valeur')) ?></th>
                                <th><?= $this->Paginator->sort('is_active', __('Statut')) ?></th>
                                <th><?= $this->Paginator->sort('created', __('Créé le')) ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($commissionTiers as $tier): ?>
                            <tr>
                                <td><?= $this->Number->format($tier->id) ?></td>
                                <td><?= h($tier->name) ?></td>
                                <td>
                                    <?php 
                                    $applyTypeBadges = [
                                        'all' => 'badge-secondary',
                                        'single' => 'badge-primary',
                                        'combined' => 'badge-success'
                                    ];
                                    $applyTypeLabels = [
                                        'all' => 'Global',
                                        'single' => 'Individuel',
                                        'combined' => 'Combiné'
                                    ];
                                    ?>
                                    <span class="badge <?= $applyTypeBadges[$tier->apply_type] ?>">
                                        <?= $applyTypeLabels[$tier->apply_type] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($tier->packs)): ?>
                                        <?php foreach ($tier->packs as $pack): ?>
                                            <span class="badge badge-primary"><?= h($pack->name) ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Aucun</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $this->Number->format($tier->min_quantity, ['precision' => 0]) ?> packs</td>
                                <td><?= $tier->max_quantity ? $this->Number->format($tier->max_quantity, ['precision' => 0]) . ' packs' : '<span class="badge badge-info">Illimité</span>' ?></td>
                                <td>
                                    <?php if ($tier->commission_type === 'fixed'): ?>
                                        <span class="badge badge-primary">Fixe</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Pourcentage</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($tier->commission_type === 'fixed'): ?>
                                        <?= $this->Number->format($tier->commission_value, ['precision' => 2]) ?> DH
                                    <?php else: ?>
                                        <?= $this->Number->format($tier->commission_value, ['precision' => 2]) ?> %
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($tier->is_active): ?>
                                        <span class="badge badge-success">Actif</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactif</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= h($tier->created->format('Y-m-d H:i')) ?></td>
                                <td class="actions">
                                    <?= $this->Html->link(
                                        '<i class="fa fa-eye"></i>',
                                        ['action' => 'view', $tier->id],
                                        ['class' => 'btn btn-sm btn-info', 'escape' => false, 'title' => __('Voir')]
                                    ) ?>
                                    <?= $this->Html->link(
                                        '<i class="fa fa-edit"></i>',
                                        ['action' => 'edit', $tier->id],
                                        ['class' => 'btn btn-sm btn-warning', 'escape' => false, 'title' => __('Modifier')]
                                    ) ?>
                                    <?= $this->Form->postLink(
                                        '<i class="fa fa-power-off"></i>',
                                        ['action' => 'toggleActive', $tier->id],
                                        ['class' => 'btn btn-sm ' . ($tier->is_active ? 'btn-secondary' : 'btn-success'), 'escape' => false, 'title' => $tier->is_active ? __('Désactiver') : __('Activer'), 'confirm' => __('Voulez-vous vraiment changer le statut de ce palier?')]
                                    ) ?>
                                    <?= $this->Form->postLink(
                                        '<i class="fa fa-trash"></i>',
                                        ['action' => 'delete', $tier->id],
                                        ['class' => 'btn btn-sm btn-danger', 'escape' => false, 'title' => __('Supprimer'), 'confirm' => __('Voulez-vous vraiment supprimer ce palier #{0}?', $tier->id)]
                                    ) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="paginator mt-3">
                    <ul class="pagination">
                        <?= $this->Paginator->first('<< ' . __('Première')) ?>
                        <?= $this->Paginator->prev('< ' . __('Précédente')) ?>
                        <?= $this->Paginator->numbers() ?>
                        <?= $this->Paginator->next(__('Suivante') . ' >') ?>
                        <?= $this->Paginator->last(__('Dernière') . ' >>') ?>
                    </ul>
                    <p><?= $this->Paginator->counter(['format' => __('Page {{page}} sur {{pages}}, montrant {{current}} enregistrement(s) sur {{count}} au total')]) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
