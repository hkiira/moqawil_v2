<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($slip));
$this->assign('title', 'Ajouter un nouveau bon de '.$sliptype->title);
$this->assign('subtitle', 'vous pouvez ajouter un bon de '.$sliptype->title);
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-2"></div>
        <div class="col-xl-8">
            <div class="my-5">
                <?php if ($sliptype->id==4): ?>
                    <?= $this->element('slip/transfert')  ?>
                <?php elseif ($sliptype->id==3) : ?>
                    <?= $this->element('slip/deplacement')  ?>
                <?php elseif ($sliptype->id==2) : ?>
                    <?= $this->element('slip/dechargement')  ?>
                    
                <?php endif ?>
                
            </div>
        </div>
        <div class="col-xl-2"></div>
    </div>
</div>
