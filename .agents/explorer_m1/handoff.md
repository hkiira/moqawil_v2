# Handoff Report

## 1. Observation
The following details were observed during static analysis of the codebase:
* In `src/Model/Table/CustomersTable.php`, line 39:
  ```php
  $this->setTable('customers');
  ```
* In `src/Model/Table/PacksTable.php`, line 59:
  ```php
  $this->setTable('packs');
  ```
* Existing migrations in `config/Migrations/` use a Phinx-based wrapper. In `20240320000003_AddMeasurementQuantityToPacks.php` (lines 9-13):
  ```php
  $table->addColumn('measurement_quantity', 'decimal', [
      'default' => 1,
      'null' => false,
      'precision' => 10,
      'scale' => 2,
  ```
  In `20260111000001_CreateCommissionTiers.php` (lines 88-111), multiple columns are modified in a single migration class method.
* In `src/Model/Entity/Customer.php`, the `$_accessible` array spans lines 40-62.
* In `src/Model/Entity/Pack.php`, the `$_accessible` array spans lines 61-102.
* Running unit tests via `.\vendor\bin\phpunit` returned:
  ```
  Fatal error: Cannot acquire reference to $GLOBALS in D:\wamp64\www\moqa\vendor\phpunit\phpunit\src\Util\Configuration.php on line 543
  ```

---

## 2. Logic Chain
1. Since the `CustomersTable.php` and `PacksTable.php` classes explicitly call `$this->setTable(...)` with `'customers'` and `'packs'` respectively, the actual database table names are `customers` and `packs`.
2. Existing migrations use decimal precision of `10` and scale of `2` for numerical values that represent currency, percentages, or quantities (e.g., `commission_value`, `min_quantity`, `measurement_quantity`). Thus, the fields `wallet_balance` and `bonus_amount` should also be created as `decimal` with precision `10` and scale `2`, defaulting to `0.00` and non-nullable.
3. `bonus_unit_threshold` represents a count/threshold of units, which is discrete, and should be created as `integer`.
4. In CakePHP, mass-assignment is guarded by the `$_accessible` array in Entity files. To allow `$this->patchEntity($entity, $data)` to populate these new fields from request payloads, we must append `'wallet_balance' => true` to `Customer.php` and `'bonus_amount' => true, 'bonus_unit_threshold' => true` to `Pack.php`.

---

## 3. Caveats
* The actual migration file timestamp prefix (e.g. `20260622220000`) is generation-time dependent and must be chosen when generating the file.
* The test command `.\vendor\bin\phpunit` failed due to an incompatibility between the legacy phpunit version installed and the running PHP version on the machine. As a result, automated unit tests could not be run to check current coverage or health.

---

## 4. Conclusion
* **Tables**: Customer table is `customers`. Pack table is `packs`.
* **Migration**: A new migration file `YYYYMMDDHHMMSS_AddWalletAndBonusFields.php` should be added in `config/Migrations/` to update both tables.
* **Entities**: Both `Customer.php` and `Pack.php` require modifications in their `$_accessible` definitions to allow the fields to be set.

---

## 5. Verification Method
* To verify the changes, a developer or subagent can:
  1. Inspect the written analysis file: `d:\wamp64\www\moqa\.agents\explorer_m1\analysis.md`.
  2. Perform a dry-run migration check or run the migration command (e.g. `bin/cake migrations status` or `bin/cake migrations migrate`) after writing the migration files to ensure Phinx executes them successfully.
  3. Validate using CakePHP shell commands or Tinker tool that the entity objects allow the assignment of `wallet_balance`, `bonus_amount`, and `bonus_unit_threshold`.
