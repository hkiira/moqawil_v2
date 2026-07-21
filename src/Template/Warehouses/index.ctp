<?php use Cake\Routing\Router; ?>
<?php 
$addBtn = $this->Html->link(
    __('<i class="fas fa-plus mr-2 font-size-sm"></i> Ajouter un nouveau'), 
    ['action' => 'add', $this->fetch('id')],
    ['escape' => false, 'class' => 'btn btn-primary font-weight-bolder btn-sm shadow-sm', 'type' => 'button']
);
$this->assign('actionsubh', $addBtn);

$this->assign('title', 'Liste des entrepôts');
$this->assign('subtitle', 'Gérez les stocks de vos entrepôts principaux et véhicules vendeurs.');
?>

<div class="row">
    <?php foreach ($warehouses as $key => $warehouse): ?>
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-6">
            <!--begin::Card-->
            <div class="card card-custom card-stretch shadow-sm hover-shadow transition-all duration-200" style="border: 1px solid rgba(0,0,0,.05); border-radius: 0.85rem;">
                <!--begin::Body-->
                <div class="card-body pt-8 d-flex flex-column justify-content-between" style="min-height: 280px;">
                    <div>
                        <!--begin::Toolbar-->
                        <div class="d-flex justify-content-between align-items-center mb-5">
                            <span class="text-muted font-weight-bold font-size-sm">#<?= h($warehouse->code) ?></span>
                            <?php if ($warehouse->statut == 1): ?>
                                <span class="label label-light-success label-inline label-bold">Actif</span>
                            <?php else: ?>
                                <span class="label label-light-danger label-inline label-bold">Inactif</span>
                            <?php endif; ?>
                        </div>
                        <!--end::Toolbar-->
                        
                        <!--begin::User-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Symbol-->
                            <div class="symbol symbol-60 mr-4 symbol-light-primary">
                                <span class="symbol-label" style="border-radius: 0.75rem;">
                                    <?php if ($warehouse->whtype_id == 1): ?>
                                        <span class="svg-icon svg-icon-2x svg-icon-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M12 2L2 12H5V21H19V12H22L12 2Z" fill="currentColor"/>
                                            </svg>
                                        </span>
                                    <?php else: ?>
                                        <span class="svg-icon svg-icon-2x svg-icon-success">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M20 8H17V4H3C2.45 4 2 4.45 2 5V17H4C4 18.66 5.34 20 7 20C8.66 20 10 18.66 10 17H14C14 18.66 15.34 20 17 20C18.66 20 20 18.66 20 17H22V11L20 8M7 18.5C6.17 18.5 5.5 17.83 5.5 17C5.5 16.17 6.17 15.5 7 15.5C7.83 15.5 8.5 16.17 8.5 17C8.5 17.83 7.83 18.5 7 18.5M17 18.5C16.17 18.5 15.5 17.83 15.5 17C15.5 16.17 16.17 15.5 17 15.5C17.83 15.5 18.5 16.17 18.5 17C18.5 17.83 17.83 18.5 17 18.5M17 12V9.5H19.5L20.8 11.2V12H17Z" fill="currentColor"/>
                                            </svg>
                                        </span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <!--end::Symbol-->
                            
                            <!--begin::Title-->
                            <div class="d-flex flex-column">
                                <?php if ($warehouse->whtype_id == 1): ?>
                                    <?= $this->Html->link(h($warehouse->title), ['action' => 'view', $warehouse->id], ['class' => 'text-dark-75 font-weight-bolder text-hover-primary font-size-lg mb-1']) ?>
                                    <span class="text-muted font-weight-bold font-size-xs">Entrepôt principal</span>
                                <?php else: ?>
                                    <?php 
                                    $fullName = 'Vendeur / Livreur';
                                    $roleTitle = 'Vendeur';
                                    if (!empty($warehouse->pofsales[0]->pofsusers[0]->user)) {
                                        $u = $warehouse->pofsales[0]->pofsusers[0]->user;
                                        $fullName = $u->firstname . ' ' . $u->lastname;
                                        if (!empty($u->role->title)) {
                                            $roleTitle = $u->role->title;
                                        }
                                    }
                                    ?>
                                    <?= $this->Html->link(h($fullName), ['action' => 'view', $warehouse->id], ['class' => 'text-dark-75 font-weight-bolder text-hover-primary font-size-lg mb-1']) ?>
                                    <span class="text-muted font-weight-bold font-size-xs"><?= h($roleTitle) ?></span>
                                <?php endif; ?>
                            </div>
                            <!--end::Title-->
                        </div>
                        <!--end::User-->
                        
                        <!--begin::Info-->
                        <div class="mb-5 p-3 rounded-lg" style="background-color: #f8f9fa; border-radius: 0.5rem;">
                            <div class="d-flex justify-content-between align-items-center mb-2 font-size-sm">
                                <span class="text-muted font-weight-bold">Sous-dépôts:</span>
                                <span class="text-dark font-weight-bolder"><?= count($warehouse->subwarehouses) ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center font-size-sm">
                                <span class="text-muted font-weight-bold">Type de stockage:</span>
                                <span class="text-dark font-weight-bolder">
                                    <?= $warehouse->whtype_id == 1 ? 'Fixe (Bâtiment)' : 'Mobile (Véhicule)' ?>
                                </span>
                            </div>
                        </div>
                        <!--end::Info-->
                    </div>
                    
                    <!--begin::Actions-->
                    <div class="d-flex align-items-center justify-content-between pt-4 border-top">
                        <?php if ($warehouse->whtype_id == 1): ?>
                            <?= $this->Html->link('<i class="fas fa-eye mr-2"></i> Voir Dépôts', ['action' => 'index', $warehouse->id], ['escape' => false, 'class' => 'btn btn-light-success btn-xs font-weight-bolder px-3 py-2']) ?>
                            <?= $this->Html->link('<i class="fas fa-sliders-h mr-2"></i> Ajuster Stock', ['action' => 'update', $warehouse->id], ['escape' => false, 'class' => 'btn btn-light-primary btn-xs font-weight-bolder px-3 py-2']) ?>
                        <?php else: ?>
                            <?= $this->Html->link('<i class="fas fa-sliders-h mr-2"></i> Ajuster Stock du Vendeur', ['action' => 'update', $warehouse->id], ['escape' => false, 'class' => 'btn btn-light-primary btn-xs font-weight-bolder px-3 py-2 w-100 text-center']) ?>
                        <?php endif; ?>
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Body-->
            </div>
            <!--end::Card-->
        </div>
    <?php endforeach ?>
</div>

<!--begin::Pagination-->
<div class="row mt-4">
    <div class="col-md-6 d-flex align-items-center">
        <p class="text-muted font-size-sm mb-0">
            <?= $this->Paginator->counter(['format' => __('Page {{page}} sur {{pages}}, affichage de {{current}} entrepôts sur {{count}}')]) ?>
        </p>
    </div>
    <div class="col-md-6">
        <ul class="pagination float-right mb-0">
            <?php
            $this->Paginator->setTemplates([
                'prevActive' => '<li class="page-item"><a class="page-link font-size-sm" href="{{url}}">{{text}}</a></li>',
                'prevDisabled' => '<li class="page-item disabled"><a class="page-link font-size-sm" href="{{url}}">{{text}}</a></li>',
                'number' => '<li class="page-item"><a class="page-link font-size-sm" href="{{url}}">{{text}}</a></li>',
                'current' => '<li class="page-item active"><a class="page-link font-size-sm" href="{{url}}">{{text}}</a></li>',
                'nextActive' => '<li class="page-item"><a class="page-link font-size-sm" href="{{url}}">{{text}}</a></li>',
                'nextDisabled' => '<li class="page-item disabled"><a class="page-link font-size-sm" href="{{url}}">{{text}}</a></li>'
            ]); ?>
            <?= $this->Paginator->prev('< ' .'Précédent') ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next('Suivant'. ' >') ?>
        </ul>
    </div>
</div>
<!--end::Pagination-->