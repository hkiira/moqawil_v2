<?php
    $this->assign('title', 'Ajouter un emballage produit');
    $this->assign('action', $this->Html->link(__('Nouvel emballage produit'), ['action' => 'add'], ['class' => 'btn btn-primary']));
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= __('Ajouter un emballage produit') ?></h3>
    </div>
    <div class="card-body">
        <?= $this->Form->create($productPackage) ?>
        <div class="row">
            <div class="col-md-12 mb-3">
                <?= $this->Form->control('products._ids', [
                    'options' => $products,
                    'class' => 'form-control select2',
                    'multiple' => true,
                    'label' => __('Produits'),
                    'required' => true
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $this->Form->control('weight', [
                    'type' => 'number',
                    'step' => '0.01',
                    'class' => 'form-control',
                    'label' => __('Poids/Taille'),
                    'required' => true
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $this->Form->control('unit', [
                    'options' => [
                        'kg' => 'kg',
                        'g' => 'g',
                        'l' => 'l',
                        'ml' => 'ml',
                        'pcs' => 'pcs'
                    ],
                    'class' => 'form-control selectpicker',
                    'label' => __('Unité'),
                    'required' => true
                ]) ?>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <?= $this->Form->control('is_default', [
                    'type' => 'checkbox',
                    'class' => 'form-check-input',
                    'label' => __('Emballage par défaut')
                ]) ?>
            </div>
        </div>
        <div class="card-footer">
            <?= $this->Form->button(__('Enregistrer'), ['class' => 'btn btn-primary']) ?>
            <?= $this->Html->link(__('Retour'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>

<?php
$this->Html->script('bootstrap-select.min', ['block' => true]);
$this->Html->css('bootstrap-select.min', ['block' => true]);
$this->Html->css('select2.min', ['block' => true]);
$this->Html->script('select2.min', ['block' => true]);
?>

<?php $this->start('script'); ?>
<script>
    $(document).ready(function() {
        $('.selectpicker').selectpicker();
        
        $('.select2').select2({
            placeholder: "<?= __('Sélectionner des produits') ?>",
            allowClear: true,
            language: {
                noResults: function() {
                    return "<?= __('Aucun résultat trouvé') ?>";
                }
            }
        });
    });
</script>
<?php $this->end(); ?> 