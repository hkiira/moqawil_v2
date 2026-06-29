# Commission Multi-Pack System Guide

## Overview
The commission tier system now supports multiple pack selection with three different application modes.

## Apply Types

### 1. Global Mode (`all`)
- **Use Case**: Commission applies to all packs combined (legacy behavior)
- **No pack selection needed**
- **Example**: If total quantity across all packs is 10-20, commission is applied once

### 2. Single Mode (`single`)
- **Use Case**: Commission is calculated individually for each selected pack
- **Select specific packs**
- **Example**: 
  - Tier: 10-20 units = 1 DH
  - Selected packs: Pack A, Pack B, Pack C
  - If Pack A has 15 units → 1 DH commission
  - If Pack B has 12 units → 1 DH commission
  - If Pack C has 5 units → 0 DH (below minimum)
  - **Total: 2 DH**

### 3. Combined Mode (`combined`)
- **Use Case**: Commission is calculated based on the sum of selected packs
- **Select specific packs**
- **Example**: 
  - Tier: 10-20 units = 1 DH
  - Selected packs: Pack A, Pack B, Pack C
  - Pack A: 5 units
  - Pack B: 6 units
  - Pack C: 4 units
  - **Combined total: 15 units → 1 DH commission**

## Database Schema

### commission_tiers table
- `apply_type` ENUM('all', 'single', 'combined') DEFAULT 'all'

### commission_tiers_packs (junction table)
- `id` INT PRIMARY KEY
- `commission_tier_id` INT FOREIGN KEY
- `pack_id` INT FOREIGN KEY
- Many-to-many relationship between CommissionTiers and Packs

## How to Create Commission Tiers

### Creating a Global Tier
1. Go to Commission Tiers → Add New
2. Fill in: Name, Min/Max Quantity, Commission Type & Value
3. Select "Global (tous les packs)" for Apply Type
4. Don't select any packs
5. Save

### Creating a Single-Pack Tier
1. Go to Commission Tiers → Add New
2. Fill in tier details
3. Select "Individuel (par pack)" for Apply Type
4. **Check the boxes** for the packs you want to apply this tier to
5. Save

Each selected pack will be evaluated individually against the tier thresholds.

### Creating a Combined-Pack Tier
1. Go to Commission Tiers → Add New
2. Fill in tier details
3. Select "Combiné (somme des packs sélectionnés)" for Apply Type
4. **Check the boxes** for the packs you want to combine
5. Save

The quantities of all selected packs will be summed before checking the tier thresholds.

## Calculation Logic

The calculation happens in `CompensationsTable::calculateAndSaveCommission()`:

```php
// For 'all' mode
- No pack filtering
- Total quantity of all packs combined is checked
- Commission applied once if quantity matches tier

// For 'single' mode  
- For each pack in the tier's selected packs
- If that pack's individual quantity matches the tier
- Commission is applied for that pack
- Total commission = sum of all matching packs

// For 'combined' mode
- Sum quantities of all selected packs in the tier
- Check if combined quantity matches tier
- Commission applied once if combined quantity matches
```

## Example Scenarios

### Scenario 1: Reward High-Volume Individual Packs
**Goal**: Give 2 DH commission for each pack that reaches 20+ units

**Setup**:
- Name: "High Volume Per Pack"
- Min: 20, Max: empty
- Type: Fixed, Value: 2 DH
- Apply Type: **Individuel**
- Select: Pack A, Pack B, Pack C

**Result**: If Pack A has 25 units and Pack B has 22 units → 4 DH commission

### Scenario 2: Reward Combined Orders
**Goal**: Give 5 DH when certain packs together reach 50+ units

**Setup**:
- Name: "Combined Volume Bonus"
- Min: 50, Max: empty
- Type: Fixed, Value: 5 DH
- Apply Type: **Combiné**
- Select: Pack A, Pack B, Pack C

**Result**: If Pack A (15) + Pack B (20) + Pack C (18) = 53 units → 5 DH commission

### Scenario 3: Global All-Packs Commission
**Goal**: Give 1% commission when total order exceeds 100 units

**Setup**:
- Name: "Global Order Bonus"
- Min: 100, Max: empty
- Type: Percentage, Value: 1%
- Apply Type: **Global**
- Packs: None selected

**Result**: If total across ALL packs = 120 units → 1% of order total

## UI Features

### Add/Edit Forms
- Apply Type dropdown automatically shows/hides pack selection
- When "Global" is selected, pack checkboxes are hidden
- When "Individuel" or "Combiné" is selected, pack checkboxes appear

### Index Page
- Shows Apply Type as badge (Global/Individuel/Combiné)
- Shows selected packs as badges
- Color-coded for easy identification

### View Page
- Displays Apply Type with description
- Lists all selected packs with links
- Shows how commission will be calculated

## Technical Notes

- The `findByQuantityAndPacks()` method in CommissionTiersTable handles the filtering logic
- Junction table allows many-to-many relationship between tiers and packs
- Legacy `pack_id` field is preserved but no longer used in favor of the many-to-many relationship
- All migrations have been applied successfully

## Migration History
1. `20260111000001_CreateCommissionTiers.php` - Initial tier table
2. `20260111000002_AddPackIdToCommissionTiers.php` - Added pack_id (legacy)
3. `20260111000003_UpdateCommissionTiersForMultiplePacks.php` - Added apply_type and junction table

All migrations completed successfully.
