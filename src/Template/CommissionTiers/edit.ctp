<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CommissionTier $commissionTier
 */
$this->assign('title', 'Modifier un Palier de Commission');
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= __('Modifier le Palier: {0}', h($commissionTier->name)) ?></h3>
                <div class="card-toolbar">
                    <?= $this->Form->postLink(
                        __('Supprimer'),
                        ['action' => 'delete', $commissionTier->id],
                        ['confirm' => __('Voulez-vous vraiment supprimer ce palier?'), 'class' => 'btn btn-danger']
                    ) ?>
                    <?= $this->Html->link(__('Retour à la liste'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                </div>
            </div>
            <div class="card-body">
                <?= $this->Form->create($commissionTier) ?>
                <div class="row">
                    <div class="col-md-12">
                        <?= $this->Form->control('name', [
                            'label' => __('Nom du palier'),
                            'class' => 'form-control',
                            'placeholder' => __('Ex: Palier 1: 10-20 packs'),
                            'required' => true
                        ]) ?>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <?= $this->Form->control('min_quantity', [
                            'label' => __('Quantité Minimum (packs)'),
                            'class' => 'form-control',
                            'type' => 'number',
                            'step' => '0.01',
                            'min' => '0',
                            'required' => true
                        ]) ?>
                        <small class="form-text text-muted"><?= __('Nombre minimum de packs pour ce palier') ?></small>
                    </div>
                    <div class="col-md-6">
                        <?= $this->Form->control('max_quantity', [
                            'label' => __('Quantité Maximum (packs)'),
                            'class' => 'form-control',
                            'type' => 'number',
                            'step' => '0.01',
                            'min' => '0'
                        ]) ?>
                        <small class="form-text text-muted"><?= __('Laisser vide pour illimité') ?></small>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <?= $this->Form->control('commission_type', [
                            'label' => __('Type de Commission'),
                            'class' => 'form-control',
                            'options' => [
                                'fixed' => __('Fixe (DH)'),
                                'percentage' => __('Pourcentage (%)')
                            ],
                            'required' => true,
                            'id' => 'commission_type'
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $this->Form->control('commission_value', [
                            'label' => __('Valeur de la Commission'),
                            'class' => 'form-control',
                            'type' => 'number',
                            'step' => '0.01',
                            'min' => '0',
                            'required' => true,
                            'id' => 'commission_value'
                        ]) ?>
                        <small class="form-text text-muted" id="commission_hint">
                            <?= __('Montant en DH ou pourcentage selon le type sélectionné') ?>
                        </small>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <?= $this->Form->control('apply_type', [
                            'label' => __('Mode d\'Application'),
                            'class' => 'form-control',
                            'options' => [
                                'all' => __('Global (tous les packs)'),
                                'single' => __('Individuel (par pack)'),
                                'combined' => __('Combiné (somme des packs sélectionnés)')
                            ],
                            'required' => true,
                            'id' => 'apply_type'
                        ]) ?>
                        <small class="form-text text-muted"><?= __('Comment calculer la commission pour plusieurs packs') ?></small>
                    </div>
                </div>
                
                <div class="row mt-3" id="packs-section">
                    <div class="col-md-12">
                        <label><?= __('Packs Spécifiques (Optionnel)') ?></label>
                        <small class="form-text text-muted d-block mb-3"><?= __('Sélectionnez les packs pour les modes individuel/combiné. Laisser vide pour le mode global.') ?></small>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="packs-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;"><?= __('Sélectionner') ?></th>
                                        <th style="width: 80px;"><?= __('Image') ?></th>
                                        <th><?= __('Code') ?></th>
                                        <th><?= __('Nom du Pack') ?></th>
                                        <th><?= __('Marque') ?></th>
                                        <th><?= __('Type') ?></th>
                                        <th><?= __('Stock') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $selectedPacks = [];
                                    if (!empty($commissionTier->packs)) {
                                        $selectedPacks = array_map(function($p) { return $p->id; }, $commissionTier->packs);
                                    }
                                    
                                    if (!empty($fullPacks)): 
                                        foreach ($fullPacks as $pack): 
                                    ?>
                                        <tr>
                                            <td class="text-center">
                                                <?= $this->Form->checkbox('packs._ids[]', [
                                                    'value' => $pack->id, 
                                                    'checked' => in_array($pack->id, $selectedPacks),
                                                    'hiddenField' => false
                                                ]) ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (!empty($pack->photo) && !empty($pack->photo->title)): ?>
                                                    <img src="<?= $this->Url->build($pack->photo->dir.'/'.$pack->photo->photo) ?>" alt="<?= h($pack->title) ?>" class="img-thumbnail" style="max-width: 70px; max-height: 70px;">
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">No Image</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= h($pack->code) ?></td>
                                            <td><strong><?= h($pack->title) ?></strong></td>
                                            <td><?= !empty($pack->brand) ? h($pack->brand->name) : '<span class="text-muted">-</span>' ?></td>
                                            <td><?= !empty($pack->packtype) ? h($pack->packtype->name) : '<span class="text-muted">-</span>' ?></td>
                                            <td><span class="badge badge-info"><?= $pack->gstock ?></span></td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="checkbox-inline">
                                <?= $this->Form->control('is_active', [
                                    'label' => __('Actif'),
                                    'type' => 'checkbox'
                                ]) ?>
                            </div>
                            <small class="form-text text-muted"><?= __('Seuls les paliers actifs seront utilisés pour le calcul') ?></small>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <?= $this->Form->button(__('Enregistrer'), ['class' => 'btn btn-primary']) ?>
                        <?= $this->Html->link(__('Annuler'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var typeSelect = document.getElementById('commission_type');
    var valueInput = document.getElementById('commission_value');
    var hint = document.getElementById('commission_hint');
    var applyTypeSelect = document.getElementById('apply_type');
    var packsSection = document.getElementById('packs-section');
    
    function updateHint() {
        if (typeSelect.value === 'percentage') {
            hint.textContent = '<?= __("Pourcentage du montant total de la commande (ex: 2 pour 2%)") ?>';
            valueInput.setAttribute('max', '100');
        } else {
            hint.textContent = '<?= __("Montant fixe en DH par compensation") ?>';
            valueInput.removeAttribute('max');
        }
    }
    
    function updatePacksVisibility() {
        if (applyTypeSelect.value === 'all') {
            packsSection.style.display = 'none';
        } else {
            packsSection.style.display = 'block';
        }
    }
    
    typeSelect.addEventListener('change', updateHint);
    applyTypeSelect.addEventListener('change', updatePacksVisibility);
    updateHint();
    updatePacksVisibility();
});
</script>
