<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('edit',$this->Html->link(__('<i class="mdi mdi-bookmark-multiple mr-2"></i> Valider'), ['action' => 'edit',$receipt->id,'validation'],['escape' => false,'class' => 'btn btn-success waves-effect waves-light','type'=>'button']));
$this->assign('title', 'Confirmer le bon de réception N° : '.$receipt->code);
?>
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
                              <?= $this->Form->control('supporderproducts.'.$supporderproduct->id.'.qtepercs', ['type' => 'hidden','class' => 'form-control','label' => false, 'value' => $supporderproduct->product->packproducts[0]->pack->packunites[0]->quantity]); ?>
                              <td style="width: 50%;">
                                <?=$supporderproduct->product->packproducts[0]->pack->title  ?><br>
                                <?=$supporderproduct->product->packproducts[0]->pack->packunites[0]->quantity.' '.$supporderproduct->product->packproducts[0]->pack->packunites[0]->unite->parentunite->abrev.' par '.$supporderproduct->product->packproducts[0]->pack->packunites[0]->unite->abrev ?>
                              </td>
                              <td>
                                <?= $this->Form->control('supporderproducts.'.$supporderproduct->id.'.quantity', ['type' => 'number','min'=>'0' ,'class' => 'form-control','label' => false, 'value' => intVal($supporderproduct->quantity/$supporderproduct->product->packproducts[0]->pack->packunites[0]->quantity)]); ?>
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