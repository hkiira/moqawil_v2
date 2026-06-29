<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CommissionTier $commissionTier
 */
$this->assign('title', 'Détails du Palier de Commission');
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= h($commissionTier->name) ?></h3>
                <div class="card-toolbar">
                    <?= $this->Html->link(__('Modifier'), ['action' => 'edit', $commissionTier->id], ['class' => 'btn btn-warning']) ?>
                    <?= $this->Form->postLink(__('Supprimer'), ['action' => 'delete', $commissionTier->id], ['confirm' => __('Voulez-vous vraiment supprimer ce palier?'), 'class' => 'btn btn-danger']) ?>
                    <?= $this->Html->link(__('Retour à la liste'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px;"><?= __('ID') ?></th>
                        <td><?= $this->Number->format($commissionTier->id) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Nom') ?></th>
                        <td><?= h($commissionTier->name) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Plage de Quantité') ?></th>
                        <td>
                            <strong><?= $this->Number->format($commissionTier->min_quantity, ['precision' => 0]) ?></strong> packs
                            - 
                            <?php if ($commissionTier->max_quantity): ?>
                                <strong><?= $this->Number->format($commissionTier->max_quantity, ['precision' => 0]) ?></strong> packs
                            <?php else: ?>
                                <span class="badge badge-info">Illimité</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?= __('Type de Commission') ?></th>
                        <td>
                            <?php if ($commissionTier->commission_type === 'fixed'): ?>
                                <span class="badge badge-primary">Fixe</span>
                            <?php else: ?>
                                <span class="badge badge-success">Pourcentage</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?= __('Valeur de la Commission') ?></th>
                        <td>
                            <strong>
                                <?php if ($commissionTier->commission_type === 'fixed'): ?>
                                    <?= $this->Number->format($commissionTier->commission_value, ['precision' => 2]) ?> DH
                                <?php else: ?>
                                    <?= $this->Number->format($commissionTier->commission_value, ['precision' => 2]) ?> %
                                <?php endif; ?>
                            </strong>
                        </td>
                    </tr>
                    
                    <tr>
                        <th><?= __('Mode d\'Application') ?></th>
                        <td>
                            <?php 
                            $applyTypeLabels = [
                                'all' => 'Global (tous les packs)',
                                'single' => 'Individuel (par pack)',
                                'combined' => 'Combiné (somme des packs)'
                            ];
                            $applyTypeBadges = [
                                'all' => 'badge-secondary',
                                'single' => 'badge-primary',
                                'combined' => 'badge-success'
                            ];
                            ?>
                            <span class="badge <?= $applyTypeBadges[$commissionTier->apply_type] ?>">
                                <?= $applyTypeLabels[$commissionTier->apply_type] ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th><?= __('Packs Sélectionnés') ?></th>
                        <td>
                            <?php if (!empty($commissionTier->packs)): ?>
                                <?php foreach ($commissionTier->packs as $pack): ?>
                                    <?= $this->Html->link($pack->title, ['controller' => 'Packs', 'action' => 'view', $pack->id], ['class' => 'badge badge-primary mr-1']) ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="badge badge-secondary">Aucun pack spécifique</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?= __('Statut') ?></th>
                        <td>
                            <?php if ($commissionTier->is_active): ?>
                                <span class="badge badge-success">Actif</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?= __('Créé le') ?></th>
                        <td><?= h($commissionTier->created->format('Y-m-d H:i:s')) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Modifié le') ?></th>
                        <td><?= h($commissionTier->modified->format('Y-m-d H:i:s')) ?></td>
                    </tr>
                </table>
                
                <div class="alert alert-info mt-4">
                    <h5><i class="fa fa-info-circle"></i> <?= __('Comment ça marche?') ?></h5>
                    <p class="mb-0">
                        <?php if ($commissionTier->has('pack')): ?>
                            <?php if ($commissionTier->commission_type === 'fixed'): ?>
                                <?= __('Ce palier attribue une commission fixe de <strong>{0} DH</strong> pour le pack <strong>{1}</strong> lorsque la quantité de ce pack est entre <strong>{2}</strong> et <strong>{3}</strong> unités.', 
                                    $this->Number->format($commissionTier->commission_value, ['precision' => 2]),
                                    h($commissionTier->pack->name),
                                    $this->Number->format($commissionTier->min_quantity, ['precision' => 0]),
                                    $commissionTier->max_quantity ? $this->Number->format($commissionTier->max_quantity, ['precision' => 0]) : '∞'
                                ) ?>
                            <?php else: ?>
                                <?= __('Ce palier attribue une commission de <strong>{0}%</strong> du montant total pour le pack <strong>{1}</strong> lorsque la quantité de ce pack est entre <strong>{2}</strong> et <strong>{3}</strong> unités.', 
                                    $this->Number->format($commissionTier->commission_value, ['precision' => 2]),
                                    h($commissionTier->pack->name),
                                    $this->Number->format($commissionTier->min_quantity, ['precision' => 0]),
                                    $commissionTier->max_quantity ? $this->Number->format($commissionTier->max_quantity, ['precision' => 0]) : '∞'
                                ) ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if ($commissionTier->commission_type === 'fixed'): ?>
                                <?= __('Ce palier attribue une commission fixe de <strong>{0} DH</strong> lorsque le nombre total de packs traités est entre <strong>{1}</strong> et <strong>{2}</strong> packs.', 
                                    $this->Number->format($commissionTier->commission_value, ['precision' => 2]),
                                    $this->Number->format($commissionTier->min_quantity, ['precision' => 0]),
                                    $commissionTier->max_quantity ? $this->Number->format($commissionTier->max_quantity, ['precision' => 0]) : '∞'
                                ) ?>
                            <?php else: ?>
                                <?= __('Ce palier attribue une commission de <strong>{0}%</strong> du montant total des commandes lorsque le nombre total de packs traités est entre <strong>{1}</strong> et <strong>{2}</strong> packs.', 
                                    $this->Number->format($commissionTier->commission_value, ['precision' => 2]),
                                    $this->Number->format($commissionTier->min_quantity, ['precision' => 0]),
                                    $commissionTier->max_quantity ? $this->Number->format($commissionTier->max_quantity, ['precision' => 0]) : '∞'
                                ) ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Related Compensations -->
        <?php if (!empty($commissionTier->compensations)): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title"><?= __('Compensations Utilisant ce Palier') ?></h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th><?= __('ID') ?></th>
                                <th><?= __('Code') ?></th>
                                <th><?= __('Utilisateur') ?></th>
                                <th><?= __('Quantité Totale') ?></th>
                                <th><?= __('Montant Commission') ?></th>
                                <th><?= __('Date') ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($commissionTier->compensations as $compensation): ?>
                            <tr>
                                <td><?= h($compensation->id) ?></td>
                                <td><?= h($compensation->code) ?></td>
                                <td><?= $compensation->has('user') ? h($compensation->user->firstname . ' ' . $compensation->user->lastname) : '' ?></td>
                                <td><?= $this->Number->format($compensation->total_quantity, ['precision' => 0]) ?> packs</td>
                                <td><?= $this->Number->format($compensation->commission_amount, ['precision' => 2]) ?> DH</td>
                                <td><?= h($compensation->created->format('Y-m-d')) ?></td>
                                <td class="actions">
                                    <?= $this->Html->link(__('Voir'), ['controller' => 'Compensations', 'action' => 'view', $compensation->id], ['class' => 'btn btn-sm btn-info']) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
