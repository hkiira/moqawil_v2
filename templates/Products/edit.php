<?= $this->Form->create($product) ?>
<fieldset>
    <legend><?= __('Edit Product') ?></legend>
    <?php
        echo $this->Form->control('reference');
        echo $this->Form->control('title');
        echo $this->Form->control('buyingprice');
        echo $this->Form->control('sellingprice');
        echo $this->Form->control('commission');
        echo $this->Form->control('category_id', ['options' => $categories]);
        echo $this->Form->control('unite_id', ['options' => $unites]);
        echo $this->Form->control('supplier_id', ['options' => $suppliers]);
    ?>
</fieldset>

<fieldset>
    <legend><?= __('Product Packages') ?></legend>
    <div id="product-packages">
        <?php foreach ($product->product_packages as $index => $package): ?>
        <div class="package-row">
            <?php
                echo $this->Form->control("product_packages.{$index}.id", [
                    'type' => 'hidden',
                    'value' => $package->id
                ]);
                echo $this->Form->control("product_packages.{$index}.weight", [
                    'label' => 'Weight/Size',
                    'type' => 'number',
                    'step' => '0.01',
                    'value' => $package->weight,
                    'required' => false
                ]);
                echo $this->Form->control("product_packages.{$index}.unit", [
                    'label' => 'Unit',
                    'options' => [
                        'kg' => 'Kilograms',
                        'g' => 'Grams',
                        'l' => 'Liters',
                        'ml' => 'Milliliters',
                        'pcs' => 'Pieces'
                    ],
                    'value' => $package->unit,
                    'required' => false
                ]);
                echo $this->Form->control("product_packages.{$index}.is_default", [
                    'label' => 'Default Package',
                    'type' => 'checkbox',
                    'checked' => $package->is_default,
                    'required' => false
                ]);
            ?>
            <button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.remove()">Remove</button>
        </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="btn btn-sm btn-primary" onclick="addPackageRow()">Add Another Package</button>
</fieldset>

<?= $this->Form->button(__('Submit')) ?>
<?= $this->Form->end() ?>

<script>
function addPackageRow() {
    const container = document.getElementById('product-packages');
    const packageCount = container.getElementsByClassName('package-row').length;
    
    const newRow = document.createElement('div');
    newRow.className = 'package-row';
    newRow.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Weight/Size</label>
                    <input type="number" name="product_packages[${packageCount}][weight]" class="form-control" step="0.01">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Unit</label>
                    <select name="product_packages[${packageCount}][unit]" class="form-control">
                        <option value="kg">Kilograms</option>
                        <option value="g">Grams</option>
                        <option value="l">Liters</option>
                        <option value="ml">Milliliters</option>
                        <option value="pcs">Pieces</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Default Package</label>
                    <input type="checkbox" name="product_packages[${packageCount}][is_default]" class="form-control">
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.parentElement.parentElement.remove()">×</button>
            </div>
        </div>
    `;
    
    container.appendChild(newRow);
}
</script> 