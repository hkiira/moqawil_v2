<?php $this->extend('/Common/crud'); ?>
<?php $this->loadHelper('Form', [ 'templates' => 'app_form' ]); 

// Use $product variable passed from ProductsController
$this->assign('objet',$this->Form->create($product,['type'=>'file','id'=>'kt_form_1'])); 
$this->assign('title', 'Modifier le produit : ' . h($product->title)); // Changed title
$this->assign('subtitle', 'Vous pouvez modifier les détails du produit ici.'); // Changed subtitle
$this->assign('action', $this->Html->link(__('Modifier le produit'), ['action' => 'edit', $product->id], ['class' => 'btn btn-primary']));
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= __('Modifier un produit') ?></h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mb-3">
                <?= $this->Form->control('product_packages._ids', [
                    'options' => $productPackages,
                    'class' => 'form-control select2',
                    'multiple' => true,
                    'label' => __('Emballages'),
                    'required' => true
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $this->Form->control('title', [
                    'class' => 'form-control',
                    'label' => __('Titre'),
                    'required' => true
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $this->Form->control('category_id', [
                    'options' => $categories,
                    'class' => 'form-control selectpicker',
                    'label' => __('Catégorie'),
                    'required' => true
                ]) ?>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <?= $this->Form->control('description', [
                    'type' => 'textarea',
                    'class' => 'form-control',
                    'label' => __('Description')
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
            placeholder: "<?= __('Sélectionner des emballages') ?>",
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
