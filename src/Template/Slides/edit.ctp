<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Slide $slide
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $slide->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $slide->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Slides'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Sliders'), ['controller' => 'Sliders', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Slider'), ['controller' => 'Sliders', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Companies'), ['controller' => 'Companies', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Company'), ['controller' => 'Companies', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="slides form large-9 medium-8 columns content">
    <?= $this->Form->create($slide) ?>
    <fieldset>
        <legend><?= __('Edit Slide') ?></legend>
        <?php
            echo $this->Form->control('slider_id', ['options' => $sliders]);
            echo $this->Form->control('title');
            echo $this->Form->control('photo');
            echo $this->Form->control('dir');
            echo $this->Form->control('statut');
            echo $this->Form->control('company_id', ['options' => $companies, 'empty' => true]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
