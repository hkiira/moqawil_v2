<?php
namespace App\Test\TestCase\Controller;

use App\Controller\OrdersController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\Datasource\ConnectionManager;

/**
 * App\Controller\OrdersController Test Case
 *
 * @uses \App\Controller\OrdersController
 */
class OrdersControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
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

    /**
     * SetUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->setupMockData();
    }

    /**
     * TearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        $_GET = [];
        parent::tearDown();
    }

    /**
     * Helper method to clear the tables and setup standard mock data.
     *
     * @return void
     */
    protected function setupMockData()
    {
        $conn = ConnectionManager::get('test');
        $conn->execute('SET FOREIGN_KEY_CHECKS = 0');
        $conn->execute('TRUNCATE TABLE orderpacks');
        $conn->execute('TRUNCATE TABLE orders');
        $conn->execute('TRUNCATE TABLE pofsales');
        $conn->execute('TRUNCATE TABLE turnovers');
        $conn->execute('TRUNCATE TABLE warehouses');

        // Ensure users 1 and 2 exist in users table
        $conn->execute("INSERT INTO users (id, firstname, lastname, username, email, password, company_id, role_id, statut) VALUES (1, 'Admin', 'User', 'admin', 'admin@example.com', 'password', 1, 1, 1) ON DUPLICATE KEY UPDATE id=id");
        $conn->execute("INSERT INTO users (id, firstname, lastname, username, email, password, company_id, role_id, statut) VALUES (2, 'Seller', 'Two', 'seller2', 'seller2@example.com', 'password', 1, 3, 1) ON DUPLICATE KEY UPDATE id=id");

        // Setup Warehouses
        $conn->execute("INSERT INTO warehouses (id, code, title, warehouse_id, whtype_id, company_id, statut, whnature_id) VALUES (1, 'WH1', 'Main Warehouse', NULL, 1, 1, 1, 1)");
        $conn->execute("INSERT INTO warehouses (id, code, title, warehouse_id, whtype_id, company_id, statut, whnature_id) VALUES (2, 'WH2', 'Sub Deposit', 1, 3, 1, 1, 1)");
        $conn->execute("INSERT INTO warehouses (id, code, title, warehouse_id, whtype_id, company_id, statut, whnature_id) VALUES (3, 'WH3', 'Other Warehouse', NULL, 1, 1, 1, 1)");

        // Setup Pofsales
        $conn->execute("INSERT INTO pofsales (id, code, title, warehouse_id, company_id, pofstype_id, statut) VALUES (1, 'POS1', 'POS Main', 1, 1, 1, 1)");
        $conn->execute("INSERT INTO pofsales (id, code, title, warehouse_id, company_id, pofstype_id, statut) VALUES (2, 'POS2', 'POS Deposit', 2, 1, 1, 1)");
        $conn->execute("INSERT INTO pofsales (id, code, title, warehouse_id, company_id, pofstype_id, statut) VALUES (3, 'POS3', 'POS Other', 3, 1, 1, 1)");

        // Setup Turnover
        $conn->execute("INSERT INTO turnovers (id, title, commission, statut) VALUES (1, 'Turnover 1', 10.0, 1)");
        $conn->execute("INSERT INTO turnovers (id, title, commission, statut) VALUES (2, 'Turnover 2', 100.0, 1)");

        // Setup Orders and Orderpacks
        // Order 1: id=1, user_id=1, pofsale_id=1, created='2022-01-10 10:00:00', company_id=1, statut=1
        $conn->execute("INSERT INTO orders (id, code, user_id, customer_id, pofsale_id, created, company_id, statut) VALUES (1, 'ORD1', 1, 1, 1, '2022-01-10 10:00:00', 1, 1)");
        $conn->execute("INSERT INTO orderpacks (id, order_id, pack_id, quantity, price, statut, turnover_id, company_id, user_id, whnature_id) VALUES (1, 1, 1, 2, 50.0, 6, 1, 1, 1, 1)");

        // Order 2: id=2, user_id=1, pofsale_id=2, created='2022-01-15 12:00:00', company_id=1, statut=1
        $conn->execute("INSERT INTO orders (id, code, user_id, customer_id, pofsale_id, created, company_id, statut) VALUES (2, 'ORD2', 1, 1, 2, '2022-01-15 12:00:00', 1, 1)");
        $conn->execute("INSERT INTO orderpacks (id, order_id, pack_id, quantity, price, statut, turnover_id, company_id, user_id, whnature_id) VALUES (2, 2, 1, 1, 150.0, 6, 2, 1, 1, 1)");

        // Order 3: id=3, user_id=2, pofsale_id=1, created='2022-01-20 14:00:00', company_id=1, statut=1
        $conn->execute("INSERT INTO orders (id, code, user_id, customer_id, pofsale_id, created, company_id, statut) VALUES (3, 'ORD3', 2, 1, 1, '2022-01-20 14:00:00', 1, 1)");
        $conn->execute("INSERT INTO orderpacks (id, order_id, pack_id, quantity, price, statut, turnover_id, company_id, user_id, whnature_id) VALUES (3, 3, 1, 3, 200.0, 1, NULL, 1, 2, 1)");

        // Order 4: id=4, user_id=1, pofsale_id=1, created='2022-02-05 09:00:00', company_id=1, statut=1
        $conn->execute("INSERT INTO orders (id, code, user_id, customer_id, pofsale_id, created, company_id, statut) VALUES (4, 'ORD4', 1, 1, 1, '2022-02-05 09:00:00', 1, 1)");
        $conn->execute("INSERT INTO orderpacks (id, order_id, pack_id, quantity, price, statut, turnover_id, company_id, user_id, whnature_id) VALUES (4, 4, 1, 5, 100.0, 6, NULL, 1, 1, 1)");

        // Order 5: id=5, user_id=1, pofsale_id=3, created='2022-01-25 11:00:00', company_id=1, statut=1
        $conn->execute("INSERT INTO orders (id, code, user_id, customer_id, pofsale_id, created, company_id, statut) VALUES (5, 'ORD5', 1, 1, 3, '2022-01-25 11:00:00', 1, 1)");
        $conn->execute("INSERT INTO orderpacks (id, order_id, pack_id, quantity, price, statut, turnover_id, company_id, user_id, whnature_id) VALUES (5, 5, 1, 1, 500.0, 6, NULL, 1, 1, 1)");

        $conn->execute('SET FOREIGN_KEY_CHECKS = 1');
    }

    /**
     * Helper to setup auth session.
     *
     * @param int $defaultwh
     * @param int $userId
     * @return void
     */
    protected function setupSession($defaultwh = 1, $userId = 1)
    {
        $this->session([
            'Auth' => [
                'User' => [
                    'id' => $userId,
                    'username' => 'admin',
                    'defaultwh' => $defaultwh,
                ]
            ]
        ]);
    }

    /**
     * Helper to perform GET request with simulated $_GET superglobal parameters.
     *
     * @param string $start
     * @param string $end
     * @param string $user
     * @return void
     */
    protected function makeGetRequest($start, $end, $user)
    {
        $_GET['keyword'] = [
            'start' => $start,
            'end' => $end,
            'user' => $user
        ];
        $url = '/orders/ventes?keyword[start]=' . urlencode($start) . '&keyword[end]=' . urlencode($end) . '&keyword[user]=' . urlencode($user);
        $this->get($url);
    }

    // ==========================================
    // FEATURE 1: Stat Cards Metrics
    // ==========================================

    public function testVentesFeature1StatCardsTotalRevenue()
    {
        $this->setupSession();
        $this->makeGetRequest('2022-01-01', '2022-01-31', '');
        $this->assertResponseOk();
        $this->assertEquals(250.0, $this->viewVariable('total'));
    }

    public function testVentesFeature1StatCardsTotalCommission()
    {
        $this->setupSession();
        $this->makeGetRequest('2022-01-01', '2022-01-31', '');
        $this->assertResponseOk();
        $this->assertEquals(160.0, $this->viewVariable('totalcommission'));
    }

    public function testVentesFeature1StatCardsTotalOrdersCount()
    {
        $this->setupSession();
        $this->makeGetRequest('2022-01-01', '2022-01-31', '');
        $this->assertResponseOk();
        $this->assertEquals(3, $this->viewVariable('totalOrders'));
    }

    public function testVentesFeature1StatCardsPendingOrdersCount()
    {
        $this->setupSession();
        $this->makeGetRequest('2022-01-01', '2022-01-31', '');
        $this->assertResponseOk();
        $this->assertEquals(3, $this->viewVariable('pendingOrders'));
    }

    public function testVentesFeature1StatCardsHtmlStructure()
    {
        $this->setupSession();
        $this->makeGetRequest('2022-01-01', '2022-01-31', '');
        $this->assertResponseOk();
        $body = (string)$this->_response->getBody();
        $this->assertContains('Total Revenue', $body);
        $this->assertContains('Total Commission', $body);
        $this->assertContains('Total Orders', $body);
        $this->assertContains('Pending Orders', $body);
        $this->assertContains('card-custom', $body);
        $this->assertContains('stretch-card', $body);
    }

    // ==========================================
    // FEATURE 2: Date Range Filters
    // ==========================================

    public function testVentesFeature2DateRangeFilterStartOnly()
    {
        $this->setupSession();
        // start date only (end is empty)
        $this->makeGetRequest('2022-01-15', '', '');
        $this->assertResponseOk();
    }

    public function testVentesFeature2DateRangeFilterEndOnly()
    {
        $this->setupSession();
        // end date only (start is empty)
        $this->makeGetRequest('', '2022-01-15', '');
        $this->assertResponseOk();
    }

    public function testVentesFeature2DateRangeFilterBoth()
    {
        $this->setupSession();
        $this->makeGetRequest('2022-01-10', '2022-01-20', '');
        $this->assertResponseOk();
        $this->assertEquals(3, $this->viewVariable('totalOrders'));
        $this->assertEquals(250.0, $this->viewVariable('total'));
    }

    public function testVentesFeature2DateRangeFilterNone()
    {
        $this->setupSession();
        // both start and end empty
        $this->makeGetRequest('', '', '');
        $this->assertResponseOk();
    }

    public function testVentesFeature2DateRangeFilterMatchSingleDay()
    {
        $this->setupSession();
        $this->makeGetRequest('2022-01-10', '2022-01-10', '');
        $this->assertResponseOk();
        $this->assertEquals(1, $this->viewVariable('totalOrders'));
        $this->assertEquals(100.0, $this->viewVariable('total'));
    }

    // ==========================================
    // FEATURE 3: Seller (User) Filters
    // ==========================================

    public function testVentesFeature3SellerFilterSpecificSeller()
    {
        $this->setupSession();
        $this->makeGetRequest('2022-01-01', '2022-01-31', '1');
        $this->assertResponseOk();
        $this->assertEquals(2, $this->viewVariable('totalOrders'));
        $this->assertEquals(250.0, $this->viewVariable('total'));
    }

    public function testVentesFeature3SellerFilterNullSeller()
    {
        $this->setupSession();
        $this->makeGetRequest('2022-01-01', '2022-01-31', '');
        $this->assertResponseOk();
        $this->assertEquals(3, $this->viewVariable('totalOrders'));
    }

    public function testVentesFeature3SellerFilterDifferentSeller()
    {
        $this->setupSession();
        $this->makeGetRequest('2022-01-01', '2022-01-31', '2');
        $this->assertResponseOk();
        $this->assertEquals(1, $this->viewVariable('totalOrders'));
        $this->assertEquals(0.0, $this->viewVariable('total'));
    }

    public function testVentesFeature3SellerFilterInvalidSeller()
    {
        $this->setupSession();
        $this->makeGetRequest('2022-01-01', '2022-01-31', '999');
        $this->assertResponseOk();
        $this->assertEquals(0, $this->viewVariable('totalOrders'));
        $this->assertEquals(0.0, $this->viewVariable('total'));
    }

    public function testVentesFeature3SellerFilterSelf()
    {
        // Logged in as User 1
        $this->setupSession(1, 1);
        $this->makeGetRequest('2022-01-01', '2022-01-31', '1');
        $this->assertResponseOk();
        $this->assertEquals(2, $this->viewVariable('totalOrders'));
    }

    // ==========================================
    // FEATURE 4: Chart Data
    // ==========================================

    public function testVentesFeature4ChartDataLineChartExists()
    {
        $this->setupSession();
        $this->makeGetRequest('2022-01-01', '2022-01-31', '');
        $this->assertResponseOk();
        $this->assertNotEmpty($this->viewVariable('dailyTrend'));
        $this->assertIsArray($this->viewVariable('dailyTrend'));
    }

    public function testVentesFeature4ChartDataDoughnutChartExists()
    {
        $this->setupSession();
        $this->makeGetRequest('2022-01-01', '2022-01-31', '');
        $this->assertResponseOk();
        $this->assertNotEmpty($this->viewVariable('statusCounts'));
        $this->assertIsArray($this->viewVariable('statusCounts'));
    }

    public function testVentesFeature4ChartDataLineChartFormat()
    {
        $this->setupSession();
        $this->makeGetRequest('2022-01-01', '2022-01-31', '');
        $this->assertResponseOk();
        $trend = $this->viewVariable('dailyTrend');
        foreach ($trend as $point) {
            $this->assertArrayHasKey('date', $point);
            $this->assertArrayHasKey('orders_count', $point);
            $this->assertArrayHasKey('revenue', $point);
        }
    }

    public function testVentesFeature4ChartDataDoughnutChartFormat()
    {
        $this->setupSession();
        $this->makeGetRequest('2022-01-01', '2022-01-31', '');
        $this->assertResponseOk();
        $statusCounts = $this->viewVariable('statusCounts');
        $this->assertArrayHasKey('En attente', $statusCounts);
        $this->assertArrayHasKey('En cours', $statusCounts);
        $this->assertArrayHasKey('Livrée', $statusCounts);
        $this->assertArrayHasKey('Annulée', $statusCounts);
    }

    public function testVentesFeature4ChartDataEmptyLineChart()
    {
        $this->setupSession();
        $this->makeGetRequest('2022-03-01', '2022-03-05', '');
        $this->assertResponseOk();
        $dailyTrend = $this->viewVariable('dailyTrend');
        $this->assertCount(5, $dailyTrend);
        foreach ($dailyTrend as $point) {
            $this->assertEquals(0, $point['orders_count']);
            $this->assertEquals(0.0, $point['revenue']);
        }
    }

    // ==========================================
    // FEATURE 5: Layout
    // ==========================================

    public function testVentesFeature5LayoutCssIncluded()
    {
        $this->setupSession();
        $this->makeGetRequest('2022-01-01', '2022-01-31', '');
        $this->assertResponseOk();
        $body = (string)$this->_response->getBody();
        $this->assertContains('card-custom', $body);
        $this->assertContains('stretch-card', $body);
    }

    public function testVentesFeature5LayoutCanvasElementsPresent()
    {
        $this->setupSession();
        $this->makeGetRequest('2022-01-01', '2022-01-31', '');
        $this->assertResponseOk();
        $body = (string)$this->_response->getBody();
        $this->assertContains('id="dailyTrendsChart"', $body);
        $this->assertContains('id="statusDistributionChart"', $body);
    }

    public function testVentesFeature5LayoutGridStructure()
    {
        $this->setupSession();
        $this->makeGetRequest('2022-01-01', '2022-01-31', '');
        $this->assertResponseOk();
        $body = (string)$this->_response->getBody();
        $this->assertContains('row', $body);
        $this->assertContains('col-xl-3', $body);
        $this->assertContains('col-lg-8', $body);
        $this->assertContains('col-lg-4', $body);
    }

    public function testVentesFeature5LayoutStatCardsRendered()
    {
        $this->setupSession();
        $this->makeGetRequest('2022-01-01', '2022-01-31', '');
        $this->assertResponseOk();
        $body = (string)$this->_response->getBody();
        $this->assertContains('Total Revenue', $body);
        $this->assertContains('Total Commission', $body);
        $this->assertContains('Total Orders', $body);
        $this->assertContains('Pending Orders', $body);
    }

    public function testVentesFeature5LayoutScriptTagsChartJs()
    {
        $this->setupSession();
        $this->makeGetRequest('2022-01-01', '2022-01-31', '');
        $this->assertResponseOk();
        $body = (string)$this->_response->getBody();
        $this->assertContains('https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js', $body);
    }
}
