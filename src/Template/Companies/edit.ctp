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
            echo '<hr><h4>Configuration des préfixes</h4>';
            echo $this->Form->control('code_prefixes.Users', ['label' => 'Préfixe Utilisateurs', 'value' => $company->code_prefixes['Users'] ?? '']);
            echo $this->Form->control('code_prefixes.Orders', ['label' => 'Préfixe Commandes', 'value' => $company->code_prefixes['Orders'] ?? '']);
            echo $this->Form->control('code_prefixes.Customers', ['label' => 'Préfixe Clients', 'value' => $company->code_prefixes['Customers'] ?? '']);
            echo $this->Form->control('code_prefixes.Shippings', ['label' => 'Préfixe Livraisons', 'value' => $company->code_prefixes['Shippings'] ?? '']);
            echo $this->Form->control('code_prefixes.Exitslips', ['label' => 'Préfixe Bons de Sortie', 'value' => $company->code_prefixes['Exitslips'] ?? '']);
            echo $this->Form->control('code_prefixes.Slips', ['label' => 'Préfixe Bons', 'value' => $company->code_prefixes['Slips'] ?? '']);
        ?>
    </div>
        </div>
    </div>
    <!--end::Form-->
</div>
