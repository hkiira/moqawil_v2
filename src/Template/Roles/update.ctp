<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('objet',$this->Form->create($role,['id'=>'kt_form_1']));
$this->assign('title', 'Modifier les accés du '.$role->title);
?>
<?php 
                $accesses=[];
                
                foreach($role->accesroles as $accesuser){
                        if(isset($accesses[$accesuser->access->controlleuraction->controlleur->id])){

                            
                            $accesses[$accesuser->access->controlleuraction->controlleur->id]['actions'][$accesuser->access->controlleuraction->action->id]=[
                                'id'=>$accesuser->id,
                                'title'=>$accesuser->access->controlleuraction->action->title,
                                'name'=>$accesuser->access->controlleuraction->action->name,
                                'hisown'=>$accesuser->hisown,
                                'authorised'=>$accesuser->authorised,
                                'display'=>$accesuser->access->controlleuraction->action->display
                            ];
                        }else{
                            $accesses[$accesuser->access->controlleuraction->controlleur->id]['controller']=[
                                'title'=>$accesuser->access->controlleuraction->controlleur->title,
                                'name'=>$accesuser->access->controlleuraction->controlleur->name,
                                'display'=>$accesuser->access->controlleuraction->controlleur->display
                            ];
                            $accesses[$accesuser->access->controlleuraction->controlleur->id]['actions'][$accesuser->access->controlleuraction->action->id]=[
                                'id'=>$accesuser->id,
                                'title'=>$accesuser->access->controlleuraction->action->title,
                                'name'=>$accesuser->access->controlleuraction->action->name,
                                'hisown'=>$accesuser->hisown,
                                'authorised'=>$accesuser->authorised,
                                'display'=>$accesuser->access->controlleuraction->action->display
                            ];
                        }
                    }
                 ?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-12 ">
            <?php foreach ($accesses as $key => $access): ?>
                <div class="table-responsive">
                <h3 class="mt-8 mb-4 header-title"><?= $access['controller']['title'] ?> - <?= $access['controller']['name'] ?></h3>
                    <table class="table">
                        <thead>
                       <tr>
                            <?php foreach ($access['actions'] as $key => $action): ?>
                                <th><?= $action['title'] ?>-<?= $action['name'] ?></th>
                            <?php endforeach ?>
                            <th>ses actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                                    <?php $auth=[0=>'Non',1=>'Oui']; ?>
                            <?php foreach ($access['actions'] as $key => $action): ?>
                                <td>
                                    <?= $this->Form->control('accesroles.'.$action['id'].'.id',['label'=>false,'type'=>'hidden','value'=>$action['id']]); ?>
                                    <?= $this->Form->control('accesroles.'.$action['id'].'.authorised',['label'=>false,'options' => $auth,'class'=>'form-control','value'=>$action['authorised'],"style"=>"width: 100px;"]); ?> 
                                </td>
                            <?php endforeach ?>
                                <td>
                                    <?= $this->Form->control('accesroles.'.$action['id'].'.hisown',['label'=>false,'options' => $auth,'class'=>'form-control','value'=>$action['hisown'],"style"=>"width: 100px;"]); ?> 
                                </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                <?php endforeach ?>
        </div>
    </div>
</div>
<?= $this->Html->script('/assets/js/pages/crud/forms/widgets/select2.js', ['block' => 'script_bottom']) ?>
<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>
    $.fn.select2.defaults.set("width", "100%");
    $('.select2').select2();
<?= $this->Html->scriptEnd(); ?>