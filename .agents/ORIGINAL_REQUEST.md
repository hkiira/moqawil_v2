# Original User Request

## Initial Request — 2026-06-22T21:45:08Z

Implement a full-stack wallet and purchase bonus system for the MOQA B2B platform. Users earn wallet credits based on individual product bonus configurations (e.g., earning 3 DH for every 10 Kg purchased).

Working directory: d:\wamp64\www\moqa
Integrity mode: demo

## Requirements

### R1. Database & Backend Data Model
Update the CakePHP database schema to add a "wallet_balance" to the user/customer table and bonus configuration fields (e.g., bonus_amount, bonus_unit_threshold) to the products table. The API endpoints must return these fields.

### R2. Backend Bonus Calculation
Update the CakePHP order processing/checkout logic. When a user completes a purchase, the system must calculate the total earned bonus from the order and automatically credit the user's wallet balance.

### R3. Flutter App Integration
The Flutter application must display the user's current wallet balance (e.g., in a profile or dashboard tab). The Product Details screen must show the potential bonus the user can earn from that product.

## Acceptance Criteria

### API Verification
- [ ] A programmatic API test (or curl script) can create an order containing a product with a bonus configuration.
- [ ] The API test verifies that the user's wallet balance is correctly incremented by the calculated bonus amount after the order is placed.

### Frontend Verification
- [ ] Launching the Flutter app displays the user's fetched wallet balance in the UI.
- [ ] The Product Details screen visually displays the bonus rule (e.g., "Earn 3 DH per 10 Kg") if the product has one configured.

## Follow-up — 2026-06-24T07:24:38+01:00

An overview for the Orders section featuring charts, metrics, and summary data, replacing the simple text sales totals.

Working directory: d:/wamp64/www/moqa
Integrity mode: demo

## Requirements

### R1. Controller Data Endpoint
Modify the controller logic in `OrdersController` to expose a method (e.g., `analytics` or update `ventes`) that fetches summary and analytics data for a given date range and optional seller filter.
The data must include:
- Total orders count.
- Total revenue (sum of delivered orders amount).
- Total commission (sum of commissions for delivered orders).
- Total pending orders count.
- Time-series data for daily orders and daily sales revenue.
- Order count distribution grouped by their status.

### R2. Analytics Dashboard View
Create a new view template file `analytics.ctp` under the `Orders` templates.
- It must render four Stat Cards using the existing `dashboard/stat_card` component:
  1. Total Revenue (Delivered Sales) in DH/MAD.
  2. Total Commission in DH/MAD.
  3. Total Orders count.
  4. Pending Orders count.
- It must include the Chart.js library (version 3.9.1 from CDN) and render two charts in a responsive two-column grid:
  1. A Line chart showing the daily sales trend (revenue and order count over time).
  2. A Doughnut or Bar chart showing the distribution of orders by status.
- It must use the styling from `webroot/css/dashboard-custom.css` to match the custom premium design of the dashboard.

### R3. Index Integration and AJAX Updates
Integrate the new `analytics.ctp` view into the main orders list view (`src/Template/Orders/index.ctp`).
- The analytics dashboard should replace the old text-based `.ventes` total sales element.
- The dashboard must refresh dynamically via AJAX when date-range or seller filters are modified on the page.

## Acceptance Criteria

### Metric Accuracy and Presentation
- [ ] The stat cards display accurate calculations for Total Revenue, Total Commission, Total Orders, and Pending Orders based on the selected date range and seller filters.
- [ ] All monetary figures are formatted in Moroccan Dirhams (DH/MAD).
- [ ] Stat card icons and colors match their purpose (e.g., wallet/success for revenue, shopping-cart/primary for orders, warning for pending).

### Chart Rendering and Interactivity
- [ ] The Line chart and Doughnut/Bar chart are successfully rendered in a responsive layout using Chart.js.
- [ ] Hovering over the charts displays tooltips with the respective metrics.
- [ ] When the date range or seller filter is changed, the AJAX request successfully fetches the new HTML, and the charts re-render correctly with the updated data.

### Code Integrity and Quality
- [ ] The new `analytics.ctp` template exists at `src/Template/Orders/analytics.ctp`.
- [ ] The dashboard cards and tables have clean transitions and adapt cleanly to desktop and mobile viewports.
