# E2E Test Infra: Orders Analytics Dashboard

## Test Philosophy
- Opaque-box, requirement-driven. No dependency on internal DB queries or private methods.
- Methodology: Category-Partition, Boundary Value Analysis, Pairwise Interaction Testing, and Real-World Workload Simulation.

## Feature Inventory
| # | Feature | Source (requirement) | Tier 1 | Tier 2 | Tier 3 |
|---|---------|---------------------|:------:|:------:|:------:|
| 1 | Stat Cards Metrics | ORIGINAL_REQUEST R1/R2 | 5 | 5 | ✓ |
| 2 | Date Range Filters | ORIGINAL_REQUEST R1/R3 | 5 | 5 | ✓ |
| 3 | Seller (User) Filters | ORIGINAL_REQUEST R1/R3 | 5 | 5 | ✓ |
| 4 | Chart Data Structure | ORIGINAL_REQUEST R1/R2 | 5 | 5 | ✓ |
| 5 | Custom CSS & Responsive Layout | ORIGINAL_REQUEST R2/R3 | 5 | 5 | ✓ |

## Test Architecture
- **Test Runner**: PHPUnit via CakePHP IntegrationTestCase.
- **Invocation**: `vendor/bin/phpunit tests/TestCase/Controller/OrdersControllerTest.php`
- **Pass/Fail Semantics**: All test cases must run successfully and exit with 0.
- **Verification Channel**:
  - GET requests to `/orders/ventes` (or `/orders/analytics`) with range parameters and user parameters.
  - Assert response code is 200.
  - Assert template variable content (e.g. `total`, `totalcommission`, time-series, status counts).
  - Inspect returned HTML response content for stat-card css classes and canvas elements for charts.

## Real-World Application Scenarios (Tier 4)
| # | Scenario | Features Exercised | Complexity |
|---|----------|--------------------|------------|
| 1 | Empty Date Range (No Orders) | F1, F2, F4 | Low |
| 2 | Single Seller, Multiple Orders | F1, F3, F4 | Medium |
| 3 | Date range spanning empty days | F1, F2, F4 | Medium |
| 4 | High volume mixed statuses | F1, F2, F3, F4 | High |
| 5 | Cross-boundary date queries | F1, F2, F3 | Medium |

## Coverage Thresholds
- **Tier 1 (Feature Coverage)**: ≥25 test cases verifying each feature's happy-path.
- **Tier 2 (Boundary & Corner Cases)**: ≥25 test cases verifying date boundaries, null seller, invalid dates, extreme values.
- **Tier 3 (Cross-Feature Combinations)**: ≥5 test cases combining date range variations with seller changes.
- **Tier 4 (Real-World Application)**: ≥5 comprehensive scenarios verifying end-to-end integration and data correctness.
