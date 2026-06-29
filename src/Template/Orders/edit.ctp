<?php 
    $this->assign('title', 'Modifier la commande :'.$order->code);
 ?>
    <div class="card card-custom card-sticky" id="kt_page_sticky_card">
        <div class="card-header">
        <div class="card-title">
            <h3 class="card-label">
                <?= $this->fetch('title') ?> <i class="mr-2"></i>
            </h3>
        </div>
        <div class="card-toolbar">
            <?php if ($order->customer->customertype_id==4): ?>
                <a href="<?= $order->id ?>/valider" class="btn btn-light-success font-weight-bolder mr-2">
                    <i class="ki ki-check icon-xs"></i>Valider
                </a>
            <?php endif ?>
            <button onclick="goBack()" class="btn btn-light-primary font-weight-bolder mr-2">
                <i class="ki ki-long-arrow-back icon-xs"></i>Retour
            </button>
        </div>
    </div>
        <div class="card-body p-0">
            <div class="row justify-content-center py-8 px-8 py-md-8 px-md-0">
                <div class="col-md-10">
                    <div class="d-flex justify-content-between flex-column flex-md-row">
                        <h1 class="display-4 font-weight-boldest mb-10">    <?= $order->customer->name ?><br><?= $order->created->i18nFormat('dd/MM/yyyy')  ?>
                        </h1>
                        <div class="d-flex flex-column align-items-md-end px-0">
                            <span class=" d-flex flex-column align-items-md-end opacity-70">
                                <?= $order->customer->adresse ?>
                                <br><?= $order->customer->zone->title ?>-<?= $order->customer->zone->city->title ?>
                                <br>Téléphone: <?= $order->customer->phone ?>
                                <a href="/customers/edit/<?= $order->customer->id ?>/1" class="btn btn-warning btn-xs">Modifier le client</a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center py-8 px-8 px-md-0">
                <div class="col-md-10">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="pl-0 font-weight-bold text-muted  text-uppercase">Article</th>
                                    <th class="text-left font-weight-bold text-muted text-uppercase">Quantité</th>
                                    <th class="text-left font-weight-bold text-muted text-uppercase">P.U</th>
                                    <th class="text-left pr-0 font-weight-bold text-muted text-uppercase">Total</th>
                                    <th class="text-left pr-0 font-weight-bold text-muted text-uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total=0; ?>
                                <?php $totalremise=0; ?>
                                <?php foreach ($order->orderpacks as $key => $orderpack): ?>
                                    <tr>
                                        <td  class="text-left"><?=  $orderpack->pack->title ?></td>
                                        <?php if ($orderpack->quantity%$orderpack->pack->packunites[0]->quantity): ?>
                                            <td class="text-left">
                                                <?php if (intVal($orderpack->quantity/$orderpack->pack->packunites[0]->quantity)>0): ?>
                                                    <?=  intVal($orderpack->quantity/$orderpack->pack->packunites[0]->quantity).' '.$orderpack->pack->packunites[0]->unite->abrev ?> 
                                                    et <?=  $orderpack->quantity % $orderpack->pack->packunites[0]->quantity.' '.$orderpack->pack->packunites[0]->unite->parentunite->abrev ?> 
                                                <?php else: ?>
                                                    <?=  $orderpack->quantity % $orderpack->pack->packunites[0]->quantity.' '.$orderpack->pack->packunites[0]->unite->parentunite->abrev ?>
                                                <?php endif ?>
                                            </td>
                                        <?php else: ?>
                                            <td class="text-left">
                                                <?= intVal($orderpack->quantity/$orderpack->pack->packunites[0]->quantity).' '.$orderpack->pack->packunites[0]->unite->abrev ?>
                                            </td>
                                        <?php endif ?>
                                        <td class="text-left"><?=  number_format($orderpack->price, 2, '.', '') ?></td>
                                        <td class="text-left"><?=  number_format(($orderpack->price*$orderpack->quantity), 2, '.', '') ?></td>
                                        <?php $total+=($orderpack->price*$orderpack->quantity) ?>
                                            
                                        <td class="text-left">
                                            <?= $this->Form->postlink('<i class="flaticon-delete text-danger"></i>', ['controller'=>'Orderpacks','action' => 'delete',$orderpack->id,$order->id], ['confirm' => __('Etes-vous sûr que vous voulez supprimer {0}?', $orderpack->pack->title),"escape"=>false]) ?>
                                             
                                             <i class="ordshp flaticon2-pen text-waring" data-id="<?= $orderpack->id ?>" ></i>
                                            </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?= $this->Form->create(null, [ 'url' => ['controller' => 'Orderpacks', 'action' => 'add'],['id'=>'id']]) ?>
                <?= $this->Form->control('orderid',['class'=>'form-control','type'=>'hidden','value'=>$order->id]); ?>
                <?= $this->Form->control('customerid',['value' => $order->customer_id, 'type'=>'hidden']); ?>
                <?= $this->Form->control('storeid',['value' => $order->store_id, 'type'=>'hidden']); ?>
                <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-sm btn-success  mr-10 float-right">Ajouter les produits</button>
                        </div>
                        <div class="col-12">
                                <div id="usercontact"></div>
                        </div>
                </div>
                <?= $this->Form->end() ?>
            <div class="row justify-content-center bg-gray-100 py-8 px-8 py-md-10 px-md-0 mx-0">
                <div class="col-md-10">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="font-weight-bold text-muted  text-uppercase text-right">MONTANT TTC</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="font-weight-bolder">
                                    <td class="text-primary font-size-h3 font-weight-boldest text-right"><?= number_format(($total-$totalremise), 2, '.', '') ?> DH</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>

  $.fn.select2.defaults.set("width", "100%");
    
        customerid=<?= $order->customer_id ?>;
        searchTags(customerid);
        
        function searchTags( keyword ){
          var data = keyword;
          $.ajax({
            method: 'get',
            url : "<?php echo $this->Url->build( [ 'controller' => 'Orders', 'action' => 'usercontact'] ); ?>",
              data: {keyword:data},
            success: function( response )
            {       
              $( '#usercontact' ).html(response);
            }
          });
        };

        $(".ordshp").click(function(event){
            event.preventDefault();
            jQuery.noConflict();
            var ordid = $(this).data('id');
            $.ajax({
                url: HURL+'orderpacks/edit/'+ordid,
                type: 'get',
                success: function(response){ 
                    $('.modal-content').html(response); 
                    $('#empModal').modal('show');
                }
            });
        });
<?= $this->Html->scriptEnd(); ?>
