# Scope: Orders Analytics Dashboard Implementation

## Architecture
- **Framework**: CakePHP 3.x
- **Data flow**: AJAX GET request from `/orders/index` to `/orders/ventes` with start date, end date, and user filter. Controller aggregates orders, computes metrics (total count, revenue, commission, pending count, daily time-series, status distribution), renders HTML template `analytics.ctp` back, frontend mounts inside `.ventes`.

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| 1 | Backend Controller Logic (R1) | Implement calculations in `OrdersController::ventes` to collect revenue, commission, counts, daily trend data, and status counts. | none | PLANNED |
| 2 | Analytics View Template (R2) | Create `src/Template/Orders/analytics.ctp` using Chart.js CDN and existing stat_card element to present summary stats and 2 charts. | M1 | PLANNED |
| 3 | Frontend AJAX Integration (R3) | Integrate dashboard into `src/Template/Orders/index.ctp`, replacing old simple sales text, with AJAX filter loading. | M2 | PLANNED |
| 4 | E2E Testing Suite (Phase 1) | Wait for `TEST_READY.md`, run and pass 100% of the opaque-box E2E test suite. | M3 | PLANNED |
| 5 | Adversarial Coverage Hardening (Phase 2) | Generate adversarial test cases based on implementation code, run and fix gaps/bugs. | M4 | PLANNED |

## Interface Contracts
### GET /orders/ventes
- **Parameters**:
  - `keyword[start]` (YYYY-MM-DD)
  - `keyword[end]` (YYYY-MM-DD)
  - `keyword[user]` (integer or empty/null)
- **Response**: HTML block containing dashboard elements and inline script tags with Chart.js config data.
