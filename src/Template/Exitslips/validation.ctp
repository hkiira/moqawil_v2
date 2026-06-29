<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($exitslip, ['class' => 'form-horizontal' ,'id' => 'myform']));
$this->assign('title', 'Valider le bon de '.$exitslip->exitsliptype->title.' N° :'.$exitslip->code);
$this->assign('subtitle', $exitslip->code);
?>
<div class="row">
    <div class="col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">En Préparations</h4>
                <table id="datatablea" class="table table-striped dt-responsive nowrap table-vertical" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Article</th>
                            <th>Quantité commandée</th>
                            <th>Client</th>
                            <th>Quantité à éliminer</th>
                            <th>action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div> <!-- end col -->
    <div class="col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">Produits validées</h4>
                <table id="datatablev" class="table table-striped dt-responsive nowrap table-vertical" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Article</th>
                            <th>Quantité annulée</th>
                            <th>Client</th>
                            <th>Quantité à ajouter</th>
                            <th>action</th>
                        </tr>
                    </thead>
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
            "sInfo":           "Affichage des commandes _START_ à _END_ sur _TOTAL_ commandes",
            "sInfoEmpty":      "Affichage des commandes 0 à 0 sur 0 commandes",
            "sInfoFiltered":   "(filtré à partir de _MAX_ éléments au total)",
            "sInfoPostFix":    "",
            "sInfoThousands":  ",",
            "sLengthMenu":     "Afficher _MENU_ éléments",
            "sLoadingRecords": "Chargement...",
            "sProcessing":     "Traitement...",
            "sSearch":         "Rechercher :",
            "sZeroRecords":    "Aucun élément correspondant trouvé",
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
            'url' : "<?php echo $this->Url->build( ['action' => 'instancebn' ,$exitslip->id] ); ?>",
        },
        'columns': [
            { data: 'product'},
            { data: 'productdis'},
            { data: 'customer'},
            { data: 'quantity'},
            { data: 'action'},
        ],
        'fnDrawCallback': function(oSettings){
            $('.rmvord').click(function(){
                var ordid = $(this).data('id');
                var qte= $('#'+ordid).val();
                $.ajax({
                    url: "<?php echo $this->Url->build( [ 'action' => 'rmvbn',$exitslip->id] ); ?>",
                    type: 'get',
                    data: {ordid: ordid,qte: qte},
                    success: function(response){ 
                        tablea.clear();
                        tablev.clear();
                        tablea.ajax.url( '<?php echo $this->Url->build( [ 'action' => 'instancebn',$exitslip->id ] ); ?>' ).load();
                        tablev.ajax.url( '<?php echo $this->Url->build( ['action' => 'addedbn', $exitslip->id ] ); ?>' ).load();
                        $.notify("commande ajoutée avec succés", "success");

                    }
                });
            });
        }
    });
    var tablev=$('#datatablev').DataTable({
        "language": {
            "sEmptyTable":     "Aucune commande disponible",
            "sInfo":           "Affichage des commandes _START_ à _END_ sur _TOTAL_ commandes",
            "sInfoEmpty":      "Affichage des commandes 0 à 0 sur 0 commandes",
            "sInfoFiltered":   "(filtré à partir de _MAX_ éléments au total)",
            "sInfoPostFix":    "",
            "sInfoThousands":  ",",
            "sLengthMenu":     "Afficher _MENU_ éléments",
            "sLoadingRecords": "Chargement...",
            "sProcessing":     "Traitement...",
            "sSearch":         "Rechercher :",
            "sZeroRecords":    "Aucun élément correspondant trouvé",
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
            'url' : "<?php echo $this->Url->build( [ 'action' => 'addedbn', $exitslip->id ] ); ?>",
        },
        'columns': [
            { data: 'product'},
            { data: 'productdis'},
            { data: 'customer'},
            { data: 'quantity'},
            { data: 'action'},
        ],
        'fnDrawCallback': function(oSettings){
            $('.addo').click(function(){
                var ordid = $(this).data('id');
                var qte= $('#'+ordid).val();
                $.ajax({
                    url: "<?php echo $this->Url->build( ['action' => 'addbn',$exitslip->id] ); ?>",
                    type: 'get',
                    data: {ordid: ordid,qte: qte},
                    success: function(response){ 
                        tablea.clear();
                        tablev.clear();
                        tablea.ajax.url( '<?php echo $this->Url->build( ['action' => 'instancebn',$exitslip->id] ); ?>' ).load();
                        tablev.ajax.url( '<?php echo $this->Url->build( ['action' => 'addedbn', $exitslip->id ] ); ?>' ).load();
                        $.notify("commande enlevée avec succés", "error");

                    }
                });
            });
        }
    });
   
});
<?= $this->Html->scriptEnd(); ?>