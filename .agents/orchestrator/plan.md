# Project: Wallet & Purchase Bonus System

## Architecture & Data Flow
1. **Database Schema**:
   - `customers` table: Adds `wallet_balance` (decimal, default 0.00).
   - `packs` table: Adds `bonus_amount` (decimal, default 0.00) and `bonus_unit_threshold` (decimal, default 0.00).
2. **Backend Controllers**:
   - `B2bCustomerApiController::profile()` returns the `wallet_balance`.
   - `B2bCustomerApiController::products()` and `newhomeproducts()` return `bonus_amount`, `bonus_unit_threshold`, and `measurement_unit_abbreviation`.
   - `B2bCustomerApiController::addOrder()` calculates the bonus from `cartItems` and updates the customer's `wallet_balance`.
3. **Flutter App Frontend**:
   - `ProfileModel` reads `wallet_balance` from API profile response.
   - `ProfileScreen` renders the current wallet balance.
   - `ProductDetailsScreen` parses and visualizes the bonus rule (e.g., "Earn 3 DH per 10 Kg").

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| 1 | DB Schema & Models | Add database columns and configure CakePHP Entity access properties | None | DONE |
| 2 | Backend API & Checkout | Update profile, products, and checkout endpoints with wallet/bonus logic | M1 | DONE |
| 3 | API Integration Verification | Create PHPUnit integration tests to verify the bonus calculations and checkout behavior | M2 | DONE |
| 4 | Flutter App Integration | Fetch/display wallet balance on profile screen and bonus rule on product details | M2 | DONE |
| 5 | E2E & Integrity Verification | Perform final system test, run full suite, run Forensic Auditor check | M3, M4 | DONE |

## Interface Contracts
### B2B API Response format modifications:
- `/api/b2b/profile`:
  ```json
  {
    "success": true,
    "data": {
      "id": 1,
      "name": "Customer Name",
      "phone": "+123456789",
      "adresse": "Customer Address",
      "wallet_balance": 150.00
    }
  }
  ```
- `/api/b2b/products` & `/api/b2b/newhomeproducts`:
  Each product item includes:
  ```json
  {
    "id": 10,
    "title": "Product Title",
    ...
    "bonus_amount": 3.00,
    "bonus_unit_threshold": 10.00,
    "measurement_unit_abbreviation": "Kg"
  }
  ```

### Checkout calculation formula:
For each order item:
$$ \text{item\_bonus} = \frac{\text{quantity} \times \text{measurement\_quantity}}{\text{bonus\_unit\_threshold}} \times \text{bonus\_amount} $$
(Only applied if $\text{bonus\_unit\_threshold} > 0$ and $\text{bonus\_amount} > 0$).
Total bonus added to customer's wallet balance on checkout completion.
