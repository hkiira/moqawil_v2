<?php
/**
 * Standalone API Integration Verification script for wallet checkout.
 */

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// 1. Boot CakePHP using config/bootstrap.php
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/bootstrap.php';

use App\Controller\B2bCustomerApiController;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Firebase\JWT\JWT;

try {
    echo "--- Starting Wallet Checkout Integration Test ---\n";

    // 2. Setup a test customer (reset wallet_balance to 0.00)
    $customersTable = TableRegistry::getTableLocator()->get('Customers');
    $customer = $customersTable->find()->first();
    if (!$customer) {
        throw new Exception("No customer found in the database.");
    }

    $customerId = $customer->id;
    $customer->wallet_balance = 0.00;
    if (!$customersTable->save($customer)) {
        throw new Exception("Failed to reset customer wallet balance.");
    }
    echo "Successfully reset customer (ID: {$customerId}) wallet balance to 0.00.\n";

    // 3. Setup a test pack (set bonus_amount = 3.00, bonus_unit_threshold = 10.00, measurement_quantity = 10.00)
    $packsTable = TableRegistry::getTableLocator()->get('Packs');
    $pack = $packsTable->find()->first();
    if (!$pack) {
        throw new Exception("No pack found in the database.");
    }

    $packId = $pack->id;
    $pack->bonus_amount = 3.00;
    $pack->bonus_unit_threshold = 10.00;
    $pack->measurement_quantity = 10.00;
    if (!$packsTable->save($pack)) {
        throw new Exception("Failed to update test pack.");
    }
    echo "Successfully updated pack (ID: {$packId}) with bonus_amount = 3.00, bonus_unit_threshold = 10.00, measurement_quantity = 10.00.\n";

    // 4. Generate a valid B2B JWT token using the secret 'super_secret_b2b_app_key_2026_very_long_secure_string'
    $secret = 'super_secret_b2b_app_key_2026_very_long_secure_string';
    $payload = [
        'iss' => 'moqa_backend',
        'sub' => $customerId,
        'iat' => time(),
        'exp' => time() + 3600,
        'role' => 'customer',
    ];
    $token = JWT::encode($payload, $secret, 'HS256');
    echo "Generated B2B JWT token.\n";

    // 5. Build a Cake\Http\ServerRequest with the JWT token header and parsing cartItems payload containing 1 quantity of the test pack
    $request = new ServerRequest([
        'post' => [
            'cartItems' => [
                [
                    'pack_id' => $packId,
                    'quantity' => 1,
                ],
            ],
        ],
        'environment' => [
            'REQUEST_METHOD' => 'POST',
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ],
    ]);
    echo "Built Cake\\Http\\ServerRequest with Authorization header and cartItems.\n";

    // 6. Instantiate App\Controller\B2bCustomerApiController with the request, run the addOrder() action
    $response = new Response();
    $controller = new B2bCustomerApiController($request, $response);

    echo "Instantiated B2bCustomerApiController. Executing addOrder()...\n";

    // Suppress header output warnings during CLI controller execution
    ob_start();
    $controller->addOrder();
    $actionOutput = ob_get_clean();

    // Verify if there were errors in the controller's view variables (which contain the response)
    $viewVars = $controller->viewVars;
    if (isset($viewVars['response'])) {
        $apiResponse = $viewVars['response'];
        echo "API Response: " . json_encode($apiResponse, JSON_PRETTY_PRINT) . "\n";
        if (isset($apiResponse['status']) && $apiResponse['status'] !== 200) {
            throw new Exception("API checkout action failed: " . ($apiResponse['msg'] ?? 'Unknown error'));
        }
    } else {
        echo "No explicit response variable found in controller viewVars. Raw Action Output: {$actionOutput}\n";
    }

    // 7. Fetch the customer again and assert that their wallet_balance is now exactly 3.00
    $updatedCustomer = $customersTable->get($customerId);
    $finalBalance = (float)$updatedCustomer->wallet_balance;
    echo "Fetched updated customer. Current wallet balance: {$finalBalance}\n";

    if (abs($finalBalance - 3.00) < 0.0001) {
        echo "SUCCESS: Customer wallet balance is exactly 3.00.\n";
        exit(0);
    } else {
        echo "FAILURE: Expected wallet balance to be 3.00, but got {$finalBalance}.\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
