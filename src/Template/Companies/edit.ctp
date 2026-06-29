<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($company));
$this->assign('title', 'Modifier les infos de la société : '.$company->name);
$this->assign('subtitle', '');
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5"><?php
            echo $this->Form->control('adresse',['label'=>'Adresse']);
            echo $this->Form->control('tva',['label'=>'TVA']);
            echo $this->Form->control('city',['label'=>'Ville']);
            echo $this->Form->control('identifiantfiscale',['label'=>'Identifiant fiscale']);
            echo $this->Form->control('patente',['label'=>'Patente']);
            echo $this->Form->control('rc',['label'=>'R.C']);
            echo $this->Form->control('cnss',['label'=>'C.N.S.S']);
            echo $this->Form->control('ice',['label'=>'I.C.E']);
            echo $this->Form->control('phone',['label'=>'Téléphone']);
            echo $this->Form->control('mail',['label'=>'E-mail']);
        ?>
    </div>
        </div>
    </div>
    <!--end::Form-->
</div>
