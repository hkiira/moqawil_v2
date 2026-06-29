<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Cake\I18n\Time;



/**

 * Exitslips Controller

 *

 * @property \App\Model\Table\ExitslipsTable $Exitslips

 *

 * @method \App\Model\Entity\Exitslip[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])

 1: En préparation
 2: Validé

 */

class ExitslipsController extends AppController

{

    public function validation($id)
    {

        $exitslip = $this->Exitslips->get($id, ['contain' => ['Exitsliptypes']]);

        $this->set(compact('exitslip'));
    }

    public function cancel($id = null)
    {
        $exit = $this->Exitslips->get($id);

        $whnormale = $this->Exitslips->Warehouses->find('all')->where(['warehouse_id' => $exit->warehouse_id, 'whnature_id' => 1, 'whtype_id' => 2]);
        $exitslip = $this->Exitslips->get($id, ['contain' => ['Shippings.Orders.Orderpacks.Packs.Whproducts' => function ($q) use ($whnormale) {
            return $q->where(['Whproducts.warehouse_id' => $whnormale->last()->id]);
        }]]);
        $products = [];
        $exit->statut = 5;
        $this->Exitslips->save($exit);
        foreach ($exitslip->shippings as $shipping) {
            $shippupdate = $this->Exitslips->Shippings->get($shipping->id);
            $shippupdate->statut = 6;
            $this->Exitslips->Shippings->save($shippupdate);
            foreach ($shipping->orders as $order) {
                $orderupdate = $this->Exitslips->Shippings->Orders->get($order->id);
                $orderupdate->statut = 8;
                $this->Exitslips->Shippings->Orders->save($orderupdate);
                foreach ($order->orderpacks as $orderpack) {
                    $orderpackupdate = $this->Exitslips->Shippings->Orders->Orderpacks->get($orderpack->id);
                    $orderpackupdate->statut = 8;
                    $this->Exitslips->Shippings->Orders->Orderpacks->save($orderpackupdate);
                    foreach ($orderpack->pack->whproducts as $whproduct) {
                        $products[$orderpack->id]['whproductd']['pack'] = $orderpack->pack->title;
                        $products[$orderpack->id]['whproductd']['id'] = $whproduct->id;
                        $products[$orderpack->id]['whproductd']['quantity'] = $orderpack->quantity;
                    }
                }
            }
        }

        $this->inventaire($exitslip->id);
        foreach ($products as $key => $whpack) {
            $whproductd = $this->Exitslips->Warehouses->Whproducts->get($whpack['whproductd']['id']);
            $whproductd->quantity += $whpack['whproductd']['quantity'];
            $this->Exitslips->Warehouses->Whproducts->save($whproductd);
        }
        $this->inventaire($exitslip->id);
        $this->Flash->success(__('La commande est annulée.'));
        return $this->redirect(['controller' => 'Orders', 'action' => 'index']);
    }

    public function instancebn($exitslipid = null)
    {

        $draw = $_GET['draw'];
        $row = $_GET['start'];
        $rowperpage = $_GET['length']; // Rows display per page
        $columnIndex = $_GET['order'][0]['column']; // Column index
        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc
        $searchValue = $_GET['search']['value']; // Search value
        switch ($columnName) {
            case 'user':
                $columnName = "Orders.firstname";
                break;
            case 'customer':
                $columnName = "Customers.name";
                break;
            case 'carrier':
                $columnName = "Carriers.title";
                break;
            case 'city':

                $columnName = "Cities.title";

                break;

            default:

                $columnName = "Orders.id";

                break;
        }

        $exitslip = $this->Exitslips->get($exitslipid, ['contain' => ['Slips.Orders']]);



        $q = [];

        $warehouse = null;

        foreach ($exitslip->slips as $key => $slip) {

            foreach ($slip->orders as $key1 => $order) {

                $q['OR'][$order->id] = ['Orderpacks.order_id' => $order->id];
            }

            $warehouse = $this->Exitslips->Slips->Warehouses->find('all')->where(['warehouse_id' => $slip->warehouse_id, 'whnature_id' => $slip->whnature_id])->last();
        }





        $sel = $this->Exitslips->Slips->Orders->Orderpacks->find('all')->contain(['Orders.Customers', 'Packs'])->where(['Orderpacks.statut' => 9]);



        $empQuery = $this->Exitslips->Slips->Orders->Orderpacks->find('all')->contain(['Orders.Customers', 'Packs'])->where(['Orderpacks.statut' => 9]);

        $sel->where([$q]);

        $empQuery->where([$q]);





        $sel->select(['count' => $sel->func()->count('*')]);

        $totalRecords = $sel->last()->count;

        if ($row == 0) {

            $empQuery->limit($rowperpage);
        } else {

            $empQuery->limit($rowperpage);

            $empQuery->page(($row / $rowperpage) + 1);
        }



        if ($searchValue != '') {

            $or = [

                ['Packs.title LIKE' => '%' . $searchValue . '%'],

                ['Orders.code LIKE' => '%' . $searchValue . '%'],

            ];

            $sel->where(['OR' => $or]);

            $empQuery->where(['OR' => $or]);

            $empQuery->page(1);
        }

        if ($draw = 0) {

            $empQuery->page(1);
        }

        ## Total number of records with filtering

        $totalRecordwithFilter = $sel->last()->count;

        ## Fetch records

        $data = [];

        //"statut"=>'',

        foreach ($empQuery as $key => $orderpack) {

            $packproduct = $this->Exitslips->Slips->Users->Orders->Orderpacks->Packs->Packproducts->find('all')->contain(['Products.Whproducts' => function ($q) use ($warehouse) {
                return $q->where(['Whproducts.warehouse_id' => $warehouse->id]);
            }])->where(['Packproducts.pack_id' => $orderpack->pack->id])->last();

            $quantity = 0;

            foreach ($packproduct->product->whproducts as $key => $whproduct) {

                $product1 = intval($whproduct->quantity / $packproduct->quantity);

                if ($product1 < $quantity || $quantity == null) {

                    $quantity = $product1;
                }
            }

            $action = '<a data-id="' . $orderpack->id . '" class="rmvord btn btn-sm btn-danger waves-effect waves-light" >-</a>';



            $data[] = [

                "product" => $orderpack->pack->title,

                "productdis" => $quantity,

                "customer" => $orderpack->order->customer->name . ' CMD:' . $orderpack->order->code,

                "quantity" => '<input type="number" name="' . $orderpack->id . '" class="form-control" value="' . $orderpack->quantity . '" id="' . $orderpack->id . '">',

                "action" => $action

            ];
        }



        $response = [

            'rowperpage' => $rowperpage,

            'row' => $row,

            'iTotalRecords' => $totalRecords,

            'iTotalDisplayRecords' => $totalRecordwithFilter,

            'aaData' => $data,

        ];

        $this->autoRender = false;

        echo json_encode($response);

        exit;
    }



    public function addedbn($exitslipid = null)
    {

        $draw = $_GET['draw'];

        $row = $_GET['start'];

        $rowperpage = $_GET['length']; // Rows display per page

        $columnIndex = $_GET['order'][0]['column']; // Column index

        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name

        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc

        $searchValue = $_GET['search']['value']; // Search value

        switch ($columnName) {

            case 'user':

                $columnName = "Orders.firstname";

                break;

            case 'customer':

                $columnName = "Customers.name";

                break;

            case 'carrier':

                $columnName = "Carriers.title";

                break;

            case 'city':

                $columnName = "Cities.title";

                break;

            default:

                $columnName = "Orders.id";

                break;
        }

        $exitslip = $this->Exitslips->get($exitslipid, ['contain' => ['Slips.Orders']]);



        $q = [];

        $warehouse = null;

        foreach ($exitslip->slips as $key => $slip) {

            foreach ($slip->orders as $key1 => $order) {

                $q['OR'] = [$order->id => ['Orderpacks.order_id' => $order->id]];
            }

            $warehouse = $this->Exitslips->Slips->Warehouses->find('all')->where(['warehouse_id' => $slip->warehouse_id, 'whnature_id' => $slip->whnature_id])->last();
        }



        $sel = $this->Exitslips->Slips->Orders->Orderpacks->find('all')->contain(['Orders.Customers', 'Packs'])->where(['Orderpacks.statut' => 11]);



        $empQuery = $this->Exitslips->Slips->Orders->Orderpacks->find('all')->contain(['Orders.Customers', 'Packs'])->where(['Orderpacks.statut' => 11]);

        $sel->where([$q]);

        $empQuery->where([$q]);





        $sel->select(['count' => $sel->func()->count('*')]);

        $totalRecords = $sel->last()->count;

        if ($row == 0) {

            $empQuery->limit($rowperpage);
        } else {

            $empQuery->limit($rowperpage);

            $empQuery->page(($row / $rowperpage) + 1);
        }



        if ($searchValue != '') {

            $or = [

                ['Orderpacks.id LIKE' => '%' . $searchValue . '%'],

                ['Orders.code LIKE' => '%' . $searchValue . '%'],

            ];

            $sel->where(['OR' => $or]);

            $empQuery->where(['OR' => $or]);

            $empQuery->page(1);
        }

        if ($draw = 0) {

            $empQuery->page(1);
        }

        ## Total number of records with filtering

        $totalRecordwithFilter = $sel->last()->count;

        ## Fetch records

        $data = [];

        foreach ($empQuery as $key => $orderpack) {



            $action = '<a data-id="' . $orderpack->id . '" class="addo btn btn-sm btn-success waves-effect waves-light" >+</a>';



            $data[] = [

                "product" => $orderpack->pack->title,

                "productdis" => $orderpack->quantity,

                "customer" => $orderpack->order->customer->name . ' CMD:' . $orderpack->order->code,

                "quantity" => '<input type="number" name="' . $orderpack->id . '" class="form-control" value="' . $orderpack->quantity . '" id="' . $orderpack->id . '">',

                "action" => $action

            ];
        }



        $response = [

            'rowperpage' => $rowperpage,

            'row' => $row,

            'iTotalRecords' => $totalRecords,

            'iTotalDisplayRecords' => $totalRecordwithFilter,

            'aaData' => $data,

        ];

        $this->autoRender = false;

        echo json_encode($response);

        exit;
    }





    public function rmvbn($slipid = null)
    {

        // récuperer lidentifiant du pack et la quantité

        $orderpackid = json_decode($_GET['ordid'], true);

        $qte = intval(json_decode($_GET['qte'], true));



        $orderpack = $this->Exitslips->Slips->Orders->Orderpacks->get($orderpackid, ['contain' => ['Orderpackproducts']]);

        $quantity = $orderpack->quantity;



        $orderpacksout = $this->Exitslips->Slips->Orders->Orderpacks->find('all')->contain(['Orderpackproducts'])->where(['Orderpacks.pack_id' => $orderpack->pack_id, 'Orderpacks.order_id' => $orderpack->order_id, 'Orderpacks.statut' => 11]);

        if ($orderpacksout->toArray()) {

            if ($qte <= 0) {

                echo 'changer la quantité';
            } elseif ($qte > $quantity) {

                echo 'quantite doit etre moins de la quantite commande';
            } elseif ($qte == $quantity) {

                $orderpackouteddatas['id'] = $orderpacksout->last()->id;

                $orderpackouteddatas['quantity'] = $orderpacksout->last()->quantity + intVal($qte);

                foreach ($orderpacksout->last()->orderpackproducts as $key => $orderpackproduct) {

                    $qtypackproduct = $orderpackproduct->quantity / $orderpacksout->last()->quantity;

                    $orderpackouteddatas['orderpackproducts'][$orderpackproduct->id]['id'] = $orderpackproduct->id;

                    $orderpackouteddatas['orderpackproducts'][$orderpackproduct->id]['quantity'] = $orderpackproduct->quantity + (intVal($qte) * $qtypackproduct);
                }

                $orderpackouted = $this->Exitslips->Slips->Orders->Orderpacks->get($orderpackouteddatas['id'], ['contain' => ['Orderpackproducts']]);

                $orderpackouted = $this->Exitslips->Slips->Orders->Orderpacks->patchEntity($orderpackouted, $orderpackouteddatas, ['associated' => ['Orderpackproducts']]);

                if ($this->Exitslips->Slips->Orders->Orderpacks->delete($orderpack)) {

                    $this->Exitslips->Slips->Orders->Orderpacks->save($orderpackouted);
                }
            } else {

                $orderpackouteddatas['id'] = $orderpacksout->last()->id;

                $orderpackouteddatas['quantity'] = $orderpacksout->last()->quantity + intVal($qte);

                foreach ($orderpacksout->last()->orderpackproducts as $key => $orderpackproduct) {

                    $qtypackproduct = $orderpackproduct->quantity / $orderpacksout->last()->quantity;

                    $orderpackouteddatas['orderpackproducts'][$orderpackproduct->id]['id'] = $orderpackproduct->id;

                    $orderpackouteddatas['orderpackproducts'][$orderpackproduct->id]['quantity'] = $orderpackproduct->quantity + (intVal($qte) * $qtypackproduct);
                }

                $orderpackouted = $this->Exitslips->Slips->Orders->Orderpacks->get($orderpackouteddatas['id'], ['contain' => ['Orderpackproducts']]);

                $orderpackouted = $this->Exitslips->Slips->Orders->Orderpacks->patchEntity($orderpackouted, $orderpackouteddatas, ['associated' => ['Orderpackproducts']]);



                if ($this->Exitslips->Slips->Orders->Orderpacks->save($orderpackouted)) {

                    $orderpackdatas = ['id' => $orderpack->id];



                    foreach ($orderpack->orderpackproducts as $key => $orderpackproduct) {

                        // la quantité des produits dans le pack

                        $qtypackproduct = $orderpackproduct->quantity / $orderpack->quantity;

                        $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['id'] = $orderpackproduct->id;

                        $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['quantity'] = $quantity - intVal($qte) * $qtypackproduct;
                    }

                    $orderpackdatas['quantity'] = $quantity - $qte;

                    $orderpack = $this->Exitslips->Slips->Orders->Orderpacks->patchEntity($orderpack, $orderpackdatas, ['associated' => ['Orderpackproducts']]);



                    $this->Exitslips->Slips->Orders->Orderpacks->save($orderpack);
                }
            }
        } else {

            if ($qte <= 0) {

                echo 'changer la quantité';
            } elseif ($qte > $quantity) {

                echo 'quantite doit etre moins de la quantite commande';
            } elseif ($qte == $quantity) {

                $orderpackdatas = ['id' => $orderpack->id, 'statut' => 11];

                foreach ($orderpack->orderpackproducts as $key => $orderpackproduct) {

                    $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['id'] = $orderpackproduct->id;

                    $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['statut'] = 11;
                }

                $orderpack = $this->Exitslips->Slips->Orders->Orderpacks->patchEntity($orderpack, $orderpackdatas, ['associated' => ['Orderpackproducts']]);

                $this->Exitslips->Slips->Orders->Orderpacks->save($orderpack);
            } else {

                $neworderpack = $this->Exitslips->Slips->Orders->Orderpacks->newEntity();

                $neworderpackdatas['pack_id'] = $orderpack->pack_id;

                $neworderpackdatas['quantity'] = $qte;

                $neworderpackdatas['order_id'] = $orderpack->order_id;

                $neworderpackdatas['price'] = $orderpack->price;

                $neworderpackdatas['tranche_id'] = $orderpack->tranche_id;

                $neworderpackdatas['commission'] = $orderpack->commission;

                $neworderpackdatas['statut'] = 11;

                $neworderpackdatas['company_id'] = $orderpack->company_id;

                $neworderpackdatas['user_id'] = $this->Auth->user('id');

                foreach ($orderpack->orderpackproducts as $key => $orderpackproduct) {

                    // la quantité des produits dans le pack

                    $qtypackproduct = $orderpackproduct->quantity / $orderpack->quantity;

                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['id'] = $orderpackproduct->id;

                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['quantity'] = intVal($qte) * $qtypackproduct;

                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['buyingprice'] = $orderpackproduct->buyingprice;

                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['statut'] = $neworderpackdatas['statut'];

                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['company_id'] = $orderpackproduct->company_id;

                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['user_id'] = $this->Auth->user('id');
                }

                $neworderpack = $this->Exitslips->Slips->Orders->Orderpacks->patchEntity($neworderpack, $neworderpackdatas, ['associated' => ['Orderpackproducts']]);



                if ($this->Exitslips->Slips->Orders->Orderpacks->save($neworderpack)) {



                    $orderpackdatas = ['id' => $orderpack->id];

                    foreach ($orderpack->orderpackproducts as $key => $orderpackproduct) {

                        // la quantité des produits dans le pack

                        $qtypackproduct = $orderpackproduct->quantity / $orderpack->quantity;

                        $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['id'] = $orderpackproduct->id;

                        $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['quantity'] = $quantity - intVal($qte) * $qtypackproduct;
                    }

                    $orderpackdatas['quantity'] = $quantity - $qte;

                    $orderpack = $this->Exitslips->Slips->Orders->Orderpacks->patchEntity($orderpack, $orderpackdatas, ['associated' => ['Orderpackproducts']]);



                    $this->Exitslips->Slips->Orders->Orderpacks->save($orderpack);
                }
            }
        }

        $this->autoRender = false;
    }



    public function addbn($slipid = null)

    {

        // récuperer lidentifiant du pack et la quantité

        $orderpackid = json_decode($_GET['ordid'], true);

        $qte = intval(json_decode($_GET['qte'], true));



        $orderpack = $this->Exitslips->Slips->Orders->Orderpacks->get($orderpackid, ['contain' => ['Orderpackproducts', 'Orders']]);

        $quantity = $orderpack->quantity;



        $orderpacksin = $this->Exitslips->Slips->Orders->Orderpacks->find('all')->contain(['Orderpackproducts'])->where(['Orderpacks.pack_id' => $orderpack->pack_id, 'Orderpacks.order_id' => $orderpack->order_id, 'Orderpacks.statut' => 9]);

        if ($orderpacksin->toArray()) {

            if ($qte <= 0) {

                echo 'changer la quantité';
            } elseif ($qte > $quantity) {

                echo 'quantite doit etre moins de la quantite commande';
            } elseif ($qte == $quantity) {

                $orderpackouteddatas['id'] = $orderpacksin->last()->id;

                $orderpackouteddatas['quantity'] = $orderpacksin->last()->quantity + intVal($qte);

                $orderpackouteddatas['user_id'] = $orderpack->user_id;

                foreach ($orderpacksin->last()->orderpackproducts as $key => $orderpackproduct) {

                    $qtypackproduct = $orderpackproduct->quantity / $orderpacksin->last()->quantity;

                    $orderpackouteddatas['orderpackproducts'][$orderpackproduct->id]['id'] = $orderpackproduct->id;

                    $orderpackouteddatas['orderpackproducts'][$orderpackproduct->id]['user_id'] = $orderpack->user_id;

                    $orderpackouteddatas['orderpackproducts'][$orderpackproduct->id]['quantity'] = $orderpackproduct->quantity + (intVal($qte) * $qtypackproduct);
                }

                $orderpackouted = $this->Exitslips->Slips->Orders->Orderpacks->get($orderpackouteddatas['id'], ['contain' => ['Orderpackproducts']]);

                $orderpackouted = $this->Exitslips->Slips->Orders->Orderpacks->patchEntity($orderpackouted, $orderpackouteddatas, ['associated' => ['Orderpackproducts']]);

                if ($this->Exitslips->Slips->Orders->Orderpacks->delete($orderpack)) {

                    $this->Exitslips->Slips->Orders->Orderpacks->save($orderpackouted);
                }
            } else {

                $orderpackouteddatas['id'] = $orderpacksin->last()->id;

                $orderpackouteddatas['quantity'] = $orderpacksin->last()->quantity + intVal($qte);

                foreach ($orderpacksin->last()->orderpackproducts as $key => $orderpackproduct) {

                    $qtypackproduct = $orderpackproduct->quantity / $orderpacksin->last()->quantity;

                    $orderpackouteddatas['orderpackproducts'][$orderpackproduct->id]['id'] = $orderpackproduct->id;

                    $orderpackouteddatas['orderpackproducts'][$orderpackproduct->id]['quantity'] = $orderpackproduct->quantity + (intVal($qte) * $qtypackproduct);
                }

                $orderpackouted = $this->Exitslips->Slips->Orders->Orderpacks->get($orderpackouteddatas['id'], ['contain' => ['Orderpackproducts']]);

                $orderpackouted = $this->Exitslips->Slips->Orders->Orderpacks->patchEntity($orderpackouted, $orderpackouteddatas, ['associated' => ['Orderpackproducts']]);



                if ($this->Exitslips->Slips->Orders->Orderpacks->save($orderpackouted)) {

                    $orderpackdatas = ['id' => $orderpack->id];



                    foreach ($orderpack->orderpackproducts as $key => $orderpackproduct) {

                        // la quantité des produits dans le pack

                        $qtypackproduct = $orderpackproduct->quantity / $orderpack->quantity;

                        $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['id'] = $orderpackproduct->id;

                        $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['quantity'] = $quantity - intVal($qte) * $qtypackproduct;
                    }

                    $orderpackdatas['quantity'] = $quantity - $qte;

                    $orderpack = $this->Exitslips->Slips->Orders->Orderpacks->patchEntity($orderpack, $orderpackdatas, ['associated' => ['Orderpackproducts']]);



                    $this->Exitslips->Slips->Orders->Orderpacks->save($orderpack);
                }
            }
        } else {

            if ($qte <= 0) {

                echo 'changer la quantité';
            } elseif ($qte > $quantity) {

                echo 'quantite doit etre moins de la quantite commande';
            } elseif ($qte == $quantity) {

                $orderpackdatas = ['id' => $orderpack->id, 'statut' => 9, 'user_id' => $orderpack->order->user_id];

                foreach ($orderpack->orderpackproducts as $key => $orderpackproduct) {

                    $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['id'] = $orderpackproduct->id;

                    $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['statut'] = 9;

                    $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['user_id'] = $orderpack->order->user_id;
                }

                $orderpack = $this->Exitslips->Slips->Orders->Orderpacks->patchEntity($orderpack, $orderpackdatas, ['associated' => ['Orderpackproducts']]);

                $this->Exitslips->Slips->Orders->Orderpacks->save($orderpack);
            } else {

                $neworderpack = $this->Exitslips->Slips->Orders->Orderpacks->newEntity();

                $neworderpackdatas['pack_id'] = $orderpack->pack_id;

                $neworderpackdatas['quantity'] = $qte;

                $neworderpackdatas['order_id'] = $orderpack->order_id;

                $neworderpackdatas['price'] = $orderpack->price;

                $neworderpackdatas['tranche_id'] = $orderpack->tranche_id;

                $neworderpackdatas['commission'] = $orderpack->commission;

                $neworderpackdatas['statut'] = 9;

                $neworderpackdatas['company_id'] = $orderpack->company_id;

                $neworderpackdatas['user_id'] = $orderpack->order->user_id;

                foreach ($orderpack->orderpackproducts as $key => $orderpackproduct) {

                    // la quantité des produits dans le pack

                    $qtypackproduct = $orderpackproduct->quantity / $orderpack->quantity;

                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['id'] = $orderpackproduct->id;

                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['quantity'] = intVal($qte) * $qtypackproduct;

                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['buyingprice'] = $orderpackproduct->buyingprice;

                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['statut'] = $neworderpackdatas['statut'];

                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['company_id'] = $orderpackproduct->company_id;

                    $neworderpackdatas['orderpackproducts'][$orderpackproduct->id]['user_id'] = $orderpack->order->user_id;
                }

                $neworderpack = $this->Exitslips->Slips->Orders->Orderpacks->patchEntity($neworderpack, $neworderpackdatas, ['associated' => ['Orderpackproducts']]);

                if ($this->Exitslips->Slips->Orders->Orderpacks->save($neworderpack)) {



                    $orderpackdatas = ['id' => $orderpack->id];

                    foreach ($orderpack->orderpackproducts as $key => $orderpackproduct) {

                        // la quantité des produits dans le pack

                        $qtypackproduct = $orderpackproduct->quantity / $orderpack->quantity;

                        $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['id'] = $orderpackproduct->id;

                        $orderpackdatas['orderpackproducts'][$orderpackproduct->id]['quantity'] = $quantity - intVal($qte) * $qtypackproduct;
                    }

                    $orderpackdatas['quantity'] = $quantity - $qte;

                    $orderpack = $this->Exitslips->Slips->Orders->Orderpacks->patchEntity($orderpack, $orderpackdatas, ['associated' => ['Orderpackproducts']]);



                    $this->Exitslips->Slips->Orders->Orderpacks->save($orderpack);
                }
            }
        }

        $this->autoRender = false;
    }



    public function prints($id = null)
    {
        $this->loadModel('Customers');
        $distanceField = '(6371.0072 * acos (cos ( radians(:latitude) )
    * cos( radians( latitude ) )
    * cos( radians( longitude )
    - radians(:longitude) )
    + sin ( radians(:latitude) )
    * sin( radians( latitude ) )))';
        $empQuery = $this->Customers->find('all')
            ->select([
                'Customers.id',
                'Customers.code',
                'Customers.name',
                'Customers.phone',
                'Customers.adresse',
                'Customers.id',
                'Zones.id',
                'Zones.title',
                'Cities.title',
                'Customertypes.title',
                'Customertypes.id',
                'Customers.longitude',
                'Customers.latitude',
                'Customers.referral',
                'Customers.referred',
                'Customers.ice',
                'Customers.statut',
                'distance' => $distanceField
            ])
            ->bind(':latitude', 33.589261, 'float')
            ->bind(':longitude', -7.484916, 'float')
            ->contain(['Shippings.Orders.Users', 'Shippings' => function ($q) use ($id) {
                return $q->where(['Shippings.exitslip_id' => $id]);
            }, 'Shippings.Orders.Orderpacks.Packs.Packunites.Unites.Parentunites', 'Zones.Cities', 'Customertypes', 'Shippings.Orders.Orderpacks.Packs.Brands', 'Shippings.Orders.Orderpacks.Packs.Prices', 'Shippings.Orders.Orderpacks.Packs.Categories'])
            ->order(["distance" => "ASC"]);
        $increment = 0;
        $data = [];
        foreach ($empQuery as $customer) {
            foreach ($customer->shippings as $shipping) {
                foreach ($shipping->orders as $order) {
                    $data[$increment] = [
                        "id" => $order->id,
                        "code" => $order->code,
                        "user" => $order->user->firstname,
                        "date" => $order->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                        "statut" => $order->statut,
                    ];
                    $photo = $this->Customers->Photos->find('all')->where(['controleur' => 'customers', 'objectid' => $customer->id])->order(['created' => 'ASC'])->last();
                    $img = Router::Url('/') . 'webroot/img/unvailable.jpg';
                    if ($photo) {
                        $img = Router::Url('/') . $photo->dir . '/' . $photo->title;
                    }

                    $customerdata = [
                        "id" => $customer->id,
                        "name" => $customer->name . "",
                        "customertype" => ["id" => $customer->customertype->id, "title" => $customer->customertype->title . ""],
                        "zone" => ["id" => $customer->zone->id, "title" => $customer->zone->title . ""],
                        "adresse" => $customer->adresse . "",
                        "photo" => $img,
                        "phone" => $customer->phone . "",
                        "latitude" => $customer->latitude . "",
                        "longitude" => $customer->longitude . "",
                        "proximite" => $customer->distance * 1000,
                        "ice" => $customer->ice . "",
                        "city" => $customer->zone->city->title . "",
                        "statut" => $customer->statut,
                    ];
                    $data[$increment]["customer"] = $customerdata;
                    foreach ($order->orderpacks as $key1 => $orderpack) {
                        $data[$increment]["orderpacks"][$key1] = [
                            "id" => $orderpack->id,
                            "date" => $orderpack->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                            "price" => $orderpack->price,
                            "quantity" => $orderpack->quantity,
                            "statut" => $orderpack->statut,
                            "commissionpack" => ($orderpack->commissionpack) ? $orderpack->commissionpack : 0,
                        ];
                        $photo = $this->Customers->Orders->Orderpacks->Packs->Photos->find('all')->where(['controleur' => 'packs', 'objectid' => $orderpack->pack->id])->order(['created' => 'ASC'])->last();
                        $img = Router::Url('/') . 'webroot/img/unvailable.jpg';
                        $images = [];
                        if ($photo) {
                            $img = Router::Url('/') . $photo->dir . '/' . 'thumbnail160-' . $photo->photo;
                            $images[] = Router::Url('/') . $photo->dir . '/' . $photo->photo;
                        } else {
                            $images[] = $img;
                        }
                        $variants = [];
                        //Sac & Unité
                        if ($orderpack->pack->packunites[0]->statut == 1) {
                            $variants[0] = [
                                'id' => $orderpack->pack->packunites[0]->unite->id,
                                'title' => $orderpack->pack->packunites[0]->unite->title,
                                'quantity' => $orderpack->pack->packunites[0]->quantity,
                                'statut' => 1,
                            ];
                            $variants[1] = [
                                'id' => $orderpack->pack->packunites[0]->unite->parentunite->id,
                                'title' => $orderpack->pack->packunites[0]->unite->parentunite->title,
                                'quantity' => 1,
                                'statut' => 1,
                            ];
                            // Sac
                        } elseif ($orderpack->pack->packunites[0]->statut == 2) {

                            $variants[0] = [
                                'id' => $orderpack->pack->packunites[0]->unite->id,
                                'title' => $orderpack->pack->packunites[0]->unite->title,
                                'quantity' => $orderpack->pack->packunites[0]->quantity,
                                'statut' => 1,
                            ];
                            $variants[1] = [
                                'id' => $orderpack->pack->packunites[0]->unite->parentunite->id,
                                'title' => $orderpack->pack->packunites[0]->unite->parentunite->title,
                                'quantity' => 1,
                                'statut' => 0,
                            ];
                            //Unité
                        } else {
                            $variants[0] = [
                                'id' => $orderpack->pack->packunites[0]->unite->id,
                                'title' => $orderpack->pack->packunites[0]->unite->title,
                                'quantity' => $orderpack->pack->packunites[0]->quantity,
                                'statut' => 0,
                            ];
                            $variants[1] = [
                                'id' => $orderpack->pack->packunites[0]->unite->parentunite->id,
                                'title' => $orderpack->pack->packunites[0]->unite->parentunite->title,
                                'quantity' => 1,
                                'statut' => 1,
                            ];
                        }
                        $product = [
                            "id" => $orderpack->pack->id,
                            "code" => $orderpack->pack->code,
                            "title" => $orderpack->pack->title,
                            "price" => $orderpack->pack->prices[0]->price,
                            "pricemin" => $orderpack->pack->prices[0]->minp,
                            "pricemax" => $orderpack->pack->prices[0]->maxp,
                            "type" => $orderpack->pack->packunites[0]->statut,
                            "quantity" => 0,
                            "image" => $img,
                            "images" => $images,
                            "statut" => $orderpack->pack->statut,
                            "variants" => $variants,
                            "brand" => ["id" => $orderpack->pack->brand->id, "title" => $orderpack->pack->brand->title],
                            "category" => ["id" => $orderpack->pack->category->id, "title" => $orderpack->pack->category->title],
                        ];
                        $data[$increment]["orderpacks"][$key1]['product'] = $product;
                    }
                }
                $increment++;
            }
        }
        $exitslip = $this->Exitslips->get($id, ["contain" => ['Companies']]);
        ini_set('max_execution_time', '300');
        ini_set("pcre.backtrack_limit", "5000000");

        $this->set(compact('exitslip', 'data'));
    }



    public function export($datedebut = null, $datefin = null)
    {


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'QTE');
        $sheet->setCellValue('B1', 'CATEGORIE');
        $sheet->setCellValue('C1', 'REFERENCE');
        $sheet->setCellValue('D1', 'PRODUIT');
        $sheet->setCellValue('E1', 'PA');
        $sheet->setCellValue('F1', 'MONTANT ACHAT ( TTC )');
        $sheet->setCellValue('G1', 'PV');
        $sheet->setCellValue('H1', 'MONTANT VENTE ( TTC )');
        $sheet->setCellValue('I1', 'DATE');
        $sheet->setCellValue('J1', 'CLIENT');
        $sheet->setCellValue('K1', 'PREVENDEUR');
        $sheet->setCellValue('L1', 'BL/AV');
        $sheet->setCellValue('M1', 'BS');
        $sheet->setCellValue('N1', 'LIVREUR');
        $sheet->setCellValue('O1', '% Commission');
        $sheet->setCellValue('P1', 'Total commission');
        $sheet->setCellValue('Q1', 'Nature du Retour');

        $k = 0;
        //vente indirect
        $exitslips = $this->Exitslips->find()->contain(['Users', 'Shippings', 'Shippings.Customers', 'Shippings.Orders.Orderpacks.Packs.Categories', 'Shippings.Orders.Users', 'Shippings.Orders.Orderpacks.Orderpackproducts']);
        $exitslips->where(['AND' => ['DATE(Exitslips.created) <= ' => $datefin, 'DATE(Exitslips.created) >= ' => $datedebut, 'Exitslips.warehouse_id' => $this->Auth->user('defaultwh')]]);

        foreach ($exitslips as $key1 => $exitslip) {
            foreach ($exitslip->shippings as $key2 => $shipping) {
                foreach ($shipping->orders as $key3 => $order) {
                    foreach ($order->orderpacks as $key4 => $orderpack) {
                        $k += 1;
                        $sheet->setCellValue('A' . ($k + 1), $orderpack->quantity);
                        $sheet->setCellValue('B' . ($k + 1), $orderpack->pack->category->title);
                        $sheet->setCellValue('C' . ($k + 1), $orderpack->pack->code);
                        $sheet->setCellValue('D' . ($k + 1), $orderpack->pack->title);
                        $sheet->setCellValue('E' . ($k + 1), $orderpack->pack->buyingprice);
                        $sheet->setCellValue('F' . ($k + 1), $orderpack->pack->buyingprice * $orderpack->quantity);
                        $sheet->setCellValue('G' . ($k + 1), $orderpack->price);
                        $sheet->setCellValue('H' . ($k + 1), ($orderpack->price * $orderpack->quantity));
                        $sheet->setCellValue('I' . ($k + 1), $orderpack->created);
                        $sheet->setCellValue('J' . ($k + 1), $shipping->customer->name);
                        $sheet->setCellValue('K' . ($k + 1), $order->user->firstname . " " . $order->user->lastname);
                        $sheet->setCellValue('L' . ($k + 1), $shipping->code);
                        $sheet->setCellValue('M' . ($k + 1), $exitslip->code);
                        $sheet->setCellValue('N' . ($k + 1), $exitslip->user->firstname . " " . $exitslip->user->lastname);
                        $sheet->setCellValue('O' . ($k + 1), 1);
                        $sheet->setCellValue('P' . ($k + 1), (($orderpack->quantity * $orderpack->price) / 100));
                    }
                }
            }
        }
        //vente direct
        $vendeurSales = $this->Exitslips->Shippings->find('all')->contain(['Users', 'Customers', 'Orders.Orderpacks.Packs.Categories', 'Orders.Users', 'Orders.Orderpacks.Orderpackproducts']);
        $vendeurSales->where(['AND' => ['DATE(Shippings.created) <= ' => $datefin, 'DATE(Shippings.created) >= ' => $datedebut]]);
        $vendeurUsers = $this->Exitslips->Users->Whusers->find('all')->contain(['Users'])->where(['Users.role_id' => 3, 'Whusers.warehouse_id' => $this->Auth->user('defaultwh')]);
        $qvendeurs = [];
        foreach ($vendeurUsers as $whuser) {
            $qvendeurs['OR'][$whuser->id] = ['Shippings.user_id' => $whuser->user->id];
        }
        if ($qvendeurs) {
            $vendeurSales->where([$qvendeurs]);
        } else {
            $vendeurSales->where(['Shippings.user_id' => 0]);
        }
        foreach ($vendeurSales as $shipping) {
            foreach ($shipping->orders as $order) {
                foreach ($order->orderpacks as $orderpack) {
                    $k += 1;
                    $sheet->setCellValue('A' . ($k + 1), $orderpack->quantity);
                    $sheet->setCellValue('B' . ($k + 1), $orderpack->pack->category->title);
                    $sheet->setCellValue('C' . ($k + 1), $orderpack->pack->code);
                    $sheet->setCellValue('D' . ($k + 1), $orderpack->pack->title);
                    $sheet->setCellValue('E' . ($k + 1), $orderpack->pack->buyingprice);
                    $sheet->setCellValue('F' . ($k + 1), $orderpack->pack->buyingprice * $orderpack->quantity);
                    $sheet->setCellValue('G' . ($k + 1), $orderpack->price);
                    $sheet->setCellValue('H' . ($k + 1), ($orderpack->price * $orderpack->quantity));
                    $sheet->setCellValue('I' . ($k + 1), $orderpack->created);
                    $sheet->setCellValue('J' . ($k + 1), $shipping->customer->name);
                    $sheet->setCellValue('K' . ($k + 1), $order->user->firstname . " " . $order->user->lastname);
                    $sheet->setCellValue('L' . ($k + 1), $shipping->code);
                    $sheet->setCellValue('M' . ($k + 1), $shipping->code);
                    $sheet->setCellValue('N' . ($k + 1), $shipping->user->firstname . " " . $shipping->user->lastname);
                    $sheet->setCellValue('O' . ($k + 1), 1);
                    $sheet->setCellValue('P' . ($k + 1), (($orderpack->quantity * $orderpack->price) / 100));
                }
            }
        }

        //Retour des produits
        $warehouseG = $this->Exitslips->Warehouses->get($this->Auth->user('defaultwh'));
        $slips = $this->Exitslips->Warehouses->Slips->find('all')->contain(['Slipproducts.Packs.Categories', 'Users', 'Slipproducts.Whnatures'])->order(["Slips.id" => "DESC"])->where(['Slips.uservalidate IS NOT' => NULL, 'Slips.warehoused' => $warehouseG->id]);
        $slips->where(['Slips.sliptype_id' => 2]);

        if ($datedebut && $datefin) {
            $slips->where(['DATE(Slips.created) <= ' => $datefin, 'DATE(Slips.created) >= ' => $datedebut]);
        }

        foreach ($slips as $slip) {
            $warehousetitle = NULL;
            $warehoused = $this->Exitslips->Warehouses->get($slip->warehoused);
            $warehousedtitle = $warehoused->title;
            $warehouse = $this->Exitslips->Warehouses->get($slip->warehouse_id, ['contain' => ['Pofsales.Pofsusers.Users']]);
            $role_id = $warehouse->pofsales[0]->pofsusers[0]->user->role_id;
            $warehousetitle = $warehouse->pofsales[0]->pofsusers[0]->user->firstname . ' ' . $warehouse->pofsales[0]->pofsusers[0]->user->lastname;
            $uservalidate = $this->Exitslips->Users->get($slip->uservalidate);
            foreach ($slip->slipproducts as $slipproduct) {
                $k += 1;
                $sheet->setCellValue('A' . ($k + 1), $slipproduct->quantity);
                $sheet->setCellValue('B' . ($k + 1), $slipproduct->pack->category->title);
                $sheet->setCellValue('C' . ($k + 1), $slipproduct->pack->code);
                $sheet->setCellValue('D' . ($k + 1), $slipproduct->pack->title);
                $sheet->setCellValue('E' . ($k + 1), - ($slipproduct->pack->buyingprice));
                $sheet->setCellValue('F' . ($k + 1), - ($slipproduct->pack->buyingprice * $slipproduct->quantity));
                $sheet->setCellValue('G' . ($k + 1), - ($slipproduct->price));
                $sheet->setCellValue('H' . ($k + 1), - ($slipproduct->price * $slipproduct->quantity));
                $sheet->setCellValue('I' . ($k + 1), $slip->created);
                $sheet->setCellValue('J' . ($k + 1), "");
                if ($role_id == 3) {
                    $sheet->setCellValue('K' . ($k + 1), $warehousetitle);
                } else {
                    $sheet->setCellValue('K' . ($k + 1), $slip->user->firstname . " " . $slip->user->lastname);
                }
                $sheet->setCellValue('L' . ($k + 1), $slip->code);
                $sheet->setCellValue('M' . ($k + 1), "");
                $sheet->setCellValue('N' . ($k + 1), $warehousetitle);
                $sheet->setCellValue('O' . ($k + 1), 1);
                $sheet->setCellValue('P' . ($k + 1), - (($slipproduct->price * $slipproduct->quantity) / 100));
                if ($slipproduct->whnature_id == 99) {
                    $sheet->setCellValue('Q' . ($k + 1), "Rupture");
                } else {
                    $sheet->setCellValue('Q' . ($k + 1), $slipproduct->whnature->title);
                }
            }
        }
        $sheet->setCellValue('A' . ($k + 2), ("=SUM(A1:A" . ($k + 1) . ")"));
        $sheet->setCellValue('E' . ($k + 2), ("=SUM(E1:E" . ($k + 1) . ")"));
        $sheet->setCellValue('G' . ($k + 2), ("=SUM(G1:G" . ($k + 1) . ")"));
        $sheet->setCellValue('P' . ($k + 2), ("=SUM(P1:P" . ($k + 1) . ")"));

        $date = date('d-m-y-' . substr((string)microtime(), 1, 8));
        $date = str_replace(".", "", $date);
        $filename = "transaction_" . $date . ".xlsx";

        try {
            $writer = new Xlsx($spreadsheet);
            $writer->save($filename);
            $content = file_get_contents($filename);
        } catch (Exception $e) {
            exit($e->getMessage());
        }

        header("Content-Disposition: attachment; filename=" . $filename);
        unlink($filename);
        exit($content);
    }




    /**

     * Index method

     *

     * @return \Cake\Http\Response|null

     */

    public function index($id = null)
    {
        $whusers = $this->Exitslips->Users->Whusers->find('all')->contain(['Users' => function ($q) {
            return $q->where(['Users.statut' => 1, 'Users.role_id' => 6]);
        }])->where(['Whusers.warehouse_id' => $this->Auth->user('defaultwh')]);
        $users = [];
        foreach ($whusers as $whuser) {
            $users[$whuser->user->id] = $whuser->user->firstname . ' ' . $whuser->user->lastname;
        }
        $this->set(compact('id', 'users'));
    }



    /**

     * View method

     *

     * @param string|null $id Exitslip id.

     * @return \Cake\Http\Response|null

     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.

     */

    public function view($id = null)

    {

        $exitslip = $this->Exitslips->get($id, [

            'contain' => ['Companies', 'Users', 'Shippings'],

        ]);



        $this->set('exitslip', $exitslip);
    }


    public function inventaire($exitslipid = null)
    {
        $inventory = $this->Exitslips->Warehouses->Inventories->newEntity();
        $warehouse = $this->Exitslips->Warehouses->find('all')->where(['warehouse_id' => $this->Auth->user('defaultwh'), 'whnature_id' => 1, 'whtype_id' => 2])->last();

        $whproducts = $this->Exitslips->Warehouses->Whproducts->find('all')->where(['warehouse_id' => $warehouse->id]);
        $datas['warehouse_id'] = $this->Auth->user('defaultwh');
        $datas['whnature_id'] = 1;
        $datas['exitslip_id'] = $exitslipid;
        foreach ($whproducts as $key => $whproduct) {
            $datas['invproducts'][$whproduct->id]['pack_id'] = $whproduct->pack_id;
            $datas['invproducts'][$whproduct->id]['quantity'] = $whproduct->quantity;
            $datas['invproducts'][$whproduct->id]['statut'] = 1;
        }
        $inventory = $this->Exitslips->Warehouses->Inventories->patchEntity($inventory, $datas, ['associated' => ['Invproducts']]);

        $inventory->statut = 1;
        $code = $this->Exitslips->Warehouses->Inventories->Companies->Companycodes->find('all')->where(['controleur' => 'Inventories', 'company_id' => $this->Auth->user('company_id')])->last();
        $inventory->code = $code->prefixe . ($code->compteur + 1);
        $inventory->company_id = $this->Auth->user('company_id');
        $inventory->user_id = $this->Auth->user('id');

        if ($this->Exitslips->Warehouses->Inventories->save($inventory)) {
            $code->compteur += 1;
            $this->Exitslips->Companies->Companycodes->save($code);
        }
    }


    /**

     * Add method

     *

     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.

     */

    public function add()
    {

        $exitslip = $this->Exitslips->newEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            if (isset($data['zoneusers']) &&  $data['zoneusers']) {
                if ($data['user_id']) {
                    $userid = $data['user_id'];

                    $codeexit = $this->Exitslips->Companies->Companycodes->find('all')->where(['controleur' => 'Exitslips', 'company_id' => $this->Auth->user('company_id')])->last();
                    $codeplus = 1;
                    $codeshipping = $this->Exitslips->Companies->Companycodes->find('all')->where(['controleur' => 'Shippings', 'company_id' => $this->Auth->user('company_id')])->last();
                    $pofsale = $this->Exitslips->Users->Pofsusers->find('all')->contain(['Pofsales'])->where(['Pofsusers.user_id' => $userid]);

                    $warehousedepot = $this->Exitslips->Users->Pofsusers->Pofsales->Warehouses->get($this->Auth->user('defaultwh'), ['contain' => ['Subwarehouses' => function ($q) {
                        return $q->where(['Subwarehouses.whnature_id' => 1, 'Subwarehouses.whtype_id' => 2]);
                    }]]);
                    $warehouseuser = $this->Exitslips->Users->Pofsusers->Pofsales->Warehouses->get($pofsale->toArray()[0]['pofsale']['warehouse_id'], ['contain' => ['Subwarehouses' => function ($q) {
                        return $q->where(['Subwarehouses.whnature_id' => 1, 'Subwarehouses.whtype_id' => 2]);
                    }]]);
                    $dataslip = ['user_id' => $userid, 'uservalidate' => $this->Auth->user('id'), 'company_id' => $this->Auth->user('company_id'), 'code' => $codeexit->prefixe . '' . ($codeexit->compteur + 1), 'sliptype_id' => 5, 'warehouse_id' => $warehousedepot->id, 'warehoused' => $warehouseuser->id, 'whnature_id' => 1, 'statut' => 2];
                    $exitslip = $this->Exitslips->patchEntity($exitslip, $dataslip);
                    $products = [];
                    if ($this->Exitslips->save($exitslip)) {
                        $codeexit->compteur += 1;
                        $this->Exitslips->Companies->Companycodes->save($codeexit);
                        $this->inventaire($exitslip->id);
                        foreach ($data['zoneusers'] as $zoneuser) {
                            //récuperer les bon de livraison en attente
                            $orders = $this->Exitslips->Shippings->Orders->find('all')->where(['Orders.statut' => 1, 'Orders.user_id' => $zoneuser, "DATE(Orders.created) " => $data["created"]]);
                            //si y a des livraison en attente
                            if ($orders->toArray()) {
                                foreach ($orders as $order) {
                                    $orderdata = [];
                                    $orderupdate = $this->Exitslips->Shippings->Orders->get($order->id, ['contain' => ['Shippings', 'Orderpacks.Packs.Whproducts' => function ($q) use ($warehousedepot, $warehouseuser) {
                                        return $q->where(['OR' => [['Whproducts.warehouse_id' => $warehousedepot->subwarehouses[0]->id], ['Whproducts.warehouse_id' => $warehouseuser->subwarehouses[0]->id]]]);
                                    }]]);

                                    $orderdata['id'] = $orderupdate->id;
                                    $orderdata['statut'] = 5;
                                    if ($orderupdate->shipping) {
                                        $orderdata['shipping']['id'] = $orderupdate->shipping->id;
                                        $orderdata['shipping']['exitslip_id'] = $exitslip->id;
                                        $orderdata['shipping']['statut'] = 3;
                                    } else {
                                        $orderdata['shipping']['statut'] = 3;
                                        $orderdata['shipping']['company_id'] = $this->Auth->user('company_id');
                                        $orderdata['shipping']['user_id'] = $orderupdate->user_id;
                                        $orderdata['shipping']['exitslip_id'] = $exitslip->id;
                                        $orderdata['shipping']['code'] = $codeshipping->prefixe . '' . ($codeshipping->compteur + $codeplus);
                                        $orderdata['shipping']['customer_id'] = $orderupdate->customer_id;
                                        $orderdata['shipping']['warehouse_id'] = $this->Auth->user('defaultwh');
                                        $codeplus++;
                                    }
                                    $totalPayments=0;
                                    foreach ($orderupdate->orderpacks as $orderpack) {
                                        $orderdata['orderpacks'][$orderpack->id]['id'] = $orderpack->id;
                                        $orderdata['orderpacks'][$orderpack->id]['statut'] = 5;
                                        $totalPayments += $orderpack->price * $orderpack->quantity;
                                        foreach ($orderpack->pack->whproducts as $whproduct) {
                                            if ($whproduct->warehouse_id == $warehousedepot->subwarehouses[0]->id) {
                                                $products[$orderpack->id]['whproductd']['pack'] = $orderpack->pack->title;
                                                $products[$orderpack->id]['whproductd']['id'] = $whproduct->id;
                                                $products[$orderpack->id]['whproductd']['quantity'] = $orderpack->quantity;
                                            } else {
                                                $products[$orderpack->id]['whproductliv']['pack'] = $orderpack->pack->title;
                                                $products[$orderpack->id]['whproductliv']['id'] = $whproduct->id;
                                                $products[$orderpack->id]['whproductliv']['quantity'] = $orderpack->quantity;
                                            }
                                        }
                                    }
                                    // $orderPayments[]=[
                                    //     "payment_method_id"=>5,
                                    //     "amount"=>$totalPayments,
                                    //     "statut"=>1
                                    // ];
                                    // $orderdata['order_payments'] = $orderPayments;
                                    $orderupdate = $this->Exitslips->Shippings->Orders->patchEntity($orderupdate, $orderdata, ['associated' => ['Orderpacks','OrderPayments', 'Shippings']]);
                                    $this->Exitslips->Shippings->Orders->save($orderupdate);
                                }
                            }
                        }
                        foreach ($products as $key => $whpack) {
                            $whproductd = $this->Exitslips->Warehouses->Whproducts->get($whpack['whproductd']['id']);
                            $whproductliv = $this->Exitslips->Warehouses->Whproducts->get($whpack['whproductliv']['id']);
                            $whproductd->quantity -= $whpack['whproductd']['quantity'];
                            $whproductliv->quantity += $whpack['whproductliv']['quantity'];
                            $this->Exitslips->Warehouses->Whproducts->save($whproductliv);
                            $this->Exitslips->Warehouses->Whproducts->save($whproductd);
                        }
                        $this->inventaire($exitslip->id);
                    }
                    $this->Flash->success(__('Les bons de sortie ont étés enregistrés.'));
                    return $this->redirect(['action' => 'index', 5]);
                } else {
                    $this->Flash->error(__('Merci de sélectionner les livreurs avant la validation.'));
                    return $this->redirect(['action' => 'addsortie']);
                }
            } else {
                $this->Flash->error(__('Merci de sélectionner les secteurs avant la validation.'));
                return $this->redirect(['action' => 'addsortie']);
            }
        }



        //récupérer les commandes avec le statut en attente

        $orders = $this->Exitslips->Shippings->Orders->find('all')->contain(['Customers.Zones'])->where(['Orders.statut' => 1]);
        //récupérer selement les id des zones pour chercher les livreurs de ces zones

        $qzones = [];

        if ($orders->toArray()) {

            foreach ($orders as $key => $order) {
                if ($order->customer->zone) {
                    $qzones['OR'][$order->customer->zone->zone_id] = ['Zoneusers.zone_id' => $order->customer->zone->zone_id];
                }
            }
            //rechercher les livreurs qui ont les mêmes zones des commande en attente
            $dfwh = $this->Auth->user('defaultwh');

            $livreurs = $this->Exitslips->Users->find('all')->contain(['Whusers' => function ($q) use ($dfwh) {
                return $q->where(['Whusers.warehouse_id' => $dfwh]);
            }, 'Zoneusers'])->where(['role_id' => 6, 'company_id' => $this->Auth->user('company_id')]);


            $users = [];

            foreach ($livreurs as $livreur) {
                if ($livreur->whusers) {
                    if ($livreur->zoneusers) {

                        $users[$livreur->id] = $livreur->firstname . ' ' . $livreur->lastname;
                    }
                }
            }
        } else {

            $users = null;
        }

        $this->set(compact('exitslip', 'users'));
    }

    /**

     * Edit method

     *

     * @param string|null $id Exitslip id.

     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.

     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.

     */
    public function inventoryprint($exitslipid)
    {
        $exitslip = $this->Exitslips->get($exitslipid, ['contain' => ['Shippings.Orders.Orderpacks', 'Inventories.Invproducts.Packs.Packunites.Unites.Parentunites']]);

        $datas = [];
        foreach ($exitslip->inventories as $key => $inventory) {
            if ($key == 0) {
                foreach ($inventory->invproducts as $invproduct) {
                    $datas[$invproduct->pack_id]['pack'] = $invproduct->pack->title;
                    $datas[$invproduct->pack_id]['qtepercarton'] = $invproduct->pack->packunites[0]->quantity;
                    $datas[$invproduct->pack_id]['saccarton'] = $invproduct->pack->packunites[0]->unite->abrev;
                    $datas[$invproduct->pack_id]['unitekg'] = $invproduct->pack->packunites[0]->unite->parentunite->abrev;
                    $datas[$invproduct->pack_id]['stockdepart'] = $invproduct->quantity;
                    $datas[$invproduct->pack_id]['stockdepart'] = $invproduct->quantity;
                }
            } else {
                foreach ($inventory->invproducts as $invproduct) {
                    $datas[$invproduct->pack_id]['stockfinal'] = $invproduct->quantity;
                }
            }
        }
        foreach ($exitslip->shippings as $shipping) {
            foreach ($shipping->orders as $order) {
                foreach ($order->orderpacks as $orderpack) {
                    if (isset($datas[$orderpack->pack_id]['exitslip'])) {
                        $datas[$orderpack->pack_id]['exitslip'] += $orderpack->quantity;
                    } else {
                        $datas[$orderpack->pack_id]['exitslip'] = $orderpack->quantity;
                    }
                }
            }
        }
        $company = $this->Exitslips->Companies->find('all')->last();
        $exitslipdata = $this->Exitslips->get($exitslipid);

        ini_set('max_execution_time', '300');
        ini_set("pcre.backtrack_limit", "5000000");
        $this->set(compact('datas', 'company', 'exitslipdata'));
    }

    public function edit($id = null, $validate = null)

    {

        $exitslip = $this->Exitslips->get($id, [

            'contain' => ['Shippings.Orders.Orderpacks'],

        ]);

        $warehouse = $this->Exitslips->Warehouses->find('all')->where(['warehouse_id' => 1, 'whtype_id' => 2, 'whnature_id' => 1]);
        $pofsuser = $this->Exitslips->Warehouses->Pofsales->Pofsusers->find('all')->contain(['Pofsales.Warehouses.Subwarehouses' => function ($q) {
            return $q->where(['Subwarehouses.whnature_id' => 1]);
        }])->where(['Pofsusers.user_id' => $exitslip->user_id]);
        foreach ($exitslip->shippings as $shipping) {
            foreach ($shipping->orders as $order) {
                foreach ($order->orderpacks as $orderpack) {
                    $whproductdep = $this->Exitslips->Warehouses->Whproducts->find('all')->where(['pack_id' => $orderpack->pack_id, 'warehouse_id' => $warehouse->last()->id])->last();
                    $updatdepotwhp = $this->Exitslips->Warehouses->Whproducts->get($whproductdep->id);
                    $updatdepotwhp->quantity = $updatdepotwhp->quantity - $orderpack->quantity;

                    $whproductliv = $this->Exitslips->Warehouses->Whproducts->find('all')->where(['pack_id' => $orderpack->pack_id, 'warehouse_id' => $pofsuser->last()->pofsale->warehouse->subwarehouses[0]->id])->last();
                    $updatlivwhp = $this->Exitslips->Warehouses->Whproducts->get($whproductliv->id);
                    $updatlivwhp->quantity = $updatlivwhp->quantity + $orderpack->quantity;

                    if ($this->Exitslips->Warehouses->Whproducts->save($updatdepotwhp)) {
                    }
                    if ($this->Exitslips->Warehouses->Whproducts->save($updatlivwhp)) {
                    }
                    $orderpackedit = $this->Exitslips->Shippings->Orders->Orderpacks->get($orderpack->id);
                    $orderpackedit->statut = 5;
                    $this->Exitslips->Shippings->Orders->Orderpacks->save($orderpackedit);
                }
            }
        }
        die();

        $this->set(compact('exitslip'));
    }



    /**

     * Delete method

     *

     * @param string|null $id Exitslip id.

     * @return \Cake\Http\Response|null Redirects to index.

     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.

     */



    public function orders($id = null)

    {

        $exitslip = $this->Exitslips->get($id, ['contain' => ['Companies', 'Shippings.Customers', 'Shippings.Orders.Orderpacks.Orderpackproducts', 'Shippings.Orders.Orderpacks.Packs']]);



        $this->set(compact('exitslip'));
    }





    public function instanceord($exitslipid = null)

    {

        $userid = $this->request->getQuery('keyword');
        $userzones = $this->Exitslips->Users->Zoneusers->find('all')->where(['user_id' => $userid]);
        $qzones = [];
        foreach ($userzones as $key => $userzone) {
            $qzones['OR'][$userzone->zone_id] = ['Users.id' => $userzone->user_id];
        }

        $zoneusers = $this->Exitslips->Users->find('all')->contain(['Zoneusers.Zones', 'Orders' => function ($q) {
            return $q->where(['Orders.statut' => 1]);
        }])->where(['OR' => [['Users.role_id' => 5], ['Users.role_id' => 1]]], $qzones);
        $zoneuserdatas = [];
        foreach ($zoneusers as $key => $zoneuser) {
                if ($zoneuser->orders) {
                    $zoneuserdatas[$zoneuser->id] = $zoneuser->firstname . ' ' . $zoneuser->lastname;
                }
        }
        $this->set(compact('zoneuserdatas'));
    }



    public function addedord($exitslipid = null)

    {

        $draw = $_GET['draw'];

        $row = $_GET['start'];

        $rowperpage = $_GET['length']; // Rows display per page

        $columnIndex = $_GET['order'][0]['column']; // Column index

        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name

        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc

        $searchValue = $_GET['search']['value']; // Search value

        switch ($columnName) {

            case 'user':

                $columnName = "Orders.firstname";

                break;

            case 'customer':

                $columnName = "Customers.name";

                break;

            case 'carrier':

                $columnName = "Carriers.title";

                break;

            case 'city':

                $columnName = "Cities.title";

                break;

            default:

                $columnName = "Orders.code";

                break;
        }

        $exitsusers = $this->Exitslips->Exsusers->find('all')->where(['exitslip_id' => $exitslipid]);





        $sel = $this->Exitslips->Shippings->Slips->find('all')->contain(['Slipproducts', 'Users']);

        $sel->where(['Slips.sliptype_id' => 1, 'Slips.warehoused IS' => NULL, 'Slips.company_id' => $this->Auth->user('company_id'), 'Slips.statut' => 4, 'Slips.exitslip_id' => $exitslipid]);

        $q = [];

        foreach ($exitsusers as $key => $exitsuser) {

            $q['OR'][$key] = [['Slips.user_id' => $exitsuser->user_id]];
        }

        $empQuery = $this->Exitslips->Shippings->Slips->find('all')->contain(['Slipproducts', 'Users']);

        $empQuery->where(['Slips.sliptype_id' => 1, 'Slips.warehoused IS' => NULL, 'Slips.company_id' => $this->Auth->user('company_id'), 'Slips.statut' => 4, 'Slips.exitslip_id' => $exitslipid]);

        $empQuery->where([$q]);

        $sel->where([$q]);



        $sel->select(['count' => $sel->func()->count('*')]);

        $totalRecords = $sel->last()->count;

        if ($row == 0) {

            $empQuery->limit($rowperpage);
        } else {

            $empQuery->limit($rowperpage);

            $empQuery->page(($row / $rowperpage) + 1);
        }



        if ($searchValue != '') {

            $or = [

                ['Slips.code LIKE' => '%' . $searchValue . '%'],

            ];

            $sel->where(['OR' => $or]);

            $empQuery->where(['OR' => $or]);

            $empQuery->page(1);
        }

        if ($draw = 0) {

            $empQuery->page(1);
        }

        ## Total number of records with filtering

        $totalRecordwithFilter = $sel->last()->count;

        ## Fetch records

        $data = [];

        //"statut"=>'',

        foreach ($empQuery as $key => $slip) {



            $action = '<button data-id="' . $slip->id . '" class="rmvord btn btn-sm btn-danger waves-effect waves-light" >-</button>';



            $data[] = [

                "Bonch" => $slip->code,

                "User" => $slip->user->firstname,

                "Products" => count($slip->slipproducts),

                "Date" => $slip->created->i18nFormat('dd/MM/yyyy'),

                "action" => $action

            ];
        }



        $response = [

            'rowperpage' => $rowperpage,

            'row' => $row,

            'iTotalRecords' => $totalRecords,

            'iTotalDisplayRecords' => $totalRecordwithFilter,

            'aaData' => $data,

        ];

        $this->autoRender = false;

        echo json_encode($response);

        exit;
    }



    public function addord($exitslipid = null)

    {

        $slipid = json_decode($_GET['ordid'], true);

        $slip = $this->Exitslips->Shippings->Slips->get($slipid, ['contain' => ['Slipproducts']]);

        foreach ($slip->slipproducts as $key => $slipproduct) {

            $slipproduct->statut = 4;
        }

        $slip->statut = 4;

        $slip->exitslip_id = $exitslipid;

        $slip->dirty('slipproducts', true);

        $this->Exitslips->Shippings->Slips->save($slip);

        $this->autoRender = false;
    }



    public function rmvord($receiptid = null)

    {

        $slipid = json_decode($_GET['ordid'], true);

        $slip = $this->Exitslips->Shippings->Slips->get($slipid, ['contain' => ['Slipproducts']]);

        foreach ($slip->slipproducts as $key => $slipproduct) {

            $slipproduct->statut = 3;
        }

        $slip->statut = 3;

        $slip->exitslip_id = NULL;

        $slip->dirty('slipproducts', true);

        $this->Exitslips->Shippings->Slips->save($slip);

        $this->autoRender = false;



        $this->autoRender = false;
    }



    public function search($exitsliptypeid = null)
    {
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
                $columnName = "Exitslips.code";
                break;

            case 'code':
                $columnName = "Exitslips.code";
                break;

            case 'created':
                $columnName = "Exitslips.created";
                break;

            case 'status':
                $columnName = "Exitslips.statut";
                break;

            default:
                $columnName = "Exitslips.created";
                $columnSort = "desc";
                break;
        }
        $pos = stripos($searchDate, ";");
        $dateend = substr($searchDate, $pos + 1);
        $datestart = substr($searchDate, 0, $pos);

        $sel = $this->Exitslips->find('all')->contain(['Users', 'Shippings.Customers', 'Inventories'])->order([$columnName => $columnSort])->where(['Exitslips.company_id' => $this->Auth->user('company_id'), 'Exitslips.warehouse_id' => $this->Auth->user('defaultwh')]);

        $empQuery = $this->Exitslips->find('all')->contain(['Users', 'Shippings.Customers', 'Inventories'])->order([$columnName => $columnSort])->where(['Exitslips.company_id' => $this->Auth->user('company_id'), 'Exitslips.warehouse_id' => $this->Auth->user('defaultwh')]);

        if ($this->Auth->user('role_id') == 3 || $this->Auth->user('role_id') == 6) {
            $empQuery->where(['Exitslips.user_id' => $this->Auth->user('id')]);
            $sel->where(['Exitslips.user_id' => $this->Auth->user('id')]);
        }

        if ($searchValue != '') {
            $sel->where(["OR" => [
                ['Exitslips.code LIKE' => '%' . $searchValue . '%'],
                ['lower(Exitslips.code) LIKE' => '%' . $searchValue . '%'],
                ['lower(Users.firstname) LIKE' => '%' . $searchValue . '%'],
                ['Users.firstname LIKE' => '%' . $searchValue . '%'],
                ['lower(Users.lastname) LIKE' => '%' . $searchValue . '%'],
                ['Users.lastname LIKE' => '%' . $searchValue . '%']
            ]]);

            $empQuery->where(["OR" => [
                ['Exitslips.code LIKE' => '%' . $searchValue . '%'],
                ['lower(Exitslips.code) LIKE' => '%' . $searchValue . '%'],
                ['lower(Users.firstname) LIKE' => '%' . $searchValue . '%'],
                ['Users.firstname LIKE' => '%' . $searchValue . '%'],
                ['lower(Users.lastname) LIKE' => '%' . $searchValue . '%'],
                ['Users.lastname LIKE' => '%' . $searchValue . '%']
            ]]);
        }
        if ($datestart && $dateend) {
            $empQuery->where(['DATE(Exitslips.created) <= ' => $dateend, 'DATE(Exitslips.created) >= ' => $datestart]);
            $sel->where(['DATE(Exitslips.created) <= ' => $dateend, 'DATE(Exitslips.created) >= ' => $datestart]);
        }
        if ($searchUser) {
            $empQuery->where(['Exitslips.user_id' => $searchUser]);
            $sel->where(['Exitslips.user_id' => $searchUser]);
        }
        if ($searchStatus) {
            $empQuery->where(['Exitslips.statut' => $searchStatus]);
            $sel->where(['Exitslips.statut' => $searchStatus]);
        }
        $empQuery->limit($perpage);
        $empQuery->page($page);
        $sel->select(['count' => $sel->func()->count('*')]);
        $total = $sel->last()->count;
        $data = [];

        //"statut"=>'',

        foreach ($empQuery as $key => $exitslip) {
            $inventory = 0;
            if ($exitslip->inventories) {
                $inventory = 1;
            }
            $data[] = [
                "id" => $exitslip->id,
                "user" => $exitslip->user->firstname . ' ' . $exitslip->user->lastname,
                "code" => $exitslip->code,
                "shipping" => count($exitslip->shippings),
                "created" => $exitslip->created->i18nFormat('dd/MM/yyyy'),
                "status" => $exitslip->statut,
                "inventory" => $inventory,
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



    public function validate($id = null)
    {

        $exitslip = $this->Exitslips->get($id);

        $this->set(compact('exitslip'));
    }

    /**
     * Generate PDF with orders list for an exit slip
     *
     * @param string|null $id Exit slip id.
     * @return \Cake\Http\Response|null
     */
    public function print($id = null)
    {
        // Get exitslip with all related data
        $exitslip = $this->Exitslips->get($id, [
            'contain' => [
                'Companies',
                'Users',
                'Warehouses',
                'Exitsliptypes',
                'Shippings' => [
                    'Customers' => ['Zones' => ['Cities']],
                    'Orders' => [
                        'Users',
                        'Orderpacks' => [
                            'Packs' => [
                                'Packunites' => ['Unites' => ['Parentunites']],
                                'Brands',
                                'Categories',
                                'Saletypes',
                                'MeasurementUnits'
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        // Load MeasurementUnits to find base units
        $this->loadModel('MeasurementUnits');
        
        // Process measurement units for each orderpack
        foreach ($exitslip->shippings as $shipping) {
            foreach ($shipping->orders as $order) {
                foreach ($order->orderpacks as $orderpack) {
                    if ($orderpack->pack->saletype_id == 4 && !empty($orderpack->pack->measurement_unit)) {
                        // Find the base unit (conversion_factor = 1) for this measurement type
                        $baseUnit = $this->MeasurementUnits->find()
                            ->where([
                                'type' => $orderpack->pack->measurement_unit->type,
                                'conversion_factor' => 1
                            ])
                            ->first();
                        
                        // Store base unit in the orderpack for use in template
                        $orderpack->base_unit = $baseUnit;
                    }
                }
            }
        }

        // Set max execution time for large PDFs
        ini_set('max_execution_time', '300');
        ini_set("pcre.backtrack_limit", "5000000");

        $this->set(compact('exitslip'));
    }

    /**
     * Generate thermal printer document with orders list for an exit slip
     * Formatted for 100mm wide thermal printers (typically receipt printers)
     *
     * @param string|null $id Exit slip id.
     * @return \Cake\Http\Response|null
     */
    public function thermalprint($id = null)
    {
        // Get exitslip with all related data
        $exitslip = $this->Exitslips->get($id, [
            'contain' => [
                'Companies',
                'Users',
                'Warehouses',
                'Exitsliptypes',
                'Shippings' => [
                    'Customers' => ['Zones' => ['Cities']],
                    'Orders' => [
                        'Users',
                        'Orderpacks' => [
                            'Packs' => [
                                'Packunites' => ['Unites' => ['Parentunites']],
                                'Brands',
                                'Categories',
                                'Saletypes',
                                'MeasurementUnits'
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        // Load MeasurementUnits to find base units
        $this->loadModel('MeasurementUnits');
        
        // Process measurement units for each orderpack
        foreach ($exitslip->shippings as $shipping) {
            foreach ($shipping->orders as $order) {
                foreach ($order->orderpacks as $orderpack) {
                    if ($orderpack->pack->saletype_id == 4 && !empty($orderpack->pack->measurement_unit)) {
                        // Find the base unit (conversion_factor = 1) for this measurement type
                        $baseUnit = $this->MeasurementUnits->find()
                            ->where([
                                'type' => $orderpack->pack->measurement_unit->type,
                                'conversion_factor' => 1
                            ])
                            ->first();
                        
                        // Store base unit in the orderpack for use in template
                        $orderpack->base_unit = $baseUnit;
                    }
                }
            }
        }

        // Set response type for thermal printer output (typically plain text or PDF)
        $this->response = $this->response->withType('text/html');
        
        // Calculate character width for 100mm thermal printer
        // Standard thermal printer: ~42-48 characters per line at 100mm
        $thermalWidth = 42;

        $this->set(compact('exitslip', 'thermalWidth'));
    }

    /**
     * Generate raw thermal printer ESC/POS commands for advanced printer support
     * 
     * @param string|null $id Exit slip id.
     * @return \Cake\Http\Response|null
     */
    public function thermalraw($id = null)
    {
        $this->response = $this->response->withType('application/octet-stream');
        
        // Get exitslip with all related data
        $exitslip = $this->Exitslips->get($id, [
            'contain' => [
                'Companies',
                'Users',
                'Warehouses',
                'Exitsliptypes',
                'Shippings' => [
                    'Customers' => ['Zones' => ['Cities']],
                    'Orders' => [
                        'Users',
                        'Orderpacks' => [
                            'Packs' => [
                                'Packunites' => ['Unites' => ['Parentunites']],
                                'Brands',
                                'Categories',
                                'Saletypes',
                                'MeasurementUnits'
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        // ESC/POS command builder
        $escpos = new \stdClass();
        $escpos->output = '';
        
        // Initialize printer
        $escpos->output .= "\x1B\x40"; // ESC @ - Initialize printer
        
        // Set character size to normal
        $escpos->output .= "\x1B\x21\x00"; // ESC ! 0
        
        // Center alignment
        $escpos->output .= "\x1B\x61\x01"; // ESC a 1
        
        // Add company header
        if ($exitslip->company) {
            $escpos->output .= $this->_wrapText($exitslip->company->name, 42) . "\n";
        }
        
        // Add document title
        $escpos->output .= str_repeat("=", 42) . "\n";
        $escpos->output .= $this->_centerText("BON DE SORTIE", 42) . "\n";
        $escpos->output .= str_repeat("=", 42) . "\n\n";
        
        // Add exit slip details
        $escpos->output .= "Ref: " . $exitslip->id . "\n";
        $escpos->output .= "Type: " . ($exitslip->exitsliptype ? $exitslip->exitsliptype->name : 'N/A') . "\n";
        $escpos->output .= "Date: " . date('d/m/Y H:i') . "\n";
        $escpos->output .= "Entrep: " . ($exitslip->warehouse ? $exitslip->warehouse->name : 'N/A') . "\n";
        $escpos->output .= "Agent: " . ($exitslip->user ? $exitslip->user->firstname : 'N/A') . "\n\n";
        
        // Left alignment for items
        $escpos->output .= "\x1B\x61\x00"; // ESC a 0
        $escpos->output .= str_repeat("-", 42) . "\n";
        
        // Add orders
        $itemCount = 0;
        foreach ($exitslip->shippings as $shipping) {
            foreach ($shipping->orders as $order) {
                foreach ($order->orderpacks as $orderpack) {
                    $itemCount++;
                    $packName = $orderpack->pack->title;
                    $quantity = $orderpack->quantity;
                    $unit = $orderpack->pack->measurement_unit ? $orderpack->pack->measurement_unit->abbreviation : 'U';
                    
                    // Wrap long pack names
                    $escpos->output .= $this->_wrapText(substr($packName, 0, 35), 42) . "\n";
                    $escpos->output .= str_pad("Qty: " . $quantity . " " . $unit, 42) . "\n";
                }
            }
        }
        
        // Footer
        $escpos->output .= "\n" . str_repeat("-", 42) . "\n";
        $escpos->output .= $this->_centerText("Total articles: " . $itemCount, 42) . "\n";
        $escpos->output .= str_repeat("=", 42) . "\n\n";
        $escpos->output .= $this->_centerText("Merci!", 42) . "\n";
        
        // Cut paper
        $escpos->output .= "\x1B\x69"; // ESC i - Partial cut
        
        // End of transmission
        $escpos->output .= "\x04";
        
        $this->response = $this->response
            ->withStringBody($escpos->output)
            ->withHeader('Content-Disposition', 'attachment; filename="exitslip_' . $id . '.prn"');
        
        return $this->response;
    }

    /**
     * Helper function to wrap text for thermal printer
     * 
     * @param string $text Text to wrap
     * @param int $width Character width
     * @return string
     */
    private function _wrapText($text, $width = 42)
    {
        return wordwrap($text, $width, "\n", true);
    }

    /**
     * Helper function to center text on thermal printer
     * 
     * @param string $text Text to center
     * @param int $width Character width
     * @return string
     */
    private function _centerText($text, $width = 42)
    {
        $padding = intval(($width - strlen($text)) / 2);
        return str_repeat(" ", $padding) . $text;
    }
}
