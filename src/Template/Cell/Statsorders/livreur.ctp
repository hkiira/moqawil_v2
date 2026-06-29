<div class="col-lg-12">
    <div class="card card-custom gutter-b">
        <div class="card-header border-0 bg-success">
            <h3 class="card-title font-weight-bolder text-white">Livraison à faire</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
                <thead>
                    <tr>
                        <th>Par</th>
                        <th>Code</th>
                        <th>Client</th>
                        <th>Commandes</th>
                        <th>Localisation</th>
                        <th>Statut</th>
                        <th>Localisation</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<?= $this->Html->scriptStart() ?>
var HOST_URL = "<?php echo $this->Url->build( ['controller'=>'Shippings','action' => 'search']); ?>";
<?= $this->Html->scriptEnd(); ?>