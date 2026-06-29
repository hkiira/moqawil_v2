# Tranches Apply Type - Implementation Summary

## What Was Implemented

You can now specify whether a tranche (pricing tier) is applied based on:
- **Quantity**: Number of items ordered (e.g., "10+ items get 5% off")
- **Amount**: Total order value in DH (e.g., "Orders over 10,000 DH get 10% off")

## Changes Made

### 1. Database Migration ✓
- File: `config/Migrations/20260102000001_AddApplyTypeToTranches.php`
- Added `apply_type` column to `tranches` table
- Default value: `QUANTITY` (backward compatible)
- Possible values: `QUANTITY`, `AMOUNT`

### 2. Model Layer ✓
- File: `src/Model/Table/TranchesTable.php`
- Added validation for `apply_type` field
- Ensures value is either 'QUANTITY' or 'AMOUNT'

### 3. User Interface ✓
**Add & Edit Forms:**
- File: `src/Template/Tranches/add.ctp` and `src/Template/Tranches/edit.ctp`
- New dropdown: "Type d'application de la tranche"
  - Option 1: "Basée sur la quantité" 
  - Option 2: "Basée sur le montant"
- Updated labels for min/max fields to reflect both use cases
- Added informational alert explaining both types

**List View:**
- File: `src/Template/Tranches/index.ctp`
- New column: "Type d'application"
- File: `webroot/js/tranches.js`
- DataTable column added to display apply_type

### 4. Business Logic ✓
- File: `src/Service/OrderPricingService.php`
- Updated `priceLine()` method signature:
  ```php
  public function priceLine(
      int $packId,
      int $quantity,
      int $customerTypeId,
      int $warehouseId,
      int $companyId,
      ?float $totalOrderAmount = null  // NEW optional parameter
  ): array
  ```
- Logic now checks `tranch->apply_type` to determine comparison value:
  - If `AMOUNT`: compares against order total amount
  - If `QUANTITY`: compares against normalized quantity (original behavior)

### 5. Search/Display ✓
- File: `src/Controller/TranchesController.php`
- Updated `search()` method to include apply_type in results
- Displays user-friendly labels in list view

### 6. Documentation ✓
- File: `docs/tranches-apply-type.md`
- Complete feature documentation with examples

## How to Use

### Creating a Quantity-Based Tranche (Traditional)
1. Go to "Tranches" → "Create"
2. Fill in fields:
   - Name: "Bulk Purchase 100+ units"
   - Type of application: "Basée sur la quantité"
   - Min: 100
   - Max: (empty for unlimited)
   - Type of discount: (your choice)
   - Discount amount: 5 (for 5% off)
   - Status: Active
3. Save

### Creating an Amount-Based Tranche (New Feature)
1. Go to "Tranches" → "Create"
2. Fill in fields:
   - Name: "Large Order 10000+ DH"
   - Type of application: **"Basée sur le montant"** ← Key difference
   - Min: **10000** (in DH, not quantity)
   - Max: (empty for unlimited)
   - Type of discount: (your choice)
   - Discount amount: 10 (for 10% off)
   - Status: Active
3. Save

## Backward Compatibility

✓ **Fully Backward Compatible**
- All existing tranches default to QUANTITY mode
- No changes needed to existing code
- Optional `totalOrderAmount` parameter in priceLine()
- Old code without the parameter still works

## Integration Notes

### For Developers Using OrderPricingService

To properly support amount-based tranches, calculate the total order amount before calling:

```php
// Calculate total order amount
$totalOrderAmount = 0;
foreach ($orderItems as $item) {
    $totalOrderAmount += ($item['quantity'] * $item['unit_price']);
}

// Price each line
$result = $pricingService->priceLine(
    packId: $packId,
    quantity: $quantity,
    customerTypeId: $customerTypeId,
    warehouseId: $warehouseId,
    companyId: $companyId,
    totalOrderAmount: $totalOrderAmount  // Pass the total
);
```

## Testing the Feature

### Test Quantity-Based
1. Create a tranche: min=50, apply_type=QUANTITY, remise=5%
2. Add item to order with quantity=49 → No discount
3. Add item to order with quantity=50 → 5% discount applied ✓

### Test Amount-Based
1. Create a tranche: min=5000, apply_type=AMOUNT, remise=10%
2. Create order with total=4999 DH → No discount
3. Create order with total=5000+ DH → 10% discount applied ✓

## Files Summary

| File | Change |
|------|--------|
| `config/Migrations/20260102000001_AddApplyTypeToTranches.php` | NEW - Database migration |
| `src/Model/Table/TranchesTable.php` | UPDATED - Added validation |
| `src/Template/Tranches/add.ctp` | UPDATED - Added apply_type select |
| `src/Template/Tranches/edit.ctp` | UPDATED - Added apply_type select |
| `src/Template/Tranches/index.ctp` | UPDATED - Column header |
| `src/Controller/TranchesController.php` | UPDATED - Display apply_type |
| `src/Service/OrderPricingService.php` | UPDATED - Logic for both types |
| `webroot/js/tranches.js` | UPDATED - DataTable column |
| `docs/tranches-apply-type.md` | NEW - Feature documentation |
| `docs/tranches-implementation-summary.md` | NEW - This file |

## Key Features

✓ Fully backward compatible - no breaking changes
✓ Database migration automatically applied
✓ User-friendly UI with helpful labels
✓ Comprehensive validation
✓ Clear documentation with examples
✓ Supports both QUANTITY and AMOUNT based pricing tiers
✓ Easy to switch between modes in existing tranches
