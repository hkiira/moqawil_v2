# Synthesis of Milestone 1 Exploration Reports

## Consensus
- **Filter Inputs**: Parameters are expected via GET as `keyword[start]`, `keyword[end]`, and `keyword[user]`.
- **Date Filtering**: Applies to `DATE(Orders.created)`.
- **Revenue Calculation**: Sum of `quantity * price` from `orderpacks` where `orderpacks.statut = 6`.
- **Commission Calculation**: Calculated from delivered orderpacks (`statut = 6`). If `turnover_id` is present, use `(price * quantity * commission_rate) / 100`. Otherwise, use `price * quantity`.
- **Pending Orders**: Defined as orders with `Orders.statut = 1`.
- **Time-Series Data**: Grouped by `DATE(created)` returning daily order count and daily revenue.
- **Status Distribution**: Grouped by `Orders.statut` to find count per status.
- **Scoping**: Must respect logged-in user's `company_id` and their default warehouse point of sale (`defaultwh`).

## Proposed Implementation Strategy
Instead of creating a brand new action and changing routes/AJAX endpoints, we will update the existing `ventes()` action in `OrdersController.php` to calculate all the required analytics variables and call `$this->render('analytics');` at the end to render the `analytics.ctp` template. This satisfies R1 and R3 cleanly with minimal changes.

## Resolved Conflicts
- **Response Format**: Explorer 2 suggested returning JSON. However, the existing AJAX callback in `index.ctp` sets HTML directly using `$(balise).html(response)`. Therefore, the action must return HTML by rendering the `analytics.ctp` template.
