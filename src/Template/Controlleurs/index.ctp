<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('title', 'Liste des controlleurs');
?>
<div class="row">
    <div class="col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-body">
                <table id="datatablea" class="table table-striped dt-responsive nowrap table-vertical" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('title') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('display') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($controlleurs as $controlleur): ?>
                        <tr>
                            <td><?= $this->Number->format($controlleur->id) ?></td>
                            <td><?= h($controlleur->title) ?></td>
                            <td><?= h($controlleur->name) ?></td>
                            <td><?= $this->Number->format($controlleur->display) ?></td>
                            <td><?= $this->Number->format($controlleur->statut) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['action' => 'view', $controlleur->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['action' => 'edit', $controlleur->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $controlleur->id], ['confirm' => __('Are you sure you want to delete # {0}?', $controlleur->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
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
