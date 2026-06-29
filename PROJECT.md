# Project: Orders Analytics Dashboard

## Architecture
- **Framework**: CakePHP 3.x (MVC structure).
- **Backend Data Fetching**: `OrdersController` processes filtering by date-range and seller (user), calculates metrics, aggregates data, and returns the response.
- **Frontend Views**:
  - `src/Template/Orders/index.ctp`: Main order listing page. Integrates the dashboard view and executes AJAX requests to fetch updated dashboard content.
  - `src/Template/Orders/analytics.ctp`: Dashboard view containing four stat cards (rendered using `Template/Element/dashboard/stat_card.ctp`) and two charts (Line chart and Doughnut/Bar chart using Chart.js).
- **CSS Styles**: Styled using `webroot/css/dashboard-custom.css` for consistent styling.
- **Data Flow**:
  1. Frontend page loads.
  2. Main script executes AJAX request to `/orders/ventes` (or `/orders/analytics`) with range filters `keyword[start]`, `keyword[end]`, and optional seller filter `keyword[user]`.
  3. Controller performs database query to find matching orders/orderpacks.
  4. Controller computes totals: Total Revenue (status 6 sales amount), Total Commission (delivered commissions), Total Orders Count, Pending Orders Count, daily metrics for line chart, status distribution for doughnut/bar chart.
  5. Controller sets view variables and renders the analytics template.
  6. Frontend replaces `.ventes` subtitle target with the returned HTML and initializes/renders the Chart.js charts.

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| 1 | E2E Testing Suite | Design and create E2E tests in a new or existing test file to cover dashboard metrics accuracy, chart rendering, filters, and write `TEST_READY.md`. | none | IN_PROGRESS (Conv: bf379fbe-f7e5-4caa-9798-6cd50f954314) |
| 2 | Backend Controller Logic | Update `OrdersController::ventes` (or expose `analytics` endpoint) to query and compute the required dashboard data: revenue, commission, order counts, time-series, and statuses. | M1 | IN_PROGRESS (Conv: 2c6af4b6-2f81-4578-923b-e361b0f62111) |
| 3 | Frontend Dashboard View & Integration | Create `src/Template/Orders/analytics.ctp` view using stat card elements, CDN Chart.js, custom CSS, and integrate AJAX reload handlers in `index.ctp`. | M2 | IN_PROGRESS (Conv: 2c6af4b6-2f81-4578-923b-e361b0f62111) |
| 4 | Adversarial Hardening (Tier 5) | Analyze implementation code paths, generate adversarial tests, resolve any uncovered issues or edge cases. | M3 | PLANNED |

## Interface Contracts
### Frontend AJAX ↔ OrdersController::ventes (or analytics)
- **Request Type**: GET
- **Parameters**:
  - `keyword[start]`: Date string (YYYY-MM-DD)
  - `keyword[end]`: Date string (YYYY-MM-DD)
  - `keyword[user]`: Integer string (Seller User ID) or empty/null (for all sellers)
- **Response**: HTML content containing the rendered stat cards, chart canvases, and inline JSON scripts to populate Chart.js graphs.

## Code Layout
- `src/Controller/OrdersController.php` - Controller handling requests and data queries.
- `src/Template/Orders/index.ctp` - Main index template that renders orders and handles filter changes.
- `src/Template/Orders/analytics.ctp` - New template containing stat cards and chart configurations.
- `src/Template/Element/dashboard/stat_card.ctp` - Reusable component for rendering individual stat metrics.
- `webroot/css/dashboard-custom.css` - Custom premium styling sheet.
