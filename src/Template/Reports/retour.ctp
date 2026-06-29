<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('edit',' ');
$this->assign('title', 'Confirmer le rapports N°:'.$report->code);
?>
<div class="row">
    <div class="col-sm-6 col-xs-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">retours en attente</h4>
                <table id="datatablea" class="table table-striped nowrap table-vertical" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Code </th>
                            <th>Vendeur</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Code </th>
                            <th>Vendeur</th>
                            <th>Total</th>
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
                <h4 class="mt-0 header-title">retours validés</h4>
                <table id="datatablev" class="table table-striped nowrap table-vertical" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Code </th>
                            <th>Vendeur</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Code </th>
                            <th>Vendeur</th>
                            <th>Total</th>
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
            "sEmptyTable":     "Aucune commande disponible",
            "sInfo":           "Affichage des retours _START_ à _END_ sur _TOTAL_ retours",
            "sInfoEmpty":      "Affichage des retours 0 à 0 sur 0 retours",
            "sInfoFiltered":   "(filtré à partir de _MAX_ retours au total)",
            "sInfoPostFix":    "",
            "sInfoThousands":  ",",
            "sLengthMenu":     "Afficher _MENU_ retours",
            "sLoadingRecords": "Chargement...",
            "sProcessing":     "Traitement...",
            "sSearch":         "Rechercher :",
            "sZeroRecords":    "Aucun retours correspondant trouvé",
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
            'url' : "<?php echo $this->Url->build( ['action' => 'instanceord' ,$report->id] ); ?>",
        },
        'columns': [
            { data: 'code'},
            { data: 'vendeur'},
            { data: 'total'},
            { data: 'date'},
            { data: 'action'},
        ],
        'fnDrawCallback': function(oSettings){
            $('.addo').click(function(){
                var ordid = $(this).data('id');
                $.ajax({
                    url: "<?php echo $this->Url->build( [ 'action' => 'addord',$report->id] ); ?>",
                    type: 'get',
                    data: {ordid: ordid},
                    success: function(response){ 
                        tablea.clear();
                        tablev.clear();
                        tablea.ajax.url( '<?php echo $this->Url->build( [ 'action' => 'instanceord',$report->id ] ); ?>' ).load();
                        tablev.ajax.url( '<?php echo $this->Url->build( ['action' => 'addedord', $report->id ] ); ?>' ).load();
                        data = JSON.parse(response);
                        total=parseFloat($('#total').val());
                        $('#total').val(total+parseFloat(data.total));
                        $.notify(data.message,data.statut );

                    }
                });
            });
        }
    });
    var tablev=$('#datatablev').DataTable({
        "language": {
            "sEmptyTable":     "Aucun commande disponible",
            "sInfo":           "Affichage des retours _START_ à _END_ sur _TOTAL_ retours",
            "sInfoEmpty":      "Affichage des retours 0 à 0 sur 0 retours",
            "sInfoFiltered":   "(filtré à partir de _MAX_ retours au total)",
            "sInfoPostFix":    "",
            "sInfoThousands":  ",",
            "sLengthMenu":     "Afficher _MENU_ retours",
            "sLoadingRecords": "Chargement...",
            "sProcessing":     "Traitement...",
            "sSearch":         "Rechercher :",
            "sZeroRecords":    "Aucun retour correspondant trouvé",
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
            'url' : "<?php echo $this->Url->build( [ 'action' => 'addedord', $report->id ] ); ?>",
        },
        'columns': [
            { data: 'code'},
            { data: 'vendeur'},
            { data: 'total'},
            { data: 'date'},
            { data: 'action'},
        ],
        'fnDrawCallback': function(oSettings){
            $('.rmvord').click(function(){
                var ordid = $(this).data('id');
                $.ajax({
                    url: "<?php echo $this->Url->build( ['action' => 'rmvord',$report->id] ); ?>",
                    type: 'get',
                    data: {ordid: ordid},
                    success: function(response){ 
                        tablea.clear();
                        tablev.clear();
                        tablea.ajax.url( '<?php echo $this->Url->build( ['action' => 'instanceord',$report->id] ); ?>' ).load();
                        tablev.ajax.url( '<?php echo $this->Url->build( ['action' => 'addedord', $report->id ] ); ?>' ).load();
                        data = JSON.parse(response);
                        total=parseFloat($('#total').val());
                        $('#total').val(total-parseFloat(data.total));
                        $.notify(data.message,data.statut );

                    }
                });
            });
        }
    });
   
});
<?= $this->Html->scriptEnd(); ?>
<style type="text/css">
    input#total {
        font-size: 30px;
        font-weight: bolder;
        color: #1bc5bd;
    }
    input#charges {
        font-size: 30px;
        font-weight: bolder;
    }
</style>