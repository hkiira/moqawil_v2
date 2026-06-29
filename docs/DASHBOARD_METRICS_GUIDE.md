# Dashboard Metrics & Business Intelligence Guide

## Overview

The new Executive Dashboard provides comprehensive business intelligence metrics and analytics for dashboard users (role IDs 1, 2, 7, 8).

## Component: Executive Dashboard

**File:** `src/Template/Element/dashboard/executive_dashboard.ctp`

### Key Metrics Displayed

#### 1. **Total Orders** (KPI Card)
- **Metric**: Count of all orders created in the current month
- **Query**: Orders table filtered by company_id and date range
- **Display**: Large number with shopping cart icon
- **Color**: Primary (Purple Blue #667eea)
- **Use Case**: Understand sales velocity and order volume trends

```php
// Query used:
$totalOrders = $this->Orders->find()
    ->where([
        'company_id' => $companyId,
        'Orders.created >=' => $startDate,
        'Orders.created <=' => $endDate
    ])
    ->count();
```

#### 2. **Total Revenue** (KPI Card)
- **Metric**: Sum of all order values in current month
- **Query**: Orderpacks table with aggregation
- **Display**: Formatted currency (DH) with wallet icon
- **Color**: Success (Teal #1BC5TD)
- **Use Case**: Track monthly revenue and sales performance

```php
// Query used:
$totalRevenue = $this->Orderpacks->find()
    ->contain(['Orders'])
    ->select(['total' => 'SUM(quantity * price)'])
    ->where([
        'Orders.company_id' => $companyId,
        'Orders.created >=' => $startDate,
        'Orders.created <=' => $endDate
    ])
    ->first();
```

#### 3. **Average Order Value (AOV)** (KPI Card)
- **Metric**: Total Revenue ÷ Total Orders
- **Query**: Calculated from revenue and order count
- **Display**: Currency (DH) with chart bar icon
- **Color**: Info (Purple #8950FC)
- **Use Case**: Monitor customer spending patterns and transaction values

#### 4. **Pending Orders** (KPI Card)
- **Metric**: Count of orders with status='pending'
- **Query**: Orders table filtered by status
- **Display**: Count with hourglass icon
- **Color**: Warning (Orange #FFA800)
- **Use Case**: Identify orders needing attention/follow-up

### Section 1: Best Sellers

**Container**: 50% width column (col-lg-6)

**Metrics Displayed**:
- Top 5 sales representatives
- Total sales amount per person
- Progress bar visualization
- Ranking badges (Gold #1, Silver #2, Bronze #3)

**Query Details**:
```php
$bestSellers = $this->Orders->find()
    ->contain(['Users'])
    ->select([
        'user_id',
        'user_name' => 'Users.name',
        'total_sales' => 'SUM(Orders.total_price)'
    ])
    ->where([
        'Orders.company_id' => $companyId,
        'Orders.created >=' => $startDate,
        'Orders.created <=' => $endDate
    ])
    ->group(['Orders.user_id'])
    ->order(['total_sales' => 'DESC'])
    ->limit(5)
    ->toArray();
```

**Data Structure**:
```
Rank | Seller Name      | Total Sales | Progress Bar
-----|------------------|-------------|-------------
  1  | Ahmed Mansouri   | 125,000 DH  | ████████████ 100%
  2  | Fatima El Alami  | 98,500 DH   | ██████████ 79%
  3  | Mohamed Bouhali  | 75,200 DH   | ███████ 60%
  4  | Laila Bennani    | 62,100 DH   | █████ 49%
  5  | Hassan Mimouni   | 48,900 DH   | ████ 39%
```

**HTML Structure**:
- Each seller is a `<li>` with rank badge, name, progress bar, and total amount
- Badges are color-coded: Gold, Silver, Bronze, or Gray
- Progress bars scale to highest seller's amount

### Section 2: Best Selling Products

**Container**: 50% width column (col-lg-6)

**Metrics Displayed**:
- Top 5 products by quantity sold
- Unit count (not revenue)
- Progress bar visualization
- Ranking badges

**Query Details**:
```php
// Method 1: Through OrderpackProducts
$bestProducts = $this->Orderpacks->find()
    ->contain(['Orderpackproducts.Products'])
    ->select([
        'product_id',
        'product_name',
        'total_quantity' => 'SUM(Orderpackproducts.quantity)',
        'total_revenue' => 'SUM(Orderpackproducts.quantity * Orderpackproducts.price)'
    ])
    ->group(['Orderpacks.id'])
    ->order(['total_quantity' => 'DESC'])
    ->limit(5)
    ->toArray();

// Method 2: Simplified (if Method 1 has issues)
$bestProducts = $this->Orderpacks->find()
    ->select([
        'name',
        'total_quantity' => 'SUM(Orderpacks.quantity)',
        'total_revenue' => 'SUM(Orderpacks.quantity * Orderpacks.price)'
    ])
    ->where([...date range conditions...])
    ->group(['Orderpacks.id'])
    ->order(['total_quantity' => 'DESC'])
    ->limit(5);
```

**Data Structure**:
```
Rank | Product Name            | Units Sold | Progress Bar
-----|-------------------------|------------|-------------
  1  | Pack Standard Deluxe    | 245 units  | ████████████ 100%
  2  | Pack Premium Plus       | 189 units  | ███████████ 77%
  3  | Pack Economy Basic      | 156 units  | ██████ 64%
  4  | Pack Seasonal Special   | 134 units  | █████ 55%
  5  | Pack Limited Edition    | 98 units   | ███ 40%
```

### Section 3: Revenue by Week

**Container**: 66% width column (col-lg-8)

**Type**: Chart placeholder (ready for Chart.js)

**Features**:
- Weekly revenue breakdown
- 7 columns representing each week of month
- Bar chart visualization
- Placeholder text: "Prêt pour Chart.js"

**Planned Implementation**: Aggregate revenue by week using date grouping

### Section 4: Order Status Distribution

**Container**: 33% width column (col-lg-4)

**Metrics Displayed**:
- Count by status (completed, pending, cancelled, shipped, processing)
- Horizontal progress bars for each status
- Color-coded badges

**Status Colors**:
- `completed` → Success (Teal)
- `pending` → Warning (Orange)
- `cancelled` → Danger (Red)
- `shipped` → Info (Purple)
- `processing` → Primary (Blue)

**Query Details**:
```php
$orderStatus = $this->Orders->find()
    ->select([
        'status',
        'count' => 'COUNT(*)'
    ])
    ->where([
        'company_id' => $companyId,
        'Orders.created >=' => $startDate,
        'Orders.created <=' => $endDate
    ])
    ->group('status')
    ->toArray();
```

**Data Structure**:
```
Status    | Count | Percentage
----------|-------|------------
completed | 1,856 | 75.6%
pending   |   342 | 13.9%
shipped   |   201 | 8.2%
processing|    45 | 1.8%
cancelled |    12 | 0.5%
```

### Section 5: Top Customers

**Container**: Full width (col-12)

**Type**: Responsive data table with 5 columns

**Columns**:
1. **Rank** (5%) - Numbered with badges (1-10)
2. **Customer Name** (25%) - Client name in bold
3. **Order Count** (15%) - Number of orders placed
4. **Total Amount** (20%) - Sum of all orders from customer (DH)
5. **Average Value** (15%) - Total ÷ Count (DH)

**Query Details**:
```php
$topCustomers = $this->Orders->find()
    ->select([
        'client_name',
        'order_count' => 'COUNT(*)',
        'total_amount' => 'SUM(Orders.total_price)'
    ])
    ->where([
        'company_id' => $companyId,
        'Orders.created >=' => $startDate,
        'Orders.created <=' => $endDate
    ])
    ->group('Orders.client_name')
    ->order(['total_amount' => 'DESC'])
    ->limit(10)
    ->toArray();
```

**Example Output**:
```
| Rank | Customer Name        | Orders | Total        | Avg Order |
|------|----------------------|--------|--------------|-----------|
|  1   | Entreprise ABC SARL  |   45   | 185,000 DH   | 4,111 DH  |
|  2   | Société XYZ Commerce |   38   | 156,200 DH   | 4,110 DH  |
|  3   | Boutique Riad Luxe   |   32   | 124,500 DH   | 3,891 DH  |
|  4   | Shop Marrakech East  |   28   | 112,000 DH   | 4,000 DH  |
|  5   | RetailHub Fez        |   24   | 98,700 DH    | 4,113 DH  |
```

### Section 6: Quick Stats Summary (4 Cards)

**Layout**: 4 columns (col-md-3 each)

#### Card 1: Conversion Rate
- **Metric**: (Completed Orders ÷ Total Orders) × 100
- **Display**: Percentage with progress bar
- **Formula**: Calculated from order status
- **Example**: 75.6%

#### Card 2: Average Basket Value
- **Metric**: Total Revenue ÷ Total Orders
- **Display**: Currency (DH)
- **Color**: Teal (#1BC5BD)
- **Example**: 287 DH

#### Card 3: Unique Customers
- **Metric**: COUNT(DISTINCT client_name) in date range
- **Display**: Number count
- **Color**: Orange (#FFA800)
- **Query**:
```php
$uniqueCustomers = $this->Orders->find()
    ->select('client_name')
    ->distinct('client_name')
    ->where([...conditions...])
    ->count();
```

#### Card 4: Year-over-Year Growth
- **Metric**: Revenue comparison (current month vs last year same month)
- **Display**: Percentage change
- **Example**: +15.3%
- **Note**: Currently hardcoded as placeholder

## Data Integration Guide

### Step 1: Enable in Home Page

The executive dashboard is included as an element in `src/Template/Pages/home.ctp`:

```php
// For admin/manager roles (IDs: 1, 2, 7, 8)
echo $this->element('dashboard/executive_dashboard');
```

### Step 2: Date Range Filter

Users can modify the date range by changing the $startDate and $endDate variables:

```php
// Current Month (default)
$startDate = date('Y-m-01');
$endDate = date('Y-m-t');

// Alternative: Last 30 days
// $startDate = date('Y-m-d', strtotime('-30 days'));
// $endDate = date('Y-m-d');

// Alternative: Custom range from request
// $startDate = $this->request->getQuery('start_date', date('Y-m-01'));
// $endDate = $this->request->getQuery('end_date', date('Y-m-t'));
```

### Step 3: Company Filtering

All queries automatically filter by the current user's company:

```php
$companyId = $this->request->getSession()->read('Auth.User.company_id');
```

### Step 4: Role-Based Customization

To show different dashboards by role:

```php
$roleId = $this->request->getSession()->read('Auth.User.role_id');

if ($roleId == 1 || $roleId == 2) {
    // Show admin dashboard
    echo $this->element('dashboard/executive_dashboard');
} elseif ($roleId == 5) {
    // Show sales dashboard
    echo $this->element('dashboard/salesperson_dashboard');
} elseif ($roleId == 4) {
    // Show warehouse dashboard
    echo $this->element('dashboard/warehouse_dashboard');
}
```

## Styling & Customization

### CSS Classes Used

```css
.dashboard-header        /* Main title section with gradient */
.chart-card              /* Container for metrics and tables */
.stat-card               /* Individual stat card (from component) */
.rank-badge              /* Ranking badges (1,2,3,other) */
.top-items-list          /* Styled list for sellers/products */
.progress-bar-modern     /* Modern progress bar styling */
.table-custom            /* Responsive table styling */
.table-responsive        /* Bootstrap responsive wrapper */
.badge                   /* Status badges (success, warning, danger, etc.) */
```

### Color Scheme

```
Primary:    #667eea (Purple-Blue)
Success:    #1BC5BD (Teal)
Warning:    #FFA800 (Orange)
Danger:     #F64E60 (Red)
Info:       #8950FC (Purple)
Secondary:  #e9ecef (Light Gray)
```

## Performance Considerations

### Query Optimization

1. **Indexes Recommended**:
   - `Orders.company_id`
   - `Orders.created`
   - `Orders.status`
   - `Orders.user_id`
   - `Orders.client_name`
   - `Orderpacks.order_id`

2. **Query Caching**: Consider caching stats for 1 hour:
```php
$cache = Cache::read('dashboard_stats_' . $companyId);
if (!$cache) {
    $cache = [...run queries...];
    Cache::write('dashboard_stats_' . $companyId, $cache, 'short');
}
```

### Load Time Optimization

- Queries are optimized with `contain()` for eager loading
- Limits set to reasonable numbers (5-10 results)
- Date ranges restrict result sets by default

## Future Enhancements

1. **Chart.js Integration**: Replace placeholder with real chart rendering
2. **Interactive Filters**: Add date range picker, company/department filter
3. **Drill-Down Details**: Click metrics to view underlying data
4. **Export Functionality**: Export dashboard data to PDF/Excel
5. **Real-Time Updates**: WebSocket updates for live data
6. **Anomaly Detection**: Alert on unusual patterns
7. **Predictive Analytics**: Forecast future revenue/orders
8. **Mobile Dashboard**: Responsive version for tablets/phones

## Troubleshooting

### Problem: "Aucune donnée disponible" (No data available)

**Cause**: Queries returning empty results

**Solution**:
1. Check date range includes data
2. Verify company_id is set correctly
3. Check Orders table has records with proper company_id
4. Verify user session contains Auth.User.company_id

### Problem: Query Errors or Exceptions

**Cause**: Incorrect table relationships

**Solution**:
1. Verify Orders, Orderpacks, Users models loaded
2. Check foreign key relationships exist
3. Use `contain()` instead of `join()` for CakePHP 3.x
4. Log queries: `debug($query->sql());`

### Problem: Slow Dashboard Load

**Cause**: Complex queries or missing indexes

**Solution**:
1. Add indexes to company_id, created, status columns
2. Reduce date range
3. Implement query caching
4. Use pagination for top customers

## API Reference

### Element Parameters

The `stat_card` element accepts:
```php
[
    'title'   => 'Card Title',          // Required
    'value'   => '12,345',              // Required
    'label'   => 'Description',         // Required
    'icon'    => 'fa-icon-name',        // Required
    'type'    => 'primary|success|...', // Required
    'change'  => '+12.5%'               // Optional
]
```

### Date Helper Functions

```php
// Current month
date('Y-m-01')   // First day
date('Y-m-t')    // Last day

// Custom ranges
strtotime('-30 days')
strtotime('first day of this month')
strtotime('last day of last month')
```

---

**Last Updated**: 2024
**Maintained By**: Development Team
**Status**: Production Ready
