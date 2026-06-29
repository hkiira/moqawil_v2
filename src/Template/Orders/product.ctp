 <?php
    if($unite->packunites){
        foreach($unite->packunites as $packunite){
            $uniteqte=$packunite->quantity;    
        }
             
    }else{
        $uniteqte=1; 
    }
 ?>
    <?php if($avoir=='avoir'){ ?>
        <td>
            <?= h($pack->code) ?> - <?= h($pack->title) ?> ( <?= h($unite->title) ?> )
            <?= $this->Form->control('orderpacks.'.$pack->id.'.pack_id', ['type' => 'hidden','class' => 'form-control','label' => false, 'value' => $pack->id]); ?>
        </td>
        <td>
            <?= $this->Form->control('orderpacks.'.$pack->id.'.whnature_id', ['options' => $whnatures, 'label'=>false,'class'=>'size select2']); ?>
        </td>

        <td width="20%">

            <?php if($pack->gstock==0){ ?>

              <?= $this->Form->control('orderpacks.'.$pack->id.'.quantity', ['type' => 'number','min'=>'0','class' => 'form-control','label' => false, 'value' => 0]); ?>

            <?php }else{ ?>

              <?= $this->Form->control('orderpacks.'.$pack->id.'.quantity', ['type' => 'number','min'=>'0','max'=>$quantity,'class' => 'form-control','label' => false, 'value' => 0]); ?>

              Quantité maximale : <?= $quantity ?>

            <?php } ?>
            <script>
                  packid=<?= $pack->id ?>;
                  $(function () {
                     $( "#orderpacks-"+packid+"-quantity" ).change(function() {
                        var max = parseInt($(this).attr('max'));
                        var min = parseInt($(this).attr('min'));
                        if ($(this).val() > max)
                        {
                            $(this).val(max);
                        }
                        else if ($(this).val() < min)
                        {
                            $(this).val(min);
                        }       
                      }); 
                  });
                </script>

        </td>

        <td>

          <?php foreach ($pack->prices as $key => $price): ?>

            <?php if ($key==0): ?>

                <?php if ($price->editted==1): ?>

                      <?= $this->Form->control('orderpacks.'.$pack->id.'.price', ['type' => 'number','min'=>$price->minp,'max'=>$price->maxp,'class' => 'form-control','label' => false, 'value' => $price->price]); ?>
                
                <?php else: ?>

                  <b><?= $price->price*$uniteqte  ?></b><br>

                <?php endif ?>

            <?php endif ?>

              <?php if ($price->trancheprices): ?>

                ( Qté : <?= $price->trancheprice->tranch->min.'-'.$price->trancheprice->tranch->max ?> - <?= $price->trancheprice->tranch->title ?> )

            <?php endif ?>

          <?php endforeach ?>

        </td>

        <td>

          <a class='btn btn-delete btn-danger'>-</a>

        </td>
    <?php }else{ ?>
        <td>
            <?= h($pack->code) ?> - <?= h($pack->title) ?> ( <?= h($unite->title) ?> )
            <?= $this->Form->control('orderpacks.'.$pack->id.'.pack_id', ['type' => 'hidden','class' => 'form-control','label' => false, 'value' => $pack->id]); ?>
            <?= $this->Form->control('orderpacks.'.$pack->id.'.unite', ['type' => 'hidden','class' => 'form-control','label' => false, 'value' => $unite->id]); ?>
        </td>

        <td width="20%">

            <?php if($pack->gstock==0){ ?>

              <?= $this->Form->control('orderpacks.'.$pack->id.'.quantity', ['type' => 'number','min'=>'0','class' => 'form-control','label' => false, 'value' => 0]); ?>

            <?php }else{ ?>

              <?= $this->Form->control('orderpacks.'.$pack->id.'.quantity', ['type' => 'number','min'=>'0','max'=>$quantity,'class' => 'form-control','label' => false, 'value' => 0]); ?>

              Quantité maximale : <?= $quantity ?>

            <?php } ?>
            <script>
                  packid=<?= $pack->id ?>;
                  $(function () {
                     $( "#orderpacks-"+packid+"-quantity" ).change(function() {
                        var max = parseInt($(this).attr('max'));
                        var min = parseInt($(this).attr('min'));
                        if ($(this).val() > max)
                        {
                            $(this).val(max);
                        }
                        else if ($(this).val() < min)
                        {
                            $(this).val(min);
                        }       
                      }); 
                  });
                </script>

        </td>

        <td>

          <?php foreach ($pack->prices as $key => $price): ?>

            <?php if ($key==0): ?>

                <?php if ($price->editted==1 ): ?>

                      <?= $this->Form->control('orderpacks.'.$pack->id.'.price', ['type' => 'number','min'=>$price->minp,'max'=>$price->maxp,'class' => 'form-control','label' => false, 'value' => $price->price*$uniteqte]); ?>
                
                <?php elseif($this->request->getSession()->read('Auth.User.role_id')==1 || $this->request->getSession()->read('Auth.User.role_id')==2 || $this->request->getSession()->read('Auth.User.role_id')==7 || $this->request->getSession()->read('Auth.User.role_id')==8): ?>
                      <?= $this->Form->control('orderpacks.'.$pack->id.'.price', ['type' => 'number','class' => 'form-control','label' => false, 'value' => $price->price*$uniteqte]); ?>
                <?php else: ?>

                  <b><?= $price->price*$uniteqte  ?></b><br>

                <?php endif ?>

            <?php endif ?>

              <?php if ($price->trancheprices): ?>

                ( Qté : <?= $price->trancheprice->tranch->min.'-'.$price->trancheprice->tranch->max ?> - <?= $price->trancheprice->tranch->title ?> )

            <?php endif ?>

          <?php endforeach ?>

        </td>

        <td>

          <a class='btn btn-delete btn-danger'>-</a>

        </td>
    <?php } ?>



<script>

    $('table').on('click', '.btn-delete', function () {

      tableID = '#' + $(this).closest('table').attr('id');

      $(this).closest('tr').remove();

      renumber_table(tableID);

    });

     

    function log (name, evt) {

      if (!evt) {

        var args = "{}";

      } else {

        var args = JSON.stringify(evt.params, function (key, value) {

          if (value && value.nodeName) return "[DOM node]";

          if (value instanceof $.Event) return "[$.Event]";

          return value;

        });

      }

    }



    function formatResultData (data) {

      if (!data.id) return data.text;

      if (data.element.selected) return

      return data.text;

    };



    $('.size').select2();

</script>

