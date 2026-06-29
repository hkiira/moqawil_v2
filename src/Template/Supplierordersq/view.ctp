<?php
$this->assign('title', 'Afficher la commande :' . $supplierorder->code);
?>
<div class="card card-custom card-sticky" id="kt_page_sticky_card">
    <div class="card-header">
        <div class="card-title">
            <h3 class="card-label">
                <?= $this->fetch('title') ?> <i class="mr-2"></i>
            </h3>
        </div>
        <div class="card-toolbar">
            <button onclick="goBack()" class="btn btn-light-primary font-weight-bolder mr-2">
                <i class="ki ki-long-arrow-back icon-xs"></i>Retour
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row justify-content-center py-8 px-8 py-md-8 px-md-0">
            <div class="col-md-12">
                <div class="d-flex justify-content-between flex-column flex-md-row">
                    <h1 class="display-4 font-weight-boldest mb-10"> <?= $supplierorder->supplier->name ?><br><?= $supplierorder->created->i18nFormat('dd/MM/yyyy')  ?>
                    </h1>
                    <div class="d-flex flex-column align-items-md-end px-0">
                        <span class=" d-flex flex-column align-items-md-end opacity-70">
                            <?= $supplierorder->supplier->adress->title ?>
                            <br>Téléphone: <?= $supplierorder->supplier->phone ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center py-8 px-8 px-md-0">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="pl-0 font-weight-bold text-muted  text-uppercase">Article</th>
                                <th class="text-right font-weight-bold text-muted text-uppercase">Quantité</th>
                                <th class="text-right font-weight-bold text-muted text-uppercase">P.U</th>
                                <th class="text-right pr-0 font-weight-bold text-muted text-uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $total = 0; ?>
                            <?php $totalremise = 0; ?>
                            <?php foreach ($supplierorder->supporderproducts as $key => $supporderproduct): ?>
                                <tr>
                                    <td><?= $supporderproduct->pack->title ?></td>
                                    <?php if ($supporderproduct->quantity % $supporderproduct->pack->packunites[0]->quantity): ?>
                                        <td class="text-right">
                                            <?php if (intVal($supporderproduct->quantity / $supporderproduct->pack->packunites[0]->quantity) > 0): ?>
                                                <?= intVal($supporderproduct->quantity / $supporderproduct->pack->packunites[0]->quantity) . ' ' . $supporderproduct->pack->packunites[0]->unite->abrev; ?>
                                                et <?= $supporderproduct->quantity % $supporderproduct->pack->packunites[0]->quantity . ' ' . $supporderproduct->pack->packunites[0]->unite->parentunite->abrev; ?> </td>

                                    <?php else: ?>
                                        <?= $supporderproduct->quantity % $supporderproduct->pack->packunites[0]->quantity . ' ' . $supporderproduct->pack->packunites[0]->unite->abrev ?> </td>
                                    <?php endif ?>
                                <?php else: ?>
                                    <td class="text-right">
                                        <?= $supporderproduct->quantity / $supporderproduct->pack->packunites[0]->quantity . ' ' . $supporderproduct->pack->packunites[0]->unite->abrev ?> </td>
                                    </td>
                                <?php endif ?>
                                <td class="text-right"><?= number_format($supporderproduct->price, 2, '.', '') ?></td>
                                <td class="text-right"><?= number_format(($supporderproduct->price * $supporderproduct->quantity), 2, '.', '') ?></td>
                                </tr>
                                <?php $total += ($supporderproduct->price * $supporderproduct->quantity) ?>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
<?= $this->Form->end() ?>
</div>
</div>

<div class="row justify-content-center bg-gray-100 py-8 px-8 py-md-10 px-md-0 mx-0">
    <div class="col-md-10">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th class="font-weight-bold text-muted  text-uppercase">TOTAL REMISE</th>
                        <th class="font-weight-bold text-muted  text-uppercase">STATUT DU PAIEMENT</th>
                        <th class="font-weight-bold text-muted  text-uppercase">TOTAL</th>
                        <th class="font-weight-bold text-muted  text-uppercase text-right">MONTANT TTC</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="font-weight-bolder">
                        <td><?= number_format(($totalremise), 2, '.', '') ?> DH</td>
                        <td>Success</td>
                        <td><?= number_format(($total), 2, '.', '') ?> DH</td>
                        <td class="text-primary font-size-h3 font-weight-boldest text-right"><?= number_format(($total - $totalremise), 2, '.', '') ?> DH</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block' => 'script_bottom']) ?>
$('.select2').select2();
$.fn.select2.defaults.set("width", "100%");


$('#supplier-id').select2({
placeholder: 'code ou nom du fournisseur ',
ajax: {
url : "<?php echo $this->Url->build(['controller' => 'Supplierorders', 'action' => 'suppliers']); ?>",
dataType: 'json',
delay: 500,
processResults: function (data) {
return {
results: data
};
},
cache: true
}
});

$('.groups').select2({
placeholder: 'Selectionnez une catégorie'
});

$('.produit').select2({
placeholder: 'Selectionnez un article',
});


<?= $this->Html->scriptEnd(); ?>