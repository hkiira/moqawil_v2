# Orders Analytics Dashboard - Backend Logic Analysis (Milestone 1)

This report details the backend query logic, data structures, and implementation proposal for modifying `src/Controller/OrdersController.php` to calculate and return the required analytics data for the Orders Analytics Dashboard.

---

## 1. Executive Summary & Findings

The Orders Analytics Dashboard requires the following key metrics:
- **Total orders count**: The count of all orders within the filtered date range and scoped to the user's active warehouse.
- **Total revenue**: The sum of the line total (`quantity * price`) for all delivered order items (`orderpacks.statut = 6`).
- **Total commission**: The sum of commissions from delivered order items. If an item has an associated `turnover_id`, the commission is calculated as `line_total * turnover_commission / 100`. If no `turnover_id` is present, it defaults to `100%` of the line total.
- **Total pending orders count**: The count of orders with `statut = 1` (Pending Confirmation).
- **Time-series data**: Daily orders count and daily sales revenue (delivered items), with gap-filling for dates with zero activity.
- **Order status distribution**: Grouped counts of orders by status, mapped to user-friendly labels.

All metrics are subject to filters:
- **Date Range**: `keyword[start]` (inclusive) and `keyword[end]` (inclusive).
- **Seller/User**: Optional `keyword[user]` filter.
- **Warehouse/Point-of-Sale Scoping**: Strictly scoped based on the active session's default warehouse (`$this->Auth->user('defaultwh')`), matching the business logic found in existing controller actions like `ventes()`.

---

## 2. Query Logic & Database Schema

### A. Database Mappings
- **Orders Table (`orders`)**:
  - `id` (PK)
  - `created` (Datetime - used for date range filtering and time-series grouping)
  - `statut` (Int - `1`: Pending Confirmation, `5`: In Delivery, `6`: Delivered, `8`: Cancelled)
  - `user_id` (FK - linked to the seller/user)
  - `pofsale_id` (FK - linked to point of sale, used for warehouse scoping)
- **Orderpacks Table (`orderpacks`)**:
  - `id` (PK)
  - `order_id` (FK to `orders`)
  - `quantity` (Int)
  - `price` (Float)
  - `statut` (Int - `6` represents delivered items)
  - `turnover_id` (FK to `turnovers`)
- **Turnovers Table (`turnovers`)**:
  - `id` (PK)
  - `commission` (Double - e.g. `100` represents 100%)

### B. Logical Queries

#### 1. Total Orders Count
Count distinct orders matching filters:
```sql
SELECT COUNT(Orders.id) AS count
FROM orders Orders
WHERE DATE(Orders.created) >= :start
  AND DATE(Orders.created) <= :end
  AND Orders.pofsale_id IN (:pofsale_ids)
  -- (Optional User Filter)
  AND Orders.user_id = :user_id;
```

#### 2. Total Pending Orders Count
Count orders with status `1`:
```sql
SELECT COUNT(Orders.id) AS count
FROM orders Orders
WHERE Orders.statut = 1
  AND DATE(Orders.created) >= :start
  AND DATE(Orders.created) <= :end
  AND Orders.pofsale_id IN (:pofsale_ids);
```

#### 3. Total Revenue & Total Commission
Using an optimized single query on the `orderpacks` table to avoid memory overflow in PHP:
```sql
SELECT 
    SUM(Orderpacks.quantity * Orderpacks.price) AS total_revenue,
    SUM(
        CASE 
            WHEN Orderpacks.turnover_id IS NOT NULL 
            THEN (Orderpacks.quantity * Orderpacks.price * Turnovers.commission / 100) 
            ELSE (Orderpacks.quantity * Orderpacks.price) 
        END
    ) AS total_commission
FROM orderpacks Orderpacks
INNER JOIN orders Orders ON Orderpacks.order_id = Orders.id
LEFT JOIN turnovers Turnovers ON Orderpacks.turnover_id = Turnovers.id
WHERE Orderpacks.statut = 6
  AND DATE(Orders.created) >= :start
  AND DATE(Orders.created) <= :end
  AND Orders.pofsale_id IN (:pofsale_ids);
```

#### 4. Time-Series (Daily Activity)
Two queries grouped by date:
- **Orders Count**:
  ```sql
  SELECT DATE(Orders.created) AS date, COUNT(Orders.id) AS count
  FROM orders Orders
  WHERE DATE(Orders.created) >= :start AND DATE(Orders.created) <= :end
  GROUP BY DATE(Orders.created);
  ```
- **Revenue**:
  ```sql
  SELECT DATE(Orders.created) AS date, SUM(Orderpacks.quantity * Orderpacks.price) AS revenue
  FROM orderpacks Orderpacks
  INNER JOIN orders Orders ON Orderpacks.order_id = Orders.id
  WHERE Orderpacks.statut = 6
    AND DATE(Orders.created) >= :start AND DATE(Orders.created) <= :end
  GROUP BY DATE(Orders.created);
  ```

#### 5. Order Status Distribution
Group counts by status:
```sql
SELECT Orders.statut AS statut, COUNT(Orders.id) AS count
FROM orders Orders
WHERE DATE(Orders.created) >= :start AND DATE(Orders.created) <= :end
GROUP BY Orders.statut;
```

---

## 3. Proposed Code Structure for `OrdersController.php`

To keep the controller clean, we propose adding a single action `analytics()` to `src/Controller/OrdersController.php` that returns JSON directly. 

### Implementation Action in `OrdersController.php`
```php
    /**
     * Analytics endpoint for Milestone 1 Orders Analytics Dashboard
     *
     * @return \Cake\Http\Response|null
     */
    public function analytics()
    {
        // Force JSON view serialization
        $this->viewBuilder()->setClassName('Json');

        // 1. Get query parameters
        $keyword = $this->request->getQuery('keyword') ?: [];
        $start = isset($keyword['start']) && !empty($keyword['start']) ? $keyword['start'] : null;
        $end = isset($keyword['end']) && !empty($keyword['end']) ? $keyword['end'] : null;
        $user = isset($keyword['user']) && !empty($keyword['user']) ? $keyword['user'] : null;

        // Default to last 30 days if dates are not provided
        if (empty($start)) {
            $start = (new \DateTime('-30 days'))->format('Y-m-d');
        }
        if (empty($end)) {
            $end = (new \DateTime())->format('Y-m-d');
        }

        // 2. Resolve warehouse scoping (same logic as ventes())
        $defaultWh = $this->Auth->user('defaultwh');
        $qwh = [];
        if ($defaultWh) {
            $warehouse = $this->Orders->Pofsales->Warehouses->get($defaultWh, [
                'contain' => [
                    'Subwarehouses.Pofsales',
                    'Subwarehouses' => function ($q) {
                        return $q->where(['Subwarehouses.whtype_id' => 3]);
                    }
                ]
            ]);

            if ($warehouse->subwarehouses) {
                foreach ($warehouse->subwarehouses as $subwarehouse) {
                    foreach ($subwarehouse->pofsales as $pofsale) {
                        $qwh['OR'][$pofsale->id] = ['Orders.pofsale_id' => $pofsale->id];
                    }
                }
            }

            $pofsale = $this->Orders->Pofsales->find('all')->where(['warehouse_id' => $defaultWh]);
            if ($pofsale->count() > 0) {
                $qwh['OR'][$pofsale->last()->id] = ['Orders.pofsale_id' => $pofsale->last()->id];
            }
        }

        // 3. Build overall filter conditions for Orders
        $conditions = [];
        $conditions['DATE(Orders.created) >='] = $start;
        $conditions['DATE(Orders.created) <='] = $end;

        if (!empty($user)) {
            $conditions['Orders.user_id'] = $user;
        }
        if (!empty($qwh)) {
            $conditions[] = $qwh;
        }

        // --- CALCULATIONS ---

        // A. Total orders count
        $totalOrdersCount = $this->Orders->find()
            ->where($conditions)
            ->count();

        // B. Total pending orders count (statut = 1)
        $pendingConditions = $conditions;
        $pendingConditions['Orders.statut'] = 1;
        $totalPendingOrdersCount = $this->Orders->find()
            ->where($pendingConditions)
            ->count();

        // C. Total revenue & commission (delivered items only: Orderpacks.statut = 6)
        $revenueQuery = $this->Orders->Orderpacks->find();
        $revenueQuery
            ->select([
                'total_revenue' => $revenueQuery->func()->sum('Orderpacks.quantity * Orderpacks.price'),
                'total_commission' => $revenueQuery->newExpr()->add('SUM(CASE WHEN Orderpacks.turnover_id IS NOT NULL THEN (Orderpacks.quantity * Orderpacks.price * Turnovers.commission / 100) ELSE (Orderpacks.quantity * Orderpacks.price) END)')
            ])
            ->innerJoinWith('Orders')
            ->leftJoinWith('Turnovers')
            ->where($conditions)
            ->where(['Orderpacks.statut' => 6]);

        $revResults = $revenueQuery->first();
        $totalRevenue = $revResults ? (float)$revResults->total_revenue : 0.0;
        $totalCommission = $revResults ? (float)$revResults->total_commission : 0.0;

        // D. Time-series daily data (daily orders and daily sales revenue)
        // D1. Daily orders query
        $dailyOrdersQuery = $this->Orders->find();
        $dailyOrdersQuery
            ->select([
                'date' => 'DATE(Orders.created)',
                'count' => $dailyOrdersQuery->func()->count('Orders.id')
            ])
            ->where($conditions)
            ->group(['DATE(Orders.created)'])
            ->order(['DATE(Orders.created)' => 'ASC']);

        // D2. Daily revenue query
        $dailyRevenueQuery = $this->Orders->Orderpacks->find();
        $dailyRevenueQuery
            ->select([
                'date' => 'DATE(Orders.created)',
                'revenue' => $dailyRevenueQuery->func()->sum('Orderpacks.quantity * Orderpacks.price')
            ])
            ->innerJoinWith('Orders')
            ->where($conditions)
            ->where(['Orderpacks.statut' => 6])
            ->group(['DATE(Orders.created)'])
            ->order(['DATE(Orders.created)' => 'ASC']);

        // Generate complete date list (gap-filling)
        $timeSeries = [];
        $startDate = new \DateTime($start);
        $endDate = new \DateTime($end);
        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($startDate, $interval, $endDate->modify('+1 day'));

        foreach ($dateRange as $date) {
            $formattedDate = $date->format('Y-m-d');
            $timeSeries[$formattedDate] = [
                'date' => $formattedDate,
                'orders_count' => 0,
                'revenue' => 0.0
            ];
        }

        // Map daily orders
        foreach ($dailyOrdersQuery as $row) {
            $formattedDate = date('Y-m-d', strtotime($row->date));
            if (isset($timeSeries[$formattedDate])) {
                $timeSeries[$formattedDate]['orders_count'] = (int)$row->count;
            }
        }

        // Map daily revenue
        foreach ($dailyRevenueQuery as $row) {
            $formattedDate = date('Y-m-d', strtotime($row->date));
            if (isset($timeSeries[$formattedDate])) {
                $timeSeries[$formattedDate]['revenue'] = (float)$row->revenue;
            }
        }

        $timeSeriesData = array_values($timeSeries);

        // E. Order status distribution
        $statusQuery = $this->Orders->find();
        $statusQuery
            ->select([
                'statut' => 'Orders.statut',
                'count' => $statusQuery->func()->count('Orders.id')
            ])
            ->where($conditions)
            ->group(['Orders.statut']);

        $statusLabels = [
            1 => 'Attente de confirmation',
            5 => 'En cours de livraison',
            6 => 'Livrée',
            8 => 'Annulée'
        ];

        $statusDistribution = [];
        foreach ($statusQuery as $row) {
            $statutId = (int)$row->statut;
            $label = isset($statusLabels[$statutId]) ? $statusLabels[$statutId] : 'Autre';
            $statusDistribution[] = [
                'statut' => $statutId,
                'label' => $label,
                'count' => (int)$row->count
            ];
        }

        // 4. Return serialized response
        $this->set([
            'success' => true,
            'data' => [
                'total_orders' => $totalOrdersCount,
                'total_revenue' => $totalRevenue,
                'total_commission' => $totalCommission,
                'total_pending' => $totalPendingOrdersCount,
                'time_series' => $timeSeriesData,
                'status_distribution' => $statusDistribution
            ],
            '_serialize' => ['success', 'data']
        ]);
    }
```

---

## 4. Rationale & Key Architectural Decisions

1. **Database-Level Aggregation vs PHP Loops**:
   - In standard actions (e.g. `ventes()`), CakePHP performs nested loops on returned object sets to compute totals. This is acceptable for small datasets but triggers massive memory overheads and execution timeouts with larger ranges.
   - The proposed implementation performs SQL-level aggregation (`SUM` and conditional `CASE WHEN` statements) utilizing database joins. This is highly optimized and returns calculated totals instantly.
2. **Gap Filling for Time-Series**:
   - Group-by queries in SQL omit days with zero activity. This breaks frontend line charts.
   - The proposed logic constructs a full date period using PHP `DateTime` and initialized counts to `0`. It then populates counts and revenue for active days, ensuring a continuous time-series.
3. **Graceful Defaults**:
   - If `keyword[start]` or `keyword[end]` is missing, the endpoint defaults to a 30-day window (`-30 days` to `now`), preventing query errors.
4. **Scope Isolation**:
   - Calling `$this->viewBuilder()->setClassName('Json')` inside the action isolates the JSON response to the `analytics` endpoint, leaving the rest of the controller's HTML actions unchanged.
