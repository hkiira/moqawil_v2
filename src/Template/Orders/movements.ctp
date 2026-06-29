<?php
$this->assign('title', "Mouvements d'inventaire");
$this->assign('subtitle', 'Suivi des écarts par utilisateur et pack');
?>

<div class="card card-custom">
    <div class="card-header py-4">
        <div class="card-title">
            <h3 class="card-label mb-0">Filtres</h3>
        </div>
    </div>
    <div class="card-body">
        <?= $this->Form->create(null, ['type' => 'get']) ?>
        <div class="form-row align-items-end">
            <div class="form-group col-md-4">
                <?= $this->Form->control('user_id', [
                    'label' => 'Utilisateur',
                    'type' => 'select',
                    'options' => $users,
                    'empty' => '-- Sélectionner --',
                    'value' => $selectedUserId,
                    'class' => 'form-control selectpicker'
                ]) ?>
            </div>
            <div class="form-group col-md-4">
                <?= $this->Form->control('pack_id', [
                    'label' => 'Pack',
                    'type' => 'select',
                    'options' => $packs,
                    'empty' => '-- Sélectionner --',
                    'value' => $selectedPackId,
                    'class' => 'form-control selectpicker'
                ]) ?>
            </div>
            <div class="form-group col-md-4">
                <?= $this->Form->button('Filtrer', ['class' => 'btn btn-primary font-weight-bold']) ?>
                <?php if ($selectedUserId || $selectedPackId): ?>
                    <a href="<?= $this->Url->build(['action' => 'movements']) ?>" class="btn btn-light font-weight-bold ml-2">Réinitialiser</a>
                <?php endif; ?>
            </div>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>

<div class="card card-custom mt-6">
    <div class="card-header py-4">
        <div class="card-title">
            <h3 class="card-label mb-0">Résultats</h3>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($movements)): ?>
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th style="min-width: 140px;">Inventaire</th>
                            <th style="min-width: 160px;">Utilisateur</th>
                            <th style="min-width: 180px;">Pack</th>
                            <th class="text-right" style="width: 110px;">Qté initiale</th>
                            <th class="text-right" style="width: 110px;">Qté finale</th>
                            <th class="text-right" style="width: 110px;">Écart</th>
                            <th style="min-width: 140px;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movements as $mvt): ?>
                            <?php
                                $packTitle = ($mvt['initial'] && $mvt['initial']->pack) ? $mvt['initial']->pack->title : (($mvt['final'] && $mvt['final']->pack) ? $mvt['final']->pack->title : '');
                                $unitAbrev = '';
                                $packEntity = $mvt['initial'] ? $mvt['initial']->pack : ($mvt['final'] ? $mvt['final']->pack : null);
                                if ($packEntity && !empty($packEntity->packunites)) {
                                    foreach ($packEntity->packunites as $pu) {
                                        if ($pu->statut == 1 || $pu->statut == 3) {
                                            $unitAbrev = $pu->unite->abrev;
                                            break;
                                        }
                                    }
                                }
                                
                                // Measurement-based display conversion
                                $convertQty = function($ip) use ($packEntity) {
                                    if (!$ip) { return null; }
                                    $raw = (float)$ip->quantity;
                                    // If pack has measurement_quantity (base per unit), convert quantities to base unit
                                    if ($packEntity && isset($packEntity->measurement_quantity) && (float)$packEntity->measurement_quantity > 0) {
                                        // Common case: show base-unit quantity derived from measurement quantity
                                        return $raw * (float)$packEntity->measurement_quantity/1000;
                                    }
                                    return $raw;
                                };
                                $initialQtyDisp = $convertQty($mvt['initial']);
                                $finalQtyDisp = $convertQty($mvt['final']);
                                $differenceDisp = ($finalQtyDisp !== null ? $finalQtyDisp : 0) - ($initialQtyDisp !== null ? $initialQtyDisp : 0);

                                $diffClass = 'text-muted';
                                if ($mvt['difference'] > 0) {
                                    $diffClass = 'text-success font-weight-bold';
                                } elseif ($mvt['difference'] < 0) {
                                    $diffClass = 'text-danger font-weight-bold';
                                }
                            ?>
                            <tr>
                                <td><?= h($mvt['inventory']->code) ?></td>
                                <td><?= h($mvt['inventory']->user->firstname . ' ' . $mvt['inventory']->user->lastname) ?></td>
                                <td>
                                    <div class="font-weight-bold"><?= h($packTitle) ?></div>
                                    <?php if ($unitAbrev): ?>
                                        <div class="text-muted small">Unité: <?= h($unitAbrev) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right"><?= $initialQtyDisp !== null ? number_format($initialQtyDisp, 2, ',', ' ') : '-' ?></td>
                                <td class="text-right"><?= $finalQtyDisp !== null ? number_format($finalQtyDisp, 2, ',', ' ') : '-' ?></td>
                                <td class="text-right">
                                    <span class="<?= $diffClass ?>"><?= number_format($differenceDisp, 2, ',', ' ') ?></span>
                                </td>
                                <td><?= $mvt['inventory']->created ? $mvt['inventory']->created->format('d/m/Y H:i') : '' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($selectedUserId && $selectedPackId): ?>
            <div class="alert alert-info mb-0">Aucun mouvement trouvé pour cette sélection.</div>
        <?php else: ?>
            <div class="alert alert-secondary mb-0">Sélectionnez un utilisateur et un pack pour afficher les mouvements.</div>
        <?php endif; ?>
    </div>
</div>
