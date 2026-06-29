<?php
namespace App\View\Cell;

use Cake\View\Cell;

/**
 * Executive Dashboard Cell
 * 
 * Handles business intelligence metrics and data retrieval
 * for the executive dashboard display
 */
class ExecutiveDashboardCell extends Cell
{
    /**
     * Initialize method
     */
    public function initialize(): void
    {
        parent::initialize();
    }

    /**
     * Display method - renders the executive dashboard with real metrics
     * 
     * @param mixed $companyId The company ID to filter by
     * @param string $startDate Start date for filtering (format: YYYY-MM-DD)
     * @param string $endDate End date for filtering (format: YYYY-MM-DD)
     */
    public function display($companyId = null, $startDate = null, $endDate = null)
    {
        // Get models using TableLocator
        $ordersTable = $this->getTableLocator()->get('Orders');
        $orderpacksTable = $this->getTableLocator()->get('Orderpacks');
        $usersTable = $this->getTableLocator()->get('Users');

        // Use provided company ID or fallback to default
        if (!$companyId) {
            $companyId = 1;
        }
        
        // Get date range (use provided dates or default to current month)
        if (!$startDate) {
            $startDate = date('Y-m-01');
        }
        if (!$endDate) {
            $endDate = date('Y-m-t');
        }

        // Convert dates to timestamps for proper comparison
        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';

        // 1. Total Orders
        $totalOrders = $ordersTable->find()
            ->where([
                'Orders.company_id' => $companyId,
                'Orders.created >=' => $startDateTime,
                'Orders.created <=' => $endDateTime
            ])
            ->count();

        // 2. Total Revenue (aggregate from orderpacks)
        $totalRevenueResult = $orderpacksTable->find()
            ->select(['total' => 'SUM(Orderpacks.quantity * Orderpacks.price)'])
            ->innerJoinWith('Orders')
            ->where([
                'Orders.company_id' => $companyId,
                'Orders.created >=' => $startDateTime,
                'Orders.created <=' => $endDateTime
            ])
            ->first();

        $revenue = $totalRevenueResult && $totalRevenueResult->total ? round($totalRevenueResult->total, 2) : 0;

        // 3. Average Order Value
        $avgOrder = $totalOrders > 0 ? round($revenue / $totalOrders, 2) : 0;

        // 4. Pending Orders - Set to 0 if status tracking not available
        $pendingOrders = 0;

        // 4.b Net Loyalty Points
        $loyaltyPointsIn = $orderpacksTable->find()
            ->select(['total' => 'SUM(Orderpacks.quantity * Orderpacks.loyaltypoints)'])
            ->innerJoinWith('Orders')
            ->innerJoinWith('Orders.Customers')
            ->where([
                'Orders.company_id' => $companyId,
                'Orders.created >=' => $startDateTime,
                'Orders.created <=' => $endDateTime,
                'Orders.ordertype_id' => 1,
                'Orders.statut' => 6,
                'Orderpacks.statut' => 6,
                'Orderpacks.loyaltypointgift_id IS' => null,
                'Customers.statut' => 1
            ])
            ->first();

        $loyaltyPointsOut = $orderpacksTable->find()
            ->select(['total' => 'SUM(Orderpacks.quantity * Orderpacks.loyaltypoints)'])
            ->innerJoinWith('Orders')
            ->innerJoinWith('Orders.Customers')
            ->where([
                'Orders.company_id' => $companyId,
                'Orders.created >=' => $startDateTime,
                'Orders.created <=' => $endDateTime,
                'Orders.ordertype_id' => 2,
                'Orders.statut' => 6,
                'Orderpacks.loyaltypointgift_id IS' => null,
                'Customers.statut' => 1
            ])
            ->first();

        $inTotal = $loyaltyPointsIn && $loyaltyPointsIn->total ? (float)$loyaltyPointsIn->total : 0;
        $outTotal = $loyaltyPointsOut && $loyaltyPointsOut->total ? (float)$loyaltyPointsOut->total : 0;
        $netLoyaltyPoints = $inTotal - $outTotal;

        // 4.c Calculated Loyalty Points (Non Reclamés)
        $calcLoyaltyIn = $orderpacksTable->find()
            ->select(['total' => 'SUM(Orderpacks.quantity * Orderpacks.loyaltypoints)'])
            ->innerJoinWith('Orders')
            ->innerJoinWith('Orders.Customers')
            ->where([
                'Orders.company_id' => $companyId,
                'Orders.ordertype_id' => 1,
                'Orders.statut' => 6,
                'Orderpacks.statut' => 6,
                'Orderpacks.loyaltypointgift_id IS' => null,
                'Customers.statut' => 1
            ])
            ->first();

        $calcLoyaltyOut = $orderpacksTable->find()
            ->select(['total' => 'SUM(Orderpacks.quantity * Orderpacks.loyaltypoints)'])
            ->innerJoinWith('Orders')
            ->innerJoinWith('Orders.Customers')
            ->where([
                'Orders.company_id' => $companyId,
                'Orders.ordertype_id' => 2,
                'Orders.statut' => 6,
                'Orderpacks.loyaltypointgift_id IS' => null,
                'Customers.statut' => 1
            ])
            ->first();

        $calcLoyaltyInTotal = $calcLoyaltyIn && $calcLoyaltyIn->total ? (float)$calcLoyaltyIn->total : 0;
        $calcLoyaltyOutTotal = $calcLoyaltyOut && $calcLoyaltyOut->total ? (float)$calcLoyaltyOut->total : 0;
        $totalLoyaltyPointsCalculated = $calcLoyaltyInTotal - $calcLoyaltyOutTotal;

        // 5. Best Sellers
        $bestSellers = $ordersTable->find()
            ->select([
                'user_id' => 'Orders.user_id',
                'firstname' => 'Users.firstname',
                'lastname' => 'Users.lastname',
                'total_sales' => 'SUM(Orderpacks.quantity * Orderpacks.price)'
            ])
            ->innerJoinWith('Users')
            ->innerJoinWith('Orderpacks')
            ->where([
                'Orders.company_id' => $companyId,
                'Orders.created >=' => $startDateTime,
                'Orders.created <=' => $endDateTime
            ])
            ->group(['Orders.user_id', 'Users.firstname', 'Users.lastname'])
            ->order(['total_sales' => 'DESC'])
            ->limit(5)
            ->toArray();

        // 6. Best Products
        $bestProducts = $orderpacksTable->find()
            ->contain(['Packs'])
            ->select([
                'pack_id' => 'Orderpacks.pack_id',
                'pack_title' => 'Packs.title',
                'total_quantity' => 'SUM(Orderpacks.quantity)',
                'total_revenue' => 'SUM(Orderpacks.quantity * Orderpacks.price)'
            ])
            ->innerJoinWith('Orders')
            ->where([
                'Orders.company_id' => $companyId,
                'Orders.created >=' => $startDateTime,
                'Orders.created <=' => $endDateTime
            ])
            ->group(['Orderpacks.pack_id', 'Packs.title'])
            ->order(['total_quantity' => 'DESC'])
            ->limit(5)
            ->toArray();

        // 7. Order Status Distribution (empty if not applicable)
        $orderStatus = [];

        // 8. Top Customers
        $topCustomers = $ordersTable->find()
            ->select([
                'customer_id' => 'Orders.customer_id',
                'customer_name' => 'Customers.name',
                'client_code' => 'Orders.code',
                'order_count' => 'COUNT(*)',
                'total_amount' => 'SUM(Orderpacks.quantity * Orderpacks.price)'
            ])
            ->innerJoinWith('Orderpacks')
            ->leftJoinWith('Customers')
            ->where([
                'Orders.company_id' => $companyId,
                'Orders.created >=' => $startDateTime,
                'Orders.created <=' => $endDateTime
            ])
            ->group(['Orders.customer_id', 'Orders.code', 'Customers.name'])
            ->order(['total_amount' => 'DESC'])
            ->limit(10)
            ->toArray();

        // 9. Unique Customers
        $uniqueCustomers = $ordersTable->find()
            ->select('code')
            ->distinct('code')
            ->where([
                'Orders.company_id' => $companyId,
                'Orders.created >=' => $startDateTime,
                'Orders.created <=' => $endDateTime
            ])
            ->count();

        // 10. Conversion Rate (default if status column doesn't exist)
        $completedOrders = 0;
        $conversionRate = 0;

        // Pass data to view
        $this->set(compact(
            'totalOrders',
            'revenue',
            'avgOrder',
            'pendingOrders',
            'netLoyaltyPoints',
            'totalLoyaltyPointsCalculated',
            'bestSellers',
            'bestProducts',
            'orderStatus',
            'topCustomers',
            'uniqueCustomers',
            'conversionRate',
            'startDate',
            'endDate'
        ));
    }
}
