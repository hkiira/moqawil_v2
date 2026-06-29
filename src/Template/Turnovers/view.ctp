<?php
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]);
$this->assign('goback', ' ');
$this->assign('edit', '
<a href="../index" class="btn btn-primary font-weight-bolder">
<i class="ki ki-check icon-xs"></i>
</a>');
$this->assign('title', 'Liste des produits pour chiffre : ' . $turnover->title);
?>
<div class="row">
    <div class="col-sm-6 col-xs-12">
        <div class="ml-5 mr-5">
            <table id="datatablea" class="table table-striped table-bordered nowrap table-vertical" style="border-collapse: collapse; border-spacing: 0;">
                <thead>
                    <tr>
                        <th style="width:80%">Produits</th>
                        <th>action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th style="width:80%">Produits</th>
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
                        <th style="width:80%">Produits</th>
                        <th>action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th style="width:80%">Produits</th>
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
<?= $this->Html->scriptStart(['block' => 'script_bottom']) ?>
$(document).ready(function(){
var tablea=$('#datatablea').DataTable({
"language": {
"sEmptyTable": "Aucune produit disponible",
"sInfo": "Affichage des produits _START_ à _END_ sur _TOTAL_ produits",
"sInfoEmpty": "Affichage des produits 0 à 0 sur 0 produits",
"sInfoFiltered": "(filtré à partir de _MAX_ produits au total)",
"sInfoPostFix": "",
"sInfoThousands": ",",
"sLengthMenu": "Afficher _MENU_ produits",
"sLoadingRecords": "Chargement...",
"sProcessing": "Traitement...",
"sSearch": "<b>En attente :</b>",
"sZeroRecords": "Aucun produits correspondant trouvé",
"oPaginate": {
"sFirst": "Premier",
"sLast": "Dernier",
"sNext": "Suivant",
"sPrevious": "Précédent"
},
},
"bLengthChange": false,
'processing': true,
'serverSide': true,
'serverMethod': 'get',
'ajax': {
'url' : "<?php echo $this->Url->build(['action' => 'instanceord', $turnover->id]); ?>",
},
'columns': [
{ data: 'code'},
{ data: 'action'},
],
"pageLength": 20,
'fnDrawCallback': function(oSettings){
$('.addo').click(function(){
var ordid = $(this).data('id');
$.ajax({
url: "<?php echo $this->Url->build(['action' => 'addord', $turnover->id]); ?>",
type: 'get',
data: {ordid: ordid},
success: function(response){
tablea.clear();
tablev.clear();
tablea.ajax.url( '<?php echo $this->Url->build(['action' => 'instanceord', $turnover->id]); ?>' ).load();
tablev.ajax.url( '<?php echo $this->Url->build(['action' => 'addedord', $turnover->id]); ?>' ).load();
data = JSON.parse(response);
$('#total').val(data.total);
$('#retour').val(data.retour);


}
});
});
}
});
var tablev=$('#datatablev').DataTable({
"language": {
"sEmptyTable": "Aucun produit disponible",
"sInfo": "Affichage des produits _START_ à _END_ sur _TOTAL_ produits",
"sInfoEmpty": "Affichage des produits 0 à 0 sur 0 produits",
"sInfoFiltered": "(filtré à partir de _MAX_ produits au total)",
"sInfoPostFix": "",
"sInfoThousands": ",",
"sLengthMenu": "Afficher _MENU_ produits",
"sLoadingRecords": "Chargement...",
"sProcessing": "Traitement...",
"sSearch": "<b>validés :</b>",
"sZeroRecords": "Aucun produits correspondant trouvé",
"oPaginate": {
"sFirst": "Premier",
"sLast": "Dernier",
"sNext": "Suivant",
"sPrevious": "Précédent"
},
},
'processing': true,
'serverSide': true,
'serverMethod': 'get',
'ajax': {
'url' : "<?php echo $this->Url->build(['action' => 'addedord', $turnover->id]); ?>",
},
'columns': [
{ data: 'code'},
{ data: 'action'},
],
"bLengthChange": false,
"pageLength": 20,
'fnDrawCallback': function(oSettings){
$('.rmvord').click(function(){
var ordid = $(this).data('id');
$.ajax({
url: "<?php echo $this->Url->build(['action' => 'rmvord', $turnover->id]); ?>",
type: 'get',
data: {ordid: ordid},
success: function(response){
tablea.clear();
tablev.clear();
tablea.ajax.url( '<?php echo $this->Url->build(['action' => 'instanceord', $turnover->id]); ?>' ).load();
tablev.ajax.url( '<?php echo $this->Url->build(['action' => 'addedord', $turnover->id]); ?>' ).load();
data = JSON.parse(response);
$('#total').val(data.total);
$('#retour').val(data.retour);
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

    input#retour {
        font-size: 30px;
        font-weight: bolder;
    }
</style>