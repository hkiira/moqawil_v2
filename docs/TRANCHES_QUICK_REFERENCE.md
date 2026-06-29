# Tranches Apply Type - Quick Reference

## Overview
Tranches can now be applied based on **QUANTITY** or **AMOUNT** (Total Order Value)

## Visual Comparison

### Quantity-Based Tranches (Original)
```
Tranche: "Bulk 100+ Units"
Apply Type: QUANTITY
Min: 100 units
Max: 499 units
Discount: 5%

Order #1: 50 units → NO discount ❌
Order #2: 150 units → 5% discount ✓
Order #3: 500 units → NO discount (above max) ❌
```

### Amount-Based Tranches (New)
```
Tranche: "Large Order 10,000 DH+"
Apply Type: AMOUNT
Min: 10,000 DH
Max: unlimited
Discount: 10%

Order #1: Total 8,000 DH → NO discount ❌
Order #2: Total 12,000 DH → 10% discount ✓
Order #3: Total 15,000 DH → 10% discount ✓
```

## When to Use Each

| Scenario | Type | Example |
|----------|------|---------|
| Encourage bulk purchasing | QUANTITY | "10+ items = 5% off" |
| Volume-based discounts | QUANTITY | "100+ kg = 10% off" |
| Minimum order value | AMOUNT | "Orders over 5,000 DH = 8% off" |
| Customer loyalty tiers | AMOUNT | "VIP: 10,000+ DH = 15% off" |
| Seasonal promotions | AMOUNT | "Black Friday: 50,000+ DH = 20% off" |

## Database Schema

```sql
CREATE TABLE tranches (
    id INT PRIMARY KEY,
    code VARCHAR(255),
    title VARCHAR(255),
    min INT,
    max INT,
    remise INT,
    remisetype_id INT,
    company_id INT,
    pack_id INT,
    statut INT,
    apply_type VARCHAR(50) DEFAULT 'QUANTITY',  -- NEW COLUMN
    created TIMESTAMP,
    modified TIMESTAMP,
    FOREIGN KEY (remisetype_id) REFERENCES remisetypes(id),
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (pack_id) REFERENCES packs(id)
);
```

## Service Method Signature

### Before
```php
$pricing->priceLine($packId, $quantity, $customerTypeId, $warehouseId, $companyId)
```

### After (New Optional Parameter)
```php
$pricing->priceLine(
    $packId,
    $quantity,
    $customerTypeId,
    $warehouseId,
    $companyId,
    $totalOrderAmount = null  // NEW: optional for AMOUNT-based tranches
)
```

## Implementation Checklist

- [x] Database column added (apply_type VARCHAR(50) DEFAULT 'QUANTITY')
- [x] Model validation added
- [x] Add/Edit forms updated with dropdown selector
- [x] List view shows apply_type column
- [x] Pricing service logic handles both types
- [x] Backward compatible (defaults to QUANTITY)
- [x] Documentation complete

## Configuration Examples

### Example 1: Tiered Quantity Discounts
```php
Tranche 1: min=50, max=99, remise=3%, apply_type=QUANTITY
Tranche 2: min=100, max=499, remise=6%, apply_type=QUANTITY
Tranche 3: min=500, remise=10%, apply_type=QUANTITY
```

### Example 2: Tiered Amount Discounts
```php
Tranche 1: min=5000, max=9999, remise=5%, apply_type=AMOUNT
Tranche 2: min=10000, max=24999, remise=8%, apply_type=AMOUNT
Tranche 3: min=25000, remise=12%, apply_type=AMOUNT
```

### Example 3: Mixed Approach (Recommended)
```php
// Quantity-based for small/medium orders
Tranche 1: min=10, max=99, remise=3%, apply_type=QUANTITY
Tranche 2: min=100, remise=6%, apply_type=QUANTITY

// Amount-based for large orders (overrides quantity if match)
Tranche 3: min=10000, remise=10%, apply_type=AMOUNT
```

## UI Screenshots

### Add/Edit Form
```
┌─────────────────────────────────────────┐
│ Nom: [Large Order Discount]              │
│ Type d'application: [Basée sur le montant▼]
│ Min: [10000]                             │
│ Max: [        ]  (leave empty for ∞)    │
│ Type de remise: [% (Pourcentage)    ▼]  │
│ Remise: [10]                             │
│ Statut: [✓ Actif]                        │
└─────────────────────────────────────────┘
```

### List View
```
Code    │ Title              │ Type   │ Remise │ Type d'app  │ Status
--------|------------------|--------|--------|-------------|---------
TR001   │ Bulk 100+        │ %      │ 5%     │ Quantité    │ Active
TR002   │ Large Order 10k  │ %      │ 10%    │ Montant (DH)│ Active
TR003   │ VIP Customer     │ RED    │ 50 DH  │ Montant (DH)│ Active
```

## Validation Rules

| Field | Rule | Valid |
|-------|------|-------|
| apply_type | Must be QUANTITY or AMOUNT | ✓ QUANTITY, ✓ AMOUNT, ❌ PRICE, ❌ TOTAL |
| min | Positive integer | ✓ 100, ✓ 5000, ❌ -5, ❌ 3.5 |
| max | Positive integer or null | ✓ 500, ✓ null, ❌ -500 |

## Common Issues & Solutions

### Issue: Amount-based tranche not applying
**Cause:** totalOrderAmount parameter not passed to priceLine()
**Solution:** Calculate and pass total order amount to the service

### Issue: Tranches not applying at all
**Cause:** apply_type might be NULL in legacy data
**Action:** Migration defaults to QUANTITY - update manually if needed

### Issue: Multiple tranches matching
**Behavior:** First matching tranche (by min value) is applied
**Tip:** Order tranches by min value to control priority

## Migration from Existing System

1. **No action needed** - all existing tranches automatically default to QUANTITY
2. **To enable amount-based tranches:**
   - Edit existing tranche
   - Change "Type d'application" to "Basée sur le montant"
   - Update min/max values to use monetary amounts
   - Save

## Performance Notes

- Minimal performance impact (one additional field check)
- No additional database queries required
- Both types use the same pricing engine
- Backward compatible - existing logic unchanged for QUANTITY mode

---

**Created:** January 2, 2026
**Status:** Production Ready ✓
