# Scope: E2E Testing Suite for Orders Analytics Dashboard

## Architecture
- **Framework**: CakePHP 3.x IntegrationTestCase
- **Test File**: `tests/TestCase/Controller/OrdersControllerTest.php` (or a dedicated file like `tests/TestCase/Controller/OrdersAnalyticsTest.php`)
- **Main Endpoint**: `/orders/ventes` (accepts date range and seller filters)

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| 1 | Explore & Setup | Explore DB schema, existing test run command, check default credentials/auth handling in tests | none | DONE |
| 2 | Tier 1 Tests | Design and implement 25 Tier 1 Feature Coverage tests (at least 5 per feature) | M1 | IN_PROGRESS |
| 3 | Tier 2 Tests | Design and implement 25 Tier 2 Boundary & Edge cases | M2 | PLANNED |
| 4 | Tier 3 & 4 Tests | Design and implement 5 Tier 3 Cross-Feature and 5 Tier 4 Real-World scenarios | M3 | PLANNED |
| 5 | Verify & Publish | Run all tests, pass review, perform forensic audit, and write TEST_READY.md | M4 | PLANNED |

## Feature Inventory & Test Goals
- **F1: Stat Cards Metrics**: Assert variables set (`total`, `totalcommission`, etc.) and stat-card styling / canvas tags are present in HTML response.
- **F2: Date Range Filters**: Test standard start/end date filtering, same day filtering, long ranges.
- **F3: Seller (User) Filters**: Test filtering by specific seller IDs and filtering when user is null (all sellers).
- **F4: Chart Data Structure**: Assert view variables for time-series charts and status distribution charts contain correct structure.
- **F5: Custom CSS & Layout**: Assert response contains custom css stylesheets or relevant layout indicators.
