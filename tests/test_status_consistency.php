<?php
/**
 * Test script to check database status consistency between Orders and Orderpacks
 */
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/bootstrap.php';

use Cake\ORM\TableRegistry;

try {
    echo "--- Checking Status Consistency in DB ---\n";

    $ordersTable = TableRegistry::getTableLocator()->get('Orders');

    // Check if there are orderpacks with status=6 under orders that are NOT status=6 (delivered)
    $nonDeliveredOrders = $ordersTable->find('all')
        ->contain(['Orderpacks'])
        ->where(['Orders.statut !=' => 6])
        ->toArray();

    $mismatches = 0;
    foreach ($nonDeliveredOrders as $o) {
        foreach ($o->orderpacks as $op) {
            if ($op->statut == 6) {
                echo "Mismatch found: Order ID {$o->id} has status {$o->statut}, but Orderpack ID {$op->id} has status 6 (delivered)!\n";
                $mismatches++;
            }
        }
    }
    echo "Total orderpacks with status=6 under non-delivered orders: {$mismatches}\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
