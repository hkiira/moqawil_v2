<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('title', 'Détails du : '.$pack->title);
$this->assign('edit', ' <button type="button" class="btn btn-primary font-weight-bold" onclick="window.print();">Imprimer</button>');
?>
<div class="card-body">
    <div class="row pb-4">
        <div class="col-lg-6">
            <h3 class="h3"> 
                STOCK DISPONIBLE : <?= intVal($pack->whproducts[0]->quantity/$pack->packunites[0]->quantity).' '.$pack->packunites[0]->unite->title  ?>
            </h3>
            <h4 class="h4"> 
                STOCK DISPONIBLE : <?= intVal($pack->whproducts[0]->quantity).' '.$pack->packunites[0]->unite->parentunite->title  ?>
            </h4>
            </div>
            <div class="col-lg-6">
            <h3 class="h3"> 
                STOCK ENDOMAGEE : <?= intVal(($pack->whproducts[1]->quantity+$pack->whproducts[2]->quantity+$pack->whproducts[3]->quantity)/$pack->packunites[0]->quantity).' '.$pack->packunites[0]->unite->title  ?>
            </h3>
            <h4 class="h4"> 
                STOCK ENDOMAGEE : <?= intVal(($pack->whproducts[1]->quantity+$pack->whproducts[2]->quantity+$pack->whproducts[3]->quantity)).' '.$pack->packunites[0]->unite->parentunite->title  ?>
            </h4>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 col-6 pb-2">
            <a href="#" class="card card-custom bg-dark bg-hover-state-dark mb-0">
                <div class="text-inverse-dark font-weight-bolder font-size-h5 my-1 mx-5"> ACHATS <p class="font-weight-bold text-inverse-dark font-size-sm achats">(Historique des achats)</p></div>
            </a>
        </div>
        <div class="col-lg-4 col-6">
            <a href="#" class="card card-custom bg-dark bg-hover-state-dark mb-0">
                <div class="text-inverse-dark font-weight-bolder font-size-h5 my-1 mx-5">VENTES <p class="font-weight-bold text-inverse-dark font-size-sm ventes">(Historique des ventes)</p></div>
            </a>
            
        </div>
        <div class="col-lg-4 col-6">
            <a href="#" class="card card-custom bg-dark bg-hover-state-dark mb-0">
                <div class="text-inverse-dark font-weight-bolder font-size-h5 my-1 mx-2">PRIX <p class="font-weight-bold text-inverse-dark font-size-sm prices">(Historique des changement de prix)</p></div>
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class=" pr-5">
            <div class="col-xl-6 col-sm-6">
                <input type="text" class="form-control" name="daterange" id="daterange" placeholder="Sélectionner les dates" readonly="">
            </div>
        </div>
    </div>
    <div class="infos">
    </div>
</div>
   
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $(".achats").click(function(){
        $.ajax({
        method: 'get',
        url : "<?php echo $this->Url->build( [ 'controller' => 'Packs', 'action' => 'achats',$pack->id] ); ?>",
        success: function( response )
        {       
          $( '.infos' ).html(response);
        }
      });
    });

    $(".ventes").click(function(){
        $.ajax({
        method: 'get',
        url : "<?php echo $this->Url->build( [ 'controller' => 'Packs', 'action' => 'ventes',$pack->id] ); ?>",
        success: function( response )
        {       
          $( '.infos' ).html(response);
        }
      });
    });

    $(".prices").click(function(){
        $.ajax({
        method: 'get',
        url : "<?php echo $this->Url->build( [ 'controller' => 'Packs', 'action' => 'prices',$pack->id] ); ?>",
        success: function( response )
        {       
          $( '.infos' ).html(response);
        }
      });
    });
<?= $this->Html->scriptEnd(); ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    var start = moment().subtract(2, 'years');
    var end = moment();

    function cb(start, end) {
        $('#daterange span').html(start.format('DD/M/yyyy') + ' - ' + end.format('DD/M/yyyy'));
    }

    $('#daterange').daterangepicker({
        startDate: start,
        endDate: end,
        locale: {
             format: 'DD/MM/yyyy',
            "separator": " - ",
            "applyLabel": "Appliquer",
            "cancelLabel": "Annuler",
            "fromLabel": "Du",
            "toLabel": "Au",
            "customRangeLabel": "Personnalisé",
            "daysOfWeek": [
                "Di",
                "Lu",
                "Ma",
                "Me",
                "Je",
                "Ve",
                "Sa"
            ],
            "monthNames": [
                "Janvier",
                "Février",
                "Mars",
                "Avril",
                "Mai",
                "Juin",
                "Juillet",
                "Aôut",
                "Septembre",
                "Octobre",
                "Novembre",
                "Décembre"
            ],
            "firstDay": 1
        },

    }, cb);

    cb(start, end);
<?= $this->Html->scriptEnd(); ?>