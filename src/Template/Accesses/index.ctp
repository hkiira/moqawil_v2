<?php   
$this->extend('/Common/crud');
?>
<?php $this->loadHelper('Form', [
    'templates' => 'app_form',
]); 
$this->assign('title', 'Liste des accés');
?>
<div class="row">
    <div class="col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-body">
                <table id="datatablea" class="table table-striped dt-responsive nowrap table-vertical" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('controlleur_id') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('action_id') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('company_id') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('statut') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($accesses as $access): ?>
                        <tr>
                            <td><?= $this->Number->format($access->id) ?></td>
                            <td><?= $access->has('controlleuraction') ? $this->Html->link($access->controlleuraction->controlleur->name, ['controller' => 'Controlleurs', 'action' => 'view', $access->controlleuraction->controlleur->id]) : '' ?></td>
                            <td><?= $access->has('controlleuraction') ? $this->Html->link($access->controlleuraction->action->name, ['controller' => 'Actions', 'action' => 'view', $access->controlleuraction->action->id]) : '' ?></td>
                            <td><?= $access->has('company') ? $this->Html->link($access->company->name, ['controller' => 'Companies', 'action' => 'view', $access->company->id]) : '' ?></td>
                            <td><?= $this->Number->format($access->statut) ?></td>
                            <td><?= h($access->created) ?></td>
                            <td><?= h($access->modified) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['action' => 'view', $access->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['action' => 'edit', $access->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $access->id], ['confirm' => __('Are you sure you want to delete # {0}?', $access->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                 </table>
                 </div>
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
