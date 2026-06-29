# Analysis Report: Database & Model Modifications

## Summary of Findings
1. **Tables**: The customers table is named `customers` and the packs table is named `packs`.
2. **Migrations**: Migrations are CakePHP Phinx migrations stored in `config/Migrations/`. They use a 14-digit timestamp prefix (`YYYYMMDDHHMMSS`) followed by an underscore and StudlyCase class name (e.g., `20260622220000_AddWalletBalanceToCustomers.php`). Columns such as balances and monetary values are structured as `decimal` with precision 10 and scale 2, and unit thresholds as `integer`.
3. **Entity Accessible Fields**: Both entity classes `Customer.php` and `Pack.php` define mass-assignable properties in their `$_accessible` array. To allow mass-assignment of the new columns, we must explicitly add the new columns with `=> true` to the array.

---

## 1. Table Verification

### Customer Table
- **File**: `src/Model/Table/CustomersTable.php`
- **Line 39**: `$this->setTable('customers');`
- **Verification**: The table name in the database is explicitly set to `customers`.

### Pack Table
- **File**: `src/Model/Table/PacksTable.php`
- **Line 59**: `$this->setTable('packs');`
- **Verification**: The table name in the database is explicitly set to `packs`.

---

## 2. Migration Structure & Naming Conventions

### File Naming Convention
Migrations are located in `config/Migrations/` and follow the pattern:
`[Timestamp]_[MigrationClassName].php`
Where `[Timestamp]` is a 14-digit UTC timestamp `YYYYMMDDHHMMSS` (e.g., `20260622220000`), and `[MigrationClassName]` is the StudlyCase matching class name.

### Field Definitions
- `wallet_balance` represents a currency value. Based on existing migrations (e.g., `CreateCommissionTiers`), currency/financial values are stored as `decimal` with precision `10` and scale `2` (`decimal(10,2)`). It should default to `0.00` and not be null.
- `bonus_amount` is a currency value and should also be `decimal(10,2)`, defaulting to `0.00` and not be null.
- `bonus_unit_threshold` represents a count of units, which should be an `integer`, defaulting to `0` and not be null.

### Proposed Migrations

#### Option A: Single Unified Migration
This approach updates both tables in one migration file, named e.g., `20260622220000_AddWalletAndBonusFields.php` containing class `AddWalletAndBonusFields`:

```php
<?php
use Migrations\AbstractMigration;

class AddWalletAndBonusFields extends AbstractMigration
{
    /**
     * Change Method.
     *
     * @return void
     */
    public function change()
    {
        // 1. Add wallet_balance to customers
        $customersTable = $this->table('customers');
        $customersTable->addColumn('wallet_balance', 'decimal', [
            'default' => 0.00,
            'null' => false,
            'precision' => 10,
            'scale' => 2,
            'comment' => 'Current wallet balance of the customer'
        ]);
        $customersTable->update();

        // 2. Add bonus_amount and bonus_unit_threshold to packs
        $packsTable = $this->table('packs');
        $packsTable->addColumn('bonus_amount', 'decimal', [
            'default' => 0.00,
            'null' => false,
            'precision' => 10,
            'scale' => 2,
            'comment' => 'Bonus amount associated with the pack'
        ]);
        $packsTable->addColumn('bonus_unit_threshold', 'integer', [
            'default' => 0,
            'null' => false,
            'comment' => 'Minimum unit threshold required to receive the bonus amount'
        ]);
        $packsTable->update();
    }
}
```

#### Option B: Separate Migrations
Alternatively, separate migrations can be created for separation of concerns:

1. **Customer Migration**: `20260622220001_AddWalletBalanceToCustomers.php`
```php
<?php
use Migrations\AbstractMigration;

class AddWalletBalanceToCustomers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('customers');
        $table->addColumn('wallet_balance', 'decimal', [
            'default' => 0.00,
            'null' => false,
            'precision' => 10,
            'scale' => 2,
            'comment' => 'Current wallet balance of the customer'
        ]);
        $table->update();
    }
}
```

2. **Pack Migration**: `20260622220002_AddBonusFieldsToPacks.php`
```php
<?php
use Migrations\AbstractMigration;

class AddBonusFieldsToPacks extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('packs');
        $table->addColumn('bonus_amount', 'decimal', [
            'default' => 0.00,
            'null' => false,
            'precision' => 10,
            'scale' => 2,
            'comment' => 'Bonus amount associated with the pack'
        ]);
        $table->addColumn('bonus_unit_threshold', 'integer', [
            'default' => 0,
            'null' => false,
            'comment' => 'Minimum unit threshold required to receive the bonus amount'
        ]);
        $table->update();
    }
}
```

---

## 3. Entity Modifications

### `Customer.php`
- **File**: `src/Model/Entity/Customer.php`
- **Modification**: Add `'wallet_balance' => true,` to the `$_accessible` array.

#### Code Diff
```diff
    protected $_accessible = [
        'code' => true,
        'name' => true,
        'phone' => true,
        'adresse' => true,
        'zone_id' => true,
        'customertype_id' => true,
        'ice' => true,
        'password' => true,
        'latitude' => true,
        'longitude' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'referral' => true,
        'referred' => true,
        'company_id' => true,
        'zone' => true,
        'customertype' => true,
        'company' => true,
        'photo' => true,
+       'wallet_balance' => true,
    ];
```

### `Pack.php`
- **File**: `src/Model/Entity/Pack.php`
- **Modification**: Add `'bonus_amount' => true,` and `'bonus_unit_threshold' => true,` to the `$_accessible` array.

#### Code Diff
```diff
    protected $_accessible = [
        'code' => true,
        'barecode' => true,
        'title' => true,
        'pack_id' => true,
        ...
        'whproducts' => true,
        'photo' => true,
+       'bonus_amount' => true,
+       'bonus_unit_threshold' => true,
    ];
```
