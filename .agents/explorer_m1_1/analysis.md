# Milestone 1: Orders Analytics Dashboard Backend Logic Analysis

This document provides a detailed read-only investigation and proposal on how to modify `src/Controller/OrdersController.php` to calculate and return the required analytics data for the Orders Analytics Dashboard.

---

## 1. Executive Summary

Milestone 1 requires adding a backend endpoint to `OrdersController.php` that returns key performance indicators (KPIs) and time-series data for the Orders Analytics Dashboard. The controller needs to extract:
- **Total Orders Count**: All matching orders.
- **Total Revenue**: Sum of prices of delivered orderpacks (where `statut = 6`).
- **Total Commission**: Sum of commissions computed based on delivered orderpacks and their associated turnovers.
- **Total Pending Orders**: Count of orders with `statut = 1`.
- **Time-Series Data**: Daily order counts and daily revenue.
- **Status Distribution**: Breakdown of order counts grouped by order status.

All metrics must respect filters for date range (`keyword[start]` and `keyword[end]`) and an optional seller/user (`keyword[user]`). The queries must also implement multi-tenancy scoping (filtering by `company_id`) and warehouse isolation (filtering by `pofsale_id` based on the user's `defaultwh`).

---

## 2. Existing Data Models and Relationships

The CakePHP database models relevant to this logic are:

### A. `Orders` Table (`src/Model/Table/OrdersTable.php`)
- **Primary Key**: `id`
- **Fields**:
  - `code` (string): Order code.
  - `statut` (integer): Current status of the order.
    - `1`: Attente de confirmation (Pending confirmation).
    - `2`: Validated / Processing (for customertype_id = 4).
    - `5`: En cours de livraison (In delivery).
    - `6`: Livrée (Delivered).
    - `8`: Annulée (Cancelled).
  - `company_id` (integer): Company tenancy identifier.
  - `user_id` (integer): ID of the seller/user who placed the order.
  - `pofsale_id` (integer): Point of Sale associated with the order.
  - `created` (datetime): Time of order placement.
- **Associations**:
  - `belongsTo`: `Users`, `Pofsales`, `Companies`, `Customers`, `Shippings`.
  - `hasMany`: `Orderpacks`.

### B. `Orderpacks` Table (`src/Model/Table/OrderpacksTable.php`)
- **Primary Key**: `id`
- **Fields**:
  - `order_id` (integer): Foreign key to `orders`.
  - `pack_id` (integer): Pack ID.
  - `quantity` (integer): Quantity ordered.
  - `price` (float): Stored unit price of the pack for this order.
  - `statut` (integer): Status of this pack line. `6` indicates Delivered.
  - `turnover_id` (integer, nullable): Association to a turnover band for commission rates.
- **Associations**:
  - `belongsTo`: `Orders`, `Packs`, `Turnovers`.

### C. `Turnovers` Table (`src/Model/Table/TurnoversTable.php`)
- **Primary Key**: `id`
- **Fields**:
  - `commission` (float): The commission rate in percentage (e.g., `5.0` for 5%).
- **Associations**:
  - `hasMany`: `Orderpacks`.

---

## 3. Query Logic & Calculations

### A. Filters and Tenancy Scoping
To fetch correct, secure metrics, the query must apply:
1. **Date Range**: `DATE(Orders.created) >= :start` and `DATE(Orders.created) <= :end`.
2. **User Filter**: Optional `Orders.user_id = :user_id`.
3. **Company Scoping**: `Orders.company_id = :company_id` (retrieved from `Auth.User.company_id`).
4. **Warehouse Scoping**: Restricts orders to points of sale under the user's `defaultwh`.
   Similar to the `ventes()` logic, the list of allowed `pofsale_id` is fetched as:
   ```php
   $warehouse = $this->Orders->Pofsales->Warehouses->get($warehouseId, [
       'contain' => ['Subwarehouses.Pofsales', 'Subwarehouses' => function ($q) {
           return $q->where(['Subwarehouses.whtype_id' => 3]);
       }]
   ]);
   $pofsaleIds = [];
   if ($warehouse->subwarehouses) {
       foreach ($warehouse->subwarehouses as $subwarehouse) {
           foreach ($subwarehouse->pofsales as $pofsale) {
               $pofsaleIds[] = $pofsale->id;
           }
       }
   }
   $lastPofsale = $this->Orders->Pofsales->find('all')
       ->where(['warehouse_id' => $warehouseId])
       ->last();
   if ($lastPofsale) {
       $pofsaleIds[] = $lastPofsale->id;
   }
   ```

### B. KPI Metrics Formulae

1. **Total Orders Count**
   ```sql
   SELECT COUNT(Orders.id) FROM orders Orders
   WHERE <conditions>;
   ```

2. **Total Revenue** (sum of delivered orderpacks amount)
   ```sql
   SELECT SUM(Orderpacks.quantity * Orderpacks.price)
   FROM orderpacks Orderpacks
   INNER JOIN orders Orders ON Orderpacks.order_id = Orders.id
   WHERE <conditions> AND Orderpacks.statut = 6;
   ```

3. **Total Commission** (based on delivered orderpacks)
   In `ventes()`, the PHP logic is:
   `commission = ($orderpack->turnover_id) ? ((quantity * price) * commission_rate / 100) : (quantity * price);`
   To perform this efficiently in SQL:
   ```sql
   SELECT SUM(
       CASE 
           WHEN Orderpacks.turnover_id IS NOT NULL 
           THEN (Orderpacks.price * Orderpacks.quantity * Turnovers.commission / 100)
           ELSE (Orderpacks.price * Orderpacks.quantity)
       END
   )
   FROM orderpacks Orderpacks
   INNER JOIN orders Orders ON Orderpacks.order_id = Orders.id
   LEFT JOIN turnovers Turnovers ON Orderpacks.turnover_id = Turnovers.id
   WHERE <conditions> AND Orderpacks.statut = 6;
   ```

4. **Total Pending Orders Count**
   ```sql
   SELECT COUNT(Orders.id) FROM orders Orders
   WHERE <conditions> AND Orders.statut = 1;
   ```

### C. Time-Series Data (Daily Trends)
We retrieve daily aggregates of orders and revenue, then merge them into a continuous timeline.
1. **Daily Orders**:
   ```sql
   SELECT DATE(Orders.created) AS day, COUNT(Orders.id) AS count
   FROM orders Orders
   WHERE <conditions>
   GROUP BY DATE(Orders.created)
   ORDER BY day ASC;
   ```
2. **Daily Sales Revenue**:
   ```sql
   SELECT DATE(Orders.created) AS day, SUM(Orderpacks.quantity * Orderpacks.price) AS revenue
   FROM orderpacks Orderpacks
   INNER JOIN orders Orders ON Orderpacks.order_id = Orders.id
   WHERE <conditions> AND Orderpacks.statut = 6
   GROUP BY DATE(Orders.created)
   ORDER BY day ASC;
   ```

### D. Status Distribution
```sql
SELECT Orders.statut AS status, COUNT(Orders.id) AS count
FROM orders Orders
WHERE <conditions>
GROUP BY Orders.statut;
```

---

## 4. Proposed Code Structure

A new action `analytics()` should be introduced in `src/Controller/OrdersController.php`. Below is the complete implementation design.

```php
    /**
     * Analytics method - Returns orders KPI metrics, time-series, and status distribution as JSON
     *
     * @return \Cake\Http\Response
     */
    public function analytics()
    {
        $this->request->allowMethod(['get']);

        // 1. Retrieve & Normalize Filter Params
        $keyword = $this->request->getQuery('keyword') ?: [];
        $startDate = !empty($keyword['start']) ? $keyword['start'] : date('Y-m-01');
        $endDate = !empty($keyword['end']) ? $keyword['end'] : date('Y-m-t');
        $userId = !empty($keyword['user']) ? $keyword['user'] : null;

        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';

        // 2. Fetch Scopes
        $companyId = $this->Auth->user('company_id') ?: 1;
        $warehouseId = $this->Auth->user('defaultwh');

        $conditions = [
            'Orders.company_id' => $companyId,
            'Orders.created >=' => $startDateTime,
            'Orders.created <=' => $endDateTime,
        ];

        if ($userId !== null && $userId !== '') {
            $conditions['Orders.user_id'] = $userId;
        }

        // Apply point-of-sale / warehouse scoping
        if ($warehouseId) {
            $warehouse = $this->Orders->Pofsales->Warehouses->get($warehouseId, [
                'contain' => ['Subwarehouses.Pofsales', 'Subwarehouses' => function ($q) {
                    return $q->where(['Subwarehouses.whtype_id' => 3]);
                }]
            ]);

            $pofsaleIds = [];
            if ($warehouse->subwarehouses) {
                foreach ($warehouse->subwarehouses as $subwarehouse) {
                    foreach ($subwarehouse->pofsales as $pofsale) {
                        $pofsaleIds[] = $pofsale->id;
                    }
                }
            }

            $lastPofsale = $this->Orders->Pofsales->find('all')
                ->where(['warehouse_id' => $warehouseId])
                ->last();
            if ($lastPofsale) {
                $pofsaleIds[] = $lastPofsale->id;
            }

            if (!empty($pofsaleIds)) {
                $conditions['Orders.pofsale_id IN'] = array_unique($pofsaleIds);
            } else {
                // If no point of sales found for defaultwh, force empty result
                $conditions['Orders.pofsale_id'] = 0;
            }
        }

        // 3. Execute KPI Queries
        // Total Orders Count
        $totalOrders = $this->Orders->find()
            ->where($conditions)
            ->count();

        // Total Revenue (Delivered Orderpacks)
        $revenueQuery = $this->Orders->Orderpacks->find()
            ->select(['total_revenue' => 'SUM(Orderpacks.quantity * Orderpacks.price)'])
            ->innerJoinWith('Orders')
            ->where($conditions)
            ->andWhere(['Orderpacks.statut' => 6])
            ->first();
        $totalRevenue = $revenueQuery && $revenueQuery->total_revenue ? round((float)$revenueQuery->total_revenue, 2) : 0.0;

        // Total Commission (Delivered Orderpacks + Turnovers commission)
        $commissionQuery = $this->Orders->Orderpacks->find()
            ->select([
                'total_commission' => 'SUM(CASE ' .
                    'WHEN Orderpacks.turnover_id IS NOT NULL THEN (Orderpacks.price * Orderpacks.quantity * Turnovers.commission / 100) ' .
                    'ELSE (Orderpacks.price * Orderpacks.quantity) ' .
                    'END)'
            ])
            ->innerJoinWith('Orders')
            ->leftJoinWith('Turnovers')
            ->where($conditions)
            ->andWhere(['Orderpacks.statut' => 6])
            ->first();
        $totalCommission = $commissionQuery && $commissionQuery->total_commission ? round((float)$commissionQuery->total_commission, 2) : 0.0;

        // Total Pending Orders
        $pendingOrders = $this->Orders->find()
            ->where($conditions)
            ->andWhere(['Orders.statut' => 1])
            ->count();

        // 4. Fetch Time-Series Data
        $dailyOrdersQuery = $this->Orders->find()
            ->select([
                'day' => 'DATE(Orders.created)',
                'count' => 'COUNT(Orders.id)'
            ])
            ->where($conditions)
            ->group(['DATE(Orders.created)'])
            ->order(['day' => 'ASC'])
            ->toArray();

        $dailyRevenueQuery = $this->Orders->Orderpacks->find()
            ->select([
                'day' => 'DATE(Orders.created)',
                'revenue' => 'SUM(Orderpacks.quantity * Orderpacks.price)'
            ])
            ->innerJoinWith('Orders')
            ->where($conditions)
            ->andWhere(['Orderpacks.statut' => 6])
            ->group(['DATE(Orders.created)'])
            ->order(['day' => 'ASC'])
            ->toArray();

        // Fill missing days in the range to guarantee a continuous timeline
        $timeSeries = [];
        $current = new \Cake\I18n\Time($startDate);
        $end = new \Cake\I18n\Time($endDate);
        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            $timeSeries[$dateStr] = [
                'date' => $dateStr,
                'order_count' => 0,
                'revenue' => 0.0
            ];
            $current->addDays(1);
        }

        foreach ($dailyOrdersQuery as $row) {
            $dateStr = $row->day;
            if (isset($timeSeries[$dateStr])) {
                $timeSeries[$dateStr]['order_count'] = (int)$row->count;
            }
        }

        foreach ($dailyRevenueQuery as $row) {
            $dateStr = $row->day;
            if (isset($timeSeries[$dateStr])) {
                $timeSeries[$dateStr]['revenue'] = round((float)$row->revenue, 2);
            }
        }
        $timeSeriesData = array_values($timeSeries);

        // 5. Fetch Status Distribution
        $statusQuery = $this->Orders->find()
            ->select([
                'status' => 'Orders.statut',
                'count' => 'COUNT(Orders.id)'
            ])
            ->where($conditions)
            ->group(['Orders.statut'])
            ->toArray();

        $statusLabels = [
            1 => 'Attente de confirmation',
            2 => 'Validée',
            5 => 'En cours de livraison',
            6 => 'Livrée',
            8 => 'Annulée'
        ];

        $orderDistribution = [];
        // Pre-fill default statuses
        foreach ($statusLabels as $code => $label) {
            $orderDistribution[$code] = [
                'status_code' => $code,
                'status_label' => $label,
                'count' => 0
            ];
        }

        foreach ($statusQuery as $row) {
            $statusCode = (int)$row->status;
            $label = isset($statusLabels[$statusCode]) ? $statusLabels[$statusCode] : 'Autre (' . $statusCode . ')';
            $orderDistribution[$statusCode] = [
                'status_code' => $statusCode,
                'status_label' => $label,
                'count' => (int)$row->count
            ];
        }
        $orderDistributionData = array_values($orderDistribution);

        // 6. Return JSON Response
        $responsePayload = [
            'success' => true,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'user_id' => $userId,
                'company_id' => $companyId,
                'warehouse_id' => $warehouseId
            ],
            'metrics' => [
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'total_commission' => $totalCommission,
                'total_pending' => $pendingOrders
            ],
            'time_series' => $timeSeriesData,
            'status_distribution' => $orderDistributionData
        ];

        $this->autoRender = false;
        $this->response = $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($responsePayload));
        return $this->response;
    }
```

---

## 5. Security & Edge Case Handling

1. **Authentication bypass check**: The new action must not bypass authentication. It should rely on `$this->Auth->user()` to scope data by the authenticated user's company and default warehouse.
2. **Validation**: Check that the input start and end dates are in a valid format (e.g. `YYYY-MM-DD`). Fallback to default dates if the parameters are corrupted or missing.
3. **Empty Data Sets**: If there are no orders in the specified date range, the SQL queries return `NULL` or `0`. Explicitly formatting/casting database query results using `round((float)..., 2) : 0.0` prevents type conversion warnings or returning `null` values to the frontend.
4. **Continuous Timeline**: Filling all intermediate dates between start and end date prevents graph breaks in the UI, ensuring consistent presentation.
