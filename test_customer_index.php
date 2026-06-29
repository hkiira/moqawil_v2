<?php
require 'config/requirements.php';
require 'vendor/autoload.php';
require 'config/bootstrap.php';

use Cake\ORM\TableRegistry;

$customersTable = TableRegistry::getTableLocator()->get('Customers');
$ordersTable = TableRegistry::getTableLocator()->get('Orders');

try {
    echo "--- TESTING INDEX QUERY ---\n";
    $empQuery = $customersTable->find();
    $empQuery
        ->contain(['Zones.Cities', 'Zones.Parentzones', 'Customertypes'])
        ->leftJoinWith('Orders.Orderpacks.Loyaltyorderpacks')
        ->where([
            'Customers.company_id' => 1,
            'Loyaltyorderpacks.loyaltypoint_id IS' => null
        ])
        ->select([
            'Customers.id',
            'Customers.code',
            'Customers.name',
            'loyaltypoints_sum' => $empQuery->newExpr(
                'SUM(CASE ' .
                'WHEN Orders.ordertype_id = 1 AND Orders.statut = 6 AND Orderpacks.statut = 6 THEN Orderpacks.quantity * Orderpacks.loyaltypoints ' .
                'WHEN Orders.ordertype_id = 2 AND Orders.statut = 6 THEN -(Orderpacks.quantity * Orderpacks.loyaltypoints) ' .
                'ELSE 0 END)'
            )
        ])
        ->group(['Customers.id'])
        ->limit(5);

    foreach ($empQuery as $customer) {
        echo "Customer ID: " . $customer->id . " | Name: " . $customer->name . " | Sum: " . $customer->loyaltypoints_sum . "\n";
    }

    echo "--- TESTING VIEW QUERY ---\n";
    $querySum = $ordersTable->find()
        ->leftJoinWith('Orderpacks.Loyaltyorderpacks')
        ->where([
            'Orders.customer_id' => 41,
            'Loyaltyorderpacks.loyaltypoint_id IS' => null
        ])
        ->select([
            'loyaltypoints_sum' => $ordersTable->find()->newExpr(
                'SUM(CASE ' .
                'WHEN Orders.ordertype_id = 1 AND Orders.statut = 6 AND Orderpacks.statut = 6 THEN Orderpacks.quantity * Orderpacks.loyaltypoints ' .
                'WHEN Orders.ordertype_id = 2 AND Orders.statut = 6 THEN -(Orderpacks.quantity * Orderpacks.loyaltypoints) ' .
                'ELSE 0 END)'
            )
        ])
        ->first();

    echo "Customer 41 Sum: " . ($querySum ? $querySum->loyaltypoints_sum : 0) . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

unlink(__FILE__);
