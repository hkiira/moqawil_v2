<?php 
    $this->assign('title', 'Validation du bon de réception : '.$exitslip->code);
?>
<div class="card card-custom">
    <h5 class="text-dark font-weight-bold pl-5 pt-5">Liste des bons de livraison</h5>
    <div class="card-body">
        <table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
            <thead>
                <tr>
                    <th>Par</th>
                    <th>Code</th>
                    <th>Client</th>
                    <th>Commandes</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Actions</th>
                    
                </tr>
            </thead>
        </table>
    </div>
</div>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
var HOST_URL = "<?php echo $this->Url->build( ['controller'=>'Shippings','action' => 'search',$exitslip->id]); ?>";
<?= $this->Html->scriptEnd(); ?>

<?= $this->Html->script('/assets/plugins/custom/datatables/datatables.bundle.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->script('/js/shippings.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->css('/assets/plugins/custom/datatables/datatables.bundle.css', ['block' => 'css_top']) ?>