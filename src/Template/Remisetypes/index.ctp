<?php 
$this->assign('title', 'Liste des Types de remise');
$this->assign('subtitle', '');
 ?>
<div class="card card-custom">
    <div class="card-body">
        <table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Nom</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
var HOST_URL = "<?php echo $this->Url->build( ['action' => 'search'] ); ?>";
<?= $this->Html->scriptEnd(); ?>

<?= $this->Html->script('/assets/plugins/custom/datatables/datatables.bundle.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->script('/js/remisetypes.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->css('/assets/plugins/custom/datatables/datatables.bundle.css', ['block' => 'css_top']) ?>