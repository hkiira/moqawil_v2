# Handoff Report: Milestone 1 Orders Analytics Dashboard Backend Logic

This report summarizes the read-only investigation findings and implementation design for the Orders Analytics Dashboard backend.

---

## 1. Observation

During our codebase exploration, the following key files, lines, and patterns were observed:

### A. `src/Controller/OrdersController.php`
The existing `ventes` method (lines 54-91) implements the core logic for calculating revenue and commission:
```php
54:     public function ventes()
55:     {
56:         $vrb = $_GET['keyword'];
57:         $datetime1 = new Time($vrb['start']);
58:         $datetime2 = new Time($vrb['end']);
59:         if ($vrb['user'] == NULL) {
60:             $orders = $this->Orders->find('all')->contain(['Orderpacks.Turnovers'])->where(['DATE(Orders.created) <= ' => $vrb['end'], 'DATE(Orders.created) >= ' => $vrb['start']]);
61:         } else {
62:             $orders = $this->Orders->find('all')->contain(['Orderpacks.Turnovers'])->where(['Orders.user_id' => $vrb['user'], 'DATE(Orders.created) <= ' => $vrb['end'], 'DATE(Orders.created) >= ' => $vrb['start']]);
63:         }
...
80:         $total = 0;
81:         $totalcommission = 0;
82:         foreach ($orders as $key => $order) {
83:             foreach ($order->orderpacks as $key1 => $orderpack) {
84:                 if ($orderpack->statut == 6) {
85:                     $total += ($orderpack->quantity * $orderpack->price);
86:                     $totalcommission += ($orderpack->turnover_id) ? (($orderpack->price * $orderpack->quantity) * $orderpack->turnover->commission / 100) : ($orderpack->price * $orderpack->quantity);
87:                 }
88:             }
89:         }
90:         $this->set(compact('total', 'totalcommission', 'datetime1', 'datetime2'));
91:     }
```

The order status codes were observed in the controller's docblock (lines 19-22):
```
 1: attente de confirmation
 5: En cours de livraison
 6: Livrée
 8: Annulée
```

### B. `src/Model/Table/OrdersTable.php` and `OrderpacksTable.php`
- `OrdersTable` defines `hasMany` relation to `Orderpacks` on line 90:
  ```php
  $this->hasMany('Orderpacks', [
      'foreignKey' => 'order_id',
      'dependent' => true,
      'cascadeCallbacks' => true,
  ]);
  ```
- `OrderpacksTable` defines `belongsTo` relation to `Orders` (line 53) and `Turnovers` (line 63).
- `TurnoversTable` contains the `commission` rate column (line 66 of `src/Model/Table/TurnoversTable.php`):
  ```php
  $validator
      ->numeric('commission')
      ->allowEmptyString('commission');
  ```

### C. `src/Controller/AppController.php`
The initialization hook (lines 44-46) registers the `RequestHandler` component:
```php
44:         $this->loadComponent('RequestHandler', [
45:             'enableBeforeRedirect' => false,
46:         ]);
```

---

## 2. Logic Chain

Based on the direct observations, the reasoning logic for calculating each metric is as follows:

1. **Filtering Inputs**: Since the existing `ventes` method uses `$_GET['keyword']` as a parameter array, the new analytics endpoint must parse `$this->request->getQuery('keyword')` to retrieve `start`, `end`, and `user` keys (Observation A).
2. **Total Orders Count**: A query counting all records matching date and seller filters on the `Orders` model returns the total orders count.
3. **Total Revenue**: To find delivered orders revenue, we sum the product of `quantity * price` from the `Orderpacks` table where `Orderpacks.statut = 6` (Observation A, line 84).
4. **Total Commission**: The conditional commission logic on line 86 of Observation A uses `Turnovers.commission` if `turnover_id` exists, defaulting to the full amount otherwise. In database operations, we can express this conditionally via `CASE WHEN Orderpacks.turnover_id IS NOT NULL THEN ... ELSE ... END` joined with `Turnovers` (Observation B).
5. **Pending Orders Count**: Orders with status `1` represent pending confirmation (Observation A, line 19).
6. **Time-Series Data**: By querying the daily count of orders and the daily sum of delivered orderpack amounts (`statut = 6`) grouped by `DATE(created)`, we can reconstruct daily metrics. Iterating through all dates between `start` and `end` and pre-filling missing dates with `0` ensures a continuous time series.
7. **Status Distribution**: Grouping the `Orders` query by `Orders.statut` yields counts for each status. Pre-populating a mapping for statuses `1`, `2`, `5`, `6`, and `8` ensures the API output contains a complete list even for zero-count statuses.

---

## 3. Caveats

- **Warehouse Scope Session Dependency**: We assume that `$this->Auth->user('defaultwh')` is set for all queries. If it is null or empty, the queries fallback to scoping by `company_id` only or restrict point-of-sale selection.
- **Orderpack Status vs. Order Status**: The revenue and commission calculations are based on orderpacks having `statut = 6` (Delivered). In our design, this matches the legacy `ventes` logic. If there are orders where the order has `statut = 6` but orderpacks do not, they will not contribute to revenue/commission. This strictly aligns with the observed legacy codebase behavior.

---

## 4. Conclusion

A new action `analytics()` should be added to `src/Controller/OrdersController.php`. It will:
- Safely parse date and user filters.
- Perform high-performance queries using CakePHP's Query Builder to aggregate counts, revenue, and commissions.
- Format the result as JSON and return it directly.

The complete proposed implementation file is detailed in `analysis.md`.

---

## 5. Verification Method

To verify the proposed implementation:
1. Inspect `src/Controller/OrdersController.php` to ensure the new action `analytics` is integrated.
2. Authenticate as a user and call the new endpoint:
   ```
   GET /orders/analytics?keyword[start]=2026-06-01&keyword[end]=2026-06-30
   ```
3. Verify that the response is of type `application/json` and matches the schema:
   ```json
   {
     "success": true,
     "filters": { "start_date": "...", "end_date": "...", "user_id": null },
     "metrics": {
       "total_orders": 15,
       "total_revenue": 1500.0,
       "total_commission": 75.0,
       "total_pending": 3
     },
     "time_series": [
       { "date": "2026-06-01", "order_count": 1, "revenue": 100.0 }
     ],
     "status_distribution": [
       { "status_code": 1, "status_label": "Attente de confirmation", "count": 3 }
     ]
   }
   ```
4. Confirm multi-tenancy and warehouse boundaries by changing the user's `company_id` or `defaultwh` in the session and checking that only scoped data is returned.
