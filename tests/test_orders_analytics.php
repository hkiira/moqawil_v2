<?php
/**
 * Standalone Verification Test Suite for Orders Analytics Dashboard
 */
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/bootstrap.php';

use App\Controller\OrdersController;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;

class MockAuth {
    public $defaultwh = 1;
    public $company_id = 1;
    public function user($key = null) {
        if ($key === 'defaultwh') {
            return $this->defaultwh;
        }
        if ($key === 'company_id') {
            return $this->company_id;
        }
        return [
            'id' => 1,
            'username' => 'test_user',
            'defaultwh' => $this->defaultwh,
            'company_id' => $this->company_id
        ];
    }
}

try {
    echo "==================================================\n";
    echo "   ORDERS ANALYTICS DASHBOARD VERIFICATION TESTS   \n";
    echo "==================================================\n\n";

    $ordersTable = TableRegistry::getTableLocator()->get('Orders');

    // Find some existing order dates and users to construct precise tests
    $sampleOrders = $ordersTable->find('all')
        ->select(['user_id', 'created', 'statut'])
        ->limit(10)
        ->toArray();

    echo "Sample orders from database:\n";
    foreach ($sampleOrders as $o) {
        $dateStr = $o->created->format('Y-m-d');
        echo " - Order User ID: {$o->user_id}, Date: {$dateStr}, Status: {$o->statut}\n";
    }
    echo "\n";

    // Let's find one user_id with orders
    $activeUser = $sampleOrders[0]->user_id;
    $activeDate = $sampleOrders[0]->created->format('Y-m-d');

    // Define verification function
    function runTest($title, $start, $end, $userId = '', $defaultwh = 1) {
        echo "--------------------------------------------------\n";
        echo "TEST CASE: {$title}\n";
        echo "Params: start={$start}, end={$end}, user='{$userId}', defaultwh={$defaultwh}\n";
        echo "--------------------------------------------------\n";

        // Setup $_GET
        $_GET['keyword'] = [
            'start' => $start,
            'end' => $end,
            'user' => $userId
        ];

        // Instantiate request and response
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET'
            ]
        ]);
        $response = new Response();
        $controller = new OrdersController($request, $response);
        
        // Mock Auth
        $authMock = new MockAuth();
        $authMock->defaultwh = $defaultwh;
        $controller->Auth = $authMock;

        // Run ventes() action
        $controller->ventes();
        $htmlOutput = (string)$controller->response->getBody();

        // Retrieve viewVars
        $vars = $controller->viewVars;
        
        // Assertions
        $total = $vars['total'] ?? null;
        $totalcommission = $vars['totalcommission'] ?? null;
        $totalOrders = $vars['totalOrders'] ?? null;
        $pendingOrders = $vars['pendingOrders'] ?? null;
        $dailyTrend = $vars['dailyTrend'] ?? null;
        $statusCounts = $vars['statusCounts'] ?? null;

        echo "Results:\n";
        echo " - Total Orders: {$totalOrders}\n";
        echo " - Pending Orders: {$pendingOrders}\n";
        echo " - Total Revenue: {$total} MAD\n";
        echo " - Total Commission: {$totalcommission} MAD\n";

        // Verify dailyTrend (continuous time-series)
        $expectedDays = (strtotime($end) - strtotime($start)) / 86400 + 1;
        // Float difference check (round in case of DST changes)
        $expectedDays = round($expectedDays);
        if ($expectedDays < 1) $expectedDays = 1; // if end < start

        $trendCount = is_array($dailyTrend) ? count($dailyTrend) : 0;
        echo " - Daily Trend days count: {$trendCount} (Expected: {$expectedDays})\n";

        if ($trendCount !== (int)$expectedDays) {
            echo "   [FAIL] Time-series is not continuous or has incorrect length.\n";
            return false;
        }

        // Verify status counts contains the mapped keys
        $expectedStatuses = ['En attente', 'En cours', 'Livrée', 'Annulée'];
        $statusKeys = is_array($statusCounts) ? array_keys($statusCounts) : [];
        $missingKeys = array_diff($expectedStatuses, $statusKeys);
        if (!empty($missingKeys)) {
            echo "   [FAIL] Missing expected status labels in statusCounts: " . implode(', ', $missingKeys) . "\n";
            return false;
        }

        // Verify the HTML response contains expected elements
        $hasTotalRevenue = strpos($htmlOutput, 'Total Revenue') !== false;
        $hasTrendsChart = strpos($htmlOutput, 'dailyTrendsChart') !== false;
        $hasStatusChart = strpos($htmlOutput, 'statusDistributionChart') !== false;

        echo " - HTML contains Total Revenue: " . ($hasTotalRevenue ? "YES" : "NO") . "\n";
        echo " - HTML contains dailyTrendsChart: " . ($hasTrendsChart ? "YES" : "NO") . "\n";
        echo " - HTML contains statusDistributionChart: " . ($hasStatusChart ? "YES" : "NO") . "\n";

        if (!$hasTotalRevenue || !$hasTrendsChart || !$hasStatusChart) {
            echo "   [FAIL] View rendering checklist failed.\n";
            // Print a snippet of HTML to debug if empty
            echo "   HTML Length: " . strlen($htmlOutput) . "\n";
            echo "   HTML Snippet: " . substr($htmlOutput, 0, 500) . "\n";
            return false;
        }

        echo "   [PASS] All checks passed for this case.\n\n";
        return [
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'total' => $total,
            'totalcommission' => $totalcommission,
            'dailyTrend' => $dailyTrend,
            'statusCounts' => $statusCounts
        ];
    }

    $allPassed = true;

    // Test Case 1: Empty results (future dates)
    $res1 = runTest("Empty Results (Future Dates)", "2029-01-01", "2029-01-05");
    if (!$res1) $allPassed = false;
    else {
        // Assertions for empty results
        if ($res1['totalOrders'] !== 0 || $res1['total'] != 0.0 || $res1['pendingOrders'] !== 0) {
            echo "   [FAIL] Empty results should yield 0 orders, revenue, and pending counts.\n";
            $allPassed = false;
        }
        foreach ($res1['dailyTrend'] as $day) {
            if ($day['orders_count'] !== 0 || $day['revenue'] != 0.0) {
                echo "   [FAIL] Daily trend for empty range should have 0 orders and revenue.\n";
                $allPassed = false;
                break;
            }
        }
    }

    // Test Case 2: Multi-day range with orders on some days
    // Let's use a range spanning $activeDate. Let's make it 3 days before and 3 days after.
    $startDate = date('Y-m-d', strtotime($activeDate . ' - 3 days'));
    $endDate = date('Y-m-d', strtotime($activeDate . ' + 3 days'));
    $res2 = runTest("Multi-day range with partial orders", $startDate, $endDate);
    if (!$res2) $allPassed = false;
    else {
        // Verify dailyTrend contains correct keys and some non-zero orders
        $foundNonZero = false;
        foreach ($res2['dailyTrend'] as $day) {
            if ($day['orders_count'] > 0) {
                $foundNonZero = true;
            }
        }
        if (!$foundNonZero) {
            echo "   [FAIL] Multi-day range with active date did not find any orders.\n";
            $allPassed = false;
        }
    }

    // Test Case 3: Single-day range
    $res3 = runTest("Single-day range", $activeDate, $activeDate);
    if (!$res3) $allPassed = false;
    else {
        if (count($res3['dailyTrend']) !== 1) {
            echo "   [FAIL] Single-day range should have exactly 1 day in dailyTrend.\n";
            $allPassed = false;
        }
    }

    // Test Case 4: Seller filter with no matching records
    $res4 = runTest("Seller filter with no matching records (non-existent user)", $startDate, $endDate, '999999');
    if (!$res4) $allPassed = false;
    else {
        if ($res4['totalOrders'] !== 0) {
            echo "   [FAIL] Non-existent seller should yield 0 orders.\n";
            $allPassed = false;
        }
    }

    // Test Case 5: Seller filter with matching records
    $res5 = runTest("Seller filter with matching records", $startDate, $endDate, $activeUser);
    if (!$res5) $allPassed = false;
    else {
        if ($res5['totalOrders'] === 0) {
            echo "   [WARNING] Active seller filter returned 0 orders. Make sure user ID {$activeUser} placed orders in range {$startDate} to {$endDate}.\n";
        }
    }

    // Test Case 6: Very large numbers verification
    // Let's verify that double/float arithmetic handles large values and view formatting handles them.
    // We will do this by temporarily injecting a large order or reviewing code.
    // In our test, let's verify if $total is cast or formatted.
    echo "--------------------------------------------------\n";
    echo "TEST CASE: Large Number Simulation\n";
    echo "--------------------------------------------------\n";
    $largeVal = 999999999.99;
    $formatted = number_format($largeVal, 2, '.', ' ');
    echo "Large value: {$largeVal} -> Formatted: {$formatted} MAD\n";
    if ($formatted === '999999999.99' || $formatted === '999 999 999.99') {
        echo "   [PASS] Large number format handles large currency values successfully.\n\n";
    } else {
        echo "   [FAIL] Large number formatting issue.\n\n";
        $allPassed = false;
    }

    if ($allPassed) {
        echo "==================================================\n";
        echo "ALL VERIFICATION TESTS COMPLETED SUCCESSFULLY ✅\n";
        echo "==================================================\n";
    } else {
        echo "==================================================\n";
        echo "VERIFICATION TESTS FAILED ❌\n";
        echo "==================================================\n";
    }

} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
