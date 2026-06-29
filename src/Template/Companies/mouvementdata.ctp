<div class="row">
    <div class="col-lg-6">
        <div class="card card-custom">
            <div class="card-header align-items-center border-0 mt-4">
				<h3 class="card-title align-items-start flex-column">
					<span class="font-weight-bolder text-dark">Sorties</span>
				</h3>
			</div>
            <div class="card-body">
                <table class="table table-bordered table-hover table-checkable" id="exitslip_table" style="margin-top: 13px !important">
                    <thead>
                        <tr>
                            <th>Par</th>
                            <th>Code</th>
                            <th>Bons de livraison</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card card-custom">
            <div class="card-header align-items-center border-0 mt-4">
				<h3 class="card-title align-items-start flex-column">
					<span class="font-weight-bolder text-dark">Récéptions</span>
				</h3>
			</div>
            <div class="card-body">
                <table class="table table-bordered table-hover table-checkable" id="receipt_table" style="margin-top: 13px !important">
                    <thead>
                        <tr>
                            <th>Par</th>
                            <th>Code</th>
                            <th>Fournisseur</th>
                            <th>Articles</th>
                            <th>Entrep么t</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->Html->css('/assets/plugins/custom/datatables/datatables.bundle.css') ?>
<?= $this->Html->script('/assets/plugins/custom/datatables/datatables.bundle.js') ?>
<?= $this->Html->scriptStart() ?>
    var HOST_URL1 = "<?php echo $this->Url->build( [ 'controller' => 'Receipts','action' => 'search',$vrb['start'],$vrb['end']] ); ?>";
    var HOST_URL2 = "<?php echo $this->Url->build( [ 'controller' => 'Exitslips','action' => 'search',$vrb['start'],$vrb['end']] ); ?>";
<?= $this->Html->scriptEnd(); ?>

<?= $this->Html->script('/js/mouvement.js') ?>

