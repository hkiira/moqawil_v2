# Quick Start Guide - Dashboard Components

## Using the Stat Card Component

### Basic Example
```php
<?= $this->element('dashboard/stat_card', [
    'title' => 'Total Orders',
    'value' => '2,456',
    'label' => 'Orders this month',
    'icon' => 'fa-shopping-cart',
    'type' => 'primary'
]) ?>
```

### With Change Indicator
```php
<?= $this->element('dashboard/stat_card', [
    'title' => 'Revenue',
    'value' => '156,800 DH',
    'label' => 'Revenue this month',
    'icon' => 'fa-wallet',
    'type' => 'success',
    'change' => '+12.5%'
]) ?>
```

### Different Types

#### Primary (Blue/Purple)
```php
'type' => 'primary',
'icon' => 'fa-chart-line'
```

#### Success (Teal)
```php
'type' => 'success',
'icon' => 'fa-check-circle'
```

#### Warning (Orange)
```php
'type' => 'warning',
'icon' => 'fa-exclamation-triangle'
```

#### Danger (Red)
```php
'type' => 'danger',
'icon' => 'fa-times-circle'
```

#### Info (Purple)
```php
'type' => 'info',
'icon' => 'fa-info-circle'
```

## Dashboard Header

Add a header to your dashboard page:

```php
<div class="dashboard-header">
    <h1>Dashboard Title</h1>
    <p>Subtitle or description</p>
</div>
```

## Chart Card Container

For charts and complex content:

```php
<div class="chart-card">
    <h5 class="chart-card-title">Chart Title</h5>
    <div id="my-chart" style="height: 300px;">
        <!-- Chart will render here -->
    </div>
</div>
```

## Enhanced Table

For data tables:

```php
<div class="chart-card">
    <h5 class="chart-card-title">Table Title</h5>
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>Header 1</th>
                    <th>Header 2</th>
                    <th>Header 3</th>
                </tr>
            </thead>
            <tbody>
                <!-- Table rows -->
            </tbody>
        </table>
    </div>
</div>
```

## Common Icon References

### Business Icons
- `fa-shopping-cart` - Shopping
- `fa-box` - Package/Inventory
- `fa-wallet` - Money/Revenue
- `fa-users` - Customers
- `fa-truck` - Delivery
- `fa-warehouse` - Warehouse
- `fa-barcode` - Products

### Status Icons
- `fa-check-circle` - Complete/Success
- `fa-hourglass-half` - Pending/In Progress
- `fa-times-circle` - Error/Failed
- `fa-exclamation-triangle` - Warning
- `fa-info-circle` - Information

### Chart Icons
- `fa-chart-line` - Line Chart
- `fa-chart-bar` - Bar Chart
- `fa-chart-pie` - Pie Chart
- `fa-arrow-up` - Growth/Up
- `fa-arrow-down` - Decline/Down

## CSS Variables

Customize colors using CSS variables:

```css
:root {
    --primary-color: #667eea;
    --primary-dark: #764ba2;
    --success-color: #1BC5BD;
    --warning-color: #FFA800;
    --danger-color: #F64E60;
    --info-color: #8950FC;
    --light-bg: #f8f9fa;
}
```

## Loading States

Show a loading spinner while fetching data:

```php
<div class="loading-spinner">
    <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
    </div>
    <p class="mt-3">Loading data...</p>
</div>
```

## Empty State

Display when no data is available:

```php
<div class="empty-state">
    <div class="empty-state-icon">
        <i class="fas fa-inbox"></i>
    </div>
    <div class="empty-state-title">No Data</div>
    <div class="empty-state-text">There is no data to display</div>
</div>
```

## Responsive Grid

The stat cards automatically stack on mobile devices. Use Bootstrap's grid:

```php
<div class="row">
    <!-- Cards auto-wrap to full width on mobile -->
</div>
```

## Colors in Badges

```php
<!-- Success (Green) -->
<span class="badge badge-success">Livré</span>

<!-- Warning (Orange) -->
<span class="badge badge-warning">En cours</span>

<!-- Danger (Red) -->
<span class="badge badge-danger">Cancelled</span>

<!-- Info (Light Blue) -->
<span class="badge badge-info">En attente</span>

<!-- Primary (Purple) -->
<span class="badge badge-primary">Active</span>
```

## Button Styles

```php
<!-- Outline Primary Button -->
<button class="btn btn-outline-primary">Action</button>

<!-- With Icon -->
<button class="btn btn-outline-primary">
    <i class="fas fa-plus mr-2"></i>Add
</button>
```

## Complete Dashboard Example

See `src/Template/Element/dashboard/modern_example.ctp` for a full working example with:
- All stat card types
- Chart containers
- Data table
- Proper spacing and layout
