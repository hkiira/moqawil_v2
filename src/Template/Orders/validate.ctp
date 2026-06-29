<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($order, ['class' => 'form-horizontal' ,'id' => 'myform']));
$this->assign('title', 'Valider la commande N° :'.$order->code);
?>
<div class="row">
    <div class="col-sm-6 col-xs-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">Validées</h4>
                <table id="datatablea" class="table table-striped dt-responsive nowrap table-vertical" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Article</th>
                            <th>Commande</th>
                            <th>Quantité</th>
                            <th>action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div> <!-- end col -->
    <div class="col-sm-6 col-xs-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">Annulées</h4>
                <table id="datatablev" class="table table-striped dt-responsive nowrap table-vertical" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                             <th>Article</th>
                            <th>Commande</th>
                            <th>Quantité</th>
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
            'url' : "<?php echo $this->Url->build( ['action' => 'instancebn' ,$order->id] ); ?>",
        },
        'columns': [
            { data: 'product'},
            { data: 'cmd'},
            { data: 'quantity'},
            { data: 'action'},
        ],
        'fnDrawCallback': function(oSettings){
            $('.addo').click(function(){
                var ordid = $(this).data('id');
                var qte= $('#'+ordid).val();
                $.ajax({
                    url: "<?php echo $this->Url->build( [ 'action' => 'addbn',$order->id] ); ?>",
                    type: 'get',
                    data: {ordid: ordid,qte: qte},
                    success: function(response){ 
                        tablea.clear();
                        tablev.clear();
                        tablea.ajax.url( '<?php echo $this->Url->build( [ 'action' => 'instancebn',$order->id ] ); ?>' ).load();
                        tablev.ajax.url( '<?php echo $this->Url->build( ['action' => 'addedbn', $order->id ] ); ?>' ).load();
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
            'url' : "<?php echo $this->Url->build( [ 'action' => 'addedbn', $order->id ] ); ?>",
        },
        'columns': [
            { data: 'product'},
            { data: 'cmd'},
            { data: 'quantity'},
            { data: 'action'},
        ],
        'fnDrawCallback': function(oSettings){
            $('.rmvord').click(function(){
                var ordid = $(this).data('id');
                $.ajax({
                    url: "<?php echo $this->Url->build( ['action' => 'rmvbn',$order->id] ); ?>",
                    type: 'get',
                    data: {ordid: ordid},
                    success: function(response){ 
                        tablea.clear();
                        tablev.clear();
                        tablea.ajax.url( '<?php echo $this->Url->build( ['action' => 'instancebn',$order->id] ); ?>' ).load();
                        tablev.ajax.url( '<?php echo $this->Url->build( ['action' => 'addedbn', $order->id ] ); ?>' ).load();
                        $.notify("commande enlevée avec succés", "error");

                    }
                });
            });
        }
    });
   
});
<?= $this->Html->scriptEnd(); ?>