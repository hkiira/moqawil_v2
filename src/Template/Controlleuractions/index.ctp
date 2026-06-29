<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('edit',"");
$this->assign('title', 'Les actions par controlleurs');
?>
<div class="row">
    <div class="col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">Controlleurs</h4>
                <table id="datatablea" class="table table-striped dt-responsive nowrap table-vertical" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>controlleur</th>
                            <th>actions</th>
                            <th>actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($controlleurs as $controlleur): ?>
                        <tr>
                            <td><?= h($controlleur->title) ?></td>
                            <td>
                                <?php foreach ($controlleur->controlleuractions as $key => $controlleuraction): ?>
                                    <?= h($controlleuraction->action->title) ?><br>
                                <?php endforeach ?>
                            <td class="actions">
                                <?= $this->Html->link(__('Edit'), ['action' => 'edit', $controlleur->id]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="paginator">
                    <ul class="pagination">
                        <?= $this->Paginator->first('<< ' . __('first')) ?>
                        <?= $this->Paginator->prev('< ' . __('previous')) ?>
                        <?= $this->Paginator->numbers() ?>
                        <?= $this->Paginator->next(__('next') . ' >') ?>
                        <?= $this->Paginator->last(__('last') . ' >>') ?>
                    </ul>
                    <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
