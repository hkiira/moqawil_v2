    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="col-lg-3 float-left">
                    <h4 class="mt-0 header-title">Articles</h4>
                </div>
                <div class="col-lg-3 float-left">
                    <div class="form-group">
                        <?= $this->Form->control('warehouse', ['class'=>'warehouse','options' => $warehouses,'empty'=>true,'label' => false]) ?>
                    </div>
                </div>
                <div class="col-lg-3 float-left">
                    <div class="form-group pofsales">
                        <?= $this->Form->control('pofsale', ['class'=>'pofsale','type' => 'select','empty'=>true,'label' => false]) ?>
                    </div>
                </div>
                <div class="col-lg-3 float-left">
                    <div class="form-group categories">
                        <?= $this->Form->control('category', ['class'=>'category','type' => 'select','empty'=>true,'label' => false]) ?>
                    </div>
                </div>
                <div class="col-lg-3 float-left">
                    <div class="form-group products">
                        <?= $this->Form->control('produit', ['class'=>'produit','label' => false,'type' => 'select']) ?>
                    </div>
                </div>
                <table class="table table-bordered table-hover" id="example2">
                    <thead>
                      <tr>
                        <th>Article</th>
                        <th>Quantité</th>
                        <th>Prix</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr id="product"></tr>
                    </tbody>
                  </table>
            </div>
        </div>
    </div> 
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js') ?>
<?= $this->Html->scriptStart() ?>
  $('.category').select2({
    placeholder: 'Selectionnez une catégories',
  });
  
  $('.warehouse').select2({
    placeholder: 'Selectionnez un entrepôt',
  });

  $('.pofsale').select2({
    placeholder: 'Selectionnez une point de vente',
  });

  $('.produit').select2({
    placeholder: 'Selectionnez un produit',
  });

  $('document').ready(function(){
    $(".warehouse").change(function(){
      var warehouse = $(this).val();
      searchTags( warehouse );
    });
    function searchTags( warehouse ){
      var warehouse = warehouse;
      $.ajax({
        method: 'get',
        url : "<?php echo $this->Url->build( [ 'controller' => 'Orders', 'action' => 'pofsales' ] ); ?>",
        data: {warehouse:warehouse},
        success: function( response )
        {       
           $('.pofsales').html(response);
        }
      });
    };
  });  
<?= $this->Html->scriptEnd(); ?>
