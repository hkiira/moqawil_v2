<?php use Cake\Routing\Router; ?>
<?php $test= $this->Html->link(__('<i class="fas fa-plus mr-2"></i> Ajouter un nouveau'), ['action' => 'add', $this->fetch('id')],['escape' => false,'class' => 'btn btn-light-primary font-weight-bolder btn-sm','type'=>'button']);
    $this->assign('actionsubh', $test);
?>
<?php 
$this->assign('title', 'Liste des entrepôts');
$this->assign('subtitle', '');
 ?>
 <div class="row">
    <?php foreach ($warehouses as $key => $warehouse): ?>
        <div class="col-xl-6">
            <div class="card card-custom gutter-b card-stretch">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 mr-4 symbol symbol-65 symbol-circle">
                            <?= $this->Html->image('/assets/media/e-commerce/warehouse.png') ?>
                        </div>
                        <div class="d-flex flex-column mr-auto">
                            <?php if ($warehouse->whtype_id==1): ?>
                            <?= $this->Html->link(__($warehouse->title), ['action' => 'view', $warehouse->id],['class'=>'card-title text-hover-primary font-weight-bolder font-size-h5 text-dark mb-1']) ?>
                                
                            <?php else: ?>
                            <?= $this->Html->link(__($warehouse->pofsales[0]->pofsusers[0]->user->firstname.' '.$warehouse->pofsales[0]->pofsusers[0]->user->lastname.' ('.$warehouse->pofsales[0]->pofsusers[0]->user->role->title.')'), ['action' => 'view', $warehouse->id],['class'=>'card-title text-hover-primary font-weight-bolder font-size-h5 text-dark mb-1']) ?>
                                
                            <?php endif ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center">
                    <?php if ($warehouse->whtype_id==1): ?>
                    <?= $this->Html->link(__('Voir les Livreurs & Conventionnels'), ['action' => 'index', $warehouse->id],['class'=>'btn btn-success btn-sm text-uppercase font-weight-bolder mt-5 mt-sm-0 mr-auto mr-sm-0 ml-sm-auto']) ?>
                    <?php endif ?>
                    <?= $this->Html->link(__('Ajuster le stock'), ['action' => 'update', $warehouse->id],['class'=>'btn btn-primary btn-sm text-uppercase font-weight-bolder mt-5 mt-sm-0 mr-auto mr-sm-0 ml-sm-auto']) ?>
                </div>
                <!--end::Footer-->
            </div>
            <!--end::Card-->
        </div>
    <?php endforeach ?>
</div>
<div class="row">
  <div class="col-md-6">
    <p>
        <?= $this->Paginator->counter(['format' => __('Page {{page}} sur {{pages}}, affichage de {{current}} entrepôts sur {{count}}')]) ?>
    </p>
  </div>
  <div class="col-md-6">
    <ul class="pagination float-right">
      <?php
        $this->Paginator->setTemplates([
                'prevActive' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
                'prevDisabled' => '<li class="page-item disabled"><a class="page-link" href="{{url}}">{{text}}</a></li>',
                'number' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
                'current' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
                'nextActive' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
                'nextDisabled' => '<li class="page-item disabled"><a class="page-link" href="{{url}}">{{text}}</a></li>'
      ]); ?>
      <?= $this->Paginator->prev('< ' .'Précédent') ?>
      <?= $this->Paginator->numbers() ?>
      <?= $this->Paginator->next('Suivant'. ' >') ?>
    </ul>
  </div>
</div>