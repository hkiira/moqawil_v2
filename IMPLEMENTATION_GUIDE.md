# Implementation Guide - Dashboard Components

## Getting Started

This guide explains how to implement the new dashboard components in your existing pages.

---

## Step 1: Basic Setup

### Include Required Files
All required CSS is already included in `default.ctp` layout:
```php
<?= $this->Html->css('/css/dashboard-custom.css') ?>
```

### Font Awesome Icons
Ensure Font Awesome is included in your head:
```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
```

---

## Step 2: Create Dashboard Header

Add this to your dashboard pages:

```php
<div class="dashboard-header">
    <h1>Your Dashboard Title</h1>
    <p>Subtitle or brief description</p>
</div>
```

**Example with icon:**
```php
<div class="dashboard-header">
    <h1>
        <i class="fas fa-chart-line mr-2"></i>Sales Dashboard
    </h1>
    <p>Real-time sales metrics and analytics</p>
</div>
```

---

## Step 3: Add Stat Cards

### Simple Stat Card
```php
<?= $this->element('dashboard/stat_card', [
    'title' => 'Total Sales',
    'value' => '50,000 DH',
    'label' => 'This month',
    'icon' => 'fa-shopping-bag',
    'type' => 'primary'
]) ?>
```

### With Change Indicator
```php
<?= $this->element('dashboard/stat_card', [
    'title' => 'Revenue Growth',
    'value' => '25%',
    'label' => 'Year over year',
    'icon' => 'fa-arrow-up',
    'type' => 'success',
    'change' => '+5.2% from last month'
]) ?>
```

### Complete Row of Cards
```php
<div class="row">
    <?= $this->element('dashboard/stat_card', [
        'title' => 'Total Orders',
        'value' => '1,234',
        'label' => 'This month',
        'icon' => 'fa-shopping-cart',
        'type' => 'primary'
    ]) ?>

    <?= $this->element('dashboard/stat_card', [
        'title' => 'Completed',
        'value' => '1,100',
        'label' => '89% of total',
        'icon' => 'fa-check-circle',
        'type' => 'success'
    ]) ?>

    <?= $this->element('dashboard/stat_card', [
        'title' => 'Pending',
        'value' => '134',
        'label' => '11% of total',
        'icon' => 'fa-hourglass-half',
        'type' => 'warning'
    ]) ?>

    <?= $this->element('dashboard/stat_card', [
        'title' => 'Failed',
        'value' => '12',
        'label' => '1% of total',
        'icon' => 'fa-times-circle',
        'type' => 'danger'
    ]) ?>
</div>
```

---

## Step 4: Add Chart Containers

### Basic Chart Card
```php
<div class="chart-card">
    <h5 class="chart-card-title">Sales Over Time</h5>
    <div id="sales-chart" style="height: 300px;">
        <!-- Your chart will render here -->
    </div>
</div>
```

### With Icon and Description
```php
<div class="chart-card">
    <h5 class="chart-card-title">
        <i class="fas fa-chart-line mr-2"></i>Revenue Trend
    </h5>
    <small class="text-muted">Last 30 days</small>
    <div id="revenue-chart" style="height: 350px;">
        <!-- Chart content -->
    </div>
</div>
```

### Two Column Layout
```php
<div class="row mt-4">
    <div class="col-lg-6">
        <div class="chart-card">
            <h5 class="chart-card-title">Orders by Status</h5>
            <div id="status-chart" style="height: 300px;"></div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="chart-card">
            <h5 class="chart-card-title">Top Products</h5>
            <div id="products-chart" style="height: 300px;"></div>
        </div>
    </div>
</div>
```

---

## Step 5: Create Data Tables

### Basic Table
```php
<div class="chart-card">
    <h5 class="chart-card-title">Recent Orders</h5>
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#001</td>
                    <td>Customer A</td>
                    <td>2,500 DH</td>
                    <td><span class="badge badge-success">Completed</span></td>
                    <td>2024-01-15</td>
                    <td><a href="#" class="btn btn-sm btn-light-primary">View</a></td>
                </tr>
                <tr>
                    <td>#002</td>
                    <td>Customer B</td>
                    <td>3,200 DH</td>
                    <td><span class="badge badge-warning">Pending</span></td>
                    <td>2024-01-16</td>
                    <td><a href="#" class="btn btn-sm btn-light-primary">View</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
```

### With Status Badges
```php
<td><span class="badge badge-success">Completed</span></td>
<td><span class="badge badge-warning">Pending</span></td>
<td><span class="badge badge-danger">Failed</span></td>
<td><span class="badge badge-info">Processing</span></td>
<td><span class="badge badge-primary">Active</span></td>
```

---

## Step 6: Loading States

### Loading Spinner
```php
<div class="loading-spinner">
    <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
    </div>
    <p class="mt-3">Loading dashboard data...</p>
</div>
```

### jQuery Implementation
```javascript
// Show loader
$('.dashboard').html(`
    <div class="loading-spinner">
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="mt-3">Loading data...</p>
    </div>
`);

// Fetch data and show
$.ajax({
    url: '/api/dashboard',
    success: function(response) {
        $('.dashboard').html(response);
    }
});
```

---

## Step 7: Empty States

### No Data Available
```php
<div class="empty-state">
    <div class="empty-state-icon">
        <i class="fas fa-inbox"></i>
    </div>
    <div class="empty-state-title">No Orders</div>
    <div class="empty-state-text">There are no orders to display</div>
</div>
```

### With Action Button
```php
<div class="empty-state">
    <div class="empty-state-icon">
        <i class="fas fa-plus-circle"></i>
    </div>
    <div class="empty-state-title">Start Creating</div>
    <div class="empty-state-text">You haven't created anything yet</div>
    <button class="btn btn-outline-primary mt-3">Create New</button>
</div>
```

---

## Real Data Integration

### From Database - Controller
```php
// In your controller
public function dashboard()
{
    $orders = $this->Orders->find()->count();
    $revenue = $this->Orders->find()
        ->select(['total' => 'SUM(amount)'])
        ->first();
    
    $this->set(compact('orders', 'revenue'));
}
```

### In View - Static Card
```php
<?= $this->element('dashboard/stat_card', [
    'title' => 'Total Orders',
    'value' => number_format($orders),
    'label' => 'Orders this month',
    'icon' => 'fa-shopping-cart',
    'type' => 'primary'
]) ?>
```

### In View - Currency Formatting
```php
<?= $this->element('dashboard/stat_card', [
    'title' => 'Total Revenue',
    'value' => number_format($revenue->total, 2) . ' DH',
    'label' => 'Revenue this month',
    'icon' => 'fa-wallet',
    'type' => 'success'
]) ?>
```

---

## Chart Integration (Chart.js)

### Include Chart.js
```html
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
```

### Create Simple Chart
```php
<div class="chart-card">
    <h5 class="chart-card-title">Sales Chart</h5>
    <canvas id="myChart" style="height: 300px;"></canvas>
</div>

<script>
const ctx = document.getElementById('myChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
        datasets: [{
            label: 'Sales',
            data: [100, 150, 120, 200, 180],
            borderColor: '#667eea',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
```

---

## Complete Dashboard Example

Here's a complete minimal dashboard:

```php
<?php $this->assign('title', 'Dashboard'); ?>

<div class="dashboard-header">
    <h1>Welcome to Your Dashboard</h1>
    <p>Overview of your business metrics</p>
</div>

<!-- Key Metrics -->
<div class="row">
    <?= $this->element('dashboard/stat_card', [
        'title' => 'Total Revenue',
        'value' => '150K DH',
        'label' => 'This month',
        'icon' => 'fa-wallet',
        'type' => 'primary'
    ]) ?>
    
    <?= $this->element('dashboard/stat_card', [
        'title' => 'Orders',
        'value' => '250',
        'label' => 'Total orders',
        'icon' => 'fa-shopping-cart',
        'type' => 'success'
    ]) ?>
    
    <?= $this->element('dashboard/stat_card', [
        'title' => 'Pending',
        'value' => '15',
        'label' => 'Need attention',
        'icon' => 'fa-hourglass',
        'type' => 'warning'
    ]) ?>
    
    <?= $this->element('dashboard/stat_card', [
        'title' => 'Users',
        'value' => '1.2K',
        'label' => 'Active users',
        'icon' => 'fa-users',
        'type' => 'info'
    ]) ?>
</div>

<!-- Charts -->
<div class="row mt-4">
    <div class="col-lg-6">
        <div class="chart-card">
            <h5 class="chart-card-title">Revenue Trend</h5>
            <canvas id="revenueChart" style="height: 300px;"></canvas>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="chart-card">
            <h5 class="chart-card-title">Orders Status</h5>
            <canvas id="statusChart" style="height: 300px;"></canvas>
        </div>
    </div>
</div>

<!-- Table -->
<div class="row mt-4">
    <div class="col-12">
        <div class="chart-card">
            <h5 class="chart-card-title">Recent Orders</h5>
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#001</td>
                        <td>John Doe</td>
                        <td>2,500 DH</td>
                        <td><span class="badge badge-success">Completed</span></td>
                        <td>2024-01-15</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
```

---

## Responsive Behavior

The components automatically adjust for different screen sizes:

- **Desktop (992px+):** 4 columns, full charts
- **Tablet (768px-992px):** 2 columns
- **Mobile (<768px):** 1 column, optimized touch targets

No additional CSS needed - Bootstrap handles it automatically.

---

## Troubleshooting

### Icons Not Showing
- Ensure Font Awesome is loaded
- Check icon class name (fa-icon-name)
- Clear browser cache

### Chart Not Displaying
- Ensure Chart.js is loaded
- Check canvas ID matches JavaScript
- Verify data format

### Styling Issues
- Clear cache (Ctrl+Shift+Delete)
- Check CSS file is linked
- Verify no conflicting CSS

### Responsive Issues
- Test in browser DevTools (F12)
- Check Bootstrap is loaded
- Verify viewport meta tag exists

---

## Performance Tips

1. **Lazy Load Charts**
   ```javascript
   // Only load chart when visible
   const observer = new IntersectionObserver(entries => {
       entries.forEach(entry => {
           if (entry.isIntersecting) {
               loadChart(entry.target);
           }
       });
   });
   observer.observe(chartElement);
   ```

2. **Minimize Data Requests**
   - Batch API calls
   - Cache responses
   - Use reasonable refresh rates

3. **Optimize Images**
   - Use SVG for icons
   - Compress images
   - Use webp format

---

## Accessibility

- Use semantic HTML
- Include ARIA labels
- Ensure color contrast
- Support keyboard navigation
- Provide text alternatives for icons

---

## Testing Checklist

- [ ] Test on mobile devices
- [ ] Test in different browsers
- [ ] Check responsive layout
- [ ] Verify all icons display
- [ ] Test with real data
- [ ] Check loading states
- [ ] Verify empty states
- [ ] Test accessibility

---

## Need Help?

1. Check DASHBOARD_QUICK_REFERENCE.md
2. Review example templates
3. Check dashboard-custom.css for styling
4. Review stat_card.ctp component

---

**Happy Dashboarding! 🎉**
