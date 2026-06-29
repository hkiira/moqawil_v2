<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('edit',$this->Html->link(__('<i class="mdi mdi-bookmark-multiple mr-2"></i> Valider'), ['action' => 'edit',$exitslip->id,'validation'],['escape' => false,'class' => 'btn btn-success waves-effect waves-light','type'=>'button']));
$this->assign('title', 'Confirmer le bon de sortie N°:'.$exitslip->code);
?>
<div class="row">
    <div class="col-sm-6 col-xs-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">Bons de chargement en attente</h4>
                <table id="datatablea" class="table table-striped dt-responsive nowrap table-vertical" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Code </th>
                            <th>Pré-vendeur</th>
                            <th>NBR Article</th>
                            <th>Date</th>
                            <th>action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Code </th>
                            <th>Pré-vendeur</th>
                            <th>NBR Article</th>
                            <th>Date</th>
                            <th>action</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div> <!-- end col -->
    <div class="col-sm-6 col-xs-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">Bons de chargement validés</h4>
                <table id="datatablev" class="table table-striped dt-responsive nowrap table-vertical" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Code </th>
                            <th>Pré-vendeur</th>
                            <th>NBR Article</th>
                            <th>Date</th>
                            <th>action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Code </th>
                            <th>Pré-vendeur</th>
                            <th>NBR Article</th>
                            <th>Date</th>
                            <th>action</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->

<?= $this->Html->script('/assets/plugins/custom/datatables/datatables.bundle.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->css('/assets/plugins/custom/datatables/datatables.bundle.css', ['block' => 'css_top']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$(document).ready(function(){
    var tablea=$('#datatablea').DataTable({
        "language": {
            "sEmptyTable":     "Aucun bon de chargement disponible",
            "sInfo":           "Affichage des bons de chargement _START_ à _END_ sur _TOTAL_ bons de chargement",
            "sInfoEmpty":      "Affichage des bons de chargement 0 à 0 sur 0 bons de chargement",
            "sInfoFiltered":   "(filtré à partir de _MAX_ bons de chargement au total)",
            "sInfoPostFix":    "",
            "sInfoThousands":  ",",
            "sLengthMenu":     "Afficher _MENU_ bons de chargement",
            "sLoadingRecords": "Chargement...",
            "sProcessing":     "Traitement...",
            "sSearch":         "Rechercher :",
            "sZeroRecords":    "Aucun bon de chargement correspondant trouvé",
            "oPaginate": {
                "sFirst":    "Premier",
                "sLast":     "Dernier",
                "sNext":     "Suivant",
                "sPrevious": "Précédent"
            },
        },
        'processing': true,
        'serverSide': true,
        'serverMethod': 'get',
        'ajax': {
            'url' : "<?php echo $this->Url->build( ['action' => 'instanceord' ,$exitslip->id] ); ?>",
        },
        'columns': [
            { data: 'Bonch'},
            { data: 'User'},
            { data: 'Products'},
            { data: 'Date'},
            { data: 'action'},
        ],
        'fnDrawCallback': function(oSettings){
            $('.addo').click(function(){
                var ordid = $(this).data('id');
                $.ajax({
                    url: "<?php echo $this->Url->build( [ 'action' => 'addord',$exitslip->id] ); ?>",
                    type: 'get',
                    data: {ordid: ordid},
                    success: function(response){ 
                        tablea.clear();
                        tablev.clear();
                        tablea.ajax.url( '<?php echo $this->Url->build( [ 'action' => 'instanceord',$exitslip->id ] ); ?>' ).load();
                        tablev.ajax.url( '<?php echo $this->Url->build( ['action' => 'addedord', $exitslip->id ] ); ?>' ).load();
                        $.notify("commande ajoutée avec succés", "success");

                    }
                });
            });
        }
    });
    var tablev=$('#datatablev').DataTable({
        "language": {
            "sEmptyTable":     "Aucun bon de chargement disponible",
            "sInfo":           "Affichage des bons de chargement _START_ à _END_ sur _TOTAL_ bons de chargement",
            "sInfoEmpty":      "Affichage des bons de chargement 0 à 0 sur 0 bons de chargement",
            "sInfoFiltered":   "(filtré à partir de _MAX_ bons de chargement au total)",
            "sInfoPostFix":    "",
            "sInfoThousands":  ",",
            "sLengthMenu":     "Afficher _MENU_ bons de chargement",
            "sLoadingRecords": "Chargement...",
            "sProcessing":     "Traitement...",
            "sSearch":         "Rechercher :",
            "sZeroRecords":    "Aucun bon de chargement correspondant trouvé",
            "oPaginate": {
                "sFirst":    "Premier",
                "sLast":     "Dernier",
                "sNext":     "Suivant",
                "sPrevious": "Précédent"
            },
        },
        'processing': true,
        'serverSide': true,
        'serverMethod': 'get',
        'ajax': {
            'url' : "<?php echo $this->Url->build( [ 'action' => 'addedord', $exitslip->id ] ); ?>",
        },
        'columns': [
            { data: 'Bonch'},
            { data: 'User'},
            { data: 'Products'},
            { data: 'Date'},
            { data: 'action'},
        ],
        'fnDrawCallback': function(oSettings){
            $('.rmvord').click(function(){
                var ordid = $(this).data('id');
                $.ajax({
                    url: "<?php echo $this->Url->build( ['action' => 'rmvord',$exitslip->id] ); ?>",
                    type: 'get',
                    data: {ordid: ordid},
                    success: function(response){ 
                        tablea.clear();
                        tablev.clear();
                        tablea.ajax.url( '<?php echo $this->Url->build( ['action' => 'instanceord',$exitslip->id] ); ?>' ).load();
                        tablev.ajax.url( '<?php echo $this->Url->build( ['action' => 'addedord', $exitslip->id ] ); ?>' ).load();
                        $.notify("commande enlevée avec succés", "error");

                    }
                });
            });
        }
    });
   
});
<?= $this->Html->scriptEnd(); ?>