# Tranches & Trancheprices Architecture

## Overview

The pricing system uses two complementary tables and controllers to manage quantity-based discount bands:

```
Prices ←→ Trancheprices ←→ Tranches
           (Junction)
```

## Data Model

### Tranches (Discount Bands)
**Table**: `tranches`  
**Purpose**: Define quantity-based discount rules  
**Key Fields**:
- `id` - Primary key
- `code` - Auto-generated code (e.g., TR0001)
- `title` - Description (e.g., "10-19 kg")
- `min` - Minimum quantity
- `max` - Maximum quantity (NULL = unlimited)
- `remisetype_id` - Foreign key to remisetype
- `remise` - Discount value (interpreted per type)
- `pack_id` - For GRT (gift) type only; the gift pack to include
- `company_id` - Multi-tenancy key
- `statut` - Active/Inactive flag

### Remisetypes (Discount Types)
Linked via `remisetype_id`, defines discount calculation:
- `code = '%'` → Percentage discount
- `code = 'RED'` → Fixed DH amount discount
- `code = 'GRT'` → Gift product (free pack addition)

### Trancheprices (Price ↔ Tranche Mapping)
**Table**: `trancheprices`  
**Purpose**: Link a Price (for a specific pack/customer/warehouse) to a Tranche (discount band)  
**Key Fields**:
- `id` - Primary key
- `price_id` - Foreign key to prices
- `tranche_id` - Foreign key to tranches
- `company_id` - Multi-tenancy key

### Prices (Base Prices)
Linked via `price_id`, contains:
- `pack_id` - Which product/pack
- `customertype_id` - Which customer type (nullable)
- `warehouse_id` - Which warehouse (nullable)
- `value` - The base price in DH

## Controller Responsibilities

### TranchesController
**Manages**: Tranche definitions (discount rules)  
**Actions**:
- `index()` - List all tranches by company, paginated
- `view($id)` - Display tranche details + linked prices
- `add()` - Create new tranche with validation
  - GRT type: requires `pack_id`
  - % and RED: require `remise` value
- `edit($id)` - Update tranche parameters
- `delete($id)` - Remove tranche
- `remisetype()` (AJAX) - Return conditional form fields based on remisetype code

**Contains** (eager loading):
- Remisetypes (for discount type info)
- Companies (for multi-tenancy)
- Packs (for gift product display)
- **Trancheprices** (NEW) - To show which prices use this tranche

### TranchepricesController
**Manages**: Tranche ↔ Price associations  
**Actions**:
- `index()` - List all price-to-tranche mappings
- `view($id)` - View a specific mapping
- `add()` - Create mapping (select a price and assign it to a tranche)
- `edit($id)` - Update mapping
- `delete($id)` - Remove mapping

**Contains** (eager loading):
- Prices (to show pack, customer type, warehouse, value)
- Tranches (to show discount band details)
- Companies (for multi-tenancy)

## Tranche Application Modes

Tranches work in **two modes**:

### Mode 1: **Specific Application** (with Trancheprices)
When a tranche **has Trancheprices linked**:
- Apply discount ONLY to those specific prices
- Example: Create tranche "10-19 units = 5% off", link it to only Product A, Warehouse 1
- Other products/warehouses are unaffected

### Mode 2: **Global Application** (no Trancheprices)
When a tranche **has NO Trancheprices linked**:
- Apply discount to ALL products (all prices) in that company
- Example: Create tranche "50+ units = 10% off" (no links), applies to ANY product ordered 50+
- Automatically applies across all packs, customers, warehouses

## Pricing Algorithm

The `OrderPricingService::priceLine()` method uses this priority:

```
1. Look for Specific Tranches (linked via Trancheprices to this price)
   └─ If found and quantity matches: APPLY discount, DONE
   
2. If no Specific Tranche matched, look for Global Tranches (no Trancheprices)
   └─ If found and quantity matches: APPLY discount, DONE
   
3. If no tranches match: Return base price unchanged
```

**Example Scenario:**

| Tranche ID | Title | Min | Max | Type | Remise | Trancheprices | Mode |
|------------|-------|-----|-----|------|--------|---------------|----|
| TR0001 | Large orders | 100 | NULL | % | 15 | NONE | Global (apply to all) |
| TR0002 | Product A promo | 10 | 49 | % | 5 | Link to Product A only | Specific |
| TR0003 | VIP customer gifts | 25 | NULL | GRT | PackID=99 | Link to VIP customer only | Specific |

**Order Scenario 1**: Product A, 50 units, Regular customer
- Check Specific: Tranche TR0002 (10-49) → NO MATCH (qty=50 > max=49)
- Check Global: Tranche TR0001 (100+) → NO MATCH (qty=50 < min=100)
- Result: No discount applied

**Order Scenario 2**: Product B, 150 units, Regular customer
- Check Specific: No trancheprices for Product B
- Check Global: Tranche TR0001 (100-∞, 15%) → MATCH
- Result: 15% discount applied to all 150 units

## Key Relationships

```
Tranches (Discount Rules)
    ↓
    ← Trancheprices (Mappings) →
    ↑
Prices (Base Prices: pack + customer + warehouse)
```

**Why Two Tables?**
- **Tranches**: Define "if quantity is X-Y, apply Z discount"
- **Trancheprices**: Apply those rules to specific "pack+customer+warehouse" price combos
- A single tranche can apply to multiple prices (e.g., same discount rule for different warehouses)
- A price can belong to multiple tranches (e.g., 1-5 units, 6-10 units, 11+ units)

## UI Navigation

- **TranchesController**: Admin → Gestion des tranches
  - View tranche → Shows linked prices at bottom
  - Link: "Ajouter un prix pour cette tranche" → TranchepricesController/add?tranch_id=X

- **TranchepricesController**: Admin → Gestion des prix par tranche
  - Lists all price-to-tranche mappings with pack, customer type, warehouse, price

## API Integration (OrderPricingService)

```php
$pricing = $this->OrderPricingService->priceLine(
    packId: 5,
    quantity: 15,
    customerTypeId: 1,
    warehouseId: 2,
    companyId: 1
);

// Returns:
[
    'base_price' => 100,           // From Price
    'final_unit_price' => 95,       // After tranche discount applied
    'tranche_id' => 3,              // Which tranche matched (min=10, max=19)
    'gift_pack_id' => null,         // If GRT type, pack to add for free
    'gift_quantity' => 0,           // If GRT type, quantity of gift pack
]
```

The service:
1. Finds all Prices for the pack/customer/warehouse
2. For each Price, loads its linked Trancheprices (discount bands)
3. Matches quantity against tranche min/max
4. Applies discount code logic (%, RED, or GRT)
5. Returns final price + tranche info

## Database Schema

```sql
CREATE TABLE tranches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE,
    title VARCHAR(255),
    min INT,
    max INT NULL,
    remisetype_id INT,
    remise DECIMAL(10,2),
    pack_id INT NULL, -- For GRT type only
    company_id INT,
    statut TINYINT(1) DEFAULT 1,
    created DATETIME,
    modified DATETIME,
    FOREIGN KEY (remisetype_id) REFERENCES remisetypes(id),
    FOREIGN KEY (pack_id) REFERENCES packs(id),
    FOREIGN KEY (company_id) REFERENCES companies(id)
);

CREATE TABLE trancheprices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    price_id INT,
    tranche_id INT,
    company_id INT,
    created DATETIME,
    modified DATETIME,
    FOREIGN KEY (price_id) REFERENCES prices(id),
    FOREIGN KEY (tranche_id) REFERENCES tranches(id),
    FOREIGN KEY (company_id) REFERENCES companies(id)
);
```

## Summary

- **Tranches**: The "what" (discount rules)
- **Trancheprices**: The "where" (which prices apply these rules)
- **TranchesController**: Manage discount rules
- **TranchepricesController**: Map prices to discount rules
