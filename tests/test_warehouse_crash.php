<?php
/**
 * Test case for empty warehouse points of sale (proving the crash bug)
 */
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/bootstrap.php';

use App\Controller\OrdersController;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;

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
    echo "--- Testing warehouse with NO points of sale ---\n";

    $warehousesTable = TableRegistry::getTableLocator()->get('Warehouses');
    
    // Create a new temporary warehouse that has NO points of sale
    $newWh = $warehousesTable->newEntity();
    $newWh->name = 'Temp Test Warehouse';
    $newWh->statut = 1;
    if (!$warehousesTable->save($newWh)) {
        throw new Exception("Failed to save temporary warehouse.");
    }
    $tempWhId = $newWh->id;
    echo "Created temporary warehouse with ID = {$tempWhId}\n";

    // Setup $_GET
    $_GET['keyword'] = [
        'start' => '2025-07-30',
        'end' => '2025-08-05',
        'user' => ''
    ];

    $request = new ServerRequest();
    $response = new Response();
    $controller = new OrdersController($request, $response);
    
    // Mock Auth with our temporary warehouse ID
    $authMock = new MockAuth();
    $authMock->defaultwh = $tempWhId;
    $controller->Auth = $authMock;

    echo "Calling ventes() controller method...\n";
    $controller->ventes();
    echo "SUCCESS: ventes() executed without crashing!\n";

} catch (Throwable $e) {
    echo "CRASH DETECTED ❌\n";
    echo "Error Type: " . get_class($e) . "\n";
    echo "Error Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
} finally {
    // Cleanup temporary warehouse
    if (isset($tempWhId)) {
        $warehousesTable->deleteAll(['id' => $tempWhId]);
        echo "Cleaned up temporary warehouse.\n";
    }
}
