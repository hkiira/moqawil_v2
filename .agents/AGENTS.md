# Custom rules for moqa project

## 1. Stock & Inventory Management Rules

### Polymorphic Stock Queries (Whproducts)
- **Always query by polymorphic fields**: When querying the `Whproducts` table, always filter by `item_id` and `item_type` (value `'Product'` or `'Pack'`) instead of legacy columns like `pack_id` or `product_id`.
- **Legacy Column Warning**: Legacy columns in `whproducts` (like `pack_id` and `product_id`) may contain stale or `NULL` values for newly adjusted stock, which will cause queries to return incorrect results or `null` objects.

### Stock Movement Transactions & Audit Trail
- **Log Stock Movements**: Every operation that changes `Whproducts.quantity` (in addition, subtraction, or relocation) must record a corresponding log entry in the `StockMovements` table. Do not perform direct stock edits without an audit trail.
- **Direction / Sign Verification**: 
  - **Deduction (Source)**: Subtract the quantity from the source stock (`$whproduct->quantity -= $qty`).
  - **Addition (Destination)**: Add the quantity to the destination stock (`$whproductd->quantity += $qty`).
  - Always verify that operators are correct (e.g. avoid adding to both source and destination during transfers).
