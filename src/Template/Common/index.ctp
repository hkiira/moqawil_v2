<?php use Cake\Routing\Router; ?>
<?php if(!$this->fetch('actionsubh')){
    
if (Router::getRequest('action')->getParam('action')=='index'){


            $test= $this->Html->link(__('<i class="fas fa-plus mr-2"></i> Créer un nouveau'), ['action' => 'add', $this->fetch('id')],['escape' => false,'class' => 'btn btn-light-primary font-weight-bolder btn-sm','type'=>'button']);

            $this->assign('actionsubh', $test);

        if(Router::getRequest('action')->getParam('controller')=='Packs'){
            if($this->fetch('id')){
                $test.=$this->Html->link(__('<i class="fas fa-print mr-2"></i> Imprimer'), ['action' => 'print', $this->fetch('id').'.pdf'],['escape' => false,'class' => 'btn btn-light-success font-weight-bolder btn-sm ml-2','type'=>'button']);
            }else{
                $test.=$this->Html->link(__('<i class="fas fa-print mr-2"></i> Imprimer'), ['action' => 'print.pdf'],['escape' => false,'class' => 'btn btn-light-success font-weight-bolder btn-sm ml-2','type'=>'button']);
            }

            $this->assign('actionsubh', $test);

        }
        if(Router::getRequest('action')->getParam('controller')=='Categories'){
            if($this->fetch('categoryid')){
                $test.=$this->Html->link(__('<i class="fas fa-print mr-2"></i> Imprimer'), ['controller'=>'Packs','action' => 'print', $this->fetch('categoryid').'.pdf'],['escape' => false,'class' => 'btn btn-light-success font-weight-bolder btn-sm ml-2','type'=>'button']);
            }
            $this->assign('actionsubh', $test);
        }

    } 
} ?>



<div class="card card-custom">

    <?php if(Router::getRequest('action')->getParam('controller')=='Orders'){ ?>

        <div class="card-header">

            <div class="card-title ventes">

                Total des ventes : MAD
            </div>

            <div class="card-toolbar">
                        <?= $this->Form->control('user', ['options' => $users,'class'=>'select2 form-control user','default'=>1,'label'=>'Filtrer par']); ?>

            </div>

        </div>

    <?php }elseif(Router::getRequest('action')->getParam('controller')=='Slips'){ ?>
        <?php if ($this->fetch('id')==1 || $this->fetch('id')==2): ?>
            <div class="card-header">

                <div class="card-title ventes">

                </div>

                <div class="card-toolbar">
                            <?= $this->Form->control('user', ['options' => $users,'class'=>'select2 form-control user','default'=>1,'label'=>'Filtrer par']); ?>

                </div>

            </div>
        <?php endif ?>
    <?php } ?>

    <div class="card-body">

        <?php $idtable = ($this->fetch('idtable')==NULL) ? "kt_datatable" : $this->fetch('idtable');?>

        <table class="table table-bordered table-hover table-checkable" id=<?= $idtable ?> style="margin-top: 13px !important">

            <thead>

                <tr>

                    <?= $this->fetch('td') ?>

                    

                </tr>

            </thead>

        </table>

    </div>

</div>

<?php $id=$this->fetch('id'); ?>
<?php $categoryid=$this->fetch('categoryid'); ?>

<?= $this->Html->scriptStart(['block'=>'script_bottom']) ?>

var HOST_URL = "<?php 
    if (isset($id)) {
        if(isset($categoryid)){
            echo $this->Url->build( ['action' => 'search',$id,$categoryid] ); 
        }else{
            echo $this->Url->build( ['action' => 'search',$id] ); 
        }
    } else {
        echo $this->Url->build( ['action' => 'search'] ); 
    }

?>";

<?php if ($this->fetch('js')=='slips' || $this->fetch('js')=='pofsales'): ?>

    var var1 = <?php echo  $id; ?>;

<?php endif ?>

<?= $this->Html->scriptEnd(); ?>



<?= $this->Html->script('/assets/plugins/custom/datatables/datatables.bundle.js', ['block' => 'script_bottom']) ?>

<?= $this->Html->script('/js/'.$this->fetch('js').'.js', ['block' => 'script_bottom']) ?>

<?= $this->Html->css('/assets/plugins/custom/datatables/datatables.bundle.css', ['block' => 'css_top']) ?>