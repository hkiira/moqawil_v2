# Tranches: Quantity vs. Amount-Based Application

## Overview

This feature enhancement allows tranches (pricing tiers/brackets) to be applied based on either:
1. **Quantity** - the amount of items ordered (default, backward compatible)
2. **Amount** - the total monetary value of the order in DH

## Implementation Details

### 1. Database Schema

A new column `apply_type` was added to the `tranches` table via migration:

```sql
ALTER TABLE tranches ADD COLUMN apply_type VARCHAR(50) DEFAULT 'QUANTITY' NOT NULL AFTER statut
```

**Values:**
- `QUANTITY` (default) - Tranches are applied based on ordered quantity
- `AMOUNT` - Tranches are applied based on total order amount in currency (DH)

### 2. Model Changes

#### TranchesTable.php
- Added validation for the `apply_type` field
- Validates that the value is either 'QUANTITY' or 'AMOUNT'
- Field is optional during patching to maintain backward compatibility

### 3. UI/UX Changes

#### Add & Edit Forms
Both `/src/Template/Tranches/add.ctp` and `/src/Template/Tranches/edit.ctp` now include:

- **New Select Field**: "Type d'application de la tranche" with two options:
  - "Basée sur la quantité" (QUANTITY)
  - "Basée sur le montant" (AMOUNT)

- **Updated Labels & Placeholders**:
  - Min/Max labels updated to reflect both use cases
  - Placeholders clarify the expected input format

- **Informational Alerts**: New alert box explaining:
  - QUANTITY-based tranches: Applied when ordered quantity reaches limits
  - AMOUNT-based tranches: Applied when order total (DH) reaches limits

#### List/Index View
- Added new column "Type d'application" to the tranches table
- Displays user-friendly labels: "Quantité" or "Montant (DH)"

### 4. Pricing Service Logic

#### OrderPricingService.php

**Method Signature Updated:**
```php
public function priceLine(
    int $packId,
    int $quantity,
    int $customerTypeId,
    int $warehouseId,
    int $companyId,
    ?float $totalOrderAmount = null  // NEW: Optional parameter
): array
```

**Logic Changes:**
1. Reads `apply_type` from each tranche
2. Determines the comparison value:
   - If `apply_type === 'AMOUNT'`: Uses `$totalOrderAmount` parameter
   - If `apply_type === 'QUANTITY'`: Uses normalized quantity (backward compatible)
3. Compares min/max thresholds against the appropriate value
4. Applies the discount only if conditions match

**Backward Compatibility:**
- `$totalOrderAmount` is optional (defaults to `null`)
- Existing code without this parameter continues to work
- QUANTITY-based tranches work exactly as before

### 5. Usage Examples

#### Example 1: Quantity-Based Tranche (Original behavior)
```
Tranche: "Bulk Purchase 100+ units"
Apply Type: QUANTITY
Min: 100
Max: 999
Remise: 5%
```
✓ Applied when customer orders 100 or more units

#### Example 2: Amount-Based Tranche (New feature)
```
Tranche: "Large Order 10000+ DH"
Apply Type: AMOUNT
Min: 10000
Max: null (no upper limit)
Remise: 10%
```
✓ Applied when order total reaches 10,000 DH or more

#### Example 3: Mixed Tranches
A product can have both quantity and amount-based tranches. The pricing service checks:
1. Quantity-based tranches first
2. Then amount-based tranches
3. Returns the first matching tranche

### 6. Calling the Service with Amount-Based Tranches

To properly apply amount-based tranches, the caller must provide the total order amount:

```php
$pricingService = new OrderPricingService();

// Calculate total order amount (sum of all line items)
$totalOrderAmount = 12500.00; // 12,500 DH

// Price a single line item
$result = $pricingService->priceLine(
    packId: 3,
    quantity: 50,
    customerTypeId: 2,
    warehouseId: 1,
    companyId: 1,
    totalOrderAmount: $totalOrderAmount  // Pass the total amount
);

// Result includes applied discount based on amount-based tranches
```

### 7. Files Modified

1. **Database:**
   - `config/Migrations/20260102000001_AddApplyTypeToTranches.php` (NEW)

2. **Models:**
   - `src/Model/Table/TranchesTable.php`

3. **Controllers:**
   - `src/Controller/TranchesController.php` (search method updated)

4. **Views:**
   - `src/Template/Tranches/add.ctp`
   - `src/Template/Tranches/edit.ctp`
   - `src/Template/Tranches/index.ctp`

5. **JavaScript:**
   - `webroot/js/tranches.js` (column definition)

6. **Services:**
   - `src/Service/OrderPricingService.php` (main logic)

## Integration Checklist

- [x] Database migration created and applied
- [x] Model validation added
- [x] UI forms updated with new field
- [x] List view displays apply_type
- [x] Pricing service logic updated
- [x] Backward compatibility maintained
- [x] Documentation provided

## Testing Recommendations

1. **Test Quantity-Based Tranches**
   - Create a tranche: min=100, apply_type=QUANTITY
   - Order 99 units → discount NOT applied
   - Order 100+ units → discount applied

2. **Test Amount-Based Tranches**
   - Create a tranche: min=10000, apply_type=AMOUNT
   - Order total 9999 DH → discount NOT applied
   - Order total 10000+ DH → discount applied

3. **Test Backward Compatibility**
   - Existing code calling priceLine without totalOrderAmount should work
   - QUANTITY tranches work exactly as before

4. **Test Mixed Scenarios**
   - Multiple tranches with different apply_types
   - Verify correct tranche is selected and applied

## Notes

- The feature defaults to QUANTITY for all existing tranches (backward compatible)
- Admins can update tranches to use AMOUNT-based application as needed
- The OrderPricingService now requires the calling code to calculate and pass the total order amount for proper amount-based tranche evaluation
