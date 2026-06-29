<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Slider $slider
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Slider'), ['action' => 'edit', $slider->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Slider'), ['action' => 'delete', $slider->id], ['confirm' => __('Are you sure you want to delete # {0}?', $slider->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Sliders'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Slider'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Categories'), ['controller' => 'Categories', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Category'), ['controller' => 'Categories', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Brands'), ['controller' => 'Brands', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Brand'), ['controller' => 'Brands', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Slides'), ['controller' => 'Slides', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Slide'), ['controller' => 'Slides', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="sliders view large-9 medium-8 columns content">
    <h3><?= h($slider->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($slider->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Category') ?></th>
            <td><?= $slider->has('category') ? $this->Html->link($slider->category->title, ['controller' => 'Categories', 'action' => 'view', $slider->category->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Brand') ?></th>
            <td><?= $slider->has('brand') ? $this->Html->link($slider->brand->title, ['controller' => 'Brands', 'action' => 'view', $slider->brand->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Company') ?></th>
            <td><?= $slider->has('company') ? $this->Html->link($slider->company->name, ['controller' => 'Companies', 'action' => 'view', $slider->company->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($slider->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Statut') ?></th>
            <td><?= $this->Number->format($slider->statut) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($slider->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($slider->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Slides') ?></h4>
        <?php if (!empty($slider->slides)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Slider Id') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Photo') ?></th>
                <th scope="col"><?= __('Dir') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Statut') ?></th>
                <th scope="col"><?= __('Company Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($slider->slides as $slides): ?>
            <tr>
                <td><?= h($slides->id) ?></td>
                <td><?= h($slides->slider_id) ?></td>
                <td><?= h($slides->title) ?></td>
                <td><?= h($slides->photo) ?></td>
                <td><?= h($slides->dir) ?></td>
                <td><?= h($slides->created) ?></td>
                <td><?= h($slides->modified) ?></td>
                <td><?= h($slides->statut) ?></td>
                <td><?= h($slides->company_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Slides', 'action' => 'view', $slides->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Slides', 'action' => 'edit', $slides->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Slides', 'action' => 'delete', $slides->id], ['confirm' => __('Are you sure you want to delete # {0}?', $slides->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
