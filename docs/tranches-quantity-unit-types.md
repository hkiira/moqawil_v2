# Tranches Quantity Unit Types - Enhanced Feature

## Overview

This enhancement extends the quantity-based tranches to support different unit types for counting:

1. **UNITS** - Count by individual pieces/items (default)
2. **PACKAGE** - Count by packages/packs (e.g., 1 pack = 10 units)
3. **MEASUREMENT** - Count by weight/volume (KG, liters, grams)

## Implementation Summary

### New Database Field

**Column:** `quantity_unit_type`
- **Type:** VARCHAR(50)
- **Default:** 'UNITS'
- **Values:** 'UNITS', 'PACKAGE', 'MEASUREMENT'
- **Purpose:** Specifies how to count quantities for QUANTITY-based tranches

### How It Works

When a tranche has `apply_type = 'QUANTITY'`, the system now considers the `quantity_unit_type` to determine how to count:

#### 1. UNITS Mode (Default)
- Counts individual items as-is
- Example: Order 100 pieces → compares against min/max of 100
- Best for: Products sold by piece count

#### 2. PACKAGE Mode
- Converts items to package/pack count
- Example: If 1 pack = 10 units, ordering 100 units = 10 packages
- The tranche min/max is compared against packages, not individual units
- Best for: Bulk products sold in fixed packages

#### 3. MEASUREMENT Mode
- Converts items to weight/volume
- Example: If 1 item = 500g, ordering 20 items = 10 kg
- The tranche min/max is compared against KG/liters
- Best for: Products sold by weight (meat, produce) or volume (liquids)

## User Interface Changes

### Add/Edit Forms

**New Field:** "Unité de comptage" (Quantity Unit Type)
- **Location:** Shows only when "Type d'application" is set to "Quantité"
- **Options:**
  - "Par unités (pièces individuelles)" - UNITS
  - "Par colis/paquet" - PACKAGE  
  - "Par poids/volume (KG, L)" - MEASUREMENT
- **Behavior:** Automatically hides when switching to AMOUNT-based tranches

### List View

The "Type d'application" column now shows more detailed information:
- **Quantité (unités)** - For UNITS mode
- **Quantité (colis)** - For PACKAGE mode
- **Quantité (kg/L)** - For MEASUREMENT mode
- **Montant (DH)** - For AMOUNT-based tranches (unchanged)

## Technical Implementation

### OrderPricingService

New method: `normalizeQuantityForTranche()`

This method converts the original quantity to the appropriate unit based on `quantity_unit_type`:

```php
private function normalizeQuantityForTranche(
    int $quantity,      // Original quantity
    $pack,              // Pack entity with measurement info
    string $quantityUnitType  // UNITS, PACKAGE, or MEASUREMENT
): int
```

**UNITS Mode:**
- Returns quantity as-is
- No conversion needed

**PACKAGE Mode:**
- Looks for `package_quantity` in pack
- Or uses `packunites` relationship
- Calculates: `floor(quantity / package_quantity)`
- Example: 100 units ÷ 10 units/pack = 10 packages

**MEASUREMENT Mode:**
- Uses `measurement_quantity` and `measurement_unit_id`
- For saletype_id = 4 (weight/volume products)
- Converts to base units (grams/ml) then to KG/L
- Example: 20 items × 500g each = 10,000g = 10 KG

### Tranche Comparison Logic

Both linked tranches and global tranches now use:

```php
if ($applyType === 'AMOUNT') {
    $valueToCompare = $totalOrderAmount ?? 0;
} else {
    // QUANTITY mode - normalize based on unit type
    $quantityUnitType = $tranch->quantity_unit_type ?? 'UNITS';
    $valueToCompare = $this->normalizeQuantityForTranche(
        $quantity,
        $pack,
        $quantityUnitType
    );
}
```

## Usage Examples

### Example 1: Units Mode (Individual Items)
```
Tranche: "Bulk Purchase 100+ pieces"
Apply Type: QUANTITY
Quantity Unit Type: UNITS
Min: 100
Max: 499
Discount: 5%

Order: 150 pieces
Comparison: 150 (as-is) ≥ 100 → ✓ Discount applied
```

### Example 2: Package Mode (Packs/Cartons)
```
Tranche: "Wholesale 10+ packages"
Apply Type: QUANTITY
Quantity Unit Type: PACKAGE
Min: 10
Max: null
Discount: 15%

Product: 1 package = 12 bottles
Order: 150 bottles
Calculation: 150 ÷ 12 = 12.5 packages → 12 packages
Comparison: 12 ≥ 10 → ✓ Discount applied
```

### Example 3: Measurement Mode (Weight/Volume)
```
Tranche: "Bulk 50+ KG"
Apply Type: QUANTITY
Quantity Unit Type: MEASUREMENT
Min: 50
Max: null
Discount: 10%

Product: Chicken, 500g per piece
Order: 120 pieces
Calculation: 120 × 500g = 60,000g = 60 KG
Comparison: 60 ≥ 50 → ✓ Discount applied
```

### Example 4: Amount Mode (Unchanged)
```
Tranche: "Large Order 10,000+ DH"
Apply Type: AMOUNT
Quantity Unit Type: (not applicable, field hidden)
Min: 10000
Max: null
Discount: 12%

Order: Total = 12,500 DH
Comparison: 12,500 ≥ 10,000 → ✓ Discount applied
```

## Product Requirements

### For PACKAGE Mode to Work
Your pack/product should have one of:
- `package_quantity` field (e.g., 10 means 1 pack = 10 units)
- `packunites` relationship with quantity info

### For MEASUREMENT Mode to Work
Your pack/product should have:
- `saletype_id = 4` (indicates measurement-based product)
- `measurement_unit_id` (2 or 4 for KG/L type units)
- `measurement_quantity` (e.g., 500 means 500g or 500ml per item)

OR alternatively:
- `measurement_quantity` directly on pack (total weight/volume)

## Files Modified

1. **Migration:**
   - `config/Migrations/20260103000001_AddQuantityUnitTypeToTranches.php` (NEW)

2. **Model:**
   - `src/Model/Table/TranchesTable.php` - Added validation

3. **Views:**
   - `src/Template/Tranches/add.ctp` - Added unit type field with conditional display
   - `src/Template/Tranches/edit.ctp` - Added unit type field with conditional display

4. **Controller:**
   - `src/Controller/TranchesController.php` - Updated search to display unit type

5. **Service:**
   - `src/Service/OrderPricingService.php` - Added normalizeQuantityForTranche() method

## Migration History

1. **20260102000001_AddApplyTypeToTranches.php**
   - Added `apply_type` (QUANTITY vs AMOUNT)

2. **20260103000001_AddQuantityUnitTypeToTranches.php** (NEW)
   - Added `quantity_unit_type` (UNITS, PACKAGE, MEASUREMENT)

## Backward Compatibility

✓ **Fully Backward Compatible**
- All existing tranches default to UNITS mode
- No changes to existing behavior
- UNITS mode works exactly as before
- Field is optional and hidden for AMOUNT-based tranches

## Common Scenarios

### Scenario 1: Restaurant Wholesale
```
Product: Beer bottles, sold in packs of 24
Tranche: "10+ packs = 10% off"
Configuration:
  - apply_type: QUANTITY
  - quantity_unit_type: PACKAGE
  - min: 10 (packages)
  - package_quantity: 24

Order 300 bottles → 300 ÷ 24 = 12.5 → 12 packages ✓ Discount applied
```

### Scenario 2: Meat Shop
```
Product: Chicken breast, 500g per piece
Tranche: "50+ KG = 15% off"
Configuration:
  - apply_type: QUANTITY
  - quantity_unit_type: MEASUREMENT
  - min: 50 (KG)
  - saletype_id: 4
  - measurement_quantity: 500

Order 150 pieces → 150 × 500g = 75kg ✓ Discount applied
```

### Scenario 3: Electronics Store
```
Product: USB cables, sold individually
Tranche: "100+ units = 5% off"
Configuration:
  - apply_type: QUANTITY
  - quantity_unit_type: UNITS
  - min: 100 (pieces)

Order 120 cables → 120 units ✓ Discount applied
```

### Scenario 4: Mixed Discounts
```
You can have multiple tranches with different unit types:

Tranche 1: "50+ units" (UNITS mode) = 3% off
Tranche 2: "10+ packages" (PACKAGE mode) = 8% off
Tranche 3: "Orders 5000+ DH" (AMOUNT mode) = 12% off

The system will apply the first matching tranche based on the order.
```

## Testing Checklist

- [ ] Create UNITS-based tranche, verify it counts individual items
- [ ] Create PACKAGE-based tranche, verify it counts packages correctly
- [ ] Create MEASUREMENT-based tranche, verify weight/volume calculation
- [ ] Verify unit type field hides when switching to AMOUNT mode
- [ ] Verify unit type field shows when switching back to QUANTITY mode
- [ ] Check list view shows correct unit type labels
- [ ] Test with products that have package_quantity
- [ ] Test with products that have measurement_quantity
- [ ] Verify backward compatibility with existing tranches

## Support Notes

**Q: My package-based tranche isn't working. Why?**
A: Make sure your product has `package_quantity` field set, or has a `packunites` relationship with quantity info.

**Q: My measurement-based tranche isn't working. Why?**
A: Ensure your product has:
- `saletype_id = 4`
- `measurement_unit_id` (2 or 4)
- `measurement_quantity` set correctly

**Q: Can I mix different unit types?**
A: Yes! You can create different tranches with different unit types. They'll be evaluated independently.

**Q: What happens if pack data is missing?**
A: The system falls back to 1:1 ratio (treats as UNITS mode).

---

**Created:** January 3, 2026  
**Version:** 2.0  
**Status:** Production Ready ✓
