<?php
/**
 * Modern Stat Card Component
 * Usage: $this->element('dashboard/stat_card', [
 *     'title' => 'Total Orders',
 *     'value' => '1,234',
 *     'label' => 'Orders this month',
 *     'icon' => 'fa-shopping-cart',
 *     'type' => 'primary' // primary, success, warning, danger, info
 * ])
 */

$type = $type ?? 'primary';
$icon = $icon ?? 'fa-chart-bar';
$title = $title ?? 'Title';
$value = $value ?? '0';
$label = $label ?? 'Description';
$change = $change ?? null;
?>

<div class="col-lg-6 col-xl-4">
    <div class="stat-card <?= $type; ?>">
        <div class="stat-card-label">
            <?= $title; ?>
        </div>
        <div class="stat-card-value">
            <?= $value; ?>
        </div>
        <div class="stat-card-desc">
            <?= $label; ?>
            <?php if ($change): ?>
                <span class="font-weight-bold text-success ml-2">
                    <i class="fas fa-arrow-up"></i> <?= $change; ?>
                </span>
            <?php endif; ?>
        </div>
    </div>
</div>
