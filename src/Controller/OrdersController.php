<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Service\OrderPricingService;
use Cake\Routing\Router;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Cake\I18n\Time;

/**
 * Orders Controller
 *
 * @property \App\Model\Table\OrdersTable $Orders
 *
 * @method \App\Model\Entity\Order[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])

 1: attente de confirmation
 5: En cours de livraison
 6: Livrée
 8: Annulée

 */
class OrdersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */

    public function defaultwh($warehouseid = null)
    {
        $this->request->getSession()->write('Auth.User.defaultwh', intVal($warehouseid));
        $this->loadModel('Warehouses');
        $warehouse = $this->Warehouses->get($warehouseid);
        $this->request->getSession()->write('Auth.User.defaultwhtype', $warehouse->whtype_id);
        $this->autoRender = false;
    }

    public function index($id = 1)
    {
        $whusers = $this->Orders->Users->Whusers->find('all')->contain([
            'Users' => function ($q) {
                return $q->where(['Users.statut' => 1, ['OR' => [['Users.role_id' => 5], ['Users.role_id' => 3]]]]);
            }
        ])->where(['Whusers.warehouse_id' => $this->Auth->user('defaultwh')]);
        $users = [];
        foreach ($whusers as $whuser) {
            if ($whuser->user) {
                $users[$whuser->user->id] = $whuser->user->firstname . ' ' . $whuser->user->lastname;
            }
        }

        $this->set(compact('id', 'users'));
    }

    /**
     * Full-page Order Analytics dashboard
     */
    public function orderAnalytics()
    {
        $this->viewBuilder()->setLayout('default');
        $whusers = $this->Orders->Users->Whusers->find('all')->contain([
            'Users' => function ($q) {
                return $q->where(['Users.statut' => 1, ['OR' => [['Users.role_id' => 5], ['Users.role_id' => 3]]]]);
            }
        ])->where(['Whusers.warehouse_id' => $this->Auth->user('defaultwh')]);
        $users = [];
        foreach ($whusers as $whuser) {
            if ($whuser->user) {
                $users[$whuser->user->id] = $whuser->user->firstname . ' ' . $whuser->user->lastname;
            }
        }

        $this->loadModel('Packs');
        $products = $this->Packs->find('list')->where(['Packs.statut IN' => [1, 3]])->order(['Packs.title' => 'ASC'])->toArray();

        $this->set(compact('users', 'products'));
    }

    public function ventes()
    {
        $vrb = $this->request->getQuery('keyword');
        if (empty($vrb) || !is_array($vrb)) {
            $vrb = [];
        }
        $vrb['start'] = !empty($vrb['start']) ? $vrb['start'] : date('Y-m-01');
        $vrb['end'] = !empty($vrb['end']) ? $vrb['end'] : date('Y-m-d');
        $vrb['user'] = !empty($vrb['user']) ? $vrb['user'] : null;
        $vrb['product'] = !empty($vrb['product']) ? $vrb['product'] : null;

        $datetime1 = new Time($vrb['start']);
        $datetime2 = new Time($vrb['end']);
        if ($vrb['user'] === null || $vrb['user'] === '') {
            $orders = $this->Orders->find('all')->contain(['Users', 'Orderpacks.Turnovers', 'Orderpacks.Packs'])->where(['DATE(Orders.created) <= ' => $vrb['end'], 'DATE(Orders.created) >= ' => $vrb['start']]);
        } else {
            $orders = $this->Orders->find('all')->contain(['Users', 'Orderpacks.Turnovers', 'Orderpacks.Packs'])->where(['Orders.user_id' => $vrb['user'], 'DATE(Orders.created) <= ' => $vrb['end'], 'DATE(Orders.created) >= ' => $vrb['start']]);
        }
        $warehouse = $this->Orders->Pofsales->Warehouses->get($this->Auth->user('defaultwh'), [
            'contain' => [
                'Subwarehouses.Pofsales',
                'Subwarehouses' => function ($q) {
                    return $q->where(['Subwarehouses.whtype_id' => 3]);
                }
            ]
        ]);
        $qwh = [];
        if ($warehouse->subwarehouses) {
            foreach ($warehouse->subwarehouses as $subwarehouse) {
                foreach ($subwarehouse->pofsales as $pofsale) {
                    $qwh['OR'][$pofsale->id] = ['Orders.pofsale_id' => $pofsale->id];
                }
            }
        }

        $pofsales = $this->Orders->Pofsales->find('all')->where(['warehouse_id' => $this->Auth->user('defaultwh')]);
        foreach ($pofsales as $pos) {
            $qwh['OR'][$pos->id] = ['Orders.pofsale_id' => $pos->id];
        }

        $orders->where([$qwh]);

        $ordersArray = $orders->toArray();
        $total = 0;
        $totalcommission = 0;
        $totalOrders = 0;
        $pendingOrders = 0;
        $productSales = [];
        $userProductSales = [];

        // Generate full date range to prevent gaps in trend chart
        $startDate = new \DateTime($vrb['start']);
        $endDate = new \DateTime($vrb['end']);
        $endDate->modify('+1 day');
        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($startDate, $interval, $endDate);

        $dailyTrend = [];
        foreach ($dateRange as $date) {
            $formattedDate = $date->format('Y-m-d');
            $dailyTrend[$formattedDate] = [
                'date' => $formattedDate,
                'orders_count' => 0,
                'revenue' => 0.0
            ];
        }

        $statusMapping = [
            1 => 'En attente',
            5 => 'En cours',
            6 => 'Livrée',
            8 => 'Annulée'
        ];

        $statusCounts = [];
        foreach ($statusMapping as $label) {
            $statusCounts[$label] = 0;
        }

        foreach ($ordersArray as $order) {
            // If filtering by product, check if this order has the selected product
            if (!empty($vrb['product'])) {
                $hasProduct = false;
                foreach ($order->orderpacks as $op) {
                    if ($op->pack_id == $vrb['product']) {
                        $hasProduct = true;
                        break;
                    }
                }
                if (!$hasProduct) {
                    continue; // Skip this order completely
                }
            }

            $totalOrders++;

            // Count pending orders
            if ($order->statut == 1) {
                $pendingOrders++;
            }

            // Map and count order status
            $statutVal = $order->statut;
            $statusLabel = isset($statusMapping[$statutVal]) ? $statusMapping[$statutVal] : 'Autre (' . $statutVal . ')';
            if (!isset($statusCounts[$statusLabel])) {
                $statusCounts[$statusLabel] = 0;
            }
            $statusCounts[$statusLabel]++;

            // Get order date and update daily trend
            if ($order->created instanceof \DateTimeInterface) {
                $orderDate = $order->created->format('Y-m-d');
            } else {
                $orderDate = date('Y-m-d', strtotime($order->created));
            }

            $orderRevenue = 0.0;
            foreach ($order->orderpacks as $orderpack) {
                if ($orderpack->statut == 6) {
                    if (!empty($vrb['product']) && $orderpack->pack_id != $vrb['product']) {
                        continue;
                    }
                    $itemRevenue = $orderpack->quantity * $orderpack->price;
                    $total += $itemRevenue;
                    $totalcommission += ($orderpack->turnover_id && $orderpack->turnover) ? ($itemRevenue * $orderpack->turnover->commission / 100) : 0;
                    $orderRevenue += $itemRevenue;

                    // Aggregate product sales by pack
                    $packTitle = ($orderpack->pack) ? $orderpack->pack->title : 'Produit #' . $orderpack->pack_id;
                    if (!isset($productSales[$packTitle])) {
                        $productSales[$packTitle] = ['title' => $packTitle, 'quantity' => 0, 'revenue' => 0.0];
                    }
                    $productSales[$packTitle]['quantity'] += $orderpack->quantity;
                    $productSales[$packTitle]['revenue'] += $itemRevenue;

                    // Aggregate sales by user & product
                    $sellerName = ($order->user) ? ($order->user->firstname . ' ' . $order->user->lastname) : 'Inconnu';
                    $upsKey = $order->user_id . '_' . $orderpack->pack_id;
                    if (!isset($userProductSales[$upsKey])) {
                        $userProductSales[$upsKey] = [
                            'user' => $sellerName,
                            'product' => $packTitle,
                            'quantity' => 0,
                            'total' => 0.0
                        ];
                    }
                    $userProductSales[$upsKey]['quantity'] += $orderpack->quantity;
                    $userProductSales[$upsKey]['total'] += $itemRevenue;
                }
            }

            if (isset($dailyTrend[$orderDate])) {
                $dailyTrend[$orderDate]['orders_count'] += 1;
                $dailyTrend[$orderDate]['revenue'] += $orderRevenue;
            }
        }

        $dailyTrend = array_values($dailyTrend);

        // Sort products by revenue descending and take top 10
        usort($productSales, function ($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });
        $productSales = array_slice($productSales, 0, 10);

        $this->set(compact(
            'total',
            'totalcommission',
            'datetime1',
            'datetime2',
            'totalOrders',
            'pendingOrders',
            'dailyTrend',
            'statusCounts',
            'productSales',
            'userProductSales'
        ));
        $this->viewBuilder()->setLayout('ajax');
        $this->render('analytics');
    }

    /**
     * Generate PDF for User Product Sales
     */
    public function printUserProductSales()
    {
        $this->viewBuilder()->setLayout('ajax');

        $vrb = $this->request->getQuery('keyword');
        if (empty($vrb) || !is_array($vrb)) {
            $vrb = [];
        }
        $vrb['start'] = !empty($vrb['start']) ? $vrb['start'] : date('Y-m-01');
        $vrb['end'] = !empty($vrb['end']) ? $vrb['end'] : date('Y-m-d');
        $vrb['user'] = !empty($vrb['user']) ? $vrb['user'] : null;

        $vrb['product'] = !empty($vrb['product']) ? $vrb['product'] : null;

        $datetime1 = new Time($vrb['start']);
        $datetime2 = new Time($vrb['end']);

        if ($vrb['user'] === null || $vrb['user'] === '') {
            $orders = $this->Orders->find('all')->contain(['Users', 'Orderpacks.Packs'])->where(['DATE(Orders.created) <= ' => $vrb['end'], 'DATE(Orders.created) >= ' => $vrb['start']]);
        } else {
            $orders = $this->Orders->find('all')->contain(['Users', 'Orderpacks.Packs'])->where(['Orders.user_id' => $vrb['user'], 'DATE(Orders.created) <= ' => $vrb['end'], 'DATE(Orders.created) >= ' => $vrb['start']]);
        }

        $warehouse = $this->Orders->Pofsales->Warehouses->get($this->Auth->user('defaultwh'), [
            'contain' => [
                'Subwarehouses.Pofsales',
                'Subwarehouses' => function ($q) {
                    return $q->where(['Subwarehouses.whtype_id' => 3]);
                }
            ]
        ]);
        $qwh = [];
        if ($warehouse->subwarehouses) {
            foreach ($warehouse->subwarehouses as $subwarehouse) {
                foreach ($subwarehouse->pofsales as $pofsale) {
                    $qwh['OR'][$pofsale->id] = ['Orders.pofsale_id' => $pofsale->id];
                }
            }
        }

        $pofsales = $this->Orders->Pofsales->find('all')->where(['warehouse_id' => $this->Auth->user('defaultwh')]);
        foreach ($pofsales as $pos) {
            $qwh['OR'][$pos->id] = ['Orders.pofsale_id' => $pos->id];
        }

        $orders->where([$qwh]);
        $ordersArray = $orders->toArray();

        $userProductSales = [];
        foreach ($ordersArray as $order) {
            if (!empty($vrb['product'])) {
                $hasProduct = false;
                foreach ($order->orderpacks as $op) {
                    if ($op->pack_id == $vrb['product']) {
                        $hasProduct = true;
                        break;
                    }
                }
                if (!$hasProduct) {
                    continue;
                }
            }
            foreach ($order->orderpacks as $orderpack) {
                if ($orderpack->statut == 6) {
                    if (!empty($vrb['product']) && $orderpack->pack_id != $vrb['product']) {
                        continue;
                    }
                    $itemRevenue = $orderpack->quantity * $orderpack->price;
                    $packTitle = ($orderpack->pack) ? $orderpack->pack->title : 'Produit #' . $orderpack->pack_id;
                    $sellerName = ($order->user) ? ($order->user->firstname . ' ' . $order->user->lastname) : 'Inconnu';
                    $upsKey = $order->user_id . '_' . $orderpack->pack_id;
                    if (!isset($userProductSales[$upsKey])) {
                        $userProductSales[$upsKey] = [
                            'user' => $sellerName,
                            'product' => $packTitle,
                            'quantity' => 0,
                            'total' => 0.0
                        ];
                    }
                    $userProductSales[$upsKey]['quantity'] += $orderpack->quantity;
                    $userProductSales[$upsKey]['total'] += $itemRevenue;
                }
            }
        }

        $this->set(compact('userProductSales', 'datetime1', 'datetime2'));

        $response = $this->render('print_user_product_sales');
        $html = method_exists($response, 'getBody') ? (string) $response->getBody() : $response->body();

        $filename = 'Ventes_vendeurs_produits_' . $datetime1->format('Ymd') . '_to_' . $datetime2->format('Ymd') . '.pdf';

        $mpdf = new \Mpdf\Mpdf(['tempDir' => TMP . 'mpdf']);
        $mpdf->WriteHTML($html);

        return $this->response
            ->withType('pdf')
            ->withHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->withStringBody($mpdf->Output($filename, \Mpdf\Output\Destination::STRING_RETURN));
    }

    public function commissions()
    {
        $vrb = $_GET['keyword'];
        $datetime1 = new Time($vrb['start']);
        $datetime2 = new Time($vrb['end']);
        if ($vrb['user'] == NULL) {
            $orders = $this->Orders->find('all')->contain(['Orderpacks.Turnovers'])->where(['DATE(Orders.created) <= ' => $vrb['end'], 'DATE(Orders.created) >= ' => $vrb['start']]);
        } else {
            $orders = $this->Orders->find('all')->contain(['Orderpacks.Turnovers'])->where(['Orders.user_id' => $vrb['user'], 'DATE(Orders.created) <= ' => $vrb['end'], 'DATE(Orders.created) >= ' => $vrb['start']]);
        }
        $warehouse = $this->Orders->Pofsales->Warehouses->get($this->Auth->user('defaultwh'), [
            'contain' => [
                'Subwarehouses.Pofsales',
                'Subwarehouses' => function ($q) {
                    return $q->where(['Subwarehouses.whtype_id' => 3]);
                }
            ]
        ]);
        $qwh = [];
        if ($warehouse->subwarehouses) {
            foreach ($warehouse->subwarehouses as $subwarehouse) {
                foreach ($subwarehouse->pofsales as $pofsale) {
                    $qwh['OR'][$pofsale->id] = ['Orders.pofsale_id' => $pofsale->id];
                }
            }
        }

        $pofsale = $this->Orders->Pofsales->find('all')->where(['warehouse_id' => $this->Auth->user('defaultwh')]);
        $qwh['OR'][$pofsale->last()->id] = ['Orders.pofsale_id' => $pofsale->last()->id];

        $orders->where([$qwh]);
        $total = 0;
        $totalcommission = 0;
        foreach ($orders as $key => $order) {
            foreach ($order->orderpacks as $key1 => $orderpack) {
                if ($orderpack->statut == 6) {
                    $total += ($orderpack->quantity * $orderpack->price);
                    $totalcommission += ($orderpack->turnover_id) ? (($orderpack->price * $orderpack->quantity) * $orderpack->turnover->commission / 100) : ($orderpack->price * $orderpack->quantity);
                }
            }
        }
        $this->viewBuilder()->setLayout('ajax');
        $this->set(compact('total', 'totalcommission', 'datetime1', 'datetime2'));
    }


    public function export()
    {
        $pofsale = $this->Orders->Pofsales->find('all')->where(['pofstype_id' => 3, 'warehouse_id' => $this->Auth->user('defaultwh')])->last();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'N° de la commande');
        $sheet->setCellValue('B1', 'Client');
        $sheet->setCellValue('C1', 'Numéro de téléphone');
        $sheet->setCellValue('D1', 'Adresse');
        $sheet->setCellValue('E1', 'Depôts');
        $orders = $this->Orders->find('all')->contain(['Customers'])->where(['Orders.statut' => 1, 'Orders.pofsale_id' => $pofsale->id]);
        $k = 2;
        foreach ($orders as $order) {
            $sheet->setCellValue('A' . $k, $order->code);
            $sheet->setCellValue('B' . $k, $order->customer->name);
            $sheet->setCellValue('C' . $k, $order->customer->phone);
            $sheet->setCellValue('D' . $k, $order->customer->adresse);
            $sheet->setCellValue('E' . $k, $pofsale->title);
            $k++;
        }

        $date = date('d-m-y-' . substr((string) microtime(), 1, 8));
        $date = str_replace(".", "", $date);
        $filename = "Commandes_" . $date . ".xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);
        $content = file_get_contents($filename);
        header("Content-Disposition: attachment; filename=" . $filename);
        unlink($filename);
        exit($content);
    }

    public function print($id = null)
    {

        $order = $this->Orders->get($id, ['contain' => ['Customers', 'Shippings']]);
        if ($order->customer->customertype_id == 4) {
            return $this->redirect(['controller' => 'Exitslips', 'action' => 'print', $order->shipping->exitslip_id . '.pdf']);
        } else {
            return $this->redirect(['controller' => 'shippings', 'action' => 'print', $order->shipping_id . '.pdf']);
        }
    }

    public function inventory($id = null)
    {
        $this->loadModel('Inventories');
        $inventory = $this->Inventories->find('all')
            ->where(['Inventories.code' => $id])
            ->contain([
                'Invproducts.Packs.Packunites.Unites.Parentunites',
                'Invproducts.Packs',
                'Warehouses',
                'Users'
            ])
            ->last();
        $this->loadModel('Orders');
        $order = $this->Orders->get($id, [
            'contain' => ['Customers']
        ]);
        $this->set(compact('inventory', 'order'));
    }

    // Movements report page with user and pack filters
    public function movements()
    {
        $this->loadModel('Users');
        $this->loadModel('Packs');
        $this->loadModel('Inventories');

        $users = $this->Users->find('list', [
            'keyField' => 'id',
            'valueField' => function ($row) {
                $role = ($row->role) ? $row->role->title : '';
                return trim($row->firstname . ' ' . $row->lastname . ($role ? ' (' . $role . ')' : ''));
            }
        ])
            ->contain(['Roles'])
            ->where([
                'Users.statut' => 1,
                'Users.role_id IN' => [3, 6]
            ])
            ->order(['Users.firstname' => 'ASC', 'Users.lastname' => 'ASC'])
            ->toArray();

        $packs = $this->Packs->find('list')->where(['Packs.statut IN' => [1, 3]])->order(['Packs.title' => 'ASC'])->toArray();

        $selectedUserId = $this->request->getQuery('user_id') ?: null;
        $selectedPackId = $this->request->getQuery('pack_id') ?: null;

        $movements = [];

        if ($selectedUserId && $selectedPackId) {
            // Find orders for selected user; use order code as bridge to inventories.code
            $ordersForUser = $this->Orders->find('list')
                ->where(['Orders.user_id' => (int) $selectedUserId])
                ->select(['Orders.code'])
                ->toArray();

            $orderCodes = array_values($ordersForUser);

            if (!empty($orderCodes)) {
                $inventoriesQ = $this->Inventories->find('all')
                    ->where(['Inventories.code IN' => $orderCodes])
                    ->contain([
                        'Invproducts' => function ($q) use ($selectedPackId) {
                            return $q->where(['Invproducts.pack_id' => (int) $selectedPackId])
                                ->contain(['Packs.Packunites.Unites.Parentunites'])
                                ->order(['Invproducts.statut' => 'ASC']);
                        },
                        'Users'
                    ])
                    ->order(['Inventories.created' => 'DESC']);

                foreach ($inventoriesQ as $inventory) {
                    // Pair initial (statut=2) and final (statut=3)
                    $initial = null;
                    $final = null;
                    foreach ($inventory->invproducts as $ip) {
                        if ($ip->statut == 2) {
                            $initial = $ip;
                        }
                        if ($ip->statut == 3) {
                            $final = $ip;
                        }
                    }

                    if ($initial || $final) {
                        $movements[] = [
                            'inventory' => $inventory,
                            'initial' => $initial,
                            'final' => $final,
                            'difference' => ($final ? (float) $final->quantity : 0) - ($initial ? (float) $initial->quantity : 0)
                        ];
                    }
                }
            }
        }

        $this->set(compact('users', 'packs', 'selectedUserId', 'selectedPackId', 'movements'));
    }

    /**
     * View method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $order = $this->Orders->get($id, [
            'contain' => [
                'Shippings',
                'Customers.Zones.Cities',
                'Customers.Customertypes',
                'Pofsales',
                'Users',
                'Orderpacks.Packs.Packunites.Unites.Parentunites',
                'Orderpacks.Packs.MeasurementUnits',
                'Orderpacks.Tranches.Remisetypes',
                'Orderpacks.Tranches.Packs',
                'Orderpacks.Orderpackproducts.Products',
                'Orderpacks.Loyaltyorderpacks',
            ],
        ]);

        $this->loadModel('Photos');
        $photo = $this->Photos->find('all')
            ->where(['controleur' => 'customers', 'objectid' => $order->customer->id])
            ->order(['created' => 'DESC'])
            ->first();
        $order->customer->photo = $photo;

        // Calculate order loyalty points
        $totalPoints = 0;
        $unclaimedPoints = 0;
        if (!empty($order->orderpacks)) {
            foreach ($order->orderpacks as $pack) {
                if ($pack->loyaltypointgift_id !== null) {
                    continue;
                }

                $points = $pack->quantity * $pack->loyaltypoints;
                $hasPoints = false;
                $isReturn = ($order->ordertype_id == 2);

                if (!$isReturn && $order->statut == 6 && $pack->statut == 6) {
                    $hasPoints = true;
                } else if ($isReturn && $order->statut == 6) {
                    $hasPoints = true;
                }

                if ($hasPoints) {
                    $val = $isReturn ? -$points : $points;
                    $totalPoints += $val;

                    $isClaimed = false;
                    if (!empty($pack->loyaltyorderpacks)) {
                        foreach ($pack->loyaltyorderpacks as $lop) {
                            if ($lop->loyaltypoint_id !== null) {
                                $isClaimed = true;
                                break;
                            }
                        }
                    }
                    if (!$isClaimed) {
                        $unclaimedPoints += $val;
                    }
                }
            }
        }
        $order->total_points = $totalPoints;
        $order->unclaimed_points = $unclaimedPoints;

        // Compute base vs final prices per order line using pricing service
        $pricingService = new OrderPricingService();
        $customerTypeId = (int) ($order->customer->customertype_id ? $order->customer->customertype_id : 0);
        $warehouseId = (int) ($order->pofsale->warehouse_id ? $order->pofsale->warehouse_id : 0);
        $companyId = (int) ($this->Auth->user('company_id') ? $this->Auth->user('company_id') : 0);

        $pricingByOrderpack = [];
        foreach ($order->orderpacks as $op) {
            try {
                $res = $pricingService->priceLine((int) $op->pack_id, (int) $op->quantity, $customerTypeId, $warehouseId, $companyId);
                $pricingByOrderpack[$op->id] = [
                    'base_price' => (float) $res['base_price'],
                    'final_unit_price' => (float) $res['final_unit_price'],
                    'tranche_id' => $res['tranche_id'],
                ];
            } catch (\Throwable $e) {
                // Fallback to stored price if pricing lookup fails
                $pricingByOrderpack[$op->id] = [
                    'base_price' => (float) $op->price,
                    'final_unit_price' => (float) $op->price,
                    'tranche_id' => $op->tranche_id ?? null,
                ];
            }
        }
        $this->set(compact('order', 'pricingByOrderpack'));
    }
    public function stockAdjustment()
    {
        $orders = $this->Orders->find('all')->where(['Orders.statut' => 6, 'DATE(Orders.created) >= ' => '2026-01-01'])->contain(['Orderpacks']);
        foreach ($orders as $order) {
            $this->loadModel('Inventories');
            $historyMouvements = $this->Inventories->find('all')->where(['Inventories.code' => $order->id]);
            if ($historyMouvements->count() == 0) {
                $orderpacks = [];
                foreach ($order->orderpacks as $orderpack) {
                    $orderpacks[] = [
                        'pack_id' => $orderpack->pack_id,
                        'quantity' => $orderpack->quantity
                    ];
                }
                $pofsale = $this->Orders->Pofsales->get($order->pofsale_id, [
                    'contain' => ['Warehouses.Subwarehouses']
                ]);
                $warehouse_id = $pofsale->warehouse->subwarehouses[0]->id;
                $this->processStockMovement($order->ordertype_id, $order->id, $warehouse_id, $orderpacks);

            } else {
                debug($order->id);
                die();

            }

        }
        $this->redirect(['action' => 'index']);
    }
    private function processStockMovement($order_type, $order_id, $warehouse_id, $orderpacks)
    {
        // Only process stock movement if order_type is not 2 (2 = return/credit)
        $this->loadModel('Orders');
        $order = $this->Orders->get($order_id);
        $this->loadModel('Inventories');
        $inventory = $this->Inventories->newEntity();
        $dataInventory = [];
        $dataInventory['company_id'] = 1;
        $dataInventory['warehouse_id'] = $warehouse_id;
        $dataInventory['code'] = $order->id;
        $dataInventory['user_id'] = $order->user_id;
        $dataInventory['whnature_id'] = 1;
        $dataInventory['statut'] = 1;
        $this->loadModel('Whproducts');
        if ($order_type == 2) {

            foreach ($orderpacks as $orderpack) {
                $pack_id = $orderpack['pack_id'];
                $quantity = $orderpack['quantity'];

                // Find the whproduct for this pack in subwarehouse with whnature_id = 2
                $whproduct = $this->Whproducts->find('all')
                    ->where([
                        'Whproducts.item_id' => $pack_id,
                        'Whproducts.item_type' => 'Pack',
                        'Whproducts.warehouse_id' => $warehouse_id, // whnature_id = 2 (subwarehouse)
                    ])
                    ->first();
                if ($whproduct) {
                    $dataInventory['invproducts'][] = [
                        'pack_id' => $pack_id,
                        'quantity' => $whproduct->quantity,
                        'statut' => 2,
                        'company_id' => $this->Auth->user('company_id')
                    ];
                    // Calculate new quantity after removal
                    $newQuantity = $whproduct->quantity + $quantity;

                    $pack = $this->Whproducts->Packs->get($pack_id);

                    if ($pack->saletype_id == 4 && ($pack->measurement_unit_id == 2 || $pack->measurement_unit_id == 4)) {
                        $newQuantity = $whproduct->quantity + ($quantity * 1000 / $pack->measurement_quantity);
                    }
                    // Update the whproduct quantity
                    $whproduct->quantity = $newQuantity;

                    $dataInventory['invproducts'][] = [
                        'pack_id' => $pack_id,
                        'quantity' => $whproduct->quantity,
                        'statut' => 3,
                        'company_id' => $this->Auth->user('company_id')
                    ];
                    if (!$this->Whproducts->save($whproduct)) {
                        // Log error but continue processing other products
                        // In production, you might want to handle this differently
                        error_log("Failed to update stock for pack_id: $pack_id");
                    }
                }
            }
            $inventory = $this->Inventories->patchEntity($inventory, $dataInventory, ['associated' => ['Invproducts']]);

            $this->Inventories->save($inventory);
        } else {
            foreach ($orderpacks as $orderpack) {
                $pack_id = $orderpack['pack_id'];
                $quantity = $orderpack['quantity'];

                // Find the whproduct for this pack in subwarehouse with whnature_id = 2
                $whproduct = $this->Whproducts->find('all')
                    ->where([
                        'Whproducts.item_id' => $pack_id,
                        'Whproducts.item_type' => 'Pack',
                        'Whproducts.warehouse_id' => $warehouse_id, // whnature_id = 2 (subwarehouse)
                    ])
                    ->first();
                if ($whproduct) {
                    $dataInventory['invproducts'][] = [
                        'pack_id' => $pack_id,
                        'quantity' => $whproduct->quantity,
                        'statut' => 2,
                        'company_id' => $this->Auth->user('company_id')
                    ];
                    // Calculate new quantity after removal
                    $newQuantity = $whproduct->quantity - $quantity;

                    $pack = $this->Whproducts->Packs->get($pack_id);

                    if ($pack->saletype_id == 4 && ($pack->measurement_unit_id == 2 || $pack->measurement_unit_id == 4)) {
                        $newQuantity = $whproduct->quantity - ($quantity * 1000 / $pack->measurement_quantity);
                    }
                    // Update the whproduct quantity
                    $whproduct->quantity = $newQuantity;

                    $dataInventory['invproducts'][] = [
                        'pack_id' => $pack_id,
                        'quantity' => $whproduct->quantity,
                        'statut' => 3,
                        'company_id' => $this->Auth->user('company_id')
                    ];
                    if (!$this->Whproducts->save($whproduct)) {

                        // Log error but continue processing other products
                        // In production, you might want to handle this differently
                        error_log("Failed to update stock for pack_id: $pack_id");
                    }
                }
            }
            $inventory = $this->Inventories->patchEntity($inventory, $dataInventory, ['associated' => ['Invproducts']]);

            $this->Inventories->save($inventory);
        }
    }
    public function addcustomer()
    {
        $customer = $this->Orders->Customers->newEntity();
        if ($this->request->is('post')) {
            $customer = $this->Orders->Customers->patchEntity($customer, $this->request->getData());
            $customer->statut = 1;
            $code = $this->Orders->Companies->Companycodes->find('all')->where(['controleur' => 'Customers', 'company_id' => $this->Auth->user('company_id')])->last();
            $customer->code = $code->prefixe . ($code->compteur + 1);
            $customer->company_id = $this->Auth->user('company_id');

            if ($this->Orders->Customers->save($customer)) {
                $code->compteur = $code->compteur + 1;
                $this->Orders->Customers->Companies->Companycodes->save($code);
                $this->Flash->success(__('Le client a été enregistré.'));
                return $this->redirect(['controller' => 'Orders', 'action' => 'add']);
            } else {
                $this->Flash->error(__('Le client n\'a pas pu être enregistré. Veuillez réessayer.'));
            }
        }

        $zonesd = $this->Orders->Customers->Zones->find('all')->where(['company_id' => $this->Auth->user('company_id'), 'zone_id IS NOT ' => NULL])->contain(['Zoneusers.Users']);

        $q = [];

        if ($this->Auth->user('role_id') == 6 || $this->Auth->user('role_id') == 5 || $this->Auth->user('role_id') == 3) {
            if ($this->Auth->user("zone_id")) {
                foreach ($this->Auth->user("zone_id") as $key => $zone) {
                    $q[$key] = ['id' => $zone];
                }
                $zonesd->where(['OR' => $q]);
            } else {
                $zonesd->where(['id' => 0]);
            }
        } else {
            $zonesd->where(['warehouse_id' => $this->Auth->user('defaultwh')]);
        }
        $zones = [];
        foreach ($zonesd as $key => $zone) {
            if ($zone->zoneusers) {
                $zones[$zone->id] = $zone->title . '-' . $zone->zoneusers[0]->user->firstname . ' ' . $zone->zoneusers[0]->user->lastname;
            } else {
                $zones[$zone->id] = $zone->title;
            }
        }
        $customertypes = $this->Orders->Customers->Customertypes->find('list')->where(['company_id' => $this->Auth->user('company_id')]);
        $this->set(compact('customer', 'zones', 'customertypes'));
    }
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($ordertypeid = null)
    {
        $order = $this->Orders->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();
            $iscustomer = $datas['customer_id'];
            $customerinfos = $this->Orders->Customers->get($iscustomer);

            // Point-in-Polygon boundary check (Approach B)
            $isOutOfZone = 0;
            $this->loadModel('Zones');
            $customerSubzone = $this->Zones->find('all')
                ->where(['id' => $customerinfos->zone_id])
                ->first();

            $sectorId = null;
            if ($customerSubzone) {
                $sectorId = $customerSubzone->zone_id ?: $customerSubzone->id;
            }

            if ($sectorId) {
                $this->loadModel('ZoneCoordinates');
                $boundaryCoords = $this->ZoneCoordinates->find('all')
                    ->where(['zone_id' => $sectorId])
                    ->order(['sequence_order' => 'ASC'])
                    ->toArray();

                if (!empty($boundaryCoords)) {
                    $sellerLat = $this->request->getData('latitude');
                    $sellerLng = $this->request->getData('longitude');

                    if (!empty($sellerLat) && !empty($sellerLng)) {
                        $polygon = [];
                        foreach ($boundaryCoords as $coord) {
                            $polygon[] = [
                                'lat' => (float)$coord->latitude,
                                'lng' => (float)$coord->longitude
                            ];
                        }
                        
                        $point = [
                            'lat' => (float)$sellerLat,
                            'lng' => (float)$sellerLng
                        ];

                        if (!$this->_isPointInPolygon($point, $polygon)) {
                            $isOutOfZone = 1; // Out of zone
                        }
                    } else {
                        $isOutOfZone = 2; // Unknown location
                    }
                }
            }
            $datas['is_out_of_zone'] = $isOutOfZone;

            $customer = $customerinfos->customertype_id;
            $orderpackproducts = [];
            $increment = 0;
            $warehousev = null;
            $pofsale = $this->Orders->Pofsales->get($datas['pofsale']);

            //récupérer le point de vente
            if ($this->Auth->user('role_id') == 3) {
                $warehousev = $this->Orders->Pofsales->Warehouses->get($pofsale->warehouse_id, ['contain' => ['Parentwarehouses']]);
            }
            //si le client est validé

            if ($iscustomer) {
                foreach ($this->request->getData('orderpacks') as $key => $orderpck) {
                    if (isset($orderpck[0]) && isset($orderpck[1])) {
                        if (intVal($orderpck[0]['quantity']) == 0 && intVal($orderpck[1]['quantity']) == 0) {
                            unset($datas['orderpacks'][$key]);
                        }
                    } elseif (isset($orderpck[0]) && !isset($orderpck[1])) {
                        if (intVal($orderpck[0]['quantity']) == 0) {
                            unset($datas['orderpacks'][$key]);
                        }
                    } else {
                        if (intVal($orderpck[1]['quantity']) == 0) {
                            unset($datas['orderpacks'][$key]);
                        }
                    }
                }
                if ($this->Auth->user('role_id') == 1 || $this->Auth->user('role_id') == 2 || $this->Auth->user('role_id') == 7 || $this->Auth->user('role_id') == 8) {
                    $zone = $this->Orders->Users->Zoneusers->Zones->get($customerinfos->zone_id);
                    $zoneuser = $this->Orders->Customers->Zones->Zoneusers->find('all')->contain([
                        'Users' => function ($q) {
                            return $q->where(['Users.role_id' => 5]);
                        },
                        'Users.Pofsusers'
                    ])->where(['Zoneusers.company_id' => $this->Auth->user('company_id'), 'Zoneusers.zone_id' => $zone->zone_id])->group('user_id')->first();
                    $user_id = $zoneuser->user_id;
                } else {
                    $user_id = $this->Auth->user('id');
                }
                $user_id = $this->Auth->user('id');

                $totalprice = 0;
                $pricingService = new OrderPricingService();
                //si la commande contient des produits
                if (isset($datas['orderpacks']) && $datas['orderpacks']) {
                    //boucle permet d'organiser les données dans la table orderpacks et orderpackproducts
                    foreach ($datas['orderpacks'] as $key => $orderpack) {
                        //organiser les données de la table orderpacks
                        $packunite = $this->Orders->Orderpacks->Packs->get($orderpack['pack_id'], ['contain' => ['Packunites.Unites']]);
                        if (isset($orderpack[0]) && isset($orderpack[1])) {
                            $datas['orderpacks'][$key]['quantity'] = ($orderpack[0]['quantity'] * $packunite->packunites[0]->quantity) + $orderpack[1]['quantity'];
                            unset($datas['orderpacks'][$key][0]);
                            unset($datas['orderpacks'][$key][1]);
                        } elseif (isset($orderpack[0]) && !isset($orderpack[1])) {
                            $datas['orderpacks'][$key]['quantity'] = ($orderpack[0]['quantity'] * $packunite->packunites[0]->quantity);
                            unset($datas['orderpacks'][$key][0]);
                        } else {
                            $datas['orderpacks'][$key]['quantity'] = $orderpack[1]['quantity'];
                            unset($datas['orderpacks'][$key][1]);
                        }
                        $pack = $this->Orders->Orderpacks->Packs->get($orderpack['pack_id']);
                        $datas['orderpacks'][$key]['price'] = $orderpack['price'] / $packunite->packunites[0]->quantity;
                        $datas['orderpacks'][$key]['loyaltypoints'] = $pack->loyaltypoints;
                        $datas['orderpacks'][$key]['user_id'] = $user_id;
                        $datas['orderpacks'][$key]['company_id'] = $this->Auth->user('company_id');
                        $datas['orderpacks'][$key]['warehouse_id'] = $pofsale->warehouse_id;
                        $datas['orderpacks'][$key]['statut'] = 1;

                        try {
                            $priced = $pricingService->priceLine(
                                intval($datas['orderpacks'][$key]['pack_id']),
                                intval($datas['orderpacks'][$key]['quantity']),
                                intval($customer),
                                intval($pofsale->warehouse_id),
                                intval($this->Auth->user('company_id'))
                            );
                            $datas['orderpacks'][$key]['price'] = $priced['final_unit_price'];
                            $datas['orderpacks'][$key]['tranche_id'] = $priced['tranche_id'];
                            $totalprice += ($priced['final_unit_price'] * $datas['orderpacks'][$key]['quantity']);

                            // Handle gifts (GRT): append gift pack as separate orderpack entry with price=0
                            if (!empty($priced['gift_pack_id'])) {
                                $giftPackId = intval($priced['gift_pack_id']);
                                $giftQtyPacks = !empty($priced['gift_quantity']) ? intval($priced['gift_quantity']) : 1;

                                // Add gift pack as separate orderpack with price=0
                                $increment++;
                                $datas['orderpacks'][$increment] = [
                                    'pack_id' => $giftPackId,
                                    'quantity' => $giftQtyPacks,
                                    'price' => 0,
                                    'loyaltypoints' => 0,
                                    'user_id' => $user_id,
                                    'company_id' => $this->Auth->user('company_id'),
                                    'warehouse_id' => $pofsale->warehouse_id,
                                    'statut' => 1,
                                ];
                            }
                        } catch (\RuntimeException $exception) {
                            $this->Flash->error(__('Aucun prix actif n\'a été trouvé pour ce produit.'));
                            return $this->redirect(['action' => 'add']);
                        }

                        $increment++;
                    }

                    $tarifid = NULL;
                    $tarifs = $this->Orders->Orderpacks->Packs->Prices->Tarifs->find('all')->order(['minprice' => 'ASC']);
                    foreach ($tarifs as $tarif) {
                        if ($tarif->minprice < $totalprice) {
                            $tarifid = $tarif->id;
                        }
                    }
                    // la commande ne contient aucun article un message pour resaisir la commande
                } else {
                    $this->Flash->error(__('Merci de charger les produits. Veuillez réessayer.'));
                    return $this->redirect(['action' => 'add']);
                }

                $codeshipping = $this->Orders->Companies->Companycodes->find('all')->where(['controleur' => 'Shippings', 'company_id' => $this->Auth->user('company_id')])->last();
                $datas['shipping']['company_id'] = $this->Auth->user('company_id');
                $datas['shipping']['user_id'] = $user_id;
                $datas['shipping']['code'] = $codeshipping->prefixe . '' . ($codeshipping->compteur + 1);
                $datas['shipping']['customer_id'] = $datas['customer_id'];
                $datas['shipping']['warehouse_id'] = $this->Auth->user('defaultwh');

                $datas['pofsale_id'] = $pofsale->id;
                $datas['ordertype_id'] = $ordertypeid;
                $code = $this->Orders->Companies->Companycodes->find('all')->where(['controleur' => 'Orders', 'company_id' => $this->Auth->user('company_id')])->last();
                $datas['code'] = $code->prefixe . ($code->compteur + 1);
                $datas['user_id'] = $user_id;
                $datas['company_id'] = $this->Auth->user('company_id');
                if ($customerinfos->customertype_id == 4) {
                    $datas['shipping']['comment'] = $datas['comment'];
                    $datas['statut'] = 2;
                    $warehouseN = $this->Orders->Pofsales->Warehouses->find('all')->where(['warehouse_id' => $this->Auth->user('defaultwh'), 'whnature_id' => 1, 'whtype_id' => 2])->last();
                    $codeexit = $this->Orders->Companies->Companycodes->find('all')->where(['controleur' => 'Exitslips', 'company_id' => $this->Auth->user('company_id')])->last();
                    $datas['shipping']['exitslip']['exitsliptype_id'] = 1;
                    $datas['shipping']['exitslip']['code'] = $codeexit->prefixe . ($codeexit->compteur + 1);
                    $datas['shipping']['exitslip']['company_id'] = $this->Auth->user('company_id');
                    $datas['shipping']['exitslip']['user_id'] = $user_id;
                    $datas['shipping']['exitslip']['warehouse_id'] = $pofsale->warehouse_id;
                    $datas['shipping']['exitslip']['statut'] = 2;
                    $datas['shipping']['exitslip']['livreur'] = $datas['comment'];
                    $order = $this->Orders->patchEntity($order, $datas, ['associated' => ['Orderpacks', 'Shippings.Exitslips']]);
                } else {
                    $datas['statut'] = 1;
                    $order = $this->Orders->patchEntity($order, $datas, ['associated' => ['Orderpacks', 'Shippings']]);
                }
                if ($this->Orders->save($order)) {
                    $code->compteur = $code->compteur + 1;
                    if ($this->Orders->Companies->Companycodes->save($code)) {
                        $codeshipping->compteur = $codeshipping->compteur + 1;
                        if ($customerinfos->customertype_id == 4) {
                            $codeshipping->compteur = $codeshipping->compteur + 1;
                            $this->Orders->Companies->Companycodes->save($codeshipping);
                        }
                        $codeshipping->compteur = $codeshipping->compteur + 1;
                        if ($this->Orders->Companies->Companycodes->save($codeshipping)) {
                            $this->Flash->success(__('La commande a été enregistré.'));
                            return $this->redirect(['action' => 'index', $ordertypeid]);
                        }
                    }
                    $this->Flash->error(__('La commande n\'a pas enregistré, merci de réessayer.'));
                }
            } else {
                $this->Flash->error(__('Merci de selectionner le client. Veuillez réessayer.'));
            }
        }

        if ($ordertypeid == 2) {
            return $this->redirect(['action' => 'addavoir']);
        } elseif ($ordertypeid == 4) {
            return $this->redirect(['action' => 'addgift']);
        }
        $this->set(compact('order', 'ordertypeid'));
    }

    public function addavoir($ordertypeid = null)
    {
        $ordertypeid = 'avoir';
        $order = $this->Orders->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();
            $iscustomer = $datas['customer_id'];

            //vérifier si le client est disponible
            $customerinfos = $this->Orders->Customers->get($iscustomer);
            $customer = $customerinfos->customertype_id;
            $orderpackproducts = [];
            $increment = 0;
            //récupérer le point de vente
            $pofsale = $this->Orders->Pofsales->get($datas['pofsale']);
            //si le client est validé
            if ($iscustomer) {
                if ($this->Auth->user('role_id') == 1 || $this->Auth->user('role_id') == 2 || $this->Auth->user('role_id') == 7 || $this->Auth->user('role_id') == 8) {
                    $user_id = $this->Auth->user('id');
                } else {
                    $user_id = $this->Auth->user('id');
                }
                $totalprice = 0;
                //si la commande contient des produits
                if (isset($datas['orderpacks'])) {
                    //boucle permet d'organiser les données dans la table orderpacks et orderpackproducts
                    foreach ($datas['orderpacks'] as $key => $orderpack) {
                        //organiser les données de la table orderpacks
                        $datas['orderpacks'][$key]['user_id'] = $user_id;
                        $datas['orderpacks'][$key]['company_id'] = $this->Auth->user('company_id');
                        $datas['orderpacks'][$key]['statut'] = 1;
                        if (!isset($datas['orderpacks'][$key]['price'])) {
                            //récupérer le prix du pack ont se basent sur le type du clients et les tranches
                            $packprice = $this->Orders->Orderpacks->Packs->get(
                                intval($datas['orderpacks'][$key]['pack_id']),
                                [
                                    'contain' => [
                                        'Prices' => function ($q) use ($customer, $pofsale) {
                                            return $q->where(['Prices.customertype_id' => $customer, 'Prices.warehouse_id' => $pofsale->warehouse_id, 'Prices.tarif_id' => 1]);
                                        }
                                    ]
                                ]
                            );
                            foreach ($packprice->prices as $keys => $price) {
                                $totalprice += ($price->price * $datas['orderpacks'][$key]['quantity']);
                                $datas['orderpacks'][$key]['price'] = $price->price;
                                $datas['orderpacks'][$key]['tranche_id'] = null;
                            }
                        }
                        // récupérer les produits du packs
                        $products = $this->Orders->Orderpacks->Packs->Packproducts->find('all')->contain(['Products'])->where(['Packproducts.pack_id' => $orderpack['pack_id']]);
                        //boucles permet de remplir la tables orderpackproducts 
                        foreach ($products as $key1 => $product) {
                            $datas['orderpacks'][$key]['orderpackproducts'][$key1]['product_id'] = $product->product_id;
                            $datas['orderpacks'][$key]['orderpackproducts'][$key1]['buyingprice'] = $product->product->buyingprice;
                            $datas['orderpacks'][$key]['orderpackproducts'][$key1]['quantity'] = intval($orderpack['quantity']) * $product->quantity;
                            $datas['orderpacks'][$key]['orderpackproducts'][$key1]['user_id'] = $user_id;
                            $datas['orderpacks'][$key]['orderpackproducts'][$key1]['company_id'] = $this->Auth->user('company_id');
                            $datas['orderpacks'][$key]['orderpackproducts'][$key1]['statut'] = 1;

                            $orderpackproducts[$increment]['product_id'] = $product->product_id;
                            $orderpackproducts[$increment]['price'] = 0;
                            $orderpackproducts[$increment]['user_id'] = $user_id;
                            $orderpackproducts[$increment]['company_id'] = $this->Auth->user('company_id');
                            $orderpackproducts[$increment]['quantity'] = intval($orderpack['quantity']) * $product->quantity;

                            $increment++;
                        }
                        $increment++;
                    }
                    $tarifid = NULL;
                    $tarifs = $this->Orders->Orderpacks->Packs->Prices->Tarifs->find('all')->order(['minprice' => 'ASC']);
                    foreach ($tarifs as $tarif) {
                        if ($tarif->minprice < $totalprice) {
                            $tarifid = $tarif->id;
                        }
                    }
                    //boucle permet d'organiser les données dans la table orderpacks et orderpackproducts
                    foreach ($datas['orderpacks'] as $key => $orderpack) {
                        //organiser les données de la table orderpacks
                        $datas['orderpacks'][$key]['user_id'] = $user_id;
                        $datas['orderpacks'][$key]['company_id'] = $this->Auth->user('company_id');
                        $datas['orderpacks'][$key]['warehouse_id'] = $pofsale->warehouse_id;
                        $datas['orderpacks'][$key]['statut'] = 1;
                        if (!isset($datas['orderpacks'][$key]['price'])) {
                            //récupérer le prix du pack ont se basent sur le type du clients et les tranches
                            $packprice = $this->Orders->Orderpacks->Packs->get(
                                intval($datas['orderpacks'][$key]['pack_id']),
                                [
                                    'contain' => [
                                        'Prices' => function ($q) use ($customer, $pofsale, $tarifid) {
                                            return $q->where(['Prices.customertype_id' => $customer, 'Prices.warehouse_id' => $pofsale->warehouse_id, 'Prices.tarif_id IS' => NULL]);
                                        }
                                    ]
                                ]
                            );
                            foreach ($packprice->prices as $keys => $price) {
                                $totalprice += ($price->price * $datas['orderpacks'][$key]['quantity']);
                                $datas['orderpacks'][$key]['price'] = $price->price;
                            }
                        }
                    }


                    // la commande ne contient aucun article un message pour resaisir la commande
                } else {
                    $this->Flash->error(__('Merci de charger les produits. Veuillez réessayer.'));
                    return $this->redirect(['action' => 'add']);
                }


                $order->ordertype_id = 2;
                // completer la table order
                $order->pofsale_id = $pofsale->id;
                $code = $this->Orders->Companies->Companycodes->find('all')->where(['controleur' => 'Tohaves', 'company_id' => $this->Auth->user('company_id')])->last();
                $order->code = $code->prefixe . ($code->compteur + 1);

                $order->code = $code->prefixe . ($code->compteur + 1);
                $order->user_id = $user_id;
                $order->company_id = $this->Auth->user('company_id');
                $order->statut = 1;
                $order = $this->Orders->patchEntity($order, $datas, ['associated' => ['Orderpacks.Orderpackproducts']]);
                if ($this->Orders->save($order)) {
                    $code->compteur = $code->compteur + 1;
                    if ($this->Orders->Companies->Companycodes->save($code)) {
                        $this->Flash->success(__('La commande a été enregistré.'));
                        return $this->redirect(['action' => 'index', $ordertypeid]);
                    }
                }
                $this->Flash->error(__('La commande n\'a pas enregistré, merci de réessayer.'));
            } else {
                $this->Flash->error(__('Merci de selectionner le client. Veuillez réessayer.'));
            }
        }
        $this->set(compact('order', 'ordertypeid'));
    }
    public function addgift()
    {
        $ordertypeid = 'cadeaux';
        $order = $this->Orders->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();
            $iscustomer = $datas['customer_id'];

            //vérifier si le client est disponible
            $customerinfos = $this->Orders->Customers->get($iscustomer);
            $customer = $customerinfos->customertype_id;
            $orderpackproducts = [];
            $increment = 0;
            //récupérer le point de vente
            $pofsale = $this->Orders->Pofsales->get($datas['pofsale']);
            //si le client est validé
            if ($iscustomer) {

                if ($this->Auth->user('role_id') == 1 || $this->Auth->user('role_id') == 2 || $this->Auth->user('role_id') == 7 || $this->Auth->user('role_id') == 8) {
                    $zone = $this->Orders->Users->Zoneusers->Zones->get($customerinfos->zone_id);
                    $zoneuser = $this->Orders->Customers->Zones->Zoneusers->find('all')->contain([
                        'Users' => function ($q) {
                            return $q->where(['Users.role_id' => 5]);
                        },
                        'Users.Pofsusers'
                    ])->where(['Zoneusers.company_id' => $this->Auth->user('company_id'), 'Zoneusers.zone_id' => $zone->zone_id])->group('user_id')->first();
                    $user_id = $zoneuser->user_id;
                } else {
                    $user_id = $this->Auth->user('id');
                }
                $user_id = $this->Auth->user('id');

                $totalprice = 0;
                //si la commande contient des produits
                if (isset($datas['orderpacks']) && $datas['orderpacks']) {
                    //boucle permet d'organiser les données dans la table orderpacks et orderpackproducts
                    foreach ($datas['orderpacks'] as $key => $orderpack) {
                        if ($datas['orderpacks'][$key]['quantity'] == 0) {
                            unset($datas['orderpacks'][$key]);
                            continue;
                        }
                        $pack = $this->Orders->Orderpacks->Packs->get($orderpack['pack_id']);
                        $datas['orderpacks'][$key]['price'] = 0;
                        $datas['orderpacks'][$key]['loyaltypoints'] = $pack->loyaltypoints;
                        $datas['orderpacks'][$key]['user_id'] = $user_id;
                        $datas['orderpacks'][$key]['company_id'] = $this->Auth->user('company_id');
                        $datas['orderpacks'][$key]['statut'] = 1;
                        $increment++;
                    }
                    $maxpoints = 0;
                    //boucle permet d'organiser les données dans la table orderpacks et orderpackproducts
                    foreach ($datas['orderpacks'] as $key => $orderpack) {
                        //organiser les données de la table orderpacks
                        $datas['orderpacks'][$key]['user_id'] = $user_id;
                        $datas['orderpacks'][$key]['company_id'] = $this->Auth->user('company_id');
                        $datas['orderpacks'][$key]['warehouse_id'] = $pofsale->warehouse_id;
                        $datas['orderpacks'][$key]['statut'] = 1;
                        $maxpoints += $orderpack['quantity'] * $orderpack['loyaltypoints'];
                    }
                    // la commande ne contient aucun article un message pour resaisir la commande
                } else {
                    $this->Flash->error(__('Merci de charger les produits. Veuillez réessayer.'));
                    return $this->redirect(['action' => 'add']);
                }


                $order->ordertype_id = 4;
                // completer la table order
                $order->pofsale_id = $pofsale->id;
                $code = $this->Orders->Companies->Companycodes->find('all')->where(['controleur' => 'Gifts', 'company_id' => $this->Auth->user('company_id')])->last();
                $order->code = $code->prefixe . ($code->compteur + 1);
                $order->user_id = $user_id;
                $order->company_id = $this->Auth->user('company_id');
                $order->statut = 1;
                $order = $this->Orders->patchEntity($order, $datas, ['associated' => ['Orderpacks.Orderpackproducts']]);

                // si le type du point de vente est vente indirect
                $this->loadModel('Shippings');
                $shipping = $this->Shippings->newEntity();
                $shipping->company_id = $order->company_id;
                $shipping->user_id = $order->user_id;
                $shipping->customer_id = $order->customer_id;
                $shipping->statut = 2;
                $codeship = $this->Shippings->Companies->Companycodes->find('all')->where(['controleur' => 'Shippings', 'company_id' => $this->Auth->user('company_id')])->last();
                $shipping->code = "GF" . $codeship->prefixe . ($codeship->compteur + 1);
                $shipping->orders = [0 => $order];

                if ($this->Shippings->save($shipping)) {
                    $gift = $this->Shippings->Orders->Loyaltypointgifts->newEntity();
                    $gift->code = $order->code;
                    $gift->order_id = $shipping->orders[0]->id;
                    $gift->customer_id = $order->customer_id;
                    $gift->company_id = $order->company_id;
                    $gift->created = $shipping->created;
                    $gift->modified = $shipping->modified;
                    $gift->statut = 1;
                    $this->Orders->Loyaltypointgifts->save($gift);
                    $orderpacks = $this->Shippings->Orders->Orderpacks->find('all')->contain(['Orders'])->where(['Orders.customer_id' => $order->customer_id, 'Orders.ordertype_id' => 1, 'Orderpacks.loyaltypointgift_id IS ' => NULL]);
                    $packToUpdates = [];
                    foreach ($orderpacks as $orderpack) {
                        if ($maxpoints >= $orderpack->quantity * $orderpack->loyaltypoints) {
                            $packToUpdates[] = ["id" => $orderpack->id, "quantity" => $orderpack->quantity];
                            $maxpoints -= $orderpack->quantity * $orderpack->loyaltypoints;
                        } else {
                            $packToUpdates[] = ["id" => $orderpack->id, "quantity" => $maxpoints];
                            $maxpoints = 0;
                            break;
                        }
                    }

                    foreach ($packToUpdates as $packToUpdate) {
                        $orderPackUpdate = $this->Shippings->Orders->Orderpacks->get($packToUpdate['id']);
                        if ($orderPackUpdate->quantity == $packToUpdate['quantity']) {
                            $orderPackUpdate->loyaltypointgift_id = $gift->id;
                            $this->Shippings->Orders->Orderpacks->save($orderPackUpdate);
                        } else {
                            $newOrderPack = $this->Shippings->Orders->Orderpacks->newEntity();
                            $data = [
                                "order_id" => $orderPackUpdate->order_id,
                                "pack_id" => $orderPackUpdate->pack_id,
                                "whnature_id" => $orderPackUpdate->whnature_id,
                                "price" => $orderPackUpdate->price,
                                "statut" => $orderPackUpdate->statut,
                                "created" => $orderPackUpdate->created,
                                "modified" => $orderPackUpdate->modified,
                                "company_id" => $orderPackUpdate->company_id,
                                "user_id" => $orderPackUpdate->user_id,
                                "commissionpack" => $orderPackUpdate->commissionpack,
                                "loyaltypoints" => $orderPackUpdate->loyaltypoints,
                                "loyalityvalidation" => $orderPackUpdate->loyalityvalidation,
                                "turnover_id" => $orderPackUpdate->turnover_id,
                                "loyaltypointgift_id" => $gift->id,
                                "quantity" => intVal($packToUpdate['quantity']),

                            ];
                            $newOrderPack = $this->Shippings->Orders->Orderpacks->patchEntity($newOrderPack, $data);
                            $this->Shippings->Orders->Orderpacks->save($newOrderPack);
                            $orderPackUpdate->quantity -= intVal($packToUpdate['quantity']);
                            $this->Shippings->Orders->Orderpacks->save($orderPackUpdate);
                        }
                    }
                    $code->compteur = $code->compteur + 1;
                    $this->Shippings->Companies->Companycodes->save($code);
                    $codeship->compteur = $codeship->compteur + 1;
                    $this->Shippings->Companies->Companycodes->save($codeship);
                    return $this->redirect(['action' => 'index', 4]);
                }
                $this->Flash->error(__('La commande n\'a pas enregistré, merci de réessayer.'));
            } else {
                $this->Flash->error(__('Merci de selectionner le client. Veuillez réessayer.'));
            }
        }
        $this->set(compact('order', 'ordertypeid'));
    }
    /**
     * Edit method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null, $valider = null)
    {

        $order = $this->Orders->get($id, [
            'contain' => ['Shippings', 'Customers.Zones.Cities', 'Pofsales', 'Users', 'Orderpacks.Packs.Packunites.Unites.Parentunites'],
        ]);
        if ($order->statut !== 1) {
            if ($this->Auth->user('role_id') !== 1) {
                $this->Flash->error(__('Vous n\'avez pas le droit de modifier cette commande, merci de réessayer.'));
                return $this->redirect(['action' => 'index', $order->ordertype_id]);
            }
        }

        if (($order->statut == 1 || $order->statut == 2 || $order->statut == 3 || $order->statut == 6) || $this->Auth->user('role_id') == 1) {
            if ($valider) {

                $warehouseN = $this->Orders->Pofsales->Warehouses->find('all')->where(['warehouse_id' => $this->Auth->user('defaultwh'), 'whnature_id' => 1, 'whtype_id' => 2])->last();
                $orderupdate = $this->Orders->get($order->id, ['contain' => ['Shippings.Exitslips', 'Orderpacks']]);
                $orderdata['id'] = $orderupdate->id;
                $orderdata['statut'] = 6;
                $orderdata['shipping']['id'] = $orderupdate->shipping->id;
                $orderdata['shipping']['statut'] = 4;
                $orderdata['shipping']['exitslip']['id'] = $orderupdate->shipping->exitslip->id;
                $orderdata['shipping']['exitslip']['statut'] = 3;
                foreach ($orderupdate->orderpacks as $orderpack) {
                    $orderdata['orderpack'][$orderpack->id]['id'] = $orderpack->id;
                    $orderdata['orderpack'][$orderpack->id]['statut'] = 6;
                }

                $orderupdate = $this->Orders->patchEntity($orderupdate, $orderdata, ['associated' => ['Shippings.Exitslips', 'Orderpacks']]);
                if ($this->Orders->save($orderupdate)) {
                    foreach ($orderupdate->orderpacks as $orderpack) {
                        $whproduct = $this->Orders->Orderpacks->Packs->Whproducts->find('all')->where(['item_id' => $orderpack->pack_id, 'item_type' => 'Pack', 'warehouse_id' => $warehouseN->id])->last();
                        if ($whproduct) {
                            $whproduct->quantity -= $orderpack->quantity;
                            $this->Orders->Orderpacks->Packs->Whproducts->save($whproduct);

                            // Log stock movement
                            $this->loadModel('StockMovements');
                            $stockMovement = $this->StockMovements->newEntity([
                                'item_id' => $orderpack->pack_id,
                                'item_type' => 'Pack',
                                'warehouse_id' => $warehouseN->id,
                                'quantity_change' => -$orderpack->quantity,
                                'balance_after_movement' => $whproduct->quantity,
                                'movement_type' => 'order_validation',
                                'user_id' => $this->Auth->user('id'),
                                'company_id' => $this->Auth->user('company_id'),
                                'notes' => 'Stock decremented during order validation for order #' . $orderupdate->id,
                            ]);
                            $this->StockMovements->save($stockMovement);
                        }
                    }
                    $this->Flash->success(__('La commande est confirmée avec success'));
                    return $this->redirect(['action' => 'index', $order->ordertype_id]);
                }
            }
            $categories = $this->Orders->Orderpacks->Packs->Categories->find('list')->where(['statut' => 1]);
            $this->set(compact('order', 'categories'));
        } else {
            $this->Flash->error(__('Vous n\'avez pas le droit de modifier cette commande, merci de réessayer.'));
            return $this->redirect(['action' => 'index', $order->ordertype_id]);
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */

    public function delete($id = null)
    {

        $order = $this->Orders->get($id);
        if ($order->statut == 1 || $order->statut == 2) {
            if ($this->Orders->delete($order)) {
                $this->Flash->success(__('La commande a été supprimée.'));
            } else {
                $this->Flash->error(__('La commande n\'a pas pu être supprimée. Veuillez réessayer.'));
            }
        } else {
            $this->Flash->error(__('Vous ne pouvez pas modifier une commande confirmée.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function customers()
    {
        $keyword = $this->request->getQuery('q');
        $json = [];

        $datas = $this->Orders->Customers->find('all')->contain(['Zones'])->leftJoinWith('Orders.Orderpacks');
        $datas->select([
            'Customers.id',
            'Customers.code',
            'Customers.name',
            'Zones.title',
            'loyaltypoints_sum' => $datas->newExpr(
                'SUM(CASE WHEN Orders.ordertype_id = 1 AND Orders.statut = 6 AND Orderpacks.statut = 6 THEN Orderpacks.loyaltypoints * Orderpacks.quantity ELSE 0 END) - '
                . 'SUM(CASE WHEN Orders.ordertype_id = 2 AND Orders.statut = 6 THEN Orderpacks.loyaltypoints * Orderpacks.quantity ELSE 0 END)'
            )
        ]);
        $datas->where([
            'Orderpacks.loyaltypointgift_id IS ' => NULL,
            'Customers.statut >= ' => 1,
            'OR' => [
                ['lower(Customers.name) LIKE' => '%' . $keyword . '%'],
                ['Customers.phone LIKE' => '%' . $keyword . '%'],
                ['Customers.name LIKE' => '%' . $keyword . '%'],
                ['Customers.code LIKE' => '%' . $keyword . '%'],
                ['lower(Customers.code) LIKE' => '%' . $keyword . '%']
            ]
        ]);
        $q = [];
        if ($this->Auth->user('role_id') == 3 || $this->Auth->user('role_id') == 5) {
            foreach ($this->Auth->user('zone_id') as $key => $value) {
                $q['OR'][$key] = [['Customers.zone_id' => $value]];
            }
            $datas->where([$q]);
        } else {
            $datas->where(['Zones.warehouse_id' => $this->Auth->user('defaultwh')]);
        }
        $datas->group(['Customers.id']);
        $datas->limit(50);
        if ($keyword) {
            foreach ($datas as $key => $data) {
                $json[] = ['id' => $data->id, 'text' => $data->code . ' : ' . $data->name . ' ' . $data->phone . ' (' . $data->zone->title . ') - ' . $data->loyaltypoints_sum . 'pts'];
            }
        }
        $this->autoRender = false;
        echo json_encode($json);
        exit;
    }

    public function giftproducts()
    {
        //$this->request->allowMethod('ajax');
        $keyword = $this->request->getQuery('keyword');

        $avoir = $this->request->getQuery('avoir');
        $userinfos = $this->Orders->Customers->find('all')->select(['id', 'code', 'name', 'adresse', 'zone_id', 'customertype_id'])->where(['id' => $keyword, 'statut >= ' => 1])->last();
        $categories = $this->Orders->Orderpacks->Packs->Categories->find('all')->where(['statut' => 1, 'category_id' => 8]);
        $q = [];
        if ($this->Auth->user('role_id') == 1 || $this->Auth->user('role_id') == 2 || $this->Auth->user('role_id') == 7 || $this->Auth->user('role_id') == 8) {
            if ($userinfos->customertype_id == 4) {
                $pofsales = $this->Orders->Pofsales->find('list')->where(['pofstype_id' => 3]);
            } else {
                $pofsales = null;
            }

            $pofsale = $this->Orders->Pofsales->find('all')->where(['warehouse_id' => $this->Auth->user('defaultwh'), 'pofstype_id' => 3])->last();
        } else {
            $pofsales = null;
            $pofsale = $this->Orders->Pofsales->get($this->Auth->User(['pofsale'])->id);
        }
        //récuperer le entrepot
        $packselects = [];
        $warehouse = $this->Orders->Users->Whusers->Warehouses->find('all')->where(['warehouse_id' => $pofsale->warehouse_id, 'whnature_id' => 1, 'whtype_id' => 2])->last();
        foreach ($categories as $key => $category) {
            $empQuery = $this->Orders->Customers->find();
            $empQuery->contain(['Zones.Cities', 'Zones.Parentzones', 'Customertypes'])
                ->leftJoinWith('Orders.Orderpacks')
                ->where(['Orderpacks.loyaltypointgift_id IS ' => NULL, 'Customers.id' => $userinfos->id, 'Customers.statut >=' => 1])
                ->select([
                    'Customers.id',
                    'Customers.code',
                    'Customers.name',
                    'Customers.phone',
                    'Customers.adresse',
                    'Customers.zone_id',
                    'Customers.customertype_id',
                    'Customertypes.id',
                    'Customertypes.title',
                    'Customers.statut',
                    'loyaltypoints_sum' => $empQuery->newExpr(
                        'SUM(CASE WHEN Orders.ordertype_id = 1 AND Orders.statut = 6 AND Orderpacks.statut = 6 THEN Orderpacks.loyaltypoints * Orderpacks.quantity ELSE 0 END) - '
                        . 'SUM(CASE WHEN Orders.ordertype_id = 2 AND Orders.statut = 6 THEN Orderpacks.loyaltypoints * Orderpacks.quantity ELSE 0 END)'
                    )
                ])->group(['Customers.id']);
            $packs = $this->Orders->Orderpacks->Packs->find('all')->contain([
                'Packunites.Unites.Parentunites',
                'Prices' => function ($q) use ($userinfos, $pofsale) {
                    return $q->where(['Prices.customertype_id' => $userinfos->customertype_id, 'Prices.tarif_id IS ' => NULL]);
                }
            ])->where(['Packs.category_id' => $category->id, 'Packs.loyaltypoints <= ' => $empQuery->first()->loyaltypoints_sum, ['OR' => [['Packs.statut' => 1], ['Packs.statut' => 3]]]]);
            $packselect = [];
            foreach ($packs as $key => $pack) {
                if ($pack->gstock == 0) {
                    foreach ($pack->packunites as $key2 => $packunite) {
                        if ($packunite->statut == 1) {
                            $packselect[$pack->id]['id'] = $pack->id;
                            $packselect[$pack->id]['title'] = $pack->title . ' (' . $packunite->unite->abrev . ')';
                            $packselect[$pack->id]['quantity'] = 10000;
                            $packselect[$pack->id]['disponible'] = 10000;
                            $packselect[$pack->id]['loyaltypoints'] = $pack->loyaltypoints;
                            $packselect[$pack->id]['qtepercs'] = $packunite->quantity;
                            $packselect[$pack->id]['carsac'] = $packunite->unite->abrev;
                            $packselect[$pack->id]['piecekg'] = $packunite->unite->parentunite->abrev;
                            $packselect[$pack->id][1]['price'] = $pack->prices[0]->price;
                            $packselect[$pack->id][0]['price'] = $pack->prices[0]->price * $packunite->quantity;
                        } elseif ($packunite->statut == 2) {
                            $packselect[$pack->id]['id'] = $pack->id;
                            $packselect[$pack->id]['title'] = $pack->title . ' (' . $packunite->unite->abrev . ')';
                            $packselect[$pack->id]['quantity'] = 10000;
                            $packselect[$pack->id]['disponible'] = 10000;
                            $packselect[$pack->id]['loyaltypoints'] = $pack->loyaltypoints;
                            $packselect[$pack->id]['qtepercs'] = $packunite->quantity;
                            $packselect[$pack->id]['carsac'] = $packunite->unite->abrev;
                            $packselect[$pack->id]['piecekg'] = $packunite->unite->parentunite->abrev;
                            $packselect[$pack->id][0]['price'] = $pack->prices[0]->price * $packunite->quantity;
                        } else {
                            $packselect[$pack->id]['id'] = $pack->id;
                            $packselect[$pack->id]['title'] = $pack->title . ' (' . $packunite->unite->parentunite->abrev . ')';
                            $packselect[$pack->id]['quantity'] = 10000;
                            $packselect[$pack->id]['disponible'] = 10000;
                            $packselect[$pack->id]['loyaltypoints'] = $pack->loyaltypoints;
                            $packselect[$pack->id]['qtepercs'] = $packunite->quantity;
                            $packselect[$pack->id]['carsac'] = $packunite->unite->abrev;
                            $packselect[$pack->id]['piecekg'] = $packunite->unite->parentunite->abrev;
                            $packselect[$pack->id][1]['price'] = $pack->prices[0]->price;
                        }
                    }
                } else {

                    //récuperer les produits de l'entrepot
                    $whproduct = $this->Orders->Orderpacks->Packs->Whproducts->find('all')->where(['item_id' => $pack->id, 'item_type' => 'Pack', 'warehouse_id' => $warehouse->id])->last();

                    if (isset($whproduct)) {
                        $quantity = $whproduct->quantity;
                        $orders = $this->Orders->find('all')->contain([
                            'Orderpacks' => function ($q) use ($pack) {
                                return $q->where(['Orderpacks.pack_id' => $pack->id]);
                            }
                        ])->where(['Orders.statut' => 1, 'Orders.pofsale_id' => $pofsale->id]);
                        foreach ($orders as $order) {
                            foreach ($order->orderpacks as $orderpack) {
                                $quantity -= $orderpack->quantity;
                            }
                        }

                        $slips = $this->Orders->Slips->find('all')->contain([
                            'Slipproducts' => function ($q) use ($pack) {
                                return $q->where(['Slipproducts.pack_id' => $pack->id]);
                            }
                        ])->where(['Slips.statut' => 1, 'Slips.warehouse_id' => $this->Auth->user('defaultwh')]);
                        foreach ($slips as $slip) {
                            foreach ($slip->slipproducts as $slipproduct) {
                                $quantity -= $slipproduct->quantity;
                            }
                        }
                        foreach ($pack->packunites as $key2 => $packunite) {
                            if ($packunite->statut == 1) {
                                $packselect[$pack->id]['id'] = $pack->id;
                                $packselect[$pack->id]['title'] = $pack->title . ' (' . $packunite->unite->abrev . ')';
                                $packselect[$pack->id]['quantity'] = intVal($quantity);
                                $packselect[$pack->id]['disponible'] = intVal($quantity);
                                $packselect[$pack->id]['loyaltypoints'] = $pack->loyaltypoints;
                                $packselect[$pack->id]['qtepercs'] = $packunite->quantity;
                                $packselect[$pack->id]['carsac'] = $packunite->unite->abrev;
                                $packselect[$pack->id]['piecekg'] = $packunite->unite->parentunite->abrev;
                                $packselect[$pack->id][1]['price'] = $pack->prices[0]->price;
                                $packselect[$pack->id][0]['price'] = $pack->prices[0]->price * $packunite->quantity;
                            } elseif ($packunite->statut == 2) {
                                $packselect[$pack->id]['id'] = $pack->id;
                                $packselect[$pack->id]['title'] = $pack->title . ' (' . $packunite->unite->abrev . ')';
                                $packselect[$pack->id]['quantity'] = intVal($quantity);
                                $packselect[$pack->id]['disponible'] = intVal($quantity);
                                $packselect[$pack->id]['loyaltypoints'] = $pack->loyaltypoints;
                                $packselect[$pack->id]['qtepercs'] = $packunite->quantity;
                                $packselect[$pack->id]['carsac'] = $packunite->unite->abrev;
                                $packselect[$pack->id]['piecekg'] = $packunite->unite->parentunite->abrev;
                                $packselect[$pack->id][0]['price'] = $pack->prices[0]->price * $packunite->quantity;
                            } else {
                                $packselect[$pack->id]['id'] = $pack->id;
                                $packselect[$pack->id]['title'] = $pack->title . ' (' . $packunite->unite->parentunite->abrev . ')';
                                $packselect[$pack->id]['quantity'] = intVal($quantity);
                                $packselect[$pack->id]['disponible'] = intVal($quantity);
                                $packselect[$pack->id]['loyaltypoints'] = $pack->loyaltypoints;
                                $packselect[$pack->id]['qtepercs'] = $packunite->quantity;
                                $packselect[$pack->id]['carsac'] = $packunite->unite->abrev;
                                $packselect[$pack->id]['piecekg'] = $packunite->unite->parentunite->abrev;
                                $packselect[$pack->id][1]['price'] = $pack->prices[0]->price;
                            }
                        }
                    }
                }
            }
            $packselected = $packselect;
            if ($userinfos->customertype_id == 2) {
                foreach ($packselect as $key => $packunite) {
                    if ($packunite['disponible'] <= 0) {
                        unset($packselected[$key]);
                    } elseif ($packunite['quantity'] > $packunite['disponible']) {
                        unset($packselected[$key]);
                    }
                }
            }
            $packselects[] = ['category' => $category->title, 'packs' => $packselected];
            $max = $empQuery->first()->loyaltypoints_sum;
        }
        $this->set(compact('userinfos', 'categories', 'pofsale', 'pofsales', 'avoir', 'max', 'packselects'));

    }
    public function usercontact($ordertype_id = null)
    {
        //$this->request->allowMethod('ajax');
        $keyword = $this->request->getQuery('keyword');

        $avoir = $this->request->getQuery('avoir');
        $userinfos = $this->Orders->Customers->find('all')->contain(['Zones.Cities', 'Customertypes'])->where(['Customers.id' => $keyword, 'Customers.statut >= ' => 1])->last();
        $categories = $this->Orders->Orderpacks->Packs->Categories->find('all')->where(['statut' => 1, 'company_id' => $this->Auth->user('company_id')]);
        if ($avoir == 'gift') {
            $categories = $this->Orders->Orderpacks->Packs->Categories->find('all')->where(['statut' => 1, 'category_id' => 8]);
        }
        $q = [];
        if ($this->Auth->user('role_id') == 1 || $this->Auth->user('role_id') == 2 || $this->Auth->user('role_id') == 7 || $this->Auth->user('role_id') == 8) {
            /*
                $zone=$this->Orders->Users->Zoneusers->Zones->get([$userinfos->zone_id]);
                $zoneusers=$this->Orders->Customers->Zones->Zoneusers->find('all')->contain(['Users'=>function($q){ return $q->where(['Users.role_id'=>5]);},'Users.Pofsusers'])->where(['Zoneusers.company_id'=>$this->Auth->user('company_id'),'Zoneusers.zone_id'=>$zone->zone_id])->group('user_id');

                $pofsale = $this->Orders->Pofsales->find('all');
                foreach($zoneusers as $key=>$zoneuser){
                    foreach($zoneuser->user->pofsusers as $key1=>$pofsuser){
                        $q['OR'][$key]=[['id'=>$pofsuser->pofsale_id]];
                    }
                }
                $pofsale->where($q);
                $pofsale->order(['Pofsales.pofstype_id'=>'ASC'])->last(); 


                if(empty($q)){
                    $pofsale=null;
                }
                $pofsale=$pofsale->last();

            */

            if ($userinfos->customertype_id == 4) {
                $pofsales = $this->Orders->Pofsales->find('list')->where(['pofstype_id' => 3]);
            } else {
                $pofsales = null;
            }

            $pofsale = $this->Orders->Pofsales->find('all')->where(['warehouse_id' => $this->Auth->user('defaultwh'), 'pofstype_id' => 3])->last();
        } else {
            $pofsales = null;
            $pofsale = $this->Orders->Pofsales->get($this->Auth->User(['pofsale'])->id);
        }
        //récuperer le entrepot
        $packselects = [];
        $warehouse = $this->Orders->Users->Whusers->Warehouses->find('all')->where(['warehouse_id' => $pofsale->warehouse_id, 'whnature_id' => 1, 'whtype_id' => 2])->last();
        foreach ($categories as $key => $category) {
            if ($avoir == 'gift') {
                $empQuery = $this->Orders->Customers->find();
                $empQuery->contain(['Zones.Cities', 'Zones.Parentzones', 'Customertypes'])
                    ->leftJoinWith('Orders.Orderpacks')
                    ->where(['Customers.id' => $userinfos->id, 'Customers.statut >=' => 1, 'Orderpacks.loyaltypointgift_id IS ' => NULL])
                    ->select([
                        'Customers.id',
                        'Customers.code',
                        'Customers.name',
                        'Customers.phone',
                        'Customers.adresse',
                        'Customers.zone_id',
                        'Customers.customertype_id',
                        'Customertypes.id',
                        'Customertypes.title',
                        'Customers.statut',
                        'loyaltypoints_sum' => $empQuery->newExpr(
                            'SUM(CASE WHEN Orders.ordertype_id = 1 AND Orders.statut = 6 AND Orderpacks.statut = 6 THEN Orderpacks.loyaltypoints * Orderpacks.quantity ELSE 0 END) - '
                            . 'SUM(CASE WHEN Orders.ordertype_id = 2 AND Orders.statut = 6 THEN Orderpacks.loyaltypoints * Orderpacks.quantity ELSE 0 END)'
                        )
                    ])->group(['Customers.id']);
                $packs = $this->Orders->Orderpacks->Packs->find('all')->contain([
                    'Packunites.Unites.Parentunites',
                    'Prices' => function ($q) use ($userinfos, $pofsale) {
                        return $q->where(['Prices.customertype_id' => $userinfos->customertype_id, 'Prices.tarif_id IS ' => NULL]);
                    }
                ])->where(['Packs.category_id' => $category->id, 'Packs.loyaltypoints <= ' => $empQuery->first()->loyaltypoints_sum, ['OR' => [['Packs.statut' => 1], ['Packs.statut' => 3]]]]);
            } else {
                if ($userinfos->customertype_id !== 4) {
                    $subCategories = $this->Orders->Orderpacks->Packs->Categories->find('all')->where(['statut' => 1, 'category_id' => $category->id]);
                    $categoryIds = [];
                    foreach ($subCategories as $key => $subCategory) {
                        $categoryIds[] = $subCategory->id;
                    }
                    if ($categoryIds == []) {
                        $packs = $this->Orders->Orderpacks->Packs->find('all')->where(['Packs.id' => 0]);
                    } else {
                        $packs = $this->Orders->Orderpacks->Packs->find('all')->contain([
                            'Packunites.Unites.Parentunites',
                            'Prices' => function ($q) use ($userinfos, $pofsale) {
                                return $q->where(['Prices.customertype_id' => $userinfos->customertype_id, 'Prices.tarif_id IS ' => NULL]);
                            }
                        ])->where(['Packs.category_id IN' => $categoryIds, ['OR' => [['Packs.statut' => 1], ['Packs.statut' => 3]]]]);
                    }
                } else {
                    $packs = $this->Orders->Orderpacks->Packs->find('all')->contain([
                        'Packunites.Unites.Parentunites',
                        'Prices' => function ($q) use ($userinfos, $pofsale) {
                            return $q->where(['Prices.customertype_id' => $userinfos->customertype_id, 'Prices.tarif_id IS ' => NULL]);
                        }
                    ])->where(['Packs.category_id' => $category->id]);
                }
            }
            $packselect = [];
            if ($avoir == "avoir") {
                foreach ($packs as $key => $pack) {
                    foreach ($pack->packunites as $key2 => $packunite) {
                        if ($packunite->statut == 1) {
                            $packselect[$pack->id]['id'] = $pack->id;
                            $packselect[$pack->id]['title'] = $pack->title . ' (' . $packunite->unite->parentunite->abrev . ')';
                            $packselect[$pack->id]['quantity'] = 10000;
                            $packselect[$pack->id]['disponible'] = 10000;
                            $packselect[$pack->id]['qtepercs'] = $packunite->quantity;
                            $packselect[$pack->id]['carsac'] = $packunite->unite->abrev;
                            $packselect[$pack->id]['piecekg'] = $packunite->unite->parentunite->abrev;
                            $packselect[$pack->id][1]['price'] = $pack->prices[0]->price;
                            $packselect[$pack->id][0]['price'] = $pack->prices[0]->price * $packunite->quantity;
                        } elseif ($packunite->statut == 2) {
                            $packselect[$pack->id]['id'] = $pack->id;
                            $packselect[$pack->id]['title'] = $pack->title . ' (' . $packunite->unite->abrev . ')';
                            $packselect[$pack->id]['quantity'] = 10000;
                            $packselect[$pack->id]['disponible'] = 10000;
                            $packselect[$pack->id]['qtepercs'] = $packunite->quantity;
                            $packselect[$pack->id]['carsac'] = $packunite->unite->abrev;
                            $packselect[$pack->id]['piecekg'] = $packunite->unite->parentunite->abrev;
                            $packselect[$pack->id][0]['price'] = $pack->prices[0]->price * $packunite->quantity;
                        } else {
                            $packselect[$pack->id]['id'] = $pack->id;
                            $packselect[$pack->id]['title'] = $pack->title . ' (' . $packunite->unite->parentunite->abrev . ')';
                            $packselect[$pack->id]['quantity'] = 10000;
                            $packselect[$pack->id]['disponible'] = 10000;
                            $packselect[$pack->id]['qtepercs'] = $packunite->quantity;
                            $packselect[$pack->id]['carsac'] = $packunite->unite->abrev;
                            $packselect[$pack->id]['piecekg'] = $packunite->unite->parentunite->abrev;
                            $packselect[$pack->id][1]['price'] = $pack->prices[0]->price;
                        }
                    }
                }
            } else {
                foreach ($packs as $key => $pack) {
                    if ($pack->gstock == 0) {
                        foreach ($pack->packunites as $key2 => $packunite) {
                            if ($packunite->statut == 1) {
                                $packselect[$pack->id]['id'] = $pack->id;
                                $packselect[$pack->id]['title'] = $pack->title . ' (' . $packunite->unite->abrev . ')';
                                $packselect[$pack->id]['quantity'] = 10000;
                                $packselect[$pack->id]['disponible'] = 10000;
                                $packselect[$pack->id]['qtepercs'] = $packunite->quantity;
                                $packselect[$pack->id]['carsac'] = $packunite->unite->abrev;
                                $packselect[$pack->id]['piecekg'] = $packunite->unite->parentunite->abrev;
                                $packselect[$pack->id][1]['price'] = $pack->prices[0]->price;
                                $packselect[$pack->id][0]['price'] = $pack->prices[0]->price * $packunite->quantity;
                            } elseif ($packunite->statut == 2) {
                                $packselect[$pack->id]['id'] = $pack->id;
                                $packselect[$pack->id]['title'] = $pack->title . ' (' . $packunite->unite->abrev . ')';
                                $packselect[$pack->id]['quantity'] = 10000;
                                $packselect[$pack->id]['disponible'] = 10000;
                                $packselect[$pack->id]['qtepercs'] = $packunite->quantity;
                                $packselect[$pack->id]['carsac'] = $packunite->unite->abrev;
                                $packselect[$pack->id]['piecekg'] = $packunite->unite->parentunite->abrev;
                                $packselect[$pack->id][0]['price'] = $pack->prices[0]->price * $packunite->quantity;
                            } else {
                                $packselect[$pack->id]['id'] = $pack->id;
                                $packselect[$pack->id]['title'] = $pack->title . ' (' . $packunite->unite->parentunite->abrev . ')';
                                $packselect[$pack->id]['quantity'] = 10000;
                                $packselect[$pack->id]['disponible'] = 10000;
                                $packselect[$pack->id]['qtepercs'] = $packunite->quantity;
                                $packselect[$pack->id]['carsac'] = $packunite->unite->abrev;
                                $packselect[$pack->id]['piecekg'] = $packunite->unite->parentunite->abrev;
                                $packselect[$pack->id][1]['price'] = $pack->prices[0]->price;
                            }
                        }
                    } else {
                        //récuperer les produits de l'entrepot
                        $whproduct = $this->Orders->Orderpacks->Packs->Whproducts->find('all')->where(['item_id' => $pack->id, 'item_type' => 'Pack', 'warehouse_id' => $warehouse->id])->last();
                        if (isset($whproduct)) {
                            $quantity = $whproduct->quantity;
                            $orders = $this->Orders->find('all')->contain([
                                'Orderpacks' => function ($q) use ($pack) {
                                    return $q->where(['Orderpacks.pack_id' => $pack->id]);
                                }
                            ])->where(['Orders.statut' => 1, 'Orders.pofsale_id' => $pofsale->id]);
                            foreach ($orders as $order) {
                                foreach ($order->orderpacks as $orderpack) {
                                    $quantity -= $orderpack->quantity;
                                }
                            }

                            $slips = $this->Orders->Slips->find('all')->contain([
                                'Slipproducts' => function ($q) use ($pack) {
                                    return $q->where(['Slipproducts.item_id' => $pack->id, 'Slipproducts.item_type' => 'Pack']);
                                }
                            ])->where(['Slips.statut' => 1, 'Slips.warehouse_id' => $this->Auth->user('defaultwh')]);
                            foreach ($slips as $slip) {
                                foreach ($slip->slipproducts as $slipproduct) {
                                    $quantity -= $slipproduct->quantity;
                                }
                            }
                            foreach ($pack->packunites as $key2 => $packunite) {
                                if ($packunite->statut == 1) {
                                    $packselect[$pack->id]['id'] = $pack->id;
                                    $packselect[$pack->id]['title'] = $pack->title . ' (' . $packunite->unite->abrev . ')';
                                    $packselect[$pack->id]['quantity'] = intVal($quantity);
                                    $packselect[$pack->id]['disponible'] = intVal($quantity);
                                    $packselect[$pack->id]['qtepercs'] = $packunite->quantity;
                                    $packselect[$pack->id]['carsac'] = $packunite->unite->abrev;
                                    $packselect[$pack->id]['piecekg'] = $packunite->unite->parentunite->abrev;
                                    $packselect[$pack->id][1]['price'] = $pack->prices[0]->price;
                                    $packselect[$pack->id][0]['price'] = $pack->prices[0]->price * $packunite->quantity;
                                } elseif ($packunite->statut == 2) {
                                    $packselect[$pack->id]['id'] = $pack->id;
                                    $packselect[$pack->id]['title'] = $pack->title . ' (' . $packunite->unite->abrev . ')';
                                    $packselect[$pack->id]['quantity'] = intVal($quantity);
                                    $packselect[$pack->id]['disponible'] = intVal($quantity);
                                    $packselect[$pack->id]['qtepercs'] = $packunite->quantity;
                                    $packselect[$pack->id]['carsac'] = $packunite->unite->abrev;
                                    $packselect[$pack->id]['piecekg'] = $packunite->unite->parentunite->abrev;
                                    $packselect[$pack->id][0]['price'] = $pack->prices[0]->price * $packunite->quantity;
                                } else {
                                    $packselect[$pack->id]['id'] = $pack->id;
                                    $packselect[$pack->id]['title'] = $pack->title . ' (' . $packunite->unite->parentunite->abrev . ')';
                                    $packselect[$pack->id]['quantity'] = intVal($quantity);
                                    $packselect[$pack->id]['disponible'] = intVal($quantity);
                                    $packselect[$pack->id]['qtepercs'] = $packunite->quantity;
                                    $packselect[$pack->id]['carsac'] = $packunite->unite->abrev;
                                    $packselect[$pack->id]['piecekg'] = $packunite->unite->parentunite->abrev;
                                    $packselect[$pack->id][1]['price'] = $pack->prices[0]->price;
                                }
                            }
                        }
                    }
                }
            }
            $packselected = $packselect;
            if ($userinfos->customertype_id == 2) {
                foreach ($packselect as $key => $packunite) {
                    if ($packunite['disponible'] <= 0) {
                        unset($packselected[$key]);
                    } elseif ($packunite['quantity'] > $packunite['disponible']) {
                        unset($packselected[$key]);
                    }
                }
            }
            $packselects[] = ['category' => $category->title, 'packs' => $packselect];
        }

        $this->set(compact('userinfos', 'categories', 'pofsale', 'pofsales', 'avoir', 'packselects'));
    }

    public function products($avoir = null)
    {

        //$this->request->allowMethod('ajax');
        $category = $this->request->getQuery('category');
        $pofsaleid = $this->request->getQuery('pofsale');
        $pofsale = $this->Orders->Pofsales->get($pofsaleid);
        $packs = $this->Orders->Orderpacks->Packs->find('all')->contain(['Packunites.Unites.Parentunites'])->where(['Packs.category_id' => $category, 'Packs.statut' => 1]);
        $packselect = [];
        if ($avoir) {
            foreach ($packs as $key => $pack) {
                foreach ($pack->packunites as $key2 => $packunite) {
                    if ($packunite->statut == 1) {
                        $packselect[$pack->id][$packunite->unite->parentunite->id]['id'] = $pack->id;
                        $packselect[$pack->id][$packunite->unite->parentunite->id]['title'] = $pack->title . ' (' . $packunite->unite->parentunite->abrev . ')';
                        $packselect[$pack->id][$packunite->unite->parentunite->id]['quantity'] = 10000;
                        $packselect[$pack->id][$packunite->unite->parentunite->id]['disponible'] = 10000;

                        $packselect[$pack->id][$packunite->unite->id]['id'] = $pack->id;
                        $packselect[$pack->id][$packunite->unite->id]['title'] = $pack->title . ' (' . $packunite->unite->abrev . ')';
                        $packselect[$pack->id][$packunite->unite->id]['quantity'] = 10000;
                        $packselect[$pack->id][$packunite->unite->id]['disponible'] = 10000;
                    } elseif ($packunite->statut == 2) {
                        $packselect[$pack->id][$packunite->unite->id]['id'] = $pack->id;
                        $packselect[$pack->id][$packunite->unite->id]['title'] = $pack->title . ' (' . $packunite->unite->abrev . ')';
                        $packselect[$pack->id][$packunite->unite->id]['quantity'] = 10000;
                        $packselect[$pack->id][$packunite->unite->id]['disponible'] = 10000;
                    } else {
                        $packselect[$pack->id][$packunite->unite->parentunite->id]['id'] = $pack->id;
                        $packselect[$pack->id][$packunite->unite->parentunite->id]['title'] = $pack->title . ' (' . $packunite->unite->parentunite->abrev . ')';
                        $packselect[$pack->id][$packunite->unite->parentunite->id]['quantity'] = 10000;
                        $packselect[$pack->id][$packunite->unite->parentunite->id]['disponible'] = 10000;
                    }
                }
            }
        } else {
            foreach ($packs as $key => $pack) {
                if ($pack->gstock == 0) {
                    foreach ($pack->packunites as $key2 => $packunite) {
                        if ($packunite->statut == 1) {
                            $packselect[$pack->id][$packunite->unite->parentunite->id]['id'] = $pack->id;
                            $packselect[$pack->id][$packunite->unite->parentunite->id]['title'] = $pack->title . ' (' . $packunite->unite->parentunite->abrev . ')';
                            $packselect[$pack->id][$packunite->unite->parentunite->id]['quantity'] = 10000;
                            $packselect[$pack->id][$packunite->unite->parentunite->id]['disponible'] = 10000;

                            $packselect[$pack->id][$packunite->unite->id]['id'] = $pack->id;
                            $packselect[$pack->id][$packunite->unite->id]['title'] = $pack->title . ' (' . $packunite->unite->abrev . ')';
                            $packselect[$pack->id][$packunite->unite->id]['quantity'] = 10000;
                            $packselect[$pack->id][$packunite->unite->id]['disponible'] = 10000;
                        } elseif ($packunite->statut == 2) {
                            $packselect[$pack->id][$packunite->unite->id]['id'] = $pack->id;
                            $packselect[$pack->id][$packunite->unite->id]['title'] = $pack->title . ' (' . $packunite->unite->abrev . ')';
                            $packselect[$pack->id][$packunite->unite->id]['quantity'] = 10000;
                            $packselect[$pack->id][$packunite->unite->id]['disponible'] = 10000;
                        } else {
                            $packselect[$pack->id][$packunite->unite->parentunite->id]['id'] = $pack->id;
                            $packselect[$pack->id][$packunite->unite->parentunite->id]['title'] = $pack->title . ' (' . $packunite->unite->parentunite->abrev . ')';
                            $packselect[$pack->id][$packunite->unite->parentunite->id]['quantity'] = 10000;
                            $packselect[$pack->id][$packunite->unite->parentunite->id]['disponible'] = 10000;
                        }
                    }
                } else {
                    //récuperer le entrepot
                    $warehouse = $this->Orders->Users->Whusers->Warehouses->find('all')->where(['warehouse_id' => $pofsale->warehouse_id, 'whnature_id' => 1, 'whtype_id' => 2])->last();

                    //récuperer les produits de l'entrepot
                    $whproduct = $this->Orders->Orderpacks->Packs->Whproducts->find('all')->where(['item_id' => $pack->id, 'item_type' => 'Pack', 'warehouse_id' => $warehouse->id])->last();
                    if (isset($whproduct)) {
                        foreach ($pack->packunites as $key2 => $packunite) {
                            if ($packunite->statut == 1) {
                                $packselect[$pack->id][$packunite->unite->parentunite->id]['id'] = $pack->id;
                                $packselect[$pack->id][$packunite->unite->parentunite->id]['title'] = $pack->title . ' (' . $packunite->unite->parentunite->abrev . ')';
                                $packselect[$pack->id][$packunite->unite->parentunite->id]['quantity'] = intVal($whproduct->quantity);
                                $packselect[$pack->id][$packunite->unite->parentunite->id]['disponible'] = intVal($whproduct->quantity);

                                $packselect[$pack->id][$packunite->unite->id]['id'] = $pack->id;
                                $packselect[$pack->id][$packunite->unite->id]['title'] = $pack->title . ' (' . $packunite->unite->abrev . ')';
                                $packselect[$pack->id][$packunite->unite->id]['quantity'] = intVal($whproduct->quantity / $packunite->quantity);
                                $packselect[$pack->id][$packunite->unite->id]['disponible'] = intVal($whproduct->quantity / $packunite->quantity);
                            } elseif ($packunite->statut == 2) {
                                $packselect[$pack->id][$packunite->unite->id]['id'] = $pack->id;
                                $packselect[$pack->id][$packunite->unite->id]['title'] = $pack->title . ' (' . $packunite->unite->abrev . ')';
                                $packselect[$pack->id][$packunite->unite->id]['quantity'] = intVal($whproduct->quantity / $packunite->quantity);
                                $packselect[$pack->id][$packunite->unite->id]['disponible'] = intVal($whproduct->quantity / $packunite->quantity);
                            } else {
                                $packselect[$pack->id][$packunite->unite->parentunite->id]['id'] = $pack->id;
                                $packselect[$pack->id][$packunite->unite->parentunite->id]['title'] = $pack->title . ' (' . $packunite->unite->parentunite->abrev . ')';
                                $packselect[$pack->id][$packunite->unite->parentunite->id]['quantity'] = intVal($whproduct->quantity);
                                $packselect[$pack->id][$packunite->unite->parentunite->id]['disponible'] = intVal($whproduct->quantity);
                            }
                        }
                    }
                }
            }
            /*
            $ordersininstances=$this->Orders->Orderpacks->find('all')->where(['OR'=>[['Orderpacks.statut'=>1],['Orderpacks.statut'=>2],['Orderpacks.statut'=>3],['Orderpacks.statut'=>4]]])->contain(['Packs.Packproducts']);

            foreach ($ordersininstances as $key => $orderpack) {
                foreach ($orderpack->pack->packproducts as $key1 => $packproduct) {
                    if($orderpack->pack->gstock==0){
                        $packselect[$orderpack->pack->id][0]['id']=$orderpack->pack->id;
                        $packselect[$orderpack->pack->id][0]['title']=$orderpack->pack->title;
                        $packselect[$orderpack->pack->id][$packproduct->product_id]['quantity']=10000;
                        $packselect[$orderpack->pack->id][$packproduct->product_id]['disponible']=10000;
                    }else{
                        if(isset($packselect[$orderpack->pack->id])){
                            $packselect[$orderpack->pack->id][0]['id']=$orderpack->pack->id;
                            $packselect[$orderpack->pack->id][0]['title']=$orderpack->pack->title;
                            $packselect[$orderpack->pack->id][$packproduct->product_id]['disponible']-=$orderpack->quantity;
                        }
                    }
                }
            }*/
        }
        $packselected = $packselect;
        foreach ($packselect as $key => $pack) {
            foreach ($pack as $keys => $packunite) {
                if ($packunite['disponible'] < 0) {
                    unset($packselected[$key]);
                } elseif ($packunite['quantity'] > $packunite['disponible']) {
                    unset($packselected[$key]);
                }
            }
        }

        $this->set(compact('packselected', 'avoir'));
    }

    public function product($avoir = null)
    {
        //$this->request->allowMethod('ajax');
        $packid = substr($this->request->getQuery('packid'), 0, -1);
        $uniteid = substr($this->request->getQuery('packid'), -1);
        $unite = $this->Orders->Orderpacks->Packs->Packunites->Unites->get($uniteid, [
            'contain' => [
                'Packunites' => function ($q) use ($packid) {
                    return $q->where(['pack_id' => $packid]);
                }
            ]
        ]);
        $customerid = $this->request->getQuery('customerid');
        $pofsaleid = $this->request->getQuery('pofsaleid');
        $pofsale = $this->Orders->Pofsales->get($pofsaleid);
        $customer = $this->Orders->Customers->get($customerid)->customertype_id;
        $pack = $this->Orders->Orderpacks->Packs->find('all')->contain([
            'Prices' => function ($q) use ($customer) {
                return $q->where(['Prices.customertype_id' => $customer]);
            },
            'Prices.Trancheprices.Tranches'
        ])
            ->order(['title' => 'ASC'])
            ->where(['Packs.id' => $packid])->last();

        if ($avoir) {
            $quantity = 1000;
            $whnatures = $this->Orders->Orderpacks->Whnatures->find('list');
            $this->set(compact('avoir', 'pack', 'quantity', 'whnatures', 'unite'));
        } else {
            $quantity = null;

            $warehouse = $this->Orders->Users->Whusers->Warehouses->find('all')->where(['warehouse_id' => $pofsale->warehouse_id, 'whnature_id' => 1, 'whtype_id' => 2])->last()->id;

            $packs = $this->Orders->Orderpacks->Packs->find('all')->contain([
                'Whproducts' => function ($q) use ($warehouse) {
                    return $q->where(['Whproducts.warehouse_id' => $warehouse]);
                }
            ])->where(['Packs.id' => $packid])->last();

            foreach ($packs->whproducts as $key => $whproduct) {
                $quantity = intval($whproduct->quantity);
            }
            /*$ordersininstances=$this->Orders->Orderpacks->find('all')->where(['OR'=>[['statut'=>1],['statut'=>2],['statut'=>3],['statut'=>4]],'pack_id'=>$packid]);
            foreach ($ordersininstances as $key => $orderpack) {
                $quantity-=$orderpack->quantity;
            }  */
            $this->set(compact('avoir', 'pack', 'quantity', 'unite'));
        }
    }

    public function search($ordertype_id = 1)
    {
        $this->response = $this->response
            ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->withHeader('Pragma', 'no-cache')
            ->withHeader('Expires', '0');

        $page = $this->request->getData('pagination.page');
        $pages = $this->request->getData('pagination.pages');
        $perpage = $this->request->getData('pagination.perpage');
        $total = $this->request->getData('pagination.total');
        $field = $this->request->getData('sort.field'); // Column name
        $sort = $this->request->getData('sort.sort'); // Column name

        $columnName = $this->request->getData('sort.field'); // Column name
        $columnSort = $this->request->getData('sort.sort'); // Column name
        $searchValue = strtolower($this->request->getData('query.generalSearch')); // Search value
        $searchUser = strtolower($this->request->getData('query.User')); // Search value
        $searchStatus = strtolower($this->request->getData('query.status')); // Search value
        $searchDate = strtolower($this->request->getData('query.date')); // Search value

        switch ($columnName) {
            case 'user':
                $columnName = "Users.firstname";
                break;
            case 'code':
                $columnName = "Orders.code";
                break;
            case 'customer':
                $columnName = "Customers.name";
                break;
            case 'pofsale':
                $columnName = "Pofsales.title";
                break;
            case 'created':
                $columnName = "Orders.created";
                break;
            case 'status':
                $columnName = "Orders.statut";
                break;
            default:
                $columnName = "Orders.created";
                $columnSort = "desc";
                break;
        }
        $pos = stripos($searchDate, ";");
        $dateend = substr($searchDate, $pos + 1);
        $datestart = substr($searchDate, 0, $pos);
        ## Total number of records with filtering
        $sel = $this->Orders->find('all')->contain(['Shippings', 'Users', 'Customers.Zones', 'Pofsales.Pofstypes', 'Orderpacks'])->where(['Orders.company_id' => $this->Auth->user('company_id'), 'ordertype_id' => $ordertype_id]);
        $empQuery = $this->Orders->find('all')->contain(['Shippings', 'Users', 'Customers.Zones', 'Pofsales.Pofstypes', 'Orderpacks'])->where(['Orders.company_id' => $this->Auth->user('company_id'), 'ordertype_id' => $ordertype_id]);
        $empQuery->order([$columnName => $columnSort]);
        $warehouse = $this->Orders->Pofsales->Warehouses->get($this->Auth->user('defaultwh'), [
            'contain' => [
                'Subwarehouses.Pofsales',
                'Subwarehouses' => function ($q) {
                    return $q->where(['Subwarehouses.whtype_id' => 3]);
                }
            ]
        ]);
        $qwh = [];
        if ($warehouse->subwarehouses) {
            foreach ($warehouse->subwarehouses as $subwarehouse) {
                foreach ($subwarehouse->pofsales as $pofsale) {
                    $qwh['OR'][$pofsale->id] = ['Orders.pofsale_id' => $pofsale->id];
                }
            }
        }

        $pofsale = $this->Orders->Pofsales->find('all')->where(['warehouse_id' => $this->Auth->user('defaultwh')]);
        $qwh['OR'][$pofsale->last()->id] = ['Orders.pofsale_id' => $pofsale->last()->id];

        $sel->where([$qwh]);
        $empQuery->where([$qwh]);

        if ($this->Auth->user('role_id') == 3 || $this->Auth->user('role_id') == 6 || $this->Auth->user('role_id') == 5) {
            $empQuery->where(['Orders.user_id' => $this->Auth->user('id')]);
            $sel->where(['Orders.user_id' => $this->Auth->user('id')]);
        }

        if ($datestart && $dateend) {
            $empQuery->where(['DATE(Orders.created) <= ' => $dateend, 'DATE(Orders.created) >= ' => $datestart]);
            $sel->where(['DATE(Orders.created) <= ' => $dateend, 'DATE(Orders.created) >= ' => $datestart]);
        }
        if ($searchUser) {
            $empQuery->where(['Orders.user_id' => $searchUser]);
            $sel->where(['Orders.user_id' => $searchUser]);
        }
        if ($searchStatus) {
            $empQuery->where(['Orders.statut' => $searchStatus]);
            $sel->where(['Orders.statut' => $searchStatus]);
        }

        if ($searchValue != '') {
            $sel->where([
                "OR" => [
                    ['Customers.name LIKE' => '%' . $searchValue . '%'],
                    ['lower(Customers.name) LIKE' => '%' . $searchValue . '%'],
                    ['lower(Orders.code) LIKE' => '%' . $searchValue . '%'],
                    ['Users.firstname LIKE' => '%' . $searchValue . '%'],
                    ['lower(Users.firstname) LIKE' => '%' . $searchValue . '%'],
                    ['Users.lastname LIKE' => '%' . $searchValue . '%'],
                    ['lower(Users.lastname) LIKE' => '%' . $searchValue . '%']
                ]
            ]);
            $empQuery->where([
                "OR" => [
                    ['Customers.name LIKE' => '%' . $searchValue . '%'],
                    ['lower(Customers.name) LIKE' => '%' . $searchValue . '%'],
                    ['lower(Orders.code) LIKE' => '%' . $searchValue . '%'],
                    ['Users.firstname LIKE' => '%' . $searchValue . '%'],
                    ['lower(Users.firstname) LIKE' => '%' . $searchValue . '%'],
                    ['Users.lastname LIKE' => '%' . $searchValue . '%'],
                    ['lower(Users.lastname) LIKE' => '%' . $searchValue . '%']
                ]
            ]);
        }
        $empQuery->limit($perpage);
        $empQuery->page($page);
        $sel->select(['count' => $sel->func()->count('*')]);
        $total = $sel->last()->count;
        $data = [];
        foreach ($empQuery as $key => $order) {
            $totals = 0;
            $loyaltyPointsTotal = 0;
            foreach ($order->orderpacks as $orderpack) {
                if ($orderpack->statut != 8) {
                    $totals += $orderpack->price * $orderpack->quantity;
                    if (empty($orderpack->loyaltypointgift_id)) {
                        $loyaltyPointsTotal += (float) $orderpack->quantity * (float) $orderpack->loyaltypoints;
                    }
                }
            }
            $username = $order->user ? ($order->user->firstname . ' ' . $order->user->lastname) : 'N/A';
            $customerName = $order->customer ? $order->customer->name : 'Client inconnu';
            $zoneName = ($order->customer && $order->customer->zone) ? $order->customer->zone->title : '';
            $customerDisplay = $zoneName ? ($customerName . '-' . $zoneName) : $customerName;
            $typeId = ($order->pofsale && $order->pofsale->pofstype) ? $order->pofsale->pofstype->id : 1;
            $data[] = [
                "id" => $order->id,
                "user" => $username,
                "code" => $order->code . " (" . $loyaltyPointsTotal . "pts)",
                "customer" => $customerDisplay,
                "type" => $order->ordertype_id,
                "created" => $order->created->nice('Africa/Casablanca', 'fr-FR'),
                "total" => $totals,
                "loyaltypoints" => $loyaltyPointsTotal,
                "status" => $order->statut,
                "actions" => null

            ];
        }

        $response = [
            "meta" => [
                'page' => $page,
                'pages' => $pages,
                'perpage' => $perpage,
                'total' => $total,
                'sort' => $sort
            ],
            'data' => $data,
        ];
        $this->autoRender = false;
        echo json_encode($response);
        exit;
    }

    /**
     * Point-in-Polygon validation helper (Ray-Casting Algorithm)
     */
    private function _isPointInPolygon($point, $polygon)
    {
        $x = $point['lat'];
        $y = $point['lng'];
        
        $inside = false;
        $numVertices = count($polygon);
        for ($i = 0, $j = $numVertices - 1; $i < $numVertices; $j = $i++) {
            $xi = $polygon[$i]['lat'];
            $yi = $polygon[$i]['lng'];
            $xj = $polygon[$j]['lat'];
            $yj = $polygon[$j]['lng'];
            
            $intersect = (($yi > $y) != ($yj > $y))
                && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);
            if ($intersect) {
                $inside = !$inside;
            }
        }
        
        return $inside;
    }
}
