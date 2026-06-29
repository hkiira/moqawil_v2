# Test Readiness Documentation

## Test Suite Overview
The Orders Analytics Dashboard is covered by integration tests located in:
`tests/TestCase/Controller/OrdersControllerTest.php`

## Prerequisites & Database Configuration
To run the test suite, ensure the CakePHP test database connection `'test'` is configured.
If running in a local WampServer or CLI environment, you can override the database URL using the environment variable:
`DATABASE_TEST_URL="mysql://root:@localhost/test_myapp"`

## How to Run the Tests
Run the PHPUnit tests using the appropriate PHP executable:
```bash
# Running with PHP 8.0:
d:\wamp64\bin\php\php8.0.30\php.exe vendor/phpunit/phpunit/phpunit tests/TestCase/Controller/OrdersControllerTest.php
```

## Covered Test Cases
1. **Stat Cards Metrics (Feature 1)**:
   - `testVentesFeature1StatCardsTotalRevenue`: Verifies calculation of Total Revenue (status 6 sales amount).
   - `testVentesFeature1StatCardsTotalCommission`: Verifies calculation of Total Commission (based on orderpack's turnover commission percentages).
   - `testVentesFeature1StatCardsTotalOrdersCount`: Verifies total order count query.
   - `testVentesFeature1StatCardsPendingOrdersCount`: Verifies count of pending status orders.
   - `testVentesFeature1StatCardsHtmlStructure`: Verifies that key layout components and labels are rendered in the HTML response.

2. **Date Range Filters (Feature 2)**:
   - `testVentesFeature2DateRangeFilterStartOnly`
   - `testVentesFeature2DateRangeFilterEndOnly`
   - `testVentesFeature2DateRangeFilterBoth`
   - `testVentesFeature2DateRangeFilterNone`
   - `testVentesFeature2DateRangeFilterMatchSingleDay`

3. **Seller (User) Filters (Feature 3)**:
   - `testVentesFeature3SellerFilterSpecificSeller`
   - `testVentesFeature3SellerFilterNullSeller`
   - `testVentesFeature3SellerFilterDifferentSeller`
   - `testVentesFeature3SellerFilterInvalidSeller`
   - `testVentesFeature3SellerFilterSelf`

4. **Chart Data & JSON Payloads (Feature 4)**:
   - `testVentesFeature4ChartDataLineChartExists`
   - `testVentesFeature4ChartDataDoughnutChartExists`
   - `testVentesFeature4ChartDataLineChartFormat`
   - `testVentesFeature4ChartDataDoughnutChartFormat`
   - `testVentesFeature4ChartDataEmptyLineChart`

5. **Layout Compliance (Feature 5)**:
   - `testVentesFeature5LayoutCssIncluded`
   - `testVentesFeature5LayoutCanvasElementsPresent`
   - `testVentesFeature5LayoutGridStructure`
   - `testVentesFeature5LayoutStatCardsRendered`
   - `testVentesFeature5LayoutScriptTagsChartJs`

## Fixture Management
The test suite utilizes the following fixtures:
- `app.Orders`
- `app.Customers`
- `app.Shippings`
- `app.Pofsales`
- `app.Users`
- `app.Companies`
- `app.Orderpacks`
- `app.Turnovers`
- `app.Warehouses`
- `app.Whusers`
