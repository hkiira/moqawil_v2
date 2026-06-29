<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pricetype $pricetype
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $pricetype->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $pricetype->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Pricetypes'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="pricetypes form large-9 medium-8 columns content">
    <?= $this->Form->create($pricetype) ?>
    <fieldset>
        <legend><?= __('Edit Pricetype') ?></legend>
        <?php
            echo $this->Form->control('title');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
