<?php $this->extend('/Common/crud'); ?>
<?php $this->loadHelper('Form', [ 'templates' => 'app_form' ]); 

$this->assign('title', 'Détails du produit : ' . h($product->title));
// Add an edit button to the header, similar to how 'Imprimer' was in Packs/view.ctp
$editButton = $this->Html->link('Modifier ce produit', ['action' => 'edit', $product->id], ['class' => 'btn btn-success font-weight-bold']);
$this->assign('edit', $editButton); // 'edit' is a block often used in crud.ctp for header actions
?>
<div class="card-body">
    <div class="row pb-4">
        <div class="col-lg-8">
            <h3><?= h($product->title) ?></h3>
            <table class="table table-bordered table-hover">
                <tr>
                    <th scope="row"><?= __('Référence') ?></th>
                    <td><?= h($product->reference) ?></td>
                </tr>
                <?php if ($product->has('category') && $product->category): ?>
                <tr>
                    <th scope="row"><?= __('Catégorie') ?></th>
                    <td><?= $this->Html->link($product->category->title, ['controller' => 'Categories', 'action' => 'view', $product->category->id]) ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($product->has('unite') && $product->unite): ?>
                <tr>
                    <th scope="row"><?= __('Unité') ?></th>
                    <td><?= $this->Html->link($product->unite->title, ['controller' => 'Unites', 'action' => 'view', $product->unite->id]) ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($product->has('supplier') && $product->supplier): ?>
                <tr>
                    <th scope="row"><?= __('Fournisseur') ?></th>
                    <td><?= $this->Html->link($product->supplier->name, ['controller' => 'Suppliers', 'action' => 'view', $product->supplier->id]) ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th scope="row"><?= __('Prix d\'achat') ?></th>
                    <td><?= $this->Number->currency($product->buyingprice) ?></td>
                </tr>
                <tr>
                    <th scope="row"><?= __('Prix de vente') ?></th>
                    <td><?= $this->Number->currency($product->sellingprice) ?></td>
                </tr>
                <tr>
                    <th scope="row"><?= __('Commission') ?></th>
                    <td><?= $this->Number->format($product->commission) ?></td>
                </tr>
                <tr>
                    <th scope="row"><?= __('Statut') ?></th>
                    <td><?= $product->statut ? __('Actif') : __('Innactif') ?></td>
                </tr>
                <?php if ($product->has('company') && $product->company): ?>
                <tr>
                    <th scope="row"><?= __('Société') ?></th>
                    <td><?= $this->Html->link($product->company->name, ['controller' => 'Companies', 'action' => 'view', $product->company->id]) ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th scope="row"><?= __('Créé le') ?></th>
                    <td><?= h($product->created) ?></td>
                </tr>
                <tr>
                    <th scope="row"><?= __('Modifié le') ?></th>
                    <td><?= h($product->modified) ?></td>
                </tr>
            </table>
        </div>
        <div class="col-lg-4">
            <?php if ($product->photo): // Assuming 'photo' is a field in the product entity or related Photo entity ?>
                <h4><?= __('Photo') ?></h4>
                <?php 
                // This assumes product->photo is the direct path or you have a helper.
                // Adjust if product photo is stored in a related 'Photos' table like in Packs.
                // For now, let's assume a simple field or a direct path.
                // If it's like Packs (Photos table with dir and photo fields):
                // $photoPath = ($product->has('photos') && !empty($product->photos)) ? $product->photos[0]->dir . '/' . $product->photos[0]->photo : 'img/unvailable.jpg';
                // For simplicity, if $product->photo is just the filename in webroot/img/products/
                $photoPath = 'img/products/' . $product->photo; // Placeholder, adjust to your photo logic
                // A more robust way if photo is in Photos table (like PacksController implies for products search)
                // Need to ensure $product is contained with Photos in controller if this is the case.
                // $product = $this->Products->get($id, ['contain' => ['Categories', ..., 'Photos']]);
                // if ($product->has('photos') && !empty($product->photos[0])) {
                //    $photoPath = $product->photos[0]->dir . DS . $product->photos[0]->photo;
                // } else {
                //    $photoPath = 'img/unvailable.jpg';
                // }
                // For now, using a placeholder if direct field `photo` is not available
                // $actualPhotoPath = $product->photo ? 'files/products/photo/' . $product->photo : 'img/unvailable.jpg'; // Example path
                // Let's assume the photo path is directly accessible or handled by a helper/entity virtual field
                // For now, this part is a placeholder for how image is displayed.
                // echo $this->Html->image($actualPhotoPath, ['alt' => h($product->title), 'class' => 'img-fluid']);
                echo "<!-- Placeholder for product image -->";
                if (!empty($product->photo_path)) { // Assuming a virtual field or helper sets this
                     echo $this->Html->image($product->photo_path, ['alt' => h($product->title), 'class' => 'img-fluid']);
                } elseif ($product->photo && is_string($product->photo)) { // If photo field stores filename
                     echo $this->Html->image('products_photos/' . $product->photo, ['alt' => h($product->title), 'class' => 'img-fluid', 'style' => 'max-height: 300px;']);
                } else {
                     echo $this->Html->image('unvailable.jpg', ['alt' => 'Pas d\'image', 'class' => 'img-fluid']);
                }
                ?>
            <?php endif; ?>
        </div>
    </div>

    <?php // Omitted complex AJAX history sections from Packs/view.ctp for now. ?>
    <?php // These would require significant backend changes for Products. ?>
    <?php /*
    <div class="row">
        <div class="col-lg-4 col-6 pb-2">
            <a href="#" class="card card-custom bg-dark bg-hover-state-dark mb-0">
                <div class="text-inverse-dark font-weight-bolder font-size-h5 my-1 mx-5"> ACHATS <p class="font-weight-bold text-inverse-dark font-size-sm achats">(Historique des achats)</p></div>
            </a>
        </div>
        <div class="col-lg-4 col-6">
            <a href="#" class="card card-custom bg-dark bg-hover-state-dark mb-0">
                <div class="text-inverse-dark font-weight-bolder font-size-h5 my-1 mx-5">VENTES <p class="font-weight-bold text-inverse-dark font-size-sm ventes">(Historique des ventes)</p></div>
            </a>
        </div>
        <div class="col-lg-4 col-6">
            <a href="#" class="card card-custom bg-dark bg-hover-state-dark mb-0">
                <div class="text-inverse-dark font-weight-bolder font-size-h5 my-1 mx-2">PRIX <p class="font-weight-bold text-inverse-dark font-size-sm prices">(Historique des changement de prix)</p></div>
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class=" pr-5">
            <div class="col-xl-6 col-sm-6">
                <input type="text" class="form-control" name="daterange" id="daterange" placeholder="Sélectionner les dates" readonly="">
            </div>
        </div>
    </div>
    <div class="infos">
    </div>
    */ ?>

    <?php // Display related data if needed, similar to baked template but styled if necessary ?>
    <?php if (!empty($product->packproducts)): ?>
    <div class="related pt-4 mt-4 border-top">
        <h4><?= __('Produit présent dans les Packs suivants') ?></h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col"><?= __('Pack') ?></th>
                    <th scope="col"><?= __('Quantité dans le pack') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($product->packproducts as $packproduct): ?>
            <tr>
                <td><?= $packproduct->has('pack') ? $this->Html->link($packproduct->pack->title, ['controller' => 'Packs', 'action' => 'view', $packproduct->pack_id]) : h($packproduct->pack_id) ?></td>
                <td><?= h($packproduct->quantity) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('Voir Packproduct'), ['controller' => 'Packproducts', 'action' => 'view', $packproduct->id], ['class' => 'btn btn-xs btn-info']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <?php // Add other related data sections if necessary (e.g., Orderpackproducts, Supporderproducts, Whproducts) ?>

</div>
   
<?php // Omitted JavaScript for AJAX history loading and daterangepicker from Packs/view.ctp ?>
<?php /*
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    // AJAX and daterangepicker JS from Packs/view.ctp would go here if implemented for Products
<?= $this->Html->scriptEnd(); ?>
*/ ?>
