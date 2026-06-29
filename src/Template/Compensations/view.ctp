<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Compensation $compensation
 */
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= __('Compensation') ?> #<?= h($compensation->code) ?></h3>
                <div class="card-tools">
                    <?= $this->Html->link(__('Print PDF'), ['action' => 'print', $compensation->id], ['class' => 'btn btn-info btn-sm', 'target' => '_blank']) ?>
                    <?= $this->Html->link(__('List Compensations'), ['action' => 'index'], ['class' => 'btn btn-secondary btn-sm']) ?>
                </div>
            </div>
            <div class="card-body">
                <?php
                // Compute total amount across all related orders (quantity * price)
                $compensationTotal = 0;
                if (!empty($compensation->orders)) {
                    foreach ($compensation->orders as $o) {
                        $orderTotal = 0;
                        if (!empty($o->orderpacks)) {
                            foreach ($o->orderpacks as $op) {
                                if ((int)$op->statut !== 8) {
                                    $orderTotal += (float)$op->quantity * (float)$op->price;
                                }
                            }
                        }

                        if ((int)$o->ordertype_id === 2) {
                            $compensationTotal -= $orderTotal;
                        } else {
                            $compensationTotal += $orderTotal;
                        }
                    }
                }
                ?>
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px;"><?= __('Code') ?></th>
                        <td><?= h($compensation->code) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('User') ?></th>
                        <td><?= $compensation->has('user') ? $this->Html->link($compensation->user->firstname . ' ' . $compensation->user->lastname, ['controller' => 'Users', 'action' => 'view', $compensation->user->id]) : '' ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Status') ?></th>
                        <td>
                            <?php
                            $statusLabels = [
                                0 => '<span class="badge badge-warning">' . __('Pending') . '</span>',
                                1 => '<span class="badge badge-info">' . __('Processed') . '</span>',
                                2 => '<span class="badge badge-success">' . __('Paid') . '</span>',
                                3 => '<span class="badge badge-danger">' . __('Cancelled') . '</span>'
                            ];
                            echo isset($statusLabels[$compensation->statut]) ? $statusLabels[$compensation->statut] : $compensation->statut;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?= __('Start Date (Date Depart)') ?></th>
                        <td><?= h($compensation->datedepart) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('End Date (Date Fin)') ?></th>
                        <td><?= h($compensation->datefin) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Created') ?></th>
                        <td><?= h($compensation->created) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Modified') ?></th>
                        <td><?= h($compensation->modified) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Total Amount') ?></th>
                        <td><strong><?= number_format($compensationTotal, 2) ?> DH</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Related Orders Card -->
        <?php if (!empty($compensation->orders)): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title"><?= __('Related Orders') ?></h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th><?= __('Id') ?></th>
                                <th><?= __('Code') ?></th>
                                <th><?= __('Customer') ?></th>
                                <th><?= __('User') ?></th>
                                <th><?= __('Type') ?></th>
                                <th><?= __('Created') ?></th>
                                <th><?= __('Total') ?></th>
                                <th><?= __('Status') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $statusLabels = [
                                1 => 'attente de confirmation',
                                5 => 'En cours de livraison',
                                6 => 'Livrée',
                                8 => 'Annulée'
                            ];
                            ?>
                            <?php foreach ($compensation->orders as $order): ?>
                            <?php 
                                $orderTotal = 0;
                                if (!empty($order->orderpacks)) {
                                    foreach ($order->orderpacks as $op) {
                                        if ((int)$op->statut !== 8) {
                                            $orderTotal += (float)$op->quantity * (float)$op->price;
                                        }
                                    }
                                }
                            ?>
                            <tr>
                                <td><?= h($order->id) ?></td>
                                <td><?= h($order->code) ?></td>
                                <td><?= $order->has('customer') ? h($order->customer->name) : '-' ?></td>
                                <td><?= $order->has('user') ? h($order->user->firstname . ' ' . $order->user->lastname) : '-' ?></td>
                                <td><?= $order->has('ordertype') ? h($order->ordertype->title) : '-' ?></td>
                                <td><?= h($order->created) ?></td>
                                <td><?= number_format($orderTotal, 2) ?> DH</td>
                                <td>
                                    <?php if (isset($statusLabels[$order->statut])): ?>
                                        <span class="badge badge-<?= $order->statut == 1 ? 'warning' : ($order->statut == 5 ? 'info' : ($order->statut == 6 ? 'success' : 'danger')) ?>">
                                            <?= h($statusLabels[$order->statut]) ?>
                                        </span>
                                    <?php else: ?>
                                        <?= h($order->statut) ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
