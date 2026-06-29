<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($receipt,['id'=>'kt_form_1']));
$this->assign('title', 'Ajouter un nouveau bon de réception');
$this->assign('subtitle', 'vous pouvez ajouter un bon de réception');
?>
<div class="card-body">
    <div class="row">
      <div class="col-12">
        <div class="card">
            <div class="card-body">
                  <table class="table table-bordered table-checkable" id="mytable">
                    <thead>
                      <tr>
                        <th>Article</th>
                        <th>Quantité reçu (Carton/Sac)</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($receipt->supporderproducts as $key => $supporderproduct): ?>
                            <tr>
                              <?= $this->Form->control('supporderproducts.'.$supporderproduct->id.'.id', ['type' => 'hidden','class' => 'form-control','label' => false, 'value' => $supporderproduct->id]); ?>
                              <?= $this->Form->control('supporderproducts.'.$supporderproduct->id.'.qtepercs', ['type' => 'hidden','class' => 'form-control','label' => false, 'value' => $supporderproduct->pack->packunites[0]->quantity]); ?>
                              <td style="width: 50%;">
                                <?=$supporderproduct->pack->title  ?><br>
                                <?=$supporderproduct->pack->packunites[0]->quantity.' '.$supporderproduct->pack->packunites[0]->unite->parentunite->abrev.' par '.$supporderproduct->pack->packunites[0]->unite->abrev ?>
                              </td>
                              <td>
                                <?= $this->Form->control('supporderproducts.'.$supporderproduct->id.'.quantity', ['type' => 'number','min'=>'0' ,'class' => 'form-control','label' => false, 'value' => intVal($supporderproduct->quantity/$supporderproduct->pack->packunites[0]->quantity)]); ?>
                              </td>
                            </tr>
                      <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> <!-- end row -->

<?= $this->Html->script('/assets/plugins/custom/datatables/datatables.bundle.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->script('/js/pofstypes.js', ['block' => 'script_bottom']) ?>
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
            'url' : "<?php echo $this->Url->build( [ 'controller' => 'Receipts', 'action' => 'instanceord' ,$receipt->supplierorder_id] ); ?>",
        },
        'columns': [
            { data: 'product'},
            { data: 'quantity'},
            { data: 'action'},
        ],
        'fnDrawCallback': function(oSettings){
            $('.addo').click(function(){
                var ordid = $(this).data('id');
                var qte= $('#'+ordid).val();
                $.ajax({
                    url: "<?php echo $this->Url->build( [ 'controller' => 'Receipts', 'action' => 'addord',$receipt->id] ); ?>",
                    type: 'get',
                    data: {ordid: ordid,qte: qte},
                    success: function(response){ 
                        tablea.clear();
                        tablev.clear();
                        tablea.ajax.url( '<?php echo $this->Url->build( [ 'controller' => 'Receipts', 'action' => 'instanceord',$receipt->supplierorder_id ] ); ?>' ).load();
                        tablev.ajax.url( '<?php echo $this->Url->build( [ 'controller' => 'Receipts', 'action' => 'addedord', $receipt->id ] ); ?>' ).load();
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
            'url' : "<?php echo $this->Url->build( [ 'controller' => 'Receipts', 'action' => 'addedord', $receipt->id ] ); ?>",
        },
        'columns': [
            { data: 'product'},
            { data: 'quantity'},
            { data: 'action'},
        ],
        'fnDrawCallback': function(oSettings){
            $('.rmvord').click(function(){
                var ordid = $(this).data('id');
                $.ajax({
                    url: "<?php echo $this->Url->build( [ 'controller' => 'Receipts', 'action' => 'rmvord',$receipt->id] ); ?>",
                    type: 'get',
                    data: {ordid: ordid},
                    success: function(response){ 
                        tablea.clear();
                        tablev.clear();
                        tablea.ajax.url( '<?php echo $this->Url->build( [ 'controller' => 'Receipts', 'action' => 'instanceord',$receipt->supplierorder_id] ); ?>' ).load();
                        tablev.ajax.url( '<?php echo $this->Url->build( [ 'controller' => 'Receipts', 'action' => 'addedord', $receipt->id ] ); ?>' ).load();
                        $.notify("commande enlevée avec succés", "error");

                    }
                });
            });
        }
    });
   
});
<?= $this->Html->scriptEnd(); ?>