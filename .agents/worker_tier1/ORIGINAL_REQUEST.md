## 2026-06-24T06:31:52Z
You are a worker agent (teamwork_preview_worker).
Your working directory is d:\wamp64\www\moqa\.agents\worker_tier1.
Your task is to implement the first 25 Tier 1 (Feature Coverage) test cases in `tests/TestCase/Controller/OrdersControllerTest.php`.

Requirements:
1. Define the correct fixtures list at the top of the test class:
   ```php
   public $fixtures = [
       'app.Orders',
       'app.Customers',
       'app.Shippings',
       'app.Pofsales',
       'app.Users',
       'app.Companies',
       'app.Orderpacks',
       'app.Turnovers',
       'app.Warehouses',
       'app.Whusers',
   ];
   ```
2. Write a helper method to clear the tables and setup standard mock data:
   - Clear tables: `orders`, `orderpacks`, `turnovers`, `pofsales`, `warehouses`.
   - Setup Warehouses:
     - Warehouse 1: id=1, title="Main Warehouse", warehouse_id=null, whtype_id=1, company_id=1, statut=1
     - Warehouse 2: id=2, title="Sub Deposit", warehouse_id=1, whtype_id=3, company_id=1, statut=1 (subwarehouse of 1 with type 3!)
     - Warehouse 3: id=3, title="Other Warehouse", warehouse_id=null, whtype_id=1, company_id=1, statut=1
   - Setup Pofsales:
     - Pofsale 1: id=1, title="POS Main", warehouse_id=1, company_id=1, pofstype_id=1, statut=1
     - Pofsale 2: id=2, title="POS Deposit", warehouse_id=2, company_id=1, pofstype_id=1, statut=1
     - Pofsale 3: id=3, title="POS Other", warehouse_id=3, company_id=1, pofstype_id=1, statut=1
   - Setup Turnover:
     - Turnover 1: id=1, commission=10.0, statut=1
   - Setup Orders and Orderpacks:
     - Order 1: id=1, user_id=1, pofsale_id=1, created='2022-01-10 10:00:00', company_id=1, statut=1
       - Pack: quantity=2, price=50.0, statut=6 (delivered), turnover_id=1
     - Order 2: id=2, user_id=1, pofsale_id=2, created='2022-01-15 12:00:00', company_id=1, statut=1
       - Pack: quantity=1, price=150.0, statut=6 (delivered), turnover_id=null (default commission)
     - Order 3: id=3, user_id=2, pofsale_id=1, created='2022-01-20 14:00:00', company_id=1, statut=1
       - Pack: quantity=3, price=200.0, statut=1 (pending), turnover_id=null
     - Order 4: id=4, user_id=1, pofsale_id=1, created='2022-02-05 09:00:00', company_id=1, statut=1
       - Pack: quantity=5, price=100.0, statut=6 (delivered), turnover_id=null (out of range)
     - Order 5: id=5, user_id=1, pofsale_id=3, created='2022-01-25 11:00:00', company_id=1, statut=1
       - Pack: quantity=1, price=500.0, statut=6 (delivered), turnover_id=null (wrong warehouse)
3. Implement the 25 Tier 1 test cases:
   - testVentesFeature1StatCardsTotalRevenue (assert total = 250)
   - testVentesFeature1StatCardsTotalCommission (assert totalcommission = 160)
   - testVentesFeature1StatCardsTotalOrdersCount (verify counting logic for matching orders)
   - testVentesFeature1StatCardsPendingOrdersCount (verify logic for pending orders count)
   - testVentesFeature1StatCardsHtmlStructure (verify CSS classes / elements for stat cards)
   - testVentesFeature2DateRangeFilterStartOnly
   - testVentesFeature2DateRangeFilterEndOnly
   - testVentesFeature2DateRangeFilterBoth
   - testVentesFeature2DateRangeFilterNone
   - testVentesFeature2DateRangeFilterMatchSingleDay
   - testVentesFeature3SellerFilterSpecificSeller
   - testVentesFeature3SellerFilterNullSeller
   - testVentesFeature3SellerFilterDifferentSeller
   - testVentesFeature3SellerFilterInvalidSeller
   - testVentesFeature3SellerFilterSelf
   - testVentesFeature4ChartDataLineChartExists
   - testVentesFeature4ChartDataDoughnutChartExists
   - testVentesFeature4ChartDataLineChartFormat
   - testVentesFeature4ChartDataDoughnutChartFormat
   - testVentesFeature4ChartDataEmptyLineChart
   - testVentesFeature5LayoutCssIncluded
   - testVentesFeature5LayoutCanvasElementsPresent
   - testVentesFeature5LayoutGridStructure
   - testVentesFeature5LayoutStatCardsRendered
   - testVentesFeature5LayoutScriptTagsChartJs
4. Run the test suite using PHP 8.0.30 and DATABASE_TEST_URL environment variable to ensure they compile and pass.

DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A Forensic Auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Write your changes and test execution results in a handoff.md in your working directory and send a completion message when done.
