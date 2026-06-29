# Orders pricing logic (promotions via tranches)

## What changed
- Orders now use a dedicated pricing service: `App\\Service\\OrderPricingService`.
- Line prices are computed once per order line using active `prices` rows and their linked `trancheprices -> tranches -> remisetypes`.
- TohavesController was removed; only OrdersController uses the new pricing.

## How it works
1. Base price lookup: pick the `prices` row for the pack, customer type, company, optional warehouse (warehouse-specific preferred, otherwise generic), and `tarif_id IS NULL`.
2. Tranche selection: iterate trancheprices ordered by tranche.min ascending; keep those active and where quantity is within [min, max] (open-ended if max is null/0); pick the first match.
3. Discount application:
   - If `remisetype.code` is `%` (or percent variants), apply a percentage discount: `final = base * (1 - value/100)`.
   - Otherwise apply a fixed discount: `final = base - value` (floored at 0).
4. Returned data: `base_price`, `final_unit_price`, `tranche_id`. OrdersController stores `price` and `tranche_id` on the order line; totals are computed from the final unit price.

## Usage
```php
$service = new OrderPricingService();
$result = $service->priceLine($packId, $quantity, $customerTypeId, $warehouseId, $companyId);
// $result['final_unit_price'] and $result['tranche_id'] are used to persist the line.
```

## Admin data requirements
- `prices` rows must exist for each (pack, customer type, company) and optionally per warehouse.
- `trancheprices` rows link a `price` to a `tranch` (quantity band).
- `tranches` define `min`, `max`, `remise`, `remisetype_id`, `statut`.
- `remisetypes.code` options:
   - `%`: percentage discount (final = base × (1 − value/100))
   - `RED`: fixed-amount discount (final = base − value, floored at 0)
   - `GRT`: gift products. Price is unchanged; the tranche’s `pack_id` is added as free items. Gift quantity uses `tranches.remise` (if numeric) or defaults to 1 pack. Each gift pack’s products are appended as zero-price `orderpackproducts` with quantities multiplied by the gift pack count.

## Extending later
- Add order-level promotions by introducing a dedicated table or reusing `tarifs` with a discount field; apply after summing the line totals.
- Add priorities/stacking by extending the service selection strategy.
