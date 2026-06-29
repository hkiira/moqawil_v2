<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('goback',' ');
$this->assign('edit','
<a href="../index" class="btn btn-primary font-weight-bolder">
<i class="ki ki-check icon-xs"></i>confirmer
</a>');
$this->assign('title', 'Confirmer le rapports N°:'.$report->code);
?>
<div class="row">
    <div class="col-4">
        <div class="form-group px-5 pt-2">
            <label for="retour">CREDIT</label>
            </label>
            <input type="number" step="any" id="credit" name="retour" class="form-control" disabled="disabled" value="<?= $totalCredit ?>">
        </div>
    </div>
    <div class="col-4">
        <div class="form-group px-5 pt-2">
            <label for="totalorders">TOTAL LIVRER</label>
            <input type="number" step="any" id="recover" class="form-control" disabled="disabled" value="<?= $totalToRecover ?>">
        </div>
    </div>
    <div class="col-4">
        <div class="form-group px-5 pt-2">
            <label for="totalorders">TOTAL ENCAISSER</label>
            <input type="number" step="any" id="total" class="form-control" disabled="disabled" value="<?= $total ?>">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6 col-xs-12">
        <div class="ml-5 mr-5">
                <table id="datatablea" class="table table-striped table-bordered nowrap table-vertical" style="border-collapse: collapse; border-spacing: 0;">
                    <thead>
                        <tr>
                            <th style="width:40%">Code </th>
                            <th>Total</th>
                            <th>Méthode</th>
                            <th>action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Code </th>
                            <th>Total</th>
                            <th>Méthode</th>
                            <th>action</th>
                        </tr>
                    </tfoot>
                </table>
        </div>
    </div> <!-- end col -->
    <div class="col-sm-6 col-xs-12">
        <div class="mr-5 ml-5">
                <table id="datatablev" class="table table-striped table-bordered nowrap table-vertical">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Total</th>
                            <th>Méthode</th>
                            <th>action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Code </th>
                            <th>Total</th>
                            <th>Méthode</th>
                            <th>action</th>
                        </tr>
                    </tfoot>
                </table>
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->
<style>
    div.dataTables_wrapper div.dataTables_filter {
        text-align: right;
        width: 100%;
        margin-left: -140px !important;
    }
</style>

<?= $this->Html->script('/assets/plugins/custom/datatables/datatables.bundle.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->css('/assets/plugins/custom/datatables/datatables.bundle.css', ['block' => 'css_top']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
$(document).ready(function(){
    var tablea=$('#datatablea').DataTable({
        "language": {
            "sEmptyTable":     "Aucune commande disponible",
            "sInfo":           "Affichage des commandes _START_ à _END_ sur _TOTAL_ commandes",
            "sInfoEmpty":      "Affichage des commandes 0 à 0 sur 0 commandes",
            "sInfoFiltered":   "(filtré à partir de _MAX_ commandes au total)",
            "sInfoPostFix":    "",
            "sInfoThousands":  ",",
            "sLengthMenu":     "Afficher _MENU_ commandes",
            "sLoadingRecords": "Chargement...",
            "sProcessing":     "Traitement...",
            "sSearch":         "<b>En attente :</b>",
            "sZeroRecords":    "Aucun commandes correspondant trouvé",
            "oPaginate": {
                "sFirst":    "Premier",
                "sLast":     "Dernier",
                "sNext":     "Suivant",
                "sPrevious": "Précédent"
            },
        },
        "bLengthChange": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'get',
        'ajax': {
            'url' : "<?php echo $this->Url->build( ['action' => 'instancebn' ,$report->id] ); ?>",
        },
        'columns': [
            { data: 'code'},
            { data: 'total'},
            { data: 'paymentMethod'},
            { data: 'action'},
        ],
        "pageLength": 50,
        'fnDrawCallback': function(oSettings){
            $('.addo').click(function(){
                var ordid = $(this).data('id');
                $.ajax({
                    url: "<?php echo $this->Url->build( [ 'action' => 'addbn',$report->id] ); ?>",
                    type: 'get',
                    data: {ordid: ordid},
                    success: function(response){ 
                        tablea.clear();
                        tablev.clear();
                        tablea.ajax.url( '<?php echo $this->Url->build( [ 'action' => 'instancebn',$report->id ] ); ?>' ).load();
                        tablev.ajax.url( '<?php echo $this->Url->build( ['action' => 'addedbn', $report->id ] ); ?>' ).load();
                        data = JSON.parse(response);
                        $('#total').val(data.total);
                        $('#credit').val(data.totalCredit);
                        $('#recover').val(data.totalToRecover);
                       

                    }
                });
            });
        }
    });
    var tablev=$('#datatablev').DataTable({
        "language": {
            "sEmptyTable":     "Aucun commande disponible",
            "sInfo":           "Affichage des commandes _START_ à _END_ sur _TOTAL_ commandes",
            "sInfoEmpty":      "Affichage des commandes 0 à 0 sur 0 commandes",
            "sInfoFiltered":   "(filtré à partir de _MAX_ commandes au total)",
            "sInfoPostFix":    "",
            "sInfoThousands":  ",",
            "sLengthMenu":     "Afficher _MENU_ commandes",
            "sLoadingRecords": "Chargement...",
            "sProcessing":     "Traitement...",
            "sSearch":         "<b>validés :</b>",
            "sZeroRecords":    "Aucun commandes correspondant trouvé",
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
            'url' : "<?php echo $this->Url->build( [ 'action' => 'addedbn', $report->id ] ); ?>",
        },
        'columns': [
            { data: 'code'},
            { data: 'total'},
            { data: 'paymentMethod'},
            { data: 'action'},
        ],
        "bLengthChange": false,
        "pageLength": 50,
        'fnDrawCallback': function(oSettings){
            $('.rmvord').click(function(){
                var ordid = $(this).data('id');
                $.ajax({
                    url: "<?php echo $this->Url->build( ['action' => 'rmvbn',$report->id] ); ?>",
                    type: 'get',
                    data: {ordid: ordid},
                    success: function(response){ 
                        tablea.clear();
                        tablev.clear();
                        tablea.ajax.url( '<?php echo $this->Url->build( ['action' => 'instancebn',$report->id] ); ?>' ).load();
                        tablev.ajax.url( '<?php echo $this->Url->build( ['action' => 'addedbn', $report->id ] ); ?>' ).load();
                        data = JSON.parse(response);
                        $('#total').val(data.total);
                        $('#credit').val(data.totalCredit);
                        $('#recover').val(data.totalToRecover);
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
    input#credit {
        font-size: 30px;
        font-weight: bolder;
        color: #c5211bff;
    }
    input#recover {
        font-size: 30px;
        font-weight: bolder;
        color: #132ec2ff;
    }
</style>