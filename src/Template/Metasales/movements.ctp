<div class="container">
    <h2>Inventaire - Mouvements par Utilisateur et Pack</h2>

    <div class="filter-form" style="margin-bottom: 20px;">
        <?= $this->Form->create(null, ['type' => 'get']) ?>
        <div style="display:flex; gap: 12px; align-items: flex-end;">
            <div>
                <?= $this->Form->control('user_id', [
                    'label' => 'Utilisateur',
                    'type' => 'select',
                    'options' => $users,
                    'empty' => '-- Sélectionner --',
                    'value' => $selectedUserId
                ]) ?>
            </div>
            <div>
                <?= $this->Form->control('pack_id', [
                    'label' => 'Pack',
                    'type' => 'select',
                    'options' => $packs,
                    'empty' => '-- Sélectionner --',
                    'value' => $selectedPackId
                ]) ?>
            </div>
            <div>
                <?= $this->Form->button('Filtrer', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <?= $this->Form->end() ?>
    </div>

    <?php if (!empty($movements)): ?>
        <table class="table table-striped" style="width:100%;">
            <thead>
                <tr>
                    <th>Inventaire (Code)</th>
                    <th>Utilisateur</th>
                    <th>Qté Initiale</th>
                    <th>Qté Finale</th>
                    <th>Écart</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movements as $mvt): ?>
                    <tr>
                        <td><?= h($mvt['inventory']->code) ?></td>
                        <td><?= h($mvt['inventory']->user->firstname . ' ' . $mvt['inventory']->user->lastname) ?></td>
                        <td><?= $mvt['initial'] ? number_format((float)$mvt['initial']->quantity, 2, ',', ' ') : '-' ?></td>
                        <td><?= $mvt['final'] ? number_format((float)$mvt['final']->quantity, 2, ',', ' ') : '-' ?></td>
                        <td><?= number_format((float)$mvt['difference'], 2, ',', ' ') ?></td>
                        <td><?= $mvt['inventory']->created ? $mvt['inventory']->created->format('d/m/Y H:i') : '' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($selectedUserId && $selectedPackId): ?>
        <div class="alert alert-info">Aucun mouvement trouvé pour cette sélection.</div>
    <?php else: ?>
        <div class="alert alert-secondary">Veuillez sélectionner un utilisateur et un pack pour afficher les mouvements.</div>
    <?php endif; ?>
</div>
