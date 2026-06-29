# Tranches Quantity Unit Types - Implementation Complete

## What Was Enhanced

Extended quantity-based tranches to support three unit counting methods:

### ✅ Unit Type Options

1. **UNITS** (Default) - Count individual pieces
   - Example: 100 pieces, 200 articles
   - Use case: Electronics, furniture, discrete items

2. **PACKAGE** - Count by packs/cartons
   - Example: 10 packs (where 1 pack = 12 units)
   - Use case: Wholesale, bulk packaging, beverage cases

3. **MEASUREMENT** - Count by weight/volume
   - Example: 50 KG, 100 liters
   - Use case: Food products, liquids, raw materials

## Key Features

### Smart UI
- **Conditional Display:** Unit type field only appears for quantity-based tranches
- **Auto-Hide:** Field disappears when switching to amount-based tranches
- **Clear Labels:** User-friendly French labels for each option

### Intelligent Calculation
- **Automatic Conversion:** System converts quantities based on selected unit type
- **Product-Aware:** Uses pack configuration (package_quantity, measurement_quantity)
- **Fallback Logic:** Defaults to 1:1 if product data is missing

### Enhanced Display
- List view shows: "Quantité (unités)", "Quantité (colis)", "Quantité (kg/L)"
- Clear distinction between different quantity types

## Files Modified

**Database:**
- ✅ `config/Migrations/20260103000001_AddQuantityUnitTypeToTranches.php` (NEW)

**Model:**
- ✅ `src/Model/Table/TranchesTable.php` - Added validation

**Views:**
- ✅ `src/Template/Tranches/add.ctp` - Added unit type field + JavaScript
- ✅ `src/Template/Tranches/edit.ctp` - Added unit type field + JavaScript

**Controller:**
- ✅ `src/Controller/TranchesController.php` - Updated display logic

**Service:**
- ✅ `src/Service/OrderPricingService.php` - Added normalizeQuantityForTranche() method

**Documentation:**
- ✅ `docs/tranches-quantity-unit-types.md` (NEW)

## How to Use

### Creating a Units-Based Tranche
1. Type d'application: **Basée sur la quantité**
2. Unité de comptage: **Par unités**
3. Min: 100 (count as individual pieces)
4. Discount: 5%

### Creating a Package-Based Tranche
1. Type d'application: **Basée sur la quantité**
2. Unité de comptage: **Par colis/paquet**
3. Min: 10 (count as complete packages)
4. Discount: 10%
5. **Requirement:** Product must have `package_quantity` configured

### Creating a Measurement-Based Tranche
1. Type d'application: **Basée sur la quantité**
2. Unité de comptage: **Par poids/volume (KG, L)**
3. Min: 50 (count as KG or liters)
4. Discount: 15%
5. **Requirement:** Product must have `saletype_id=4` and `measurement_quantity` configured

## Real-World Examples

### Example 1: Electronics Store (UNITS)
```
Product: USB Cable
Tranche: "100+ cables = 5% off"
Config: quantity_unit_type = UNITS

Order: 150 cables
Calculation: 150 units (no conversion)
Result: 150 ≥ 100 → ✓ 5% discount
```

### Example 2: Beverage Distributor (PACKAGE)
```
Product: Beer (1 pack = 24 bottles)
Tranche: "10+ packs = 15% off"
Config: quantity_unit_type = PACKAGE, package_quantity = 24

Order: 300 bottles
Calculation: 300 ÷ 24 = 12.5 → 12 complete packs
Result: 12 ≥ 10 → ✓ 15% discount
```

### Example 3: Butcher Shop (MEASUREMENT)
```
Product: Chicken breast (500g per piece)
Tranche: "50+ KG = 20% off"
Config: quantity_unit_type = MEASUREMENT, measurement_quantity = 500

Order: 120 pieces
Calculation: 120 × 500g = 60,000g = 60 KG
Result: 60 ≥ 50 → ✓ 20% discount
```

## Technical Highlights

### normalizeQuantityForTranche() Method

Intelligently converts quantities based on unit type:

```php
private function normalizeQuantityForTranche(
    int $quantity,           // Original order quantity
    $pack,                   // Product/pack entity
    string $quantityUnitType // UNITS, PACKAGE, or MEASUREMENT
): int
```

**UNITS:** Returns quantity as-is

**PACKAGE:** 
- Checks `pack->package_quantity`
- Or uses `pack->packunites` relationship
- Calculates complete packages

**MEASUREMENT:**
- For weight/volume products (`saletype_id = 4`)
- Uses `measurement_quantity` and `measurement_unit_id`
- Converts to KG or liters

## Migration Summary

### Previous Migrations
1. **20260102000001** - Added `apply_type` (QUANTITY vs AMOUNT)

### New Migration
2. **20260103000001** - Added `quantity_unit_type` (UNITS, PACKAGE, MEASUREMENT)

### Database Schema
```sql
ALTER TABLE tranches 
ADD COLUMN quantity_unit_type VARCHAR(50) DEFAULT 'UNITS' NOT NULL 
AFTER apply_type;
```

## Backward Compatibility

✅ **Fully Compatible**
- All existing tranches default to UNITS
- UNITS mode behaves exactly as before
- No breaking changes
- Field is optional and conditional

## Product Configuration Guide

### For UNITS Mode
✅ No special configuration needed
- Works with any product
- Counts items as-is

### For PACKAGE Mode
Required fields on product/pack:
- `package_quantity` (number of units per package)
- OR `packunites` relationship with quantity info

Example:
```php
$pack->package_quantity = 24;  // 1 pack = 24 units
```

### For MEASUREMENT Mode
Required fields on product/pack:
- `saletype_id = 4` (weight/volume product)
- `measurement_unit_id` (2 = KG, 4 = Liters)
- `measurement_quantity` (weight/volume per unit)

Example:
```php
$pack->saletype_id = 4;
$pack->measurement_unit_id = 2;  // KG
$pack->measurement_quantity = 500;  // 500g per piece
```

## Testing Checklist

- [x] Migration applied successfully
- [x] Validation rules working
- [x] Unit type field appears for QUANTITY tranches
- [x] Unit type field hides for AMOUNT tranches
- [x] JavaScript toggle works correctly
- [x] UNITS mode counts correctly (1:1)
- [x] PACKAGE mode calculates packages
- [x] MEASUREMENT mode converts to KG/L
- [x] List view displays unit types correctly
- [x] Backward compatible with existing tranches

## Status

✅ **PRODUCTION READY**

**Date:** January 3, 2026
**Version:** 2.0
**Migration:** Successfully applied

All features implemented, tested, and documented.

## Summary of Changes

| Component | Change | Status |
|-----------|--------|--------|
| Database | Added quantity_unit_type column | ✅ |
| Model | Added validation (UNITS/PACKAGE/MEASUREMENT) | ✅ |
| Add Form | Added conditional unit type selector | ✅ |
| Edit Form | Added conditional unit type selector | ✅ |
| JavaScript | Toggle visibility based on apply_type | ✅ |
| Service | normalizeQuantityForTranche() method | ✅ |
| Controller | Display unit type in list view | ✅ |
| Documentation | Complete feature guide | ✅ |

## Next Steps for Users

1. **Review existing tranches** - All defaulted to UNITS mode
2. **Identify package-based products** - Update tranches to use PACKAGE mode
3. **Identify weight/volume products** - Update tranches to use MEASUREMENT mode
4. **Configure products** - Ensure package_quantity or measurement_quantity is set
5. **Test with real orders** - Verify discounts apply correctly

---

**Complete!** The tranches system now supports flexible quantity counting with UNITS, PACKAGE, and MEASUREMENT modes.
