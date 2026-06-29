# Orders Analytics Dashboard Backend Logic Analysis (Milestone 1)

This report outlines the proposed backend changes in `src/Controller/OrdersController.php` to calculate and return the required analytics data for the Orders Analytics Dashboard. 

---

## 1. Overview of Key Metrics & Query Requirements
The dashboard must calculate the following metrics:
- **Total Orders Count**: Total count of distinct orders matching the filter criteria.
- **Total Revenue**: Sum of the amount of all delivered orders (`orderpacks` having `statut = 6`).
- **Total Commission**: Sum of commissions computed from delivered orders.
- **Total Pending Orders Count**: Count of orders that are currently in "attente de confirmation" (`statut = 1`).
- **Time-Series Data**: Daily orders count and daily sales revenue.
- **Order Count Distribution**: Count of orders grouped by their status.

---

## 2. Models & Associations Involved
From our analysis of `src/Model/Table/OrdersTable.php` and `src/Model/Table/OrderpacksTable.php`, the database models and associations are structured as follows:

- **Orders Table (`orders`)**:
  - Belongs to: `Companies`, `Users`, `Customers`, `Pofsales`, `Shippings`, `Ordertypes`.
  - Has Many: `Orderpacks` (foreign key `order_id`).
  - Fields of interest:
    - `id` (primary key)
    - `statut` (integer order status)
    - `created` (datetime of order placement)
    - `user_id` (integer ID of the user/seller who created the order)
    - `company_id` (integer company identifier)
    - `pofsale_id` (integer point of sale identifier)
    - `ordertype_id` (integer type of order; default sales = 1)

- **Orderpacks Table (`orderpacks`)**:
  - Belongs to: `Orders`, `Packs`, `Turnovers`.
  - Fields of interest:
    - `id` (primary key)
    - `order_id` (foreign key pointing to orders)
    - `pack_id` (foreign key to the product pack)
    - `quantity` (integer quantity purchased)
    - `price` (numeric unit price)
    - `statut` (integer pack status; status `6` represents **Delivered**)
    - `turnover_id` (foreign key pointing to the `turnovers` table, which holds the commission rate)

- **Turnovers Table (`turnovers`)**:
  - Has Many: `Orderpacks`.
  - Fields of interest:
    - `id` (primary key)
    - `commission` (numeric percentage, e.g., 5.0 for 5%)

---

## 3. Query Logic & SQL Formulation

### A. Scoping & Filters
To match the rest of the application (e.g. `ventes()` and `search()`), we extract the following filters from the query parameters:
1. **Date Range**: `keyword[start]` and `keyword[end]` (applied to `DATE(Orders.created)`).
2. **User Filter**: `keyword[user]` (applied to `Orders.user_id`).
3. **Company Scoping**: Restricts records to the logged-in user's company (`Orders.company_id = Auth->user('company_id')`).
4. **Point of Sale (POS) Scoping**: Filters orders by `pofsale_id` based on the user's default warehouse (`Auth->user('defaultwh')`) and its subwarehouses.
5. **Role Scoping**: For sales representatives / sellers (roles 3, 5, 6), default the user filter to their own ID (`Auth->user('id')`) if no user is specified.

### B. Summary KPIs (Single-Query Approach)
To minimize database load, we propose retrieving the **Total Orders**, **Pending Orders**, **Total Revenue**, and **Total Commission** using a single query with conditional aggregations:
```sql
SELECT 
    COUNT(DISTINCT Orders.id) AS total_orders,
    COUNT(DISTINCT CASE WHEN Orders.statut = 1 THEN Orders.id END) AS pending_orders,
    SUM(CASE WHEN Orderpacks.statut = 6 THEN Orderpacks.quantity * Orderpacks.price ELSE 0 END) AS total_revenue,
    SUM(
        CASE WHEN Orderpacks.statut = 6 THEN 
            CASE WHEN Orderpacks.turnover_id IS NOT NULL THEN 
                (Orderpacks.price * Orderpacks.quantity * Turnovers.commission / 100)
            ELSE 
                (Orderpacks.price * Orderpacks.quantity)
            END
        ELSE 0 END
    ) AS total_commission
FROM orders Orders
LEFT JOIN orderpacks Orderpacks ON Orders.id = Orderpacks.order_id
LEFT JOIN turnovers Turnovers ON Orderpacks.turnover_id = Turnovers.id
WHERE Orders.company_id = :company_id
  AND Orders.ordertype_id = 1 -- Standard sales orders
  AND DATE(Orders.created) >= :start_date
  AND DATE(Orders.created) <= :end_date
  -- Optional user filter or default scoping here
```

### C. Time-Series (Daily Orders & Revenue)
To display a trend chart, we group orders and delivered revenue by date:
```sql
SELECT 
    DATE(Orders.created) AS `date`,
    COUNT(DISTINCT Orders.id) AS orders_count,
    SUM(CASE WHEN Orderpacks.statut = 6 THEN Orderpacks.quantity * Orderpacks.price ELSE 0 END) AS revenue
FROM orders Orders
LEFT JOIN orderpacks Orderpacks ON Orders.id = Orderpacks.order_id
WHERE Orders.company_id = :company_id
  AND Orders.ordertype_id = 1
  AND DATE(Orders.created) >= :start_date
  AND DATE(Orders.created) <= :end_date
GROUP BY DATE(Orders.created)
ORDER BY DATE(Orders.created) ASC;
```

### D. Order Status Distribution
To populate a pie or bar chart representing orders grouped by status:
```sql
SELECT 
    Orders.statut AS status_id,
    COUNT(Orders.id) AS `count`
FROM orders Orders
WHERE Orders.company_id = :company_id
  AND Orders.ordertype_id = 1
  AND DATE(Orders.created) >= :start_date
  AND DATE(Orders.created) <= :end_date
GROUP BY Orders.statut;
```

---

## 4. Proposed Controller Implementation in `OrdersController.php`

The following method `analytics()` is proposed to be added to `src/Controller/OrdersController.php`:

```php
    /**
     * Analytics endpoint for the Orders Dashboard (Milestone 1)
     * Handles KPIs, daily time-series, and status distributions.
     *
     * @return \Cake\Http\Response|null
     */
    public function analytics()
    {
        $this->request->allowMethod(['get', 'post']);
        
        $this->response = $this->response
            ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->withHeader('Pragma', 'no-cache')
            ->withHeader('Expires', '0');

        // 1. Retrieve query parameters
        $keyword = $this->request->getQuery('keyword') ?: $this->request->getData('keyword');
        $start = !empty($keyword['start']) ? $keyword['start'] : date('Y-m-d', strtotime('-30 days'));
        $end = !empty($keyword['end']) ? $keyword['end'] : date('Y-m-d');
        $userFilter = !empty($keyword['user']) ? $keyword['user'] : null;

        // 2. Build base conditions (Company Scoping & Order Type)
        $conditions = [
            'Orders.company_id' => $this->Auth->user('company_id'),
            'Orders.ordertype_id' => 1, // Focus on standard sales orders
            'DATE(Orders.created) >=' => $start,
            'DATE(Orders.created) <=' => $end,
        ];

        // 3. User Scoping based on Role and Filter
        if ($userFilter) {
            $conditions['Orders.user_id'] = $userFilter;
        } else {
            // Reps/Sellers (role_id 3, 5, 6) can only view their own analytics
            if (in_array($this->Auth->user('role_id'), [3, 5, 6])) {
                $conditions['Orders.user_id'] = $this->Auth->user('id');
            }
        }

        // 4. Point of Sale (POS) Scoping based on default Warehouse
        $warehouse = $this->Orders->Pofsales->Warehouses->get($this->Auth->user('defaultwh'), [
            'contain' => [
                'Subwarehouses.Pofsales',
                'Subwarehouses' => function ($q) {
                    return $q->where(['Subwarehouses.whtype_id' => 3]);
                }
            ]
        ]);

        $qwh = [];
        if ($warehouse->subwarehouses) {
            foreach ($warehouse->subwarehouses as $subwarehouse) {
                foreach ($subwarehouse->pofsales as $pofsale) {
                    $qwh['OR'][] = ['Orders.pofsale_id' => $pofsale->id];
                }
            }
        }
        $pofsale = $this->Orders->Pofsales->find('all')->where(['warehouse_id' => $this->Auth->user('defaultwh')]);
        if ($pofsale->count() > 0) {
            $qwh['OR'][] = ['Orders.pofsale_id' => $pofsale->last()->id];
        }
        if (!empty($qwh)) {
            $conditions[] = $qwh;
        }

        // 5. Query KPI Summaries
        $kpisQuery = $this->Orders->find()
            ->leftJoinWith('Orderpacks')
            ->leftJoinWith('Orderpacks.Turnovers')
            ->where($conditions)
            ->select([
                'total_orders' => 'COUNT(DISTINCT Orders.id)',
                'pending_orders' => 'COUNT(DISTINCT CASE WHEN Orders.statut = 1 THEN Orders.id END)',
                'total_revenue' => 'SUM(CASE WHEN Orderpacks.statut = 6 THEN Orderpacks.quantity * Orderpacks.price ELSE 0 END)',
                'total_commission' => 'SUM(CASE WHEN Orderpacks.statut = 6 THEN (CASE WHEN Orderpacks.turnover_id IS NOT NULL THEN (Orderpacks.price * Orderpacks.quantity * Turnovers.commission / 100) ELSE (Orderpacks.price * Orderpacks.quantity) END) ELSE 0 END)'
            ])
            ->first();

        $kpis = [
            'total_orders' => (int)($kpisQuery->total_orders ?? 0),
            'pending_orders' => (int)($kpisQuery->pending_orders ?? 0),
            'total_revenue' => (float)($kpisQuery->total_revenue ?? 0.0),
            'total_commission' => (float)($kpisQuery->total_commission ?? 0.0),
        ];

        // 6. Query Daily Time-Series Data
        $timeSeriesQuery = $this->Orders->find()
            ->leftJoinWith('Orderpacks')
            ->where($conditions)
            ->select([
                'date' => 'DATE(Orders.created)',
                'orders_count' => 'COUNT(DISTINCT Orders.id)',
                'revenue' => 'SUM(CASE WHEN Orderpacks.statut = 6 THEN Orderpacks.quantity * Orderpacks.price ELSE 0 END)'
            ])
            ->group(['DATE(Orders.created)'])
            ->order(['DATE(Orders.created)' => 'ASC']);

        $timeSeries = [];
        foreach ($timeSeriesQuery as $row) {
            $timeSeries[] = [
                'date' => $row->date,
                'orders_count' => (int)$row->orders_count,
                'revenue' => (float)$row->revenue,
            ];
        }

        // 7. Query Order Status Distribution
        $statusDistQuery = $this->Orders->find()
            ->where($conditions)
            ->select([
                'status' => 'Orders.statut',
                'count' => 'COUNT(Orders.id)'
            ])
            ->group(['Orders.statut']);

        $statusMapping = [
            1 => 'Attente de confirmation',
            2 => 'Validée',
            3 => 'Préparée',
            5 => 'En cours de livraison',
            6 => 'Livrée',
            8 => 'Annulée'
        ];

        $distribution = [];
        foreach ($statusDistQuery as $row) {
            $statusId = (int)$row->status;
            $distribution[] = [
                'status_id' => $statusId,
                'status_name' => $statusMapping[$statusId] ?? 'Autre (' . $statusId . ')',
                'count' => (int)$row->count,
            ];
        }

        // 8. Construct & Output Response
        $response = [
            'success' => true,
            'filters' => [
                'start' => $start,
                'end' => $end,
                'user' => $userFilter
            ],
            'kpis' => $kpis,
            'time_series' => $timeSeries,
            'distribution' => $distribution
        ];

        $this->autoRender = false;
        $this->response = $this->response->withType('application/json');
        echo json_encode($response);
        exit;
    }
```

---

## 5. Routing Configuration
To expose this endpoint, a route should be registered in `config/routes.php` (if it's not using default CakePHP controller-action routing):

```php
$routes->connect('/orders/analytics', ['controller' => 'Orders', 'action' => 'analytics']);
```

---

## 6. Performance & Security Considerations

1. **SQL Injection Protection**: The CakePHP Query Builder utilizes PDO prepared statements for filtering variables (e.g. `Orders.company_id => $company_id`). Raw strings used in `SUM(CASE ...)` are static templates and do not embed user inputs directly, ensuring security.
2. **Database Indexing**: To maintain sub-second response times as the orders table grows, the following composite indexes are recommended:
   - On table `orders`: `(company_id, ordertype_id, created, user_id)`
   - On table `orderpacks`: `(order_id, statut)`
3. **Execution Efficiency**: The conditional aggregations combined with `leftJoinWith` run in a single pass over the filtered order records, preventing N+1 queries.
