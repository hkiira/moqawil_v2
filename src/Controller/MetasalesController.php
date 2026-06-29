<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;
use Cake\ORM\Query;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Collection\Collection;

class MetasalesController extends AppController
{
    public function beforeFilter(\Cake\Event\Event $event)
    {
        parent::beforeFilter($event);

        // Skip beforeFilter validation for specific actions
        $skipActions = ['homeCategories', 'homecategories'];
        if (in_array($this->request->getParam('action'), $skipActions)) {
            return;
        }

        // Get user_id from request params or data
        $user_id = null;

        // Check if user_id is in the URL parameters
        if (!empty($this->request->getParam('pass'))) {
            foreach ($this->request->getParam('pass') as $param) {
                if (is_numeric($param)) {
                    $user_id = $param;
                    break;
                }
            }
        }
        // Check if user_id is in POST data
        if (!$user_id && $this->request->is(['post', 'put'])) {
            $user_id = $this->request->getData('user_id');
        }

        // Check if user_id is in GET data
        if ($this->request->getQuery('user_id') && $this->request->is('get')) {
            $user_id = $this->request->getQuery('user_id');
        }
        // Verify user status if user_id exists
        if ($user_id) {
            $this->loadModel('Users');
            $user = $this->Users->get($user_id);
            if (!$user || $user->statut == 0) {
                $data = [
                    'statut' => 0,
                    'message' => 'Votre compte est désactivé'
                ];

                header("Access-Control-Allow-Origin: *");
                header("Access-Control-Allow-Credentials: true");
                header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
                header("Content-Type: application/json; charset=UTF-8");
                header("Access-Control-Allow-Methods: POST, OPTIONS");

                echo json_encode($data);
                exit;
            }
        }
    }

    public function livreurvendeurs($user_id)
    {

        $this->loadModel('Users');

        $user = $this->Users->get($user_id, ['contain' => 'Exitslips.Shippings.Orders']);
        if (!$user) {
            $data = [
                'statut' => 0,
                'message' => 'Utilisateur non trouvé'
            ];
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
            header("Content-Type: application/json; charset=UTF-8");
            header("Access-Control-Allow-Methods: POST, OPTIONS");
            echo json_encode($data);
            exit;
        }
        $userIds = [];
        $shippingsIds = [];
        foreach ($user->exitslips as $exitslip) {
            foreach ($exitslip->shippings as $shipping) {
                $shippingsIds[$shipping->id] = $shipping->id;
                if (count($shipping->orders) == 0)
                    continue;
                $userIds[] = $shipping->user_id;
            }
        }

        $users = $this->Users->find('all')->where(['Users.id IN ' => $userIds])->contain([
            'Roles',
            'Orders' => function (Query $q) use ($user_id, $shippingsIds) {
                return $q->where(['Orders.statut ' => 5, 'Orders.shipping_id IN' => $shippingsIds]);
            }
        ]);
        $data = [];
        foreach ($users as $user) {
            if (count($user->orders) == 0)
                continue;
            $data[] = [
                "id" => $user->id,
                "code" => count($user->orders) . "",
                "firstname" => $user->firstname,
                "lastname" => $user->lastname,
                "roleId" => $user->role_id,
                "role" => $user->role->title,
                "pofsaleId" => 1,
                "warehouseId" => 1,
                "parentwarehouseId" => 1,
            ];
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize', 'data');
        $this->RequestHandler->renderAs($this, 'json');
    }

    // users
    public function login()
    {
        $username = $this->request->getData('username');
        $password = $this->request->getData('password');
        $this->loadModel('Users');
        $msg = NULL;
        $user = $this->Users->find('all')->where(['username' => $username])->select(['id', 'code', 'firstname', 'lastname', 'role_id', 'company_id', 'password', 'statut'])->last();
        if ($user) {
            $hasher = new DefaultPasswordHasher();
            $hasher->hash($password);
            if ($hasher->check($password, $user->password) && $user->statut == 1) {
                $msg['statut'] = 1;
                $msg['message'] = 'Bienvenue';
                if ($user) {
                    $accesusers = $this->Users->Accesusers->find('all')->where(['Accesusers.user_id' => $user['id'], 'Accesusers.statut' => 1])->contain(['Accesses']);

                    $userzones = $this->Users->Zoneusers->find('all')->where(['Zoneusers.user_id' => $user['id'], 'Zoneusers.statut' => 1])->contain(['Zones.Subzones']);

                    $user['zones'] = [];
                    $user['costumertypes'] = $this->Users->Companies->Customertypes->find('list')->where(['company_id' => $user->company_id, ['OR' => [['id' => 2], ['id' => 5]]]])->toArray();


                    foreach ($userzones as $key => $userzone) {
                        foreach ($userzone->zone->subzones as $key1 => $subzone) {
                            $user['zones'][] = ['Id' => $subzone->id, 'Name' => $subzone->title];
                        }
                    }
                    $user['role'] = $this->Users->Roles->get($user->role_id)->title;
                    if ($user->role_id == 5 || $user->role_id == 3 || $user->role_id == 6) {

                        $pofusers = $this->Users->Pofsusers->find('all')->where(['user_id' => $user['id'], 'company_id' => $user['company_id']]);
                        $q = [];
                        foreach ($pofusers as $key => $pofuser) {
                            $q['OR'][$key] = [['Pofsales.id' => $pofuser->pofsale_id]];
                        }

                        //point de vente pour le vendeur , prévendeur ou livreur
                        $pofsale = $this->Users->Pofsusers->Pofsales->find('all')->contain(['Warehouses'])->where(['Pofsales.company_id' => $user->company_id]);
                        if ($user->role_id == 5) {
                            $pofsale->where(['pofstype_id' => 3]);
                        } else {
                            $pofsale->where(['pofstype_id' => 1]);
                        }
                        $pofsale->where([$q]);


                        $user['pofsaleId'] = $pofsale->first()->id;
                        $user['warehouseId'] = $pofsale->first()->warehouse_id;
                        $user['parentwarehouseId'] = $pofsale->first()->warehouse->warehouse_id;
                    }
                    $this->Auth->setUser($user);
                    $parentwarehouseId = ($pofsale->first()->warehouse->warehouse_id) ? $pofsale->first()->warehouse->warehouse_id : 0;
                    $userinfos = [
                        "id" => $user['id'],
                        "code" => $user['code'],
                        "firstname" => $user['firstname'],
                        "lastname" => $user['lastname'],
                        "roleId" => $user['role_id'],
                        "role" => $user['role'],
                        "pofsaleId" => $pofsale->first()->id,
                        "warehouseId" => $pofsale->first()->warehouse_id,
                        "parentwarehouseId" => $parentwarehouseId,
                        "zones" => $user['zones'],
                        "costumertypes" => $user['costumertypes'],
                    ];

                    $msg['statut'] = 1;
                    $msg['message'] = $userinfos;
                }
            } else {
                if ($user->statut == 1) {
                    $msg['statut'] = 0;
                    $msg['message'] = 'votre mot de passe est incorrect';
                    $user = null;
                } else {
                    $msg['statut'] = 0;
                    $msg['message'] = 'votre compte est désactivé';
                    $user = null;
                }
            }
        } else {
            $msg['statut'] = 0;
            $msg['message'] = 'Votre identifiant est incorrect';
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        echo json_encode($msg);
        exit;
    }

    // Reports
    public function addSlipRepport($report_id)
    {
        $this->loadModel('Reports');
        $slipproducts = [];
        $report = $this->Reports->get($report_id, ['contain' => ['Shippings.Orders.Orderpacks']]);
        foreach ($report->shippings as $shipping) {
            foreach ($shipping->orders as $order) {
                foreach ($order->orderpacks as $orderpack) {
                    if ($orderpack->statut == 8) {
                        if (isset($slipproducts[$order->user_id][$orderpack->pack_id])) {
                            $slipproducts[$order->user_id][$orderpack->pack_id]['quantity'] += $orderpack->quantity;
                        } else {
                            $slipproducts[$order->user_id][$orderpack->pack_id]['pack_id'] = $orderpack->pack_id;
                            $slipproducts[$order->user_id][$orderpack->pack_id]['quantity'] = $orderpack->quantity;
                            $slipproducts[$order->user_id][$orderpack->pack_id]['price'] = $orderpack->price;
                        }
                    }
                }

            }
        }
        $this->loadModel('Slips');
        $pofsuser = $this->Slips->Warehouses->Pofsales->Pofsusers->find('all')->where(['user_id' => $report->user_id])->last();
        $pofsale = $this->Slips->Warehouses->Pofsales->get($pofsuser->pofsale_id, ['contain' => ['Warehouses']]);
        foreach ($slipproducts as $user_id => $slipproduct) {
            $slip = $this->Slips->newEntity();
            $code = $this->Slips->Companies->Companycodes->find('all')->where(['controleur' => 'Slips2', 'company_id' => 1])->last();
            $slipCode = $code->prefixe . ($code->compteur + 1);
            $slipproducts = [];
            foreach ($slipproduct as $key => $value) {
                $slipproducts[] = [
                    "pack_id" => $value['pack_id'],
                    "quantity" => $value['quantity'],
                    "price" => $value['price'],
                    "whnature_id" => 1,
                    "user_id" => $user_id,
                    "statut" => 1,
                ];
            }
            $slipData = [
                'code' => $slipCode,
                'raison' => 'Retour des commandes ',
                'statut' => 2,
                'warehouse_id' => $pofsale->warehouse->id,
                'warehoused' => $pofsale->warehouse->warehouse_id,
                'whnature_id' => 1,
                'report_id' => $report->id,
                'user_id' => $user_id,
                'sliptype_id' => 2,
                'company_id' => 1,
                'slipproducts' => $slipproducts
            ];
            $slip = $this->Slips->patchEntity($slip, $slipData, ['Associated' => ['slipproducts']]);
            if ($this->Slips->save($slip)) {
                $code->compteur += 1;
                $this->Slips->Companies->Companycodes->save($code);
            }
        }
    }
    public function addReport($user_id)
    {
        $this->loadModel('Reports');
        $report = $this->Reports->newEntity();
        $datas = $this->request->getData();

        $code = $this->Reports->Companies->Companycodes->find('all')->where(['controleur' => 'Reports', 'company_id' => 1])->last();
        $reportDatas = [
            "code" => "APP" . $code->prefixe . ($code->compteur + 1),
            "company_id" => 1,
            "user_id" => $user_id,
            "sellerid" => $user_id,
            "warehouse_id" => 1,
            "statut" => 1,
        ];
        $report = $this->Reports->patchEntity($report, $reportDatas);
        if ($this->Reports->save($report)) {
            $companycode = $this->Reports->Companies->Companycodes->get($code->id);
            $companycode->compteur += 1;
            if ($this->Reports->Companies->Companycodes->save($companycode)) {
                $orderPayments = $this->Reports->OrderPayments->find("all")->where(['OrderPayments.order_id IN' => $datas['order_ids'], 'OrderPayments.payment_method_id !=' => 5]);
                foreach ($orderPayments as $orderPayment) {
                    $orderPayment->report_id = $report->id;
                    $this->Reports->OrderPayments->save($orderPayment);
                }
                $data['statut'] = 1;
                $data['message'] = 'Le rapport a été enregistré.';
            }
        } else {
            $data['statut'] = 0;
            $data['message'] = 'Aucune commande trouvées. Veuillez réessayer.';
        }

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize', 'data');
        $this->RequestHandler->renderAs($this, 'json');
    }

    public function reportsList($user_id, $searchValue = "")
    {
        $this->loadModel('Reports');
        $reports = $this->Reports->find('all')->contain(['Users', 'OrderPayments']);
        $reports->order(['Reports.id' => 'DESC']);
        $reports->where(['Reports.user_id' => $user_id]);
        $reportDatas = [];
        foreach ($reports as $key7 => $report) {
            $total = 0;
            $countOrders = 0;
            foreach ($report->order_payments as $order_payment) {
                $total += $order_payment->amount;
                $countOrders++;
            }
            $reportDatas[$key7] = [
                "id" => $report->id,
                "code" => $report->code,
                "date" => $report->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                "statut" => $report->statut,
                "countOrders" => $countOrders,
                "total" => $total,
            ];
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('reportDatas'));
        $this->set('_serialize', 'reportDatas');
        $this->RequestHandler->renderAs($this, 'json');
        // $this->loadModel('Reports');
        // $reports->order(['Reports.id' => 'DESC']);
        // $reports->where(['Reports.user_id' => $user_id]);
        // $reportDatas = [];
        // foreach ($reports as $key => $report) {
        //     }
        //     $total=0;
        //     $totalOrders=0;
        //     foreach ($report->order_payments as $order_payment) {
        //         $total+=$order_payment->amount;
        //         $totalOrders++;
        //     $reportDatas[$key] = [
        //         "id"=> $report->id,
        //         "code"=> $report->code,
        //         "date" => $report->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
        //         "statut"=> $report->statut,
        //         "countOrders" => $totalOrders,
        //         "total"=>$total,
        //     ];
        // }
    }
    public function reportDetails($report_id)
    {
        $this->loadModel('Orders');
        $orderPayments = $this->Orders->OrderPayments->find('all')->where(['report_id' => $report_id]);
        $idsOrders = [];
        foreach ($orderPayments as $orderPayment) {
            $idsOrders[] = $orderPayment->order_id;
        }
        $orders = $this->Orders->find('all')->contain(['Shippings', 'Orderpacks.Turnovers', 'OrderPayments.PaymentMethods', 'Orderpacks.Packs.MeasurementUnits', 'Orderpacks.Packs.Turnovers', 'Orderpacks.Packs.Packunites.Unites.Parentunites', 'Customers.Zones.Cities', 'Customers.Customertypes', 'Orderpacks.Packs.Brands', 'Orderpacks.Packs.Prices', 'Orderpacks.Packs.Categories']);
        $orders->where(['Orders.id IN ' => $idsOrders]);
        $orders->order(['Orders.id' => 'DESC']);
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $orders->limit($limit);
        $orders->page($page);
        $data = [];
        foreach ($orders as $key => $order) {
            $data[$key] = [
                "id" => $order->id,
                "code" => $order->code,
                "loyaltypoints" => $order->loyaltypoints,
                "user_id" => $order->user_id,
                "date" => $order->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                "statut" => $order->statut,
            ];
            $photo = $this->Orders->Customers->Photos->find('all')->where(['controleur' => 'customers', 'objectid' => $order->customer->id])->order(['created' => 'ASC'])->last();
            $img = Router::Url('/') . 'webroot/img/unvailable.jpg';
            if ($photo) {
                $img = Router::Url('/') . $photo->dir . '/' . $photo->title;
            }
            $orderPayments = [];
            foreach ($order->order_payments as $keyOrderP => $orderPayment) {
                $image = "";
                $photo = $this->Orders->Photos->find('all')->where(['controleur' => 'orderpayments', 'objectid' => $orderPayment->id])->order(['created' => 'ASC'])->last();
                if ($photo) {
                    $image = Router::Url('/') . $photo->dir . '/' . $photo->title;
                } else {
                    $image = "";
                }
                $orderPayments[] = [
                    "id" => $orderPayment->id,
                    "amount" => $orderPayment->amount,
                    "date" => $orderPayment->cheque_date ? $orderPayment->cheque_date->i18nFormat('dd/MM/yyyy', 'Africa/Casablanca') : $orderPayment->created->i18nFormat('dd/MM/yyyy ', 'Africa/Casablanca'),
                    "photo" => $image,
                    "created_at" => $orderPayment->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                    "payment_method" => [
                        "id" => $orderPayment->payment_method->id,
                        "name" => $orderPayment->payment_method->name,
                        "code" => $orderPayment->payment_method->code,
                        "requires_cheque_date" => $orderPayment->payment_method->requires_cheque_date,
                    ]
                ];
            }
            $data[$key]["order_payments"] = $orderPayments;
            $customer = [
                "id" => $order->customer->id,
                "name" => $order->customer->name . "",
                "customertype" => ["id" => $order->customer->customertype->id, "title" => $order->customer->customertype->title . ""],
                "zone" => ["id" => $order->customer->zone->id, "title" => $order->customer->zone->title . ""],
                "adresse" => $order->customer->adresse . "",
                "photo" => $img,
                "phone" => $order->customer->phone . "",
                "latitude" => $order->customer->latitude . "",
                "longitude" => $order->customer->longitude . "",
                "proximite" => 125.20,
                "ice" => $order->customer->ice . "",
                "city" => $order->customer->zone->city->title . "",
                "statut" => $order->customer->statut,
            ];
            $data[$key]["customer"] = $customer;
            foreach ($order->orderpacks as $key1 => $orderpack) {

                if ($orderpack->pack->measurement_unit->conversion_factor == 1) {
                    $loyaltypoints = $orderpack->pack->measurement_quantity . $orderpack->pack->measurement_unit->abbreviation;
                } else {
                    $getParent = $this->Orders->Orderpacks->Packs->MeasurementUnits->find('all')->where(['type' => $orderpack->pack->measurement_unit->type, 'conversion_factor' => ($orderpack->pack->measurement_unit->conversion_factor * 1000)]);
                    $loyaltypoints = $orderpack->loyaltypoints . "/" . $getParent->first()->conversion_factor . $getParent->first()->abbreviation;
                }
                $data[$key]["orderpacks"][$key1] = [
                    "id" => $orderpack->id,
                    "date" => $orderpack->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                    "price" => $orderpack->price,
                    "quantity" => $orderpack->quantity,
                    "statut" => $orderpack->statut,
                    "turnover" => $loyaltypoints,
                    "loyalty" => $orderpack->loyaltypoints,
                    "commissionpack" => ($orderpack->commissionpack) ? $orderpack->commissionpack : 0,
                ];
                $photo = $this->Orders->Orderpacks->Packs->Photos->find('all')->where(['controleur' => 'packs', 'objectid' => $orderpack->pack->id])->order(['created' => 'ASC'])->last();
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
                    "turnover" => $loyaltypoints,
                    "loyalty" => $orderpack->loyaltypoints,
                    "variants" => $variants,
                    "brand" => ["id" => $orderpack->pack->brand->id, "title" => $orderpack->pack->brand->title],
                    "category" => ["id" => $orderpack->pack->category->id, "title" => $orderpack->pack->category->title],
                ];
                $data[$key]["orderpacks"][$key1]['product'] = $product;
            }
        }

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize', 'data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    // Commandes
    public function ordersInInstance($isReport, $user_id, $searchValue = "")
    {
        if ($isReport == 2) {
            $this->loadModel('Customers');
            $zoneusers = $this->Customers->Zones->Zoneusers->find('all')->contain(['Zones.Subzones'])->where(['Zoneusers.user_id' => $user_id]);
            $q = [];
            foreach ($zoneusers as $key => $zoneuser) {
                foreach ($zoneuser->zone->subzones as $subzone) {
                    $q['OR'][$subzone->id] = [['Customers.zone_id' => $subzone->id]];
                }
            }
            $vendeurId = isset($_GET['vendeur_id']) ? $_GET['vendeur_id'] : 0;
            $userId = isset($_GET['user_id']) ? $_GET['user_id'] : 0;
            $user = $this->Users->get($userId);
            $this->loadModel('Orders');
            $exitslips = $this->Orders->Shippings->Exitslips->find('all')->select(['id'])->where(['Exitslips.user_id' => $userId]);
            $exitslipIds = [];
            foreach ($exitslips as $key => $exitip) {
                $exitslipIds[] = $exitip->id;
            }
            $orders = $this->Orders->find('all')->contain(['Users', 'OrderPayments.PaymentMethods', 'Shippings', 'Orderpacks.Packs.MeasurementUnits', 'Orderpacks.Turnovers', 'Orderpacks.Packs.Turnovers', 'Orderpacks.Packs.Packunites.Unites.Parentunites', 'Customers.Zones.Cities', 'Customers.Customertypes', 'Orderpacks.Packs.Brands', 'Orderpacks.Packs.Prices', 'Orderpacks.Packs.Categories']);

            // $orders->where(['Orders.ordertype_id' => 1]);
            $orders->where(['Orders.statut' => 5, "Shippings.exitslip_id IN " => $exitslipIds]);
            if ($vendeurId != 0) {
                $orders->where(['Orders.user_id' => $vendeurId]);
            }
            $orders->order(['Orders.id' => 'DESC']);
            // $orders->where([$q]);
            // debug($orders->toArray());die;
            $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
            $page = isset($_GET['page']) ? $_GET['page'] : 1;

            $orders->limit($limit);
            $orders->page($page);
            $increment = 0;
            $data = [];
            foreach ($orders as $key => $order) {
                $data[$key] = [
                    "id" => $order->id,
                    "code" => $order->code,
                    "loyaltypoints" => $order->loyaltypoints,
                    "user_id" => $order->user_id,
                    "user_name" => $order->user->firstname . ' ' . $order->user->lastname,
                    "ordertype_id" => $order->ordertype_id,
                    "date" => $order->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                    "statut" => $order->statut,
                ];
                $img = Router::Url('/') . 'webroot/img/unvailable.jpg';
                $photo = $this->Orders->Photos->find('all')->where(['controleur' => 'orders', 'objectid' => $order->id])->order(['created' => 'ASC'])->last();

                if ($photo) {
                    $img = Router::Url('/') . $photo->dir . '/' . $photo->title;
                } else {
                    $photo = $this->Orders->Customers->Photos->find('all')->where(['controleur' => 'customers', 'objectid' => $order->customer->id])->order(['created' => 'ASC'])->last();
                    if ($photo) {
                        $img = Router::Url('/') . $photo->dir . '/' . $photo->title;
                    }
                }
                $orderPayments = [];
                foreach ($order->order_payments as $keyOrderP => $orderPayment) {
                    $image = "";
                    $photo = $this->Orders->Photos->find('all')->where(['controleur' => 'orderpayments', 'objectid' => $orderPayment->id])->order(['created' => 'ASC'])->last();
                    if ($photo) {
                        $image = Router::Url('/') . $photo->dir . '/' . $photo->title;
                    } else {
                        $image = "";
                    }
                    $orderPayments[] = [
                        "id" => $orderPayment->id,
                        "amount" => $orderPayment->amount,
                        "date" => $orderPayment->cheque_date ? $orderPayment->cheque_date->i18nFormat('dd/MM/yyyy', 'Africa/Casablanca') : $orderPayment->created->i18nFormat('dd/MM/yyyy ', 'Africa/Casablanca'),
                        "photo" => $image,
                        "created_at" => $orderPayment->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                        "payment_method" => [
                            "id" => $orderPayment->payment_method->id,
                            "name" => $orderPayment->payment_method->name,
                            "code" => $orderPayment->payment_method->code,
                            "requires_cheque_date" => $orderPayment->payment_method->requires_cheque_date,
                        ]
                    ];
                }
                $data[$key]["order_payments"] = $orderPayments;
                $customer = [
                    "id" => $order->customer->id,
                    "name" => $order->customer->name . "",
                    "customertype" => ["id" => $order->customer->customertype->id, "title" => $order->customer->customertype->title . ""],
                    "zone" => ["id" => $order->customer->zone->id, "title" => $order->customer->zone->title . ""],
                    "adresse" => $order->customer->adresse . "",
                    "photo" => $img,
                    "phone" => $order->customer->phone . "",
                    "latitude" => $order->customer->latitude . "",
                    "longitude" => $order->customer->longitude . "",
                    "proximite" => 125.20,
                    "ice" => $order->customer->ice . "",
                    "city" => $order->customer->zone->city->title . "",
                    "statut" => $order->customer->statut,
                ];
                $data[$key]["customer"] = $customer;
                $data[$key]["orderpacks"] = [];
                foreach ($order->orderpacks as $key1 => $orderpack) {

                    if ($orderpack->pack->measurement_unit->conversion_factor == 1) {
                        $loyaltypoints = $orderpack->pack->measurement_quantity . $orderpack->pack->measurement_unit->abbreviation;
                    } else {
                        $getParent = $this->Orders->Orderpacks->Packs->MeasurementUnits->find('all')->where(['type' => $orderpack->pack->measurement_unit->type, 'conversion_factor' => ($orderpack->pack->measurement_unit->conversion_factor * 1000)]);
                        $loyaltypoints = $orderpack->loyaltypoints . "/" . $getParent->first()->conversion_factor . $getParent->first()->abbreviation;
                    }
                    $data[$key]["orderpacks"][$key1] = [
                        "id" => $orderpack->id,
                        "date" => $orderpack->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                        "price" => $orderpack->price,
                        "quantity" => $orderpack->quantity,
                        "statut" => $orderpack->statut,
                        "turnover" => $loyaltypoints,
                        "loyalty" => $orderpack->loyaltypoints,
                        "commissionpack" => ($orderpack->commissionpack) ? $orderpack->commissionpack : 0,
                    ];
                    $photo = $this->Orders->Orderpacks->Packs->Photos->find('all')->where(['controleur' => 'packs', 'objectid' => $orderpack->pack->id])->order(['created' => 'ASC'])->last();
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
                        "turnover" => $loyaltypoints,
                        "loyalty" => $orderpack->loyaltypoints,
                        "variants" => $variants,
                        "brand" => ["id" => $orderpack->pack->brand->id, "title" => $orderpack->pack->brand->title],
                        "category" => ["id" => $orderpack->pack->category->id, "title" => $orderpack->pack->category->title],
                    ];
                    $data[$key]["orderpacks"][$key1]['product'] = $product;
                }
            }
        } elseif ($isReport == 1) {
            $this->loadModel('Orders');
            $paymentMethods = $this->Orders->OrderPayments->find('all')->where(['OrderPayments.report_id IS ' => NULL, 'OrderPayments.payment_method_id !=' => 5]);
            $orderIds = [];
            foreach ($paymentMethods as $key => $paymentMethod) {
                $orderIds[] = $paymentMethod->order_id;
            }
            $this->loadModel('Orders');
            $user = $this->Orders->Users->get($user_id);

            $orders = $this->Orders->find('all')->contain(['Shippings', 'Orderpacks.Turnovers', 'OrderPayments.PaymentMethods', 'Orderpacks.Packs.MeasurementUnits', 'Orderpacks.Packs.Turnovers', 'Orderpacks.Packs.Packunites.Unites.Parentunites', 'Customers.Zones.Cities', 'Customers.Customertypes', 'Orderpacks.Packs.Brands', 'Orderpacks.Packs.Prices', 'Orderpacks.Packs.Categories']);
            $orders->where(['Orders.ordertype_id ' => 1]);
            if ($orderIds) {
                $q['OR']['Orders.id IN'] = $orderIds;
                $orders->where([$q]);
            } else {
                $orders->where(['Orders.id' => 0]);
            }
            $orders->order(['Orders.id' => 'DESC']);
            $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $orders->limit($limit);
            $orders->page($page);
            if ($user->role_id == 6) {
                $this->loadModel('Exitslips');
                $shippings = $this->Exitslips->Shippings->find('all')->contain(['Exitslips'])->where(['Exitslips.user_id' => $user_id]);
                $q = [];
                foreach ($shippings as $key => $shipping) {
                    $q['OR'][$shipping->id] = [['Shippings.id' => $shipping->id]];
                }
                if ($q) {
                    if ($isReport == 1) {
                        $orders->where([['OR' => [['Orders.statut' => 6], ['Orders.statut' => 8]]], 'Shippings.report_id IS ' => NULL]);
                        $orders->where([$q]);
                    } else {

                        $orders->where(['Orders.statut' => 5]);
                        $orders->where([$q]);
                    }
                }
            } elseif ($user->role_id == 3) {
                $orders->where(['Orders.user_id' => $user_id, 'Orders.report_id IS ' => NULL]);
            } else {
                $orders->where(['Orders.user_id' => $user_id, 'Orders.statut' => 1]);
            }
            $data = [];
            foreach ($orders as $key => $order) {
                $data[$key] = [
                    "id" => $order->id,
                    "code" => $order->code,
                    "loyaltypoints" => $order->loyaltypoints,
                    "user_id" => $order->user_id,
                    "ordertype_id" => $order->ordertype_id,
                    "date" => $order->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                    "statut" => $order->statut,
                ];
                $img = Router::Url('/') . 'webroot/img/unvailable.jpg';
                $photo = $this->Orders->Photos->find('all')->where(['controleur' => 'orders', 'objectid' => $order->id])->order(['created' => 'ASC'])->last();

                if ($photo) {
                    $img = Router::Url('/') . $photo->dir . '/' . $photo->title;
                } else {
                    $photo = $this->Orders->Customers->Photos->find('all')->where(['controleur' => 'customers', 'objectid' => $order->customer->id])->order(['created' => 'ASC'])->last();
                    if ($photo) {
                        $img = Router::Url('/') . $photo->dir . '/' . $photo->title;
                    }
                }
                $orderPayments = [];
                foreach ($order->order_payments as $keyOrderP => $orderPayment) {
                    $image = "";
                    $photo = $this->Orders->Photos->find('all')->where(['controleur' => 'orderpayments', 'objectid' => $orderPayment->id])->order(['created' => 'ASC'])->last();
                    if ($photo) {
                        $image = Router::Url('/') . $photo->dir . '/' . $photo->title;
                    } else {
                        $image = "";
                    }
                    $orderPayments[] = [
                        "id" => $orderPayment->id,
                        "amount" => $orderPayment->amount,
                        "date" => $orderPayment->cheque_date ? $orderPayment->cheque_date->i18nFormat('dd/MM/yyyy', 'Africa/Casablanca') : $orderPayment->created->i18nFormat('dd/MM/yyyy ', 'Africa/Casablanca'),
                        "photo" => $image,
                        "created_at" => $orderPayment->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                        "payment_method" => [
                            "id" => $orderPayment->payment_method->id,
                            "name" => $orderPayment->payment_method->name,
                            "code" => $orderPayment->payment_method->code,
                            "requires_cheque_date" => $orderPayment->payment_method->requires_cheque_date,
                        ]
                    ];
                }
                $data[$key]["order_payments"] = $orderPayments;
                $customer = [
                    "id" => $order->customer->id,
                    "name" => $order->customer->name . "",
                    "customertype" => ["id" => $order->customer->customertype->id, "title" => $order->customer->customertype->title . ""],
                    "zone" => ["id" => $order->customer->zone->id, "title" => $order->customer->zone->title . ""],
                    "adresse" => $order->customer->adresse . "",
                    "photo" => $img,
                    "phone" => $order->customer->phone . "",
                    "latitude" => $order->customer->latitude . "",
                    "longitude" => $order->customer->longitude . "",
                    "proximite" => 125.20,
                    "ice" => $order->customer->ice . "",
                    "city" => $order->customer->zone->city->title . "",
                    "statut" => $order->customer->statut,
                ];
                $data[$key]["customer"] = $customer;
                $data[$key]["orderpacks"] = [];
                foreach ($order->orderpacks as $key1 => $orderpack) {

                    if ($orderpack->pack->measurement_unit->conversion_factor == 1) {
                        $loyaltypoints = $orderpack->pack->measurement_quantity . $orderpack->pack->measurement_unit->abbreviation;
                    } else {
                        $getParent = $this->Orders->Orderpacks->Packs->MeasurementUnits->find('all')->where(['type' => $orderpack->pack->measurement_unit->type, 'conversion_factor' => ($orderpack->pack->measurement_unit->conversion_factor * 1000)]);
                        $loyaltypoints = $orderpack->loyaltypoints . "/" . $getParent->first()->conversion_factor . $getParent->first()->abbreviation;
                    }
                    $data[$key]["orderpacks"][$key1] = [
                        "id" => $orderpack->id,
                        "date" => $orderpack->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                        "price" => $orderpack->price,
                        "quantity" => $orderpack->quantity,
                        "statut" => $orderpack->statut,
                        "turnover" => $loyaltypoints,
                        "loyalty" => $orderpack->loyaltypoints,
                        "commissionpack" => ($orderpack->commissionpack) ? $orderpack->commissionpack : 0,
                    ];
                    $photo = $this->Orders->Orderpacks->Packs->Photos->find('all')->where(['controleur' => 'packs', 'objectid' => $orderpack->pack->id])->order(['created' => 'ASC'])->last();
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
                        "turnover" => $loyaltypoints,
                        "loyalty" => $orderpack->loyaltypoints,
                        "variants" => $variants,
                        "brand" => ["id" => $orderpack->pack->brand->id, "title" => $orderpack->pack->brand->title],
                        "category" => ["id" => $orderpack->pack->category->id, "title" => $orderpack->pack->category->title],
                    ];
                    $data[$key]["orderpacks"][$key1]['product'] = $product;
                }
            }

        } else {
            $this->loadModel('Orders');
            $user = $this->Orders->Users->get($user_id);

            $orders = $this->Orders->find('all')->contain(['Shippings', 'Orderpacks.Turnovers', 'OrderPayments.Payments', 'OrderPayments.PaymentMethods', 'Orderpacks.Packs.MeasurementUnits', 'Orderpacks.Packs.Turnovers', 'Orderpacks.Packs.Packunites.Unites.Parentunites', 'Customers.Zones.Cities', 'Customers.Customertypes', 'Orderpacks.Packs.Brands', 'Orderpacks.Packs.Prices', 'Orderpacks.Packs.Categories']);
            $orders->where(['Orders.ordertype_id ' => 1]);
            $orders->order(['Orders.id' => 'DESC']);
            $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $orders->limit($limit);
            $orders->page($page);
            if ($user->role_id == 6) {
                $this->loadModel('Exitslips');
                $shippings = $this->Exitslips->Shippings->find('all')->contain(['Exitslips'])->where(['Exitslips.user_id' => $user_id]);
                $q = [];
                foreach ($shippings as $key => $shipping) {
                    $q['OR'][$shipping->id] = [['Shippings.id' => $shipping->id]];
                }
                if ($q) {
                    if ($isReport == 1) {
                        $orders->where([['OR' => [['Orders.statut' => 6], ['Orders.statut' => 8]]], 'Shippings.report_id IS ' => NULL]);
                        $orders->where([$q]);
                    } else {

                        $orders->where(['Orders.statut' => 5]);
                        $orders->where([$q]);
                    }
                }
            } elseif ($user->role_id == 3) {
                $orders->where(['Orders.user_id' => $user_id, 'Orders.report_id IS ' => NULL]);
            } else {
                $orders->where(['Orders.user_id' => $user_id, 'Orders.statut' => 1]);
            }
            $data = [];
            foreach ($orders as $key => $order) {

                $data[$key] = [
                    "id" => $order->id,
                    "code" => $order->code,
                    "loyaltypoints" => $order->loyaltypoints,
                    "user_id" => $order->user_id,
                    "ordertype_id" => $order->ordertype_id,
                    "date" => $order->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                    "statut" => $order->statut,
                ];
                $photo = $this->Orders->Photos->find('all')->where(['controleur' => 'orders', 'objectid' => $order->id])->order(['created' => 'ASC'])->last();
                if ($photo) {
                    $data[$key]['photo'] = Router::Url('/') . $photo->dir . '/' . $photo->title;
                } else {
                    $data[$key]['photo'] = "";
                }
                $img = Router::Url('/') . 'webroot/img/unvailable.jpg';
                $photo = $this->Orders->Customers->Photos->find('all')->where(['controleur' => 'customers', 'objectid' => $order->customer->id])->order(['created' => 'ASC'])->last();
                if ($photo) {
                    $img = Router::Url('/') . $photo->dir . '/' . $photo->title;
                }
                $orderPayments = [];
                foreach ($order->order_payments as $keyOrderP => $orderPayment) {
                    $image = "";
                    $photo = $this->Orders->Photos->find('all')->where(['controleur' => 'orderpayments', 'objectid' => $orderPayment->id])->order(['created' => 'ASC'])->last();
                    if ($photo) {
                        $image = Router::Url('/') . $photo->dir . '/' . $photo->title;
                    } else {
                        $image = "";
                    }
                    $paymentCode = $orderPayment->payment ? $orderPayment->payment->code : null;
                    $paymentId = $orderPayment->payment ? $orderPayment->payment->id : null;
                    $orderPayments[] = [
                        "id" => $orderPayment->id,
                        "amount" => $orderPayment->amount,
                        "date" => $orderPayment->cheque_date ? $orderPayment->cheque_date->i18nFormat('dd/MM/yyyy', 'Africa/Casablanca') : $orderPayment->created->i18nFormat('dd/MM/yyyy ', 'Africa/Casablanca'),
                        "payment_id" => $paymentId,
                        "payment_code" => $paymentCode,
                        "photo" => $image,
                        "created_at" => $orderPayment->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                        "payment_method" => [
                            "id" => $orderPayment->payment_method->id,
                            "name" => $orderPayment->payment_method->name,
                            "code" => $orderPayment->payment_method->code,
                            "requires_cheque_date" => $orderPayment->payment_method->requires_cheque_date,
                        ]
                    ];
                }
                $data[$key]["order_payments"] = $orderPayments;
                $data[$key]["orderpacks"] = [];
                $customer = [
                    "id" => $order->customer->id,
                    "name" => $order->customer->name . "",
                    "customertype" => ["id" => $order->customer->customertype->id, "title" => $order->customer->customertype->title . ""],
                    "zone" => ["id" => $order->customer->zone->id, "title" => $order->customer->zone->title . ""],
                    "adresse" => $order->customer->adresse . "",
                    "photo" => $img,
                    "phone" => $order->customer->phone . "",
                    "latitude" => $order->customer->latitude . "",
                    "longitude" => $order->customer->longitude . "",
                    "proximite" => 125.20,
                    "ice" => $order->customer->ice . "",
                    "city" => $order->customer->zone->city->title . "",
                    "statut" => $order->customer->statut,
                ];
                $data[$key]["customer"] = $customer;
                foreach ($order->orderpacks as $key1 => $orderpack) {

                    if ($orderpack->pack->measurement_unit->conversion_factor == 1) {
                        $loyaltypoints = $orderpack->pack->measurement_quantity . $orderpack->pack->measurement_unit->abbreviation;
                    } else {
                        $getParent = $this->Orders->Orderpacks->Packs->MeasurementUnits->find('all')->where(['type' => $orderpack->pack->measurement_unit->type, 'conversion_factor' => ($orderpack->pack->measurement_unit->conversion_factor * 1000)]);
                        $loyaltypoints = $orderpack->loyaltypoints . "/" . $getParent->first()->conversion_factor . $getParent->first()->abbreviation;
                    }
                    $data[$key]["orderpacks"][$key1] = [
                        "id" => $orderpack->id,
                        "date" => $orderpack->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                        "price" => $orderpack->price,
                        "quantity" => $orderpack->quantity,
                        "statut" => $orderpack->statut,
                        "turnover" => $loyaltypoints,
                        "loyalty" => $orderpack->loyaltypoints,
                        "commissionpack" => ($orderpack->commissionpack) ? $orderpack->commissionpack : 0,
                    ];
                    $photo = $this->Orders->Orderpacks->Packs->Photos->find('all')->where(['controleur' => 'packs', 'objectid' => $orderpack->pack->id])->order(['created' => 'ASC'])->last();
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
                        "turnover" => $loyaltypoints,
                        "loyalty" => $orderpack->loyaltypoints,
                        "variants" => $variants,
                        "brand" => ["id" => $orderpack->pack->brand->id, "title" => $orderpack->pack->brand->title],
                        "category" => ["id" => $orderpack->pack->category->id, "title" => $orderpack->pack->category->title],
                    ];
                    $data[$key]["orderpacks"][$key1]['product'] = $product;
                }
            }
        }

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize', 'data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    public function ordersHistory($statut = null, $user_id, $datedepart = null, $datefin = null)
    {

        $this->loadModel('Users');
        $user = $this->Users->get($user_id);

        $this->loadModel('Orders');
        $orders = $this->Orders->find('all')->contain(['Shippings', 'OrderPayments.PaymentMethods', 'Orderpacks.Packs.Packunites.Unites.Parentunites', 'Orderpacks.Turnovers', 'Customers.Zones.Cities', 'Customers.Customertypes', 'Orderpacks.Packs.Brands', 'Orderpacks.Packs.Prices', 'Orderpacks.Packs.Categories']);

        $orders->where(["Orders.statut" => $statut]);
        $orders->order(["Orders.created" => "DESC"]);
        if ($user->role_id == 6) {
            $this->loadModel('Exitslips');
            $shippings = $this->Exitslips->Shippings->find('all')->contain(['Exitslips'])->where(['Exitslips.user_id' => $user_id]);
            $q = [];
            foreach ($shippings as $key => $shipping) {
                $q['OR'][$shipping->id] = [['Shippings.id' => $shipping->id]];
            }
            if ($q) {
                $orders->where([$q]);
            }
        } else {
            $orders->where(['Orders.user_id' => $user_id, 'Orders.statut' => $statut]);
        }
        if ($datedepart && $datefin) {
            $orders->where(['DATE(Orders.created) <= ' => $datefin, 'DATE(Orders.created) >= ' => $datedepart]);
        }
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $orders->limit($limit);
        $orders->page($page);
        $data = [];
        foreach ($orders as $key => $order) {
            $data[$key] = [
                "id" => $order->id,
                "user_id" => $order->user_id,
                "code" => $order->code,
                "ordertype_id" => $order->ordertype_id,
                "date" => $order->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                "statut" => $order->statut,
            ];
            $photo = $this->Orders->Customers->Photos->find('all')->where(['controleur' => 'customers', 'objectid' => $order->customer->id])->order(['created' => 'ASC'])->last();
            $img = Router::Url('/') . 'webroot/img/unvailable.jpg';
            if ($photo) {
                $img = Router::Url('/') . $photo->dir . '/' . $photo->title;
            }
            $orderPayments = [];
            foreach ($order->order_payments as $keyOrderP => $orderPayment) {
                $image = "";
                $photo = $this->Orders->Photos->find('all')->where(['controleur' => 'orderpayments', 'objectid' => $orderPayment->id])->order(['created' => 'ASC'])->last();
                if ($photo) {
                    $image = Router::Url('/') . $photo->dir . '/' . $photo->title;
                } else {
                    $image = "";
                }
                $orderPayments[] = [
                    "id" => $orderPayment->id,
                    "amount" => $orderPayment->amount,
                    "date" => $orderPayment->cheque_date ? $orderPayment->cheque_date->i18nFormat('dd/MM/yyyy', 'Africa/Casablanca') : $orderPayment->created->i18nFormat('dd/MM/yyyy ', 'Africa/Casablanca'),
                    "photo" => $image,
                    "created_at" => $orderPayment->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                    "payment_method" => [
                        "id" => $orderPayment->payment_method->id,
                        "name" => $orderPayment->payment_method->name,
                        "code" => $orderPayment->payment_method->code,
                        "requires_cheque_date" => $orderPayment->payment_method->requires_cheque_date,
                    ]
                ];
            }
            $data[$key]["order_payments"] = $orderPayments;
            $customer = [
                "id" => $order->customer->id,
                "name" => $order->customer->name . "",
                "customertype" => ["id" => $order->customer->customertype->id, "title" => $order->customer->customertype->title . ""],
                "zone" => ["id" => $order->customer->zone->id, "title" => $order->customer->zone->title . ""],
                "adresse" => $order->customer->adresse . "",
                "photo" => $img,
                "phone" => $order->customer->phone . "",
                "latitude" => ($order->customer->latitude !== "null") ? $order->customer->latitude . "" : "33.589261",
                "longitude" => ($order->customer->longitude !== "null") ? $order->customer->longitude . "" : "-7.484916",
                "ice" => $order->customer->ice . "",
                "city" => $order->customer->zone->city->title . "",
                "statut" => $order->customer->statut,
            ];
            $data[$key]["customer"] = $customer;
            $data[$key]["orderpacks"] = [];
            foreach ($order->orderpacks as $key1 => $orderpack) {
                $data[$key]["orderpacks"][$key1] = [
                    "id" => $orderpack->id,
                    "date" => $orderpack->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                    "price" => $orderpack->price,
                    "turnover" => $orderpack->turnover->title,
                    "quantity" => $orderpack->quantity,
                    "statut" => $orderpack->statut,
                    "commissionpack" => ($orderpack->commissionpack) ? $orderpack->commissionpack : 0,
                ];
                $photo = $this->Orders->Orderpacks->Packs->Photos->find('all')->where(['controleur' => 'packs', 'objectid' => $orderpack->pack->id])->order(['created' => 'ASC'])->last();
                $img = Router::Url('/') . 'webroot/img/unvailable.jpg';
                $images = [];
                if ($photo) {
                    $img = Router::Url('/') . $photo->dir . '/' . $photo->photo;
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
                    "type" => $orderpack->pack->packunites[0]->statut,
                    "quantity" => 0,
                    "image" => $img,
                    "images" => $images,
                    "statut" => $orderpack->pack->statut,
                    "turnover" => $orderpack->turnover->title,
                    "variants" => $variants,
                    "brand" => ["id" => $orderpack->pack->brand->id, "title" => $orderpack->pack->brand->title],
                    "category" => ["id" => $orderpack->pack->category->id, "title" => $orderpack->pack->category->title],
                ];
                $data[$key]["orderpacks"][$key1]['product'] = $product;
            }
        }

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize', 'data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    public function totalHistory($datedepart = null, $datefin = null, $user_id)
    {
        $this->loadModel('Orders');
        $orders = $this->Orders->find('all')->contain(['OrderPayments']);
        $orders->where(['Orders.user_id' => $user_id]);
        if ($datedepart && $datefin) {
            $orders->where(['DATE(Orders.created) <= ' => $datefin, 'DATE(Orders.created) >= ' => $datedepart]);
        }
        $total = 0;
        $totalcaisse = 0;
        $totalcredit = 0;
        foreach ($orders as $order) {
            foreach ($order->order_payments as $orderPayment) {
                if ($orderPayment->payment_method_id !== 5) {
                    if ($orderPayment->report_id == null)
                        $totalcaisse += ($orderPayment->amount);
                } else {
                    $totalcredit += ($orderPayment->amount);
                }
                $total += ($orderPayment->amount);
            }
        }
        $data['totalventes'] = intVal($total);
        $data['totalcommandes'] = intVal($totalcaisse);
        $data['totalcredit'] = intVal($totalcredit);
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        echo json_encode($data);
        exit;
    }
    public function orderPaymentDetails($order_id)
    {
        $this->loadModel('Orders');
        $order = $this->Orders->find('all')->contain(['OrderPayments.Payments.Photos' => function ($q) {
            return $q->where(['Photos.controleur' => 'payments', 'statut' => 1]); }, 'OrderPayments.PaymentMethods' => function ($q) {
                return $q->where(['OrderPayments.payment_id IS NOT NULL']); }])->where(['Orders.id' => $order_id])->first();

        $data = [];
        foreach ($order->order_payments as $key => $orderPayment) {
            $payment = [
                "id" => $orderPayment->payment->id,
                "code" => $orderPayment->payment->code,
                "amount" => $orderPayment->payment->amount,
                "date" => $orderPayment->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                "images" => [],
            ];
            $images = [];
            if ($orderPayment->payment->photos)
                foreach ($orderPayment->payment->photos as $photo) {
                    $images[] = Router::Url('/') . $photo->dir . '/' . $photo->title;
                }
            $payment["images"] = $images;

            $data[$key] = [
                "id" => $orderPayment->id,
                "amount" => $orderPayment->amount,
                "date" => $orderPayment->cheque_date ? $orderPayment->cheque_date->i18nFormat('dd/MM/yyyy', 'Africa/Casablanca') : $orderPayment->created->i18nFormat('dd/MM/yyyy ', 'Africa/Casablanca'),
                "payment" => $payment,
                "created_at" => $orderPayment->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                "payment_method" => [
                    "id" => $orderPayment->payment_method->id,
                    "name" => $orderPayment->payment_method->name,
                    "code" => $orderPayment->payment_method->code,
                    "requires_cheque_date" => $orderPayment->payment_method->requires_cheque_date,
                ]
            ];

        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        echo json_encode($data);
        exit;
    }
    public function addNewPayments()
    {
        $this->loadModel('Payments');
        $orderPayment = $this->Payments->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();
            $paymentImages = $datas['payment_images'];
            unset($datas['payment_images']);
            $orderPaymentsToUpdate = [];
            foreach ($datas['order_payments'] as $paymentDatas) {
                if ($paymentDatas['payment_method_id'] != 5) {
                    $orderPayments = $this->Payments->OrderPayments->find('all')->contain(['Orders'])->where(['Orders.customer_id' => $datas['customer_id'], 'OrderPayments.payment_id IS NULL', 'OrderPayments.payment_method_id' => 5]);
                    $amount = $paymentDatas['amount'];
                    foreach ($orderPayments as $keyOrder => $orderPayment) {
                        if ($orderPayment->amount == $paymentDatas['amount']) {
                            $orderPayment->payment_method_id = $paymentDatas['payment_method_id'];
                            $orderPaymentsToUpdate[] = [
                                'id' => $orderPayment->id,
                                'payment_method_id' => $paymentDatas['payment_method_id'],
                                'amount' => $orderPayment->id
                            ];
                            $amount = 0;
                        } elseif ($orderPayment->amount < $paymentDatas['amount']) {
                            $amount = $paymentDatas['amount'] - $orderPayment->amount;
                            $orderPaymentsToUpdate[] = [
                                'id' => $orderPayment->id,
                                'payment_method_id' => $paymentDatas['payment_method_id'],
                                'amount' => $amount
                            ];
                        } else {
                            $orderPaymentsToUpdate[] = [
                                'id' => $orderPayment->id,
                                'payment_method_id' => $paymentDatas['payment_method_id'],
                                'amount' => $paymentDatas['amount']
                            ];
                            $orderPaymentsToUpdate[] = [
                                'payment_method_id' => 5,
                                'order_id' => $orderPayment->order_id,
                                'amount' => $orderPayment->amount - $paymentDatas['amount']
                            ];
                            $amount = 0;
                        }
                        if ($amount == 0) {
                            break;
                        }
                    }
                }
            }
            $newPayment = $this->Payments->newEntity();
            $codePay = $this->Payments->OrderPayments->Orders->Companies->Companycodes->find('all')->where(['controleur' => 'Payments', 'company_id' => 1])->last();
            $newPayment->code = "APP" . $codePay->prefixe . ($codePay->compteur + 1);
            $newPayment->user_id = $datas['user_id'];
            $newPayment->amount = 0;
            if ($this->Payments->save($newPayment)) {
                $codePay->compteur = $codePay->compteur + 1;
                $this->Payments->OrderPayments->Orders->Companies->Companycodes->save($codePay);
                $totalPayment = 0;
                foreach ($orderPaymentsToUpdate as $key => $orderPayment) {
                    if ($orderPayment['payment_method_id'] == 5) {
                        $newCredit = $this->Payments->OrderPayments->newEntity();
                        $newCredit->amount = $orderPayment['amount'];
                        $newCredit->order_id = $orderPayment['order_id'];
                        $newCredit->payment_method_id = 5;
                        $newCredit->statut = 1;
                        $this->Payments->OrderPayments->save($newCredit);
                    } else {
                        $totalPayment += $orderPayment['amount'];
                        $orderPUpdate = $this->Payments->OrderPayments->get($orderPayment['id']);
                        $orderPUpdate->amount = $orderPayment['amount'];
                        $orderPUpdate->payment_id = $newPayment->id;
                        $this->Payments->OrderPayments->save($orderPUpdate);
                    }
                }
                $newPayment->amount = $totalPayment;
                $this->Payments->save($newPayment);
                foreach ($paymentImages as $paymentImage) {
                    if (isset($paymentImage['image']) && $paymentImage['image'] != null) {
                        $this->loadModel('Photos');
                        $photo = $this->Photos->newEntity();
                        $paymentId = $newPayment->id;
                        $paymentPhoto = ($paymentImage['image']) ? base64_decode($paymentImage['image']) : null;
                        $filename = ($paymentImage['image_path']) ? $paymentImage['image_path'] : "$paymentId.jpg";
                        if ($paymentPhoto) {
                            $temp = explode(".", $filename);
                            $extension = end($temp);
                            $name = round(microtime(true) * 1000) . '.' . $extension;
                            file_put_contents('../webroot/files/Photos/payments/' . $name, $paymentPhoto);

                            $photoData = ["title" => $name, "controleur" => "payments", "statut" => 1, "company_id" => 1, 'photo' => $name, 'dir' => 'webroot/files/Photos/payments'];
                        }
                        $photoData["objectid"] = $paymentId;
                        $photo = $this->Photos->patchEntity($photo, $photoData);
                        $this->Photos->save($photo);
                    }
                }
                $data['statut'] = 1;
                $data['message'] = 'Le paeiment a été enregistré.';
            } else {
                $data['statut'] = 0;
                $data['message'] = 'La paeiment n\'a pas enregistré, merci de réessayer.';
            }
        }

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize', 'data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    public function addOrder($order_type = null)
    {
        $this->loadModel('Orders');
        $order = $this->Orders->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();

            $order_type = isset($datas['ordertype_id']) ? $datas['ordertype_id'] : 1;
            if ($order_type == 2) {
                $user_id = $datas['user_id'];
                $customer = $this->Orders->Customers->get($datas['customer_id']);
                if ($customer->referred) {
                    $user = $this->Orders->Users->find('all')->where([
                        "OR" => [
                            ['Users.referral LIKE' => '%' . $customer->referred . '%'],
                            ['lower(Users.referral) LIKE' => '%' . $customer->referred . '%']
                        ]
                    ])->last();
                    if ($user) {
                        $user_id = $user->id;
                    }
                }
                $user = $this->Orders->Users->get($user_id);
                $statut = $user->role_id == 3 ? 6 : 1;
                //si la commande contient des produits
                if ($datas['orderpacks']) {
                    $datas["user_id"] = $user_id;
                    $datas["company_id"] = 1;
                    $datas["statut"] = $statut;
                    $total = 0;

                    // Load OrderPricingService for tranche-based pricing
                    $pricingService = new \App\Service\OrderPricingService();
                    $pofsale = $this->Orders->Pofsales->get($datas['pofsale_id'], ['contain' => ['Warehouses']]);
                    $warehouse_id = $pofsale->warehouse->id;
                    $customer = $this->Orders->Customers->get($datas['customer_id']);
                    $customertype_id = $customer->customertype_id;

                    // Calculate total order amount for amount-based tranches
                    $totalOrderAmount = 0;
                    foreach ($datas['orderpacks'] as $orderpack) {
                        $pack = $this->Orders->Orderpacks->Packs->get($orderpack['pack_id'], ['contain' => ['Prices']]);
                        $totalOrderAmount += $pack->prices[0]->price * $orderpack['quantity'];
                    }

                    foreach ($datas['orderpacks'] as $key => $orderpack) {
                        $datas['orderpacks'][$key]['company_id'] = 1;
                        $datas['orderpacks'][$key]['user_id'] = $user_id;
                        $quantity = $orderpack['quantity'];
                        $pack = $this->Orders->Orderpacks->Packs->get($orderpack['pack_id'], ['contain' => ['Prices', 'MeasurementUnits', 'Packunites.Unites.Parentunites']]);

                        // Use OrderPricingService to calculate price with tranches
                        try {
                            $priceResult = $pricingService->priceLine(
                                $orderpack['pack_id'],
                                $quantity,
                                $customertype_id,
                                $warehouse_id,
                                1, // company_id
                                $totalOrderAmount
                            );
                            $price = $priceResult['final_unit_price'];
                        } catch (\Exception $e) {
                            // Fallback to legacy pricing if service fails
                            if ($pack->measurement_unit->conversion_factor == 1) {
                                $price = $pack->prices[0]->price * $pack->measurement_quantity;
                            } else {
                                $getParent = $this->Orders->Orderpacks->Packs->MeasurementUnits->find('all')->where(['type' => $pack->measurement_unit->type, 'conversion_factor' => ($pack->measurement_unit->conversion_factor * 1000)]);
                                if ($getParent) {
                                    $price = $pack->prices[0]->price * $getParent->first()->conversion_factor / ($pack->measurement_quantity * $pack->measurement_unit->conversion_factor);
                                } else {
                                    $price = $pack->prices[0]->price;
                                }
                            }
                        }

                        if ($pack->measurement_unit->conversion_factor == 1) {
                            $datas['orderpacks'][$key]['loyaltypoints'] = round($pack->loyaltypoints * $pack->measurement_quantity, 2);
                        } else {
                            $getParent = $this->Orders->Orderpacks->Packs->MeasurementUnits->find('all')->where(['type' => $pack->measurement_unit->type, 'conversion_factor' => ($pack->measurement_unit->conversion_factor * 1000)]);
                            if ($getParent) {
                                $datas['orderpacks'][$key]['loyaltypoints'] = round($pack->loyaltypoints * $getParent->first()->conversion_factor / ($pack->measurement_quantity * $pack->measurement_unit->conversion_factor), 2);
                            } else {
                                $datas['orderpacks'][$key]['loyaltypoints'] = round($pack->loyaltypoints, 2);
                            }
                        }

                        $datas['orderpacks'][$key]['initialprice'] = $price;
                        // $datas['orderpacks'][$key]['price'] = $price;
                        $datas['orderpacks'][$key]['commissionpack'] = $pack->commission;
                        $datas['orderpacks'][$key]['turnover_id'] = $pack->turnover_id;
                        $datas['orderpacks'][$key]['statut'] = $statut;
                        $total += $price * $quantity;
                    }

                    $loyaltypoints = $this->Orders->Orderpacks->find('all')->contain(["Orders"])->where(['Orders.customer_id' => $datas['customer_id'], 'Orderpacks.loyalityvalidation' => 1]);
                    $datas['loyaltypoints'] = 0;
                    foreach ($loyaltypoints as $key => $loyaltypoint) {
                        $datas['loyaltypoints'] += ($loyaltypoint->loyaltypoints * $loyaltypoint->quantity);
                    }
                    $datas['loyaltypoints'] = round($datas['loyaltypoints'], 2);
                    $code = $this->Orders->Companies->Companycodes->find('all')->where(['controleur' => 'Orders', 'company_id' => 1])->last();
                    $datas['code'] = "APP" . $code->prefixe . ($code->compteur + 1);
                    $order = $this->Orders->patchEntity($order, $datas, ['associated' => ['Orderpacks', 'OrderPayments']]);
                    $pofsale = $this->Orders->Pofsales->get($datas['pofsale_id'], ['contain' => ['Warehouses.Subwarehouses' => function ($q) {
                        return $q->where(['Subwarehouses.whnature_id' => 1]); }]]);
                    $warehouse_id = $pofsale->warehouse->subwarehouses[0]->id;


                    if ($this->Orders->save($order)) {
                        if ($order->statut == 6) {
                            // Process stock movement for non-return orders
                            $this->processStockMovement($order_type, $order->id, $warehouse_id, $datas['orderpacks']);
                        }
                        $this->loadModel('Visites');
                        $visite = $this->Visites->newEntity([
                            'latittude' => $datas['latitude'],
                            'longitude' => $datas['longitude'],
                            'customer_id' => $datas['customer_id'],
                            'order_id' => $order->id,
                        ]);

                        $this->Visites->save($visite);
                        $data['statut'] = 1;
                        $data['message'] = 'L\'avoir a été enregistré.';
                    } else {
                        $data['statut'] = 0;
                        $data['message'] = 'L\'avoir n\'a pas enregistré, merci de réessayer.';
                    }
                    // la commande ne contient aucun article un message pour resaisir la commande
                } else {
                    $this->loadModel('Visites');
                    $visite = $this->Visites->newEntity([
                        'latittude' => $datas['latitude'],
                        'longitude' => $datas['longitude'],
                        'customer_id' => $datas['customer_id'],
                    ]);

                    $this->Visites->save($visite);
                    $data['statut'] = 1;
                    $data['message'] = 'La visite a été bien enregistrée';
                }
            } else {
                $user_id = $datas['user_id'];
                $customer = $this->Orders->Customers->get($datas['customer_id']);
                if ($customer->referred) {
                    $user = $this->Orders->Users->find('all')->where([
                        "OR" => [
                            ['Users.referral LIKE' => '%' . $customer->referred . '%'],
                            ['lower(Users.referral) LIKE' => '%' . $customer->referred . '%']
                        ]
                    ])->last();
                    if ($user) {
                        $user_id = $user->id;
                    }
                }
                $user = $this->Orders->Users->get($user_id);
                $statut = $user->role_id == 3 ? 6 : 1;
                //si la commande contient des produits
                if ($datas['orderpacks']) {
                    $datas["user_id"] = $user_id;
                    $datas["company_id"] = 1;
                    $datas["statut"] = $statut;
                    $total = 0;

                    // Load OrderPricingService for tranche-based pricing
                    $pricingService = new \App\Service\OrderPricingService();
                    $pofsale = $this->Orders->Pofsales->get($datas['pofsale_id'], ['contain' => ['Warehouses']]);
                    $warehouse_id = $pofsale->warehouse->id;
                    $customer = $this->Orders->Customers->get($datas['customer_id']);
                    $customertype_id = $customer->customertype_id;

                    // Calculate total order amount for amount-based tranches
                    $totalOrderAmount = 0;
                    foreach ($datas['orderpacks'] as $orderpack) {
                        $pack = $this->Orders->Orderpacks->Packs->get($orderpack['pack_id'], ['contain' => ['Prices']]);
                        $totalOrderAmount += $pack->prices[0]->price * $orderpack['quantity'];
                    }

                    foreach ($datas['orderpacks'] as $key => $orderpack) {
                        $datas['orderpacks'][$key]['company_id'] = 1;
                        $datas['orderpacks'][$key]['user_id'] = $user_id;
                        $quantity = $orderpack['quantity'];
                        $pack = $this->Orders->Orderpacks->Packs->get($orderpack['pack_id'], ['contain' => ['Prices', 'MeasurementUnits', 'Packunites.Unites.Parentunites']]);
                        $quantityperKg = 1;
                        if ($pack->saletype_id == 4) {
                            if ($pack->measurement_unit->conversion_factor !== 1) {
                                $getParent = $this->Orders->Orderpacks->Packs->MeasurementUnits->find('all')->where(['type' => $pack->measurement_unit->type, 'conversion_factor' => ($pack->measurement_unit->conversion_factor * 1000)]);
                                if ($getParent) {
                                    $quantityperKg = $getParent->first()->conversion_factor / ($pack->measurement_quantity * $pack->measurement_unit->conversion_factor);
                                    $quantity = $orderpack['quantity'] * $quantityperKg;
                                }
                            }
                        }
                        // Use OrderPricingService to calculate price with tranches
                        try {
                            $priceResult = $pricingService->priceLine(
                                $orderpack['pack_id'],
                                $quantity,
                                $customertype_id,
                                $warehouse_id,
                                1, // company_id
                                $totalOrderAmount
                            );
                            $price = $priceResult['final_unit_price'] * $quantityperKg;
                        } catch (\Exception $e) {
                            $price = $pack->prices[0]->price;
                        }
                        if ($pack->measurement_unit->conversion_factor == 1) {
                            $datas['orderpacks'][$key]['loyaltypoints'] = round($pack->loyaltypoints * $pack->measurement_quantity, 2);
                        } else {
                            $getParent = $this->Orders->Orderpacks->Packs->MeasurementUnits->find('all')->where(['type' => $pack->measurement_unit->type, 'conversion_factor' => ($pack->measurement_unit->conversion_factor * 1000)]);
                            if ($getParent) {
                                $datas['orderpacks'][$key]['loyaltypoints'] = round($pack->loyaltypoints * $getParent->first()->conversion_factor / ($pack->measurement_quantity * $pack->measurement_unit->conversion_factor), 2);
                            } else {
                                $datas['orderpacks'][$key]['loyaltypoints'] = round($pack->loyaltypoints, 2);
                            }
                        }

                        $datas['orderpacks'][$key]['initialprice'] = $price;
                        // $datas['orderpacks'][$key]['price'] = $price;
                        $datas['orderpacks'][$key]['commissionpack'] = $pack->commission;
                        $datas['orderpacks'][$key]['turnover_id'] = $pack->turnover_id;
                        $datas['orderpacks'][$key]['statut'] = $statut;
                        $total += $price * $quantity;
                    }

                    $orderPayments = $datas['order_payments'];
                    foreach ($orderPayments as $keyPay => $orderPayment) {
                        if ($orderPayment['amount'] == 0) {
                            unset($datas['order_payments'][$keyPay]);
                        }
                    }
                    $paymentImages = $datas['payment_images'];

                    $loyaltypoints = $this->Orders->Orderpacks->find('all')->contain(["Orders"])->where(['Orders.customer_id' => $datas['customer_id'], 'Orderpacks.loyalityvalidation' => 1]);
                    $datas['loyaltypoints'] = 0;
                    foreach ($loyaltypoints as $key => $loyaltypoint) {
                        $datas['loyaltypoints'] += ($loyaltypoint->loyaltypoints * $loyaltypoint->quantity);
                    }
                    $datas['loyaltypoints'] = round($datas['loyaltypoints'], 2);
                    $code = $this->Orders->Companies->Companycodes->find('all')->where(['controleur' => 'Orders', 'company_id' => 1])->last();
                    $datas['code'] = "APP" . $code->prefixe . ($code->compteur + 1);
                    $order = $this->Orders->patchEntity($order, $datas, ['associated' => ['Orderpacks', 'OrderPayments']]);
                    // si le type du point de vente est vente indirect
                    $this->loadModel('Shippings');
                    $shipping = $this->Shippings->newEntity();
                    $shipping->company_id = $order->company_id;
                    $shipping->user_id = $order->user_id;
                    $shipping->customer_id = $order->customer_id;
                    $shipping->statut = 2;
                    $codeship = $this->Shippings->Companies->Companycodes->find('all')->where(['controleur' => 'Shippings', 'company_id' => 1])->last();
                    $shipping->code = "APP" . $codeship->prefixe . ($codeship->compteur + 1);
                    $shipping->orders = [0 => $order];
                    $pofsale = $this->Shippings->Orders->Pofsales->get($datas['pofsale_id'], ['contain' => ['Warehouses.Subwarehouses' => function ($q) {
                        return $q->where(['Subwarehouses.whnature_id' => 1]); }]]);
                    $warehouse_id = $pofsale->warehouse->subwarehouses[0]->id;
                    if ($this->Shippings->save($shipping)) {
                        $this->loadModel('Orders');
                        $newPayment = $this->Orders->OrderPayments->Payments->newEntity();
                        $codePay = $this->Orders->Companies->Companycodes->find('all')->where(['controleur' => 'Payments', 'company_id' => 1])->last();
                        $newPayment->code = "APP" . $codePay->prefixe . ($codePay->compteur + 1);
                        $newPayment->user_id = $shipping->orders[0]->user_id;
                        $newPayment->amount = 0;
                        $this->Orders->OrderPayments->Payments->save($newPayment);
                        $codePay->compteur = $codePay->compteur + 1;
                        $this->Shippings->Companies->Companycodes->save($codePay);
                        $totalPayment = 0;
                        foreach ($shipping->orders[0]->order_payments as $key => $orderPayment) {
                            if ($orderPayment->payment_method_id !== 5) {
                                $orderPayment->payment_id = $newPayment->id;
                                $this->Orders->OrderPayments->save($orderPayment);
                                $totalPayment += $orderPayment->amount;
                            }
                        }
                        $newPayment->amount = $totalPayment;
                        $this->Orders->OrderPayments->Payments->save($newPayment);
                        foreach ($paymentImages as $paymentImage) {
                            if (isset($paymentImage['image']) && $paymentImage['image'] != null) {
                                $this->loadModel('Photos');
                                $photo = $this->Photos->newEntity();
                                $paymentId = $newPayment->id;
                                $paymentPhoto = ($paymentImage['image']) ? base64_decode($paymentImage['image']) : null;
                                $filename = ($paymentImage['image_path']) ? $paymentImage['image_path'] : "$paymentId.jpg";
                                if ($paymentPhoto) {
                                    $temp = explode(".", $filename);
                                    $extension = end($temp);
                                    $name = round(microtime(true) * 1000) . '.' . $extension;
                                    file_put_contents('../webroot/files/Photos/payments/' . $name, $paymentPhoto);

                                    $photoData = ["title" => $name, "controleur" => "payments", "statut" => 1, "company_id" => 1, 'photo' => $name, 'dir' => 'webroot/files/Photos/payments'];
                                }
                                $photoData["objectid"] = $paymentId;
                                $photo = $this->Photos->patchEntity($photo, $photoData);
                                $this->Photos->save($photo);
                            }
                        }
                        $this->loadModel('Visites');
                        $visite = $this->Visites->newEntity([
                            'latittude' => $datas['latitude'],
                            'longitude' => $datas['longitude'],
                            'customer_id' => $datas['customer_id'],
                            'order_id' => $shipping->orders[0]->id,
                        ]);

                        $this->Visites->save($visite);
                        $code->compteur = $code->compteur + 1;
                        $this->Shippings->Companies->Companycodes->save($code);
                        $codeship->compteur = $codeship->compteur + 1;
                        $this->Shippings->Companies->Companycodes->save($codeship);
                        if ($shipping->orders[0]->statut == 6) {
                            // Process stock movement for non-return orders
                            $this->processStockMovement($order_type, $shipping->orders[0]->id, $warehouse_id, $datas['orderpacks']);
                        }

                        $data['statut'] = 1;
                        $data['message'] = 'La commande a été enregistré.';
                    } else {
                        $data['statut'] = 0;
                        $data['message'] = 'La commande n\'a pas enregistré, merci de réessayer.';
                    }
                    // la commande ne contient aucun article un message pour resaisir la commande
                } else {
                    $this->loadModel('Visites');
                    $visite = $this->Visites->newEntity([
                        'latittude' => $datas['latitude'],
                        'longitude' => $datas['longitude'],
                        'customer_id' => $datas['customer_id'],
                    ]);

                    $this->Visites->save($visite);
                    $data['statut'] = 1;
                    $data['message'] = 'La visite a été bien enregistrée';
                }

            }
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize', 'data');
        $this->RequestHandler->renderAs($this, 'json');
    }

    /**
     * Process stock movement when adding a new order
     * Removes quantity from subwarehouse whproducts with whnature_id=2
     * Only if order_type != 2
     *
     * @param int $order_type Order type ID
     * @param array $orderpacks Array of orderpacks with pack_id and quantity
     * @return bool True if stock movement successful, false otherwise
     */
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
        $this->loadModel('StockMovements');
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
                        'company_id' => 1
                    ];
                    // Calculate new quantity after addition
                    $oldQty = $whproduct->quantity;
                    $newQuantity = $oldQty + $quantity;

                    $pack = $this->Whproducts->Packs->get($pack_id);

                    if ($pack->saletype_id == 4 && ($pack->measurement_unit_id == 2 || $pack->measurement_unit_id == 4)) {
                        $newQuantity = $oldQty + ($quantity * 1000 / $pack->measurement_quantity);
                    }
                    // Update the whproduct quantity
                    $whproduct->quantity = $newQuantity;

                    $dataInventory['invproducts'][] = [
                        'pack_id' => $pack_id,
                        'quantity' => $whproduct->quantity,
                        'statut' => 3,
                        'company_id' => 1
                    ];
                    if ($this->Whproducts->save($whproduct)) {
                        $stockMovement = $this->StockMovements->newEntity([
                            'item_id' => $pack_id,
                            'item_type' => 'Pack',
                            'warehouse_id' => $warehouse_id,
                            'quantity_change' => $newQuantity - $oldQty,
                            'balance_after_movement' => $newQuantity,
                            'movement_type' => 'meta_order_return',
                            'user_id' => $order->user_id,
                            'company_id' => $order->company_id,
                            'notes' => 'Meta sales return order (Order ID: ' . $order->id . ')'
                        ]);
                        $this->StockMovements->save($stockMovement);
                    } else {
                        // Log error but continue processing other products
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
                        'company_id' => 1
                    ];
                    // Calculate new quantity after removal
                    $oldQty = $whproduct->quantity;
                    $newQuantity = $oldQty - $quantity;

                    $pack = $this->Whproducts->Packs->get($pack_id);

                    if ($pack->saletype_id == 4 && ($pack->measurement_unit_id == 2 || $pack->measurement_unit_id == 4)) {
                        $newQuantity = $oldQty - ($quantity * 1000 / $pack->measurement_quantity);
                    }
                    // Update the whproduct quantity
                    $whproduct->quantity = $newQuantity;

                    $dataInventory['invproducts'][] = [
                        'pack_id' => $pack_id,
                        'quantity' => $whproduct->quantity,
                        'statut' => 3,
                        'company_id' => 1
                    ];
                    if ($this->Whproducts->save($whproduct)) {
                        $stockMovement = $this->StockMovements->newEntity([
                            'item_id' => $pack_id,
                            'item_type' => 'Pack',
                            'warehouse_id' => $warehouse_id,
                            'quantity_change' => $newQuantity - $oldQty,
                            'balance_after_movement' => $newQuantity,
                            'movement_type' => 'meta_order_sale',
                            'user_id' => $order->user_id,
                            'company_id' => $order->company_id,
                            'notes' => 'Meta sales order (Order ID: ' . $order->id . ')'
                        ]);
                        $this->StockMovements->save($stockMovement);
                    } else {
                        // Log error but continue processing other products
                        error_log("Failed to update stock for pack_id: $pack_id");
                    }
                }
            }
            $inventory = $this->Inventories->patchEntity($inventory, $dataInventory, ['associated' => ['Invproducts']]);

            $this->Inventories->save($inventory);
        }
    }

    public function editOrder()
    {
        // Récupération des données envoyées par la requête
        $datas = $this->request->getData();
        $this->loadModel('Orders');
        // Récupération de l'utilisateur qui modifie la commande
        $user = $this->Orders->Users->get($datas["user_id"]);
        // Récupération de la commande à modifier avec ses produits (Orderpacks)
        $order = $this->Orders->get($datas['order_id'], ['contain' => ['Orderpacks']]);

        // Création d'un tableau associatif des anciens produits de la commande (clé = id de l'orderpack)
        $oldOrderpacksById = [];
        foreach ($order->orderpacks as $orderpack) {
            $oldOrderpacksById[$orderpack->id] = $orderpack;
        }

        // Création d'un tableau associatif des nouveaux produits envoyés (clé = id de l'orderpack)
        $newOrderpacksById = [];
        foreach ($datas['orderpacks'] as $dataorderpack) {
            if ($dataorderpack['id'] != 0) {
                $newOrderpacksById[$dataorderpack['id']] = $dataorderpack;
            }
        }
        // Parcours des anciens produits pour gérer la modification ou l'annulation
        foreach ($oldOrderpacksById as $id => $orderpack) {
            if (isset($newOrderpacksById[$id])) {
                // Si le produit existe toujours dans la nouvelle commande, on met à jour la quantité et le statut
                $newQty = $newOrderpacksById[$id]['quantity'];

                if (($user->role_id == 6 || $user->role_id == 3) && $newQty < $orderpack->quantity) {

                    // Si l'utilisateur est un livreur et la quantité diminue, on crée un orderpack annulé pour la différence
                    $canceledPack = $this->Orders->Orderpacks->newEntity([
                        'order_id' => $orderpack->order_id,
                        'pack_id' => $orderpack->pack_id,
                        'quantity' => $orderpack->quantity - $newQty,
                        'statut' => 8, // 8 = annulé
                        'user_id' => $user->id,
                        'whnature_id' => $orderpack->whnature_id,
                        'price' => $orderpack->price,
                        'tranche_id' => $orderpack->tranche_id,
                        'tarif_id' => $orderpack->tarif_id,
                        'company_id' => $orderpack->company_id,
                        'commission_id' => ($orderpack->commission_id !== 0) ? $orderpack->commission_id : null,
                        'commissionpack' => $orderpack->commissionpack,
                        'loyaltypoints' => $orderpack->loyaltypoints,
                        'loyalityvalidation' => $orderpack->loyalityvalidation,
                        'turnover_id' => $orderpack->turnover_id,
                    ]);

                    $this->Orders->Orderpacks->save($canceledPack);

                }
                // Mise à jour de la quantité et du statut du produit
                $orderpack->quantity = $newQty;
                $orderpack->statut = ($user->role_id == 6 || $user->role_id == 3) ? 6 : 1;
                $this->Orders->Orderpacks->save($orderpack);
            } else {
                // Si le produit a été supprimé de la commande
                if ($user->role_id == 6 || $user->role_id == 3) {
                    // Si c'est un livreur, on marque le produit comme annulé
                    $orderpack->statut = 8; // 8 = annulé
                    $this->Orders->Orderpacks->save($orderpack);
                } else {
                    // Sinon, on supprime le produit de la commande
                    $this->Orders->Orderpacks->delete($orderpack);
                }
            }
        }
        $pricingService = new \App\Service\OrderPricingService();
        $pofsale = $this->Orders->Pofsales->get($order->pofsale_id, ['contain' => ['Warehouses']]);
        $warehouse_id = $pofsale->warehouse->id;
        $customer = $this->Orders->Customers->get($order->customer_id);
        $customertype_id = $customer->customertype_id;

        // Calculate total order amount for amount-based tranches
        $totalOrderAmount = 0;
        foreach ($datas['orderpacks'] as $orderpack) {
            $pack = $this->Orders->Orderpacks->Packs->get($orderpack['pack_id'], ['contain' => ['Prices']]);
            $totalOrderAmount += $pack->prices[0]->price * $orderpack['quantity'];
        }
        // Parcours des nouveaux produits pour ajouter ceux qui n'existaient pas avant (id = 0)
        foreach ($datas['orderpacks'] as $dataorderpack) {
            if ($dataorderpack['id'] == 0) {
                // Préparation des champs obligatoires
                $dataorderpack["user_id"] = $datas["user_id"];
                $dataorderpack["company_id"] = 1;
                $dataorderpack["order_id"] = $datas["order_id"];
                $dataorderpack["statut"] = ($user->role_id == 6) ? 6 : 1;
                $dataorderpack["whnature_id"] = 1;
                $quantity = $dataorderpack['quantity'];
                $pack = $this->Orders->Orderpacks->Packs->get($dataorderpack['pack_id'], ['contain' => ['Prices', 'MeasurementUnits', 'Packunites.Unites.Parentunites']]);
                // Use OrderPricingService to calculate price with tranches
                try {
                    $priceResult = $pricingService->priceLine(
                        $dataorderpack['pack_id'],
                        $quantity,
                        $customertype_id,
                        $warehouse_id,
                        1, // company_id
                        $totalOrderAmount
                    );
                    $price = $priceResult['final_unit_price'];
                } catch (\Exception $e) {
                    // Fallback to legacy pricing if service fails
                    if ($pack->measurement_unit->conversion_factor == 1) {
                        $price = $pack->prices[0]->price * $pack->measurement_quantity;
                    } else {
                        $getParent = $this->Orders->Orderpacks->Packs->MeasurementUnits->find('all')->where(['type' => $pack->measurement_unit->type, 'conversion_factor' => ($pack->measurement_unit->conversion_factor * 1000)]);
                        if ($getParent) {
                            $price = $pack->prices[0]->price * $getParent->first()->conversion_factor / ($pack->measurement_quantity * $pack->measurement_unit->conversion_factor);
                        } else {
                            $price = $pack->prices[0]->price;
                        }
                    }
                }
                if ($pack->measurement_unit->conversion_factor == 1) {
                    $dataorderpack['loyaltypoints'] = round($pack->loyaltypoints * $pack->measurement_quantity, 2);
                } else {
                    $getParent = $this->Orders->Orderpacks->Packs->MeasurementUnits->find('all')->where(['type' => $pack->measurement_unit->type, 'conversion_factor' => ($pack->measurement_unit->conversion_factor * 1000)]);
                    if ($getParent) {
                        $dataorderpack['loyaltypoints'] = round($pack->loyaltypoints * $getParent->first()->conversion_factor / ($pack->measurement_quantity * $pack->measurement_unit->conversion_factor), 2);
                    } else {
                        $dataorderpack['loyaltypoints'] = round($pack->loyaltypoints, 2);
                    }
                }
                $dataorderpack["commissionpack"] = $pack->commission;
                $dataorderpack["turnover_id"] = $pack->turnover_id;
                $dataorderpack["initialprice"] = $price;
                $dataorderpack["buyingprice"] = $pack->buyingprice;
                // Création et sauvegarde du nouveau produit
                $neworderpack = $this->Orders->Orderpacks->newEntity($dataorderpack);
                $this->Orders->Orderpacks->save($neworderpack);
            }
        }
        if ($user->role_id == 3) {
            $order->statut = 6;
            $this->Orders->save($order);
        } elseif ($user->role_id == 6) {
            $order->statut = 6;

            $orderPayments = $datas['order_payments'];
            foreach ($orderPayments as $keyPay => $orderPayment) {
                if ($orderPayment['amount'] == 0) {
                    unset($datas['order_payments'][$keyPay]);
                }
            }
            $dataPayments = [
                'id' => $datas['order_id'],
                'statut' => 6,
                'order_payments' => $datas['order_payments'],
            ];
            $paymentImages = $datas['payment_images'];

            $order = $this->Orders->patchEntity($order, $dataPayments, ['associated' => ['OrderPayments']]);
            if ($this->Orders->save($order)) {
                $newPayment = $this->Orders->OrderPayments->Payments->newEntity();
                $codePay = $this->Orders->Companies->Companycodes->find('all')->where(['controleur' => 'Payments', 'company_id' => 1])->last();
                $newPayment->code = "APP" . $codePay->prefixe . ($codePay->compteur + 1);
                $newPayment->user_id = $datas['user_id'];
                $newPayment->amount = 0;
                $this->Orders->OrderPayments->Payments->save($newPayment);
                $codePay->compteur = $codePay->compteur + 1;
                $this->Orders->Companies->Companycodes->save($codePay);
                $totalPayment = 0;
                foreach ($order->order_payments as $key => $orderPayment) {
                    if ($orderPayment->payment_method_id !== 5) {
                        $orderPayment->payment_id = $newPayment->id;
                        $this->Orders->OrderPayments->save($orderPayment);
                        $totalPayment += $orderPayment->amount;
                    }
                }
                $newPayment->amount = $totalPayment;
                $this->Orders->OrderPayments->Payments->save($newPayment);
                foreach ($paymentImages as $paymentImage) {
                    if (isset($paymentImage['image']) && $paymentImage['image'] != null) {
                        $this->loadModel('Photos');
                        $photo = $this->Photos->newEntity();
                        $paymentId = $newPayment->id;
                        $paymentPhoto = ($paymentImage['image']) ? base64_decode($paymentImage['image']) : null;
                        $filename = ($paymentImage['image_path']) ? $paymentImage['image_path'] : "$paymentId.jpg";
                        if ($paymentPhoto) {
                            $temp = explode(".", $filename);
                            $extension = end($temp);
                            $name = round(microtime(true) * 1000) . '.' . $extension;
                            file_put_contents('../webroot/files/Photos/payments/' . $name, $paymentPhoto);

                            $photoData = ["title" => $name, "controleur" => "payments", "statut" => 1, "company_id" => 1, 'photo' => $name, 'dir' => 'webroot/files/Photos/payments'];
                        }
                        $photoData["objectid"] = $paymentId;
                        $photo = $this->Photos->patchEntity($photo, $photoData);
                        $this->Photos->save($photo);
                    }
                }
            }
        }
        // Préparation de la réponse JSON
        $data['statut'] = 1;
        $data['message'] = 'La commande a été modifiée.';
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize', 'data');
        $this->RequestHandler->renderAs($this, 'json');
    }

    public function cancelOrder($order_id)
    {
        $this->loadModel('Orders');
        $order = $this->Orders->get($order_id, ['contain' => ['Orderpacks']]);
        $orderData = ["id" => $order->id, "statut" => 8];
        foreach ($order->orderpacks as $orderpack) {
            $orderData["orderpacks"][] = ["id" => $orderpack->id, "statut" => 8];
        }
        $order = $this->Orders->patchEntity($order, $orderData, ['associated' => ['Orderpacks']]);
        if ($this->Orders->save($order)) {
            $data['statut'] = 1;
            $data['message'] = 'La commande a été annulée.';
        } else {
            $data['statut'] = 0;
            $data['message'] = 'La commande n\'es pas annulée.';
        }

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize', 'data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    public function deleteOrder($order_id)
    {
        $this->loadModel('Orders');
        $order = $this->Orders->get($order_id, ['contain' => ['Orderpacks', 'Shippings']]);
        if ($order->statut == 1) {
            if ($this->Orders->delete($order)) {
                $data['statut'] = 1;
                $data['message'] = 'La commande a été supprimée.';
            } else {
                $data['statut'] = 0;
                $data['message'] = 'La commande n\'es pas supprimée.';
            }
        } else {
            $data['statut'] = 0;
            $data['message'] = 'Aucune commande trouvés.';
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize', 'data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    public function customerCredit()
    {
        $dataPayment = $this->request->getData();
        $this->loadModel('OrderPayments');
        $paymentAmount = $dataPayment['paymentAmount'];
        $orderPaymentsToUpdate = [];
        $orderPaymentsToCreate = [];

        $allOrderIds = collection($dataPayment['orders'])->extract('orderId')->toList();

        // Get all relevant payments first
        $allPayments = $this->OrderPayments->find()
            ->where([
                'order_id IN' => $allOrderIds,
                'payment_method_id' => 5
            ])
            ->all();

        $totalAvailable = $allPayments->sumOf('amount');

        if ($paymentAmount > $totalAvailable) {
            $data['statut'] = 0;
            $data['message'] = 'Le montant de paiement dépasse le total disponible.';
            $this->set(compact('data'));
            $this->set('_serialize', 'data');
            $this->RequestHandler->renderAs($this, 'json');
            return;
        }
        // Process normally
        foreach ($dataPayment["orders"] as $order) {
            if ($paymentAmount <= 0)
                break;

            $orderPayments = $allPayments->filter(function ($p) use ($order) {
                return $p->order_id == $order['orderId'];
            })->sortBy('id');

            foreach ($orderPayments as $payment) {
                if ($paymentAmount <= 0)
                    break;

                $originalAmount = $payment->amount;

                if ($originalAmount <= $paymentAmount) {
                    $payment->payment_method_id = 1;
                    $orderPaymentsToUpdate[] = $payment;

                    $paymentAmount -= $originalAmount;
                } else {
                    // Partial case
                    $payment->amount = $originalAmount - $paymentAmount;
                    $orderPaymentsToUpdate[] = $payment;

                    $newPayment = $this->OrderPayments->newEntity($payment->toArray());
                    $newPayment->id = null;
                    $newPayment->amount = $paymentAmount;
                    $newPayment->payment_method_id = 1;
                    $orderPaymentsToCreate[] = $newPayment;

                    $paymentAmount = 0;
                }
            }
        }

        // Save all changes
        $success = true;
        if (!empty($orderPaymentsToUpdate)) {
            $success = $this->OrderPayments->saveMany($orderPaymentsToUpdate);
        }

        if ($success && !empty($orderPaymentsToCreate)) {
            $success = $this->OrderPayments->saveMany($orderPaymentsToCreate);
        }

        $data['statut'] = $success ? 1 : 0;
        $data['message'] = $success
            ? 'Le crédit a été mis à jour.'
            : 'Le crédit n\'a pas été mis à jour.';
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize', 'data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    public function customerOrders($customerId)
    {

        $this->loadModel('Orders');
        $orders = $this->Orders->find('all')->contain(['Users', 'OrderPayments.PaymentMethods', 'Shippings', 'Orderpacks.Packs.MeasurementUnits', 'Orderpacks.Turnovers', 'Orderpacks.Packs.Turnovers', 'Orderpacks.Packs.Packunites.Unites.Parentunites', 'Customers.Zones.Cities', 'Customers.Customertypes', 'Orderpacks.Packs.Brands', 'Orderpacks.Packs.Prices', 'Orderpacks.Packs.Categories']);
        $orders->where(['Orders.customer_id' => $customerId]);
        $data = [];
        foreach ($orders as $key => $order) {
            $data[$key] = [
                "id" => $order->id,
                "code" => $order->code,
                "ordertype_id" => $order->ordertype_id,
                "user_id" => $order->user_id,
                "date" => $order->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                "statut" => $order->statut,
            ];
            $photo = $this->Orders->Customers->Photos->find('all')->where(['controleur' => 'customers', 'objectid' => $order->customer->id])->order(['created' => 'ASC'])->last();
            $img = Router::Url('/') . 'webroot/img/unvailable.jpg';
            if ($photo) {
                $img = Router::Url('/') . $photo->dir . '/' . $photo->photo;
            }
            $orderPayments = [];
            foreach ($order->order_payments as $keyOrderP => $orderPayment) {
                $image = "";
                $photo = $this->Orders->Photos->find('all')->where(['controleur' => 'orderpayments', 'objectid' => $orderPayment->id])->order(['created' => 'ASC'])->last();
                if ($photo) {
                    $image = Router::Url('/') . $photo->dir . '/' . $photo->title;
                } else {
                    $image = "";
                }
                $orderPayments[] = [
                    "id" => $orderPayment->id,
                    "amount" => $orderPayment->amount,
                    "date" => $orderPayment->cheque_date ? $orderPayment->cheque_date->i18nFormat('dd/MM/yyyy', 'Africa/Casablanca') : $orderPayment->created->i18nFormat('dd/MM/yyyy ', 'Africa/Casablanca'),
                    "photo" => $image,
                    "created_at" => $orderPayment->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                    "payment_method" => [
                        "id" => $orderPayment->payment_method->id,
                        "name" => $orderPayment->payment_method->name,
                        "code" => $orderPayment->payment_method->code,
                        "requires_cheque_date" => $orderPayment->payment_method->requires_cheque_date,
                    ]
                ];
            }
            $data[$key]["order_payments"] = $orderPayments;
            $customer = [
                "id" => $order->customer->id,
                "name" => $order->customer->name . "",
                "customertype" => ["id" => $order->customer->customertype->id, "title" => $order->customer->customertype->title . ""],
                "zone" => ["id" => $order->customer->zone->id, "title" => $order->customer->zone->title . ""],
                "adresse" => $order->customer->adresse . "",
                "photo" => $img,
                "phone" => $order->customer->phone . "",
                "latitude" => $order->customer->latitude . "",
                "longitude" => $order->customer->longitude . "",
                "ice" => $order->customer->ice . "",
                "city" => $order->customer->zone->city->title . "",
                "statut" => $order->customer->statut,
            ];
            $data[$key]["customer"] = $customer;
            foreach ($order->orderpacks as $key1 => $orderpack) {
                if ($orderpack->pack->measurement_unit->conversion_factor == 1) {
                    $loyaltypoints = $orderpack->pack->measurement_quantity . $orderpack->pack->measurement_unit->abbreviation;
                } else {
                    $getParent = $this->Orders->Orderpacks->Packs->MeasurementUnits->find('all')->where(['type' => $orderpack->pack->measurement_unit->type, 'conversion_factor' => ($orderpack->pack->measurement_unit->conversion_factor * 1000)]);
                    $loyaltypoints = (($getParent->first()->conversion_factor / ($orderpack->pack->measurement_quantity * $orderpack->pack->measurement_unit->conversion_factor)) * $orderpack->loyaltypoints) . "/" . $getParent->first()->conversion_factor . $getParent->first()->abbreviation;
                }
                $data[$key]["orderpacks"][$key1] = [
                    "id" => $orderpack->id,
                    "date" => $orderpack->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                    "price" => $orderpack->price,
                    "quantity" => $orderpack->quantity,
                    "turnover" => $loyaltypoints,
                    "loyalty" => $orderpack->loyaltypoints,
                    "statut" => $orderpack->statut,
                    "commissionpack" => ($orderpack->commissionpack) ? $orderpack->commissionpack : 0,
                ];
                $this->loadModel('Customers');
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
                    // Unité 
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
                    "turnover" => $loyaltypoints,
                    "loyalty" => $orderpack->loyaltypoints,
                    "variants" => $variants,
                    "brand" => ["id" => $orderpack->pack->brand->id, "title" => $orderpack->pack->brand->title],
                    "category" => ["id" => $orderpack->pack->category->id, "title" => $orderpack->pack->category->title],
                ];
                $data[$key]["orderpacks"][$key1]['product'] = $product;
            }
        }

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize', 'data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    public function paymentMethods()
    {
        $this->loadModel('PaymentMethods');
        $methods = $this->PaymentMethods->find('all')->where(['active' => 1]);
        $data = [];
        foreach ($methods as $key => $method) {
            $data[] = [
                "id" => $method->id,
                "name" => $method->name,
                "code" => $method->code,
                "requires_cheque_date" => $method->requires_cheque_date,
                "isActive" => $method->active,
            ];
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize', 'data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    // Slips
    public function slipList($sliptypeId, $user_id, $searchValue = "")
    {

        $this->loadModel('Slips');
        $slips = $this->Slips->find('all')->contain(['Slipproducts.Packs.Packunites.Unites.Parentunites', 'Slipproducts.Packs.Turnovers', 'Slipproducts.Packs.Brands', 'Slipproducts.Packs.Prices', 'Slipproducts.Packs.Categories'])->where(['Slips.sliptype_id' => $sliptypeId]);
        $slips->order(['Slips.id' => 'DESC']);
        $slips->where(['Slips.user_id' => $user_id]);
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        $slips->limit($limit);
        $slips->page($page);
        $data = [];
        foreach ($slips as $key => $slip) {
            $data[$key] = [
                "id" => $slip->id,
                "code" => $slip->code,
                "date" => $slip->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                "statut" => $slip->statut,
            ];

            foreach ($slip->slipproducts as $key1 => $slipproduct) {
                $data[$key]["slipproducts"][$key1] = [
                    "id" => $slipproduct->id,
                    "date" => $slipproduct->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                    "price" => $slipproduct->price,
                    "quantity" => $slipproduct->quantity,
                    "statut" => $slipproduct->statut,
                    "commissionpack" => ($slipproduct->commissionpack) ? $slipproduct->commissionpack : 0,
                ];
                $photo = $this->Slips->Slipproducts->Packs->Photos->find('all')->where(['controleur' => 'packs', 'objectid' => $slipproduct->pack->id])->order(['created' => 'ASC'])->last();
                $img = Router::Url('/') . 'webroot/img/unvailable.jpg';
                $images = [];
                if ($photo) {
                    $img = Router::Url('/') . $photo->dir . '/' . $photo->photo;
                    $images[] = Router::Url('/') . $photo->dir . '/' . $photo->photo;
                } else {
                    $images[] = $img;
                }
                $variants = [];
                //Sac & Unité
                if ($slipproduct->pack->packunites[0]->statut == 1) {
                    $variants[0] = [
                        'id' => $slipproduct->pack->packunites[0]->unite->id,
                        'title' => $slipproduct->pack->packunites[0]->unite->title,
                        'quantity' => $slipproduct->pack->packunites[0]->quantity,
                        'statut' => 1,
                    ];
                    $variants[1] = [
                        'id' => $slipproduct->pack->packunites[0]->unite->parentunite->id,
                        'title' => $slipproduct->pack->packunites[0]->unite->parentunite->title,
                        'quantity' => 1,
                        'statut' => 1,
                    ];
                    // Sac
                } elseif ($slipproduct->pack->packunites[0]->statut == 2) {

                    $variants[0] = [
                        'id' => $slipproduct->pack->packunites[0]->unite->id,
                        'title' => $slipproduct->pack->packunites[0]->unite->title,
                        'quantity' => $slipproduct->pack->packunites[0]->quantity,
                        'statut' => 1,
                    ];
                    $variants[1] = [
                        'id' => $slipproduct->pack->packunites[0]->unite->parentunite->id,
                        'title' => $slipproduct->pack->packunites[0]->unite->parentunite->title,
                        'quantity' => 1,
                        'statut' => 0,
                    ];
                    //Unité
                } else {
                    $variants[0] = [
                        'id' => $slipproduct->pack->packunites[0]->unite->id,
                        'title' => $slipproduct->pack->packunites[0]->unite->title,
                        'quantity' => $slipproduct->pack->packunites[0]->quantity,
                        'statut' => 0,
                    ];
                    $variants[1] = [
                        'id' => $slipproduct->pack->packunites[0]->unite->parentunite->id,
                        'title' => $slipproduct->pack->packunites[0]->unite->parentunite->title,
                        'quantity' => 1,
                        'statut' => 1,
                    ];
                }
                $product = [
                    "id" => $slipproduct->pack->id,
                    "code" => $slipproduct->pack->code,
                    "title" => $slipproduct->pack->title,
                    "price" => $slipproduct->pack->prices[0]->price,
                    "type" => $slipproduct->pack->packunites[0]->statut,
                    "quantity" => 0,
                    "image" => $img,
                    "images" => $images,
                    "statut" => $slipproduct->pack->statut,
                    "turnover" => $slipproduct->pack->turnover->title,
                    "variants" => $variants,
                    "brand" => ["id" => $slipproduct->pack->brand->id, "title" => $slipproduct->pack->brand->title],
                    "category" => ["id" => $slipproduct->pack->category->id, "title" => $slipproduct->pack->category->title],
                ];
                $data[$key]["slipproducts"][$key1]['product'] = $product;
            }
        }

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize', 'data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    public function createSlip()
    {
        $this->loadModel('Slips');
        $slip = $this->Slips->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            if ($data['slipproducts']) {
                $code = $this->Slips->Companies->Companycodes->find('all')->where(['controleur' => 'Slips' . $data['sliptype_id'], 'company_id' => 1])->last();
                $slipCode = $code->prefixe . ($code->compteur + 1);
                $pofsale = $this->Slips->Warehouses->Pofsales->get($data['pofsale_id'], ['contain' => ['Warehouses']]);
                $warehouse = 0;
                $warehoused = 0;
                if ($data['sliptype_id'] == 1) {
                    $warehouse = $pofsale->warehouse->warehouse_id;
                    $warehoused = $pofsale->warehouse->id;
                } else {
                    $warehouse = $pofsale->warehouse->id;
                    $warehoused = $pofsale->warehouse->warehouse_id;
                }
                $slipData = [
                    "code" => $slipCode,
                    "warehouse_id" => $warehouse,
                    "warehoused" => $warehoused,
                    "whnature_id" => 1,
                    "whnatured" => 1,
                    "user_id" => $data['user_id'],
                    "sliptype_id" => $data['sliptype_id'],
                    "company_id" => 1,
                    "statut" => 2,
                ];
                foreach ($data['slipproducts'] as $slipproduct) {
                    $pack = $this->Slips->Slipproducts->Packs->get($slipproduct['pack_id']);
                    $slipData['slipproducts'][] = [
                        "item_id" => $slipproduct['pack_id'],
                        "item_type" => 'Pack',
                        "unity_id" => 4,
                        "quantity" => $slipproduct['quantity'],
                        "price" => $slipproduct['price'],
                        "whnature_id" => 1,
                        "user_id" => $data['user_id'],
                        "company_id" => 1,
                        "statut" => 2,
                    ];
                }
                $slip = $this->Slips->patchEntity($slip, $slipData, ['associated' => ['slipproducts']]);

                if ($this->Slips->save($slip)) {
                    $updateCode = $this->Slips->Companies->Companycodes->get($code->id);
                    $updateCode->compteur += 1;
                    $this->Slips->Companies->Companycodes->save($updateCode);
                    $msg['statut'] = 1;
                    $msg['message'] = 'La commande a été enregistré.';
                } else {

                    $msg['statut'] = 0;
                    $msg['message'] = 'La commande n\'a pas enregistré, merci de réessayer.';
                }
                // la commande ne contient aucun article un message pour resaisir la commande
            } else {
                $msg['statut'] = 0;
                $msg['message'] = 'Merci de charger les produits. Veuillez réessayer.';
            }
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('msg'));
        $this->set('_serialize', 'msg');
        $this->RequestHandler->renderAs($this, 'json');
    }

    public function editSlip()
    {
        $datas = $this->request->getData();
        $this->loadModel('Slips');
        $oldslippacks = [];
        $newslippacks = [];
        $newslippackQts = [];
        $slip = $this->Slips->get($datas['slip_id'], ['contain' => ['Slipproducts']]);
        foreach ($slip->slipproducts as $index => $slipproduct) {
            $oldslippacks[$index] = $slipproduct->id;
        }
        foreach ($datas['slipproducts'] as $key => $dataslippack) {
            if ($dataslippack['id'] == 0) {
                $newslippack = $this->Slips->Slipproducts->newEntity();
                $dataslippack["user_id"] = $datas["user_id"];
                $dataslippack["company_id"] = 1;
                $dataslippack["order_id"] = $datas["order_id"];
                $newslippack = $this->Slips->Slipproducts->patchEntity($newslippack, $dataslippack);
                $this->Slips->Slipproducts->save($newslippack);
            }
            $newslippacks[$key] = $dataslippack['id'];
            $newslippackQts[$key] = $dataslippack['quantity'];
        }
        foreach ($oldslippacks as $index => $oldslippack) {
            if (in_array($oldslippack, $newslippacks)) {
                $key = array_search($oldslippack, $newslippacks);
                $slippack = $this->Slips->Slipproducts->get($oldslippack);
                $slippack->quantity = $newslippackQts[$key];
                $this->Slips->Slipproducts->save($slippack);
            } else {
                $deletSlipPack = $this->Slips->Slipproducts->get($oldslippack);
                $this->Slips->Slipproducts->delete($deletSlipPack);
            }
        }
        $data['statut'] = 1;
        $data['message'] = 'Le bon a été modifiée.';
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize', 'data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    public function deleteSlip($slip_id)
    {
        $this->loadModel('Slips');
        $slip = $this->Slips->get($slip_id, ['contain' => ['Slipproducts']]);
        if ($slip->statut == 1) {
            if ($this->Slips->delete($slip)) {
                $this->Flash->success(__('Le bon a été supprimé.'));
                $data['statut'] = 1;
                $data['message'] = 'Le bon a été supprimé.';
            } else {
                $data['statut'] = 0;
                $data['message'] = 'Le bon n\'es pas supprimé.';
            }
        } else {
            $data['statut'] = 0;
            $data['message'] = 'Aucun bon trouvés.';
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize', 'data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    // Customers
    public function customers($user_id = null, $latitude, $longitude, $isReturn = 0)
    {
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $searchText = isset($_GET['search']) ? $_GET['search'] : "";
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $this->loadModel('Customers');
        $user = $this->Customers->Companies->Users->find("all")->where(['id' => $user_id])->first();
        if ($user && $user->role_id == 5) {
            $customerType = 3;
        } else {
            $customerType = 2;
        }
        //a voir searchText fach katkon khawya
        $distance = 100;
        if ($user_id) {
            if ($isReturn == 1) {
                $empQuery = $this->Customers->find('all')
                    ->select([
                        'Customers.id',
                        'Customers.code',
                        'Customers.name',
                        'Customers.phone',
                        'Customers.adresse',
                        'Customers.customertype_id',
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
                    ])
                    ->contain(['Zones.Cities', 'Customertypes']);
                $empQuery->where(['Customers.customertype_id' => $customerType, 'Customers.statut' => 1]);
                if ($searchText) {
                    $empQuery->where([
                        "OR" => [
                            ['Customers.code LIKE' => '%' . $searchText . '%'],
                            ['lower(Customers.code) LIKE' => '%' . $searchText . '%'],
                            ['Customers.name LIKE' => '%' . $searchText . '%'],
                            ['Customers.phone LIKE' => '%' . $searchText . '%'],
                            ['lower(Customers.name) LIKE' => '%' . $searchText . '%'],
                            ['Customers.adresse LIKE' => '%' . $searchText . '%'],
                            ['lower(Customers.adresse) LIKE' => '%' . $searchText . '%'],
                        ]
                    ]);
                }
                $zoneusers = $this->Customers->Zones->Zoneusers->find('all')->contain(['Zones.Subzones'])->where(['Zoneusers.user_id' => $user_id, 'Zoneusers.statut' => 1]);
                $q = [];

                foreach ($zoneusers as $key => $zoneuser) {
                    foreach ($zoneuser->zone->subzones as $subzone) {
                        $q['OR'][$subzone->id] = [['Customers.zone_id' => $subzone->id]];
                    }
                }
                if ($q) {
                    $empQuery->where([$q]);
                }
                $data = [];
                $empQuery->limit($limit);
                $empQuery->page($page);
                foreach ($empQuery as $key => $customer) {
                    $photo = $this->Customers->Photos->find('all')->where(['controleur' => 'customers', 'objectid' => $customer->id])->order(['created' => 'ASC'])->last();
                    $img = Router::Url('/') . 'webroot/img/unvailable.jpg';
                    if ($photo) {
                        $img = Router::Url('/') . $photo->dir . '/' . $photo->title;
                    }
                    $referred = ($customer->referred) ? $customer->referred : "";
                    $longitude = ($customer->longitude) ? $customer->longitude : "";
                    $latitude = ($customer->latitude) ? $customer->latitude : "";
                    $phone = ($customer->phone) ? $customer->phone : "";
                    $ice = ($customer->ice) ? $customer->ice : "";
                    $orderpacks = $this->Customers->Orders->Orderpacks->find('all')
                        ->where(['Orderpacks.loyaltypointgift_id IS ' => NULL, 'Orders.customer_id' => $customer->id, 'Orderpacks.loyalityvalidation' => 1])
                        ->contain(['Orders'])
                        ->select(['totalLoyaltyPoints' => 'SUM(Orderpacks.loyaltypoints*Orderpacks.quantity)']);
                    $totalLoyaltyPoints = doubleval($orderpacks->first()->totalLoyaltyPoints) ? doubleval($orderpacks->first()->totalLoyaltyPoints) : 0;
                    $orderPayments = $this->Customers->Orders->OrderPayments->find('all')
                        ->where(['Orders.customer_id' => $customer->id, 'OrderPayments.payment_method_id' => 5])
                        ->contain(['Orders'])
                        ->select(['credit' => 'SUM(OrderPayments.amount)']);
                    $totalOrderPayments = doubleval($orderPayments->first()->credit) ? doubleval($orderPayments->first()->credit) : 0;
                    $avoirs = $this->Customers->Orders->find('all')->where(['Orders.customer_id' => $customer->id, 'Orders.ordertype_id' => 2, 'Orders.shipping_id IS' => null])
                        ->select(['totalAvoirs' => 'count(Orders.id)']);
                    $totalAvoirs = intval($avoirs->first()->totalAvoirs);

                    $data[] = [
                        "id" => $customer->id,
                        "code" => $customer->code,
                        "photo" => $img,
                        "name" => $customer->name,
                        "phone" => $customer->phone,
                        "adresse" => $customer->adresse,
                        "city" => $customer->zone->city->title,
                        "zone" => ["id" => $customer->zone->id, "title" => $customer->zone->title],
                        "customertype" => ["id" => $customer->customertype->id, "title" => $customer->customertype->title],
                        "avoirs" => $totalAvoirs,
                        "longitude" => $longitude,
                        "latitude" => $latitude,
                        "loyaltypoints" => $totalLoyaltyPoints,
                        "credit" => $totalOrderPayments,
                        "proximite" => 0,
                        "ice" => $ice,
                        "statut" => $customer->statut,
                    ];
                }
            } elseif ($isReturn == 2) {
                $customerIds = $this->Customers->find()
                    ->select(['Customers.id']) // Only select customer IDs
                    ->matching('Orders.OrderPayments', function (Query $q) {
                        return $q
                            ->where(['OrderPayments.payment_method_id' => 5])
                            ->select([
                                'Orders.user_id',
                                'total_credit' => $q->func()->sum('OrderPayments.amount')
                            ])
                            ->group(['Orders.user_id'])
                            ->having(['SUM(OrderPayments.amount) >' => 0]);
                    })
                    ->distinct(['Customers.id'])
                    ->enableHydration(false) // optional: return array instead of entities
                    ->toArray();
                $ids = (new Collection($customerIds))->extract('id')->toList();
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
                        'Customers.customertype_id',
                        'Customertypes.title',
                        'Customertypes.id',
                        'Customers.longitude',
                        'Customers.latitude',
                        'Customers.referral',
                        'Customers.referred',
                        'Customers.ice',
                        'Customers.statut',
                    ])
                    ->contain(['Zones.Cities', 'Customertypes']);
                $empQuery->where(['Customers.customertype_id' => $customerType, 'Customers.statut' => 1]);
                $empQuery->where(['Customers.id IN' => $ids]);
                if ($searchText) {
                    $empQuery->where([
                        "OR" => [
                            ['Customers.code LIKE' => '%' . $searchText . '%'],
                            ['lower(Customers.code) LIKE' => '%' . $searchText . '%'],
                            ['Customers.name LIKE' => '%' . $searchText . '%'],
                            ['Customers.phone LIKE' => '%' . $searchText . '%'],
                            ['lower(Customers.name) LIKE' => '%' . $searchText . '%'],
                            ['Customers.adresse LIKE' => '%' . $searchText . '%'],
                            ['lower(Customers.adresse) LIKE' => '%' . $searchText . '%'],
                        ]
                    ]);
                }
                $zoneusers = $this->Customers->Zones->Zoneusers->find('all')->contain(['Zones.Subzones'])->where(['Zoneusers.user_id' => $user_id, 'Zoneusers.statut' => 1]);
                $q = [];

                foreach ($zoneusers as $key => $zoneuser) {
                    foreach ($zoneuser->zone->subzones as $subzone) {
                        $q['OR'][$subzone->id] = [['Customers.zone_id' => $subzone->id]];
                    }
                }
                if ($q) {
                    $empQuery->where([$q]);
                }
                $data = [];

                $empQuery->limit($limit);
                $empQuery->page($page);
                foreach ($empQuery as $key => $customer) {
                    $photo = $this->Customers->Photos->find('all')->where(['controleur' => 'customers', 'objectid' => $customer->id])->order(['created' => 'ASC'])->last();
                    $img = Router::Url('/') . 'webroot/img/unvailable.jpg';
                    if ($photo) {
                        $img = Router::Url('/') . $photo->dir . '/' . $photo->title;
                    }
                    $referred = ($customer->referred) ? $customer->referred : "";
                    $longitude = ($customer->longitude) ? $customer->longitude : "";
                    $latitude = ($customer->latitude) ? $customer->latitude : "";
                    $phone = ($customer->phone) ? $customer->phone : "";
                    $ice = ($customer->ice) ? $customer->ice : "";
                    $orderpacks = $this->Customers->Orders->Orderpacks->find('all')
                        ->where(['Orders.customer_id' => $customer->id, 'Orders.ordertype_id' => 1, 'Orderpacks.loyalityvalidation' => 1])
                        ->contain(['Orders'])
                        ->select(['totalLoyaltyPoints' => 'SUM(Orderpacks.loyaltypoints*Orderpacks.quantity)']);

                    $orderpackAvoirs = $this->Customers->Orders->Orderpacks->find('all')
                        ->where(['Orders.customer_id' => $customer->id, 'Orders.ordertype_id' => 2, 'Orderpacks.loyalityvalidation' => 1])
                        ->contain(['Orders'])
                        ->select(['totalLoyaltyPoints' => 'SUM(Orderpacks.loyaltypoints*Orderpacks.quantity)']);
                    $totalLoyaltyPoints = doubleval($orderpacks->first()->totalLoyaltyPoints - $orderpackAvoirs->first()->totalLoyaltyPoints) ? doubleval($orderpacks->first()->totalLoyaltyPoints - $orderpackAvoirs->first()->totalLoyaltyPoints) : 0;
                    $orderPayments = $this->Customers->Orders->OrderPayments->find('all')
                        ->where(['Orders.customer_id' => $customer->id, 'OrderPayments.payment_method_id' => 5])
                        ->contain(['Orders'])
                        ->select(['credit' => 'SUM(OrderPayments.amount)']);
                    $totalOrderPayments = doubleval($orderPayments->first()->credit) ? doubleval($orderPayments->first()->credit) : 0;
                    $avoirs = $this->Customers->Orders->find('all')->where(['Orders.customer_id' => $customer->id, 'Orders.ordertype_id' => 2, 'Orders.shipping_id IS' => null])
                        ->select(['totalAvoirs' => 'count(Orders.id)']);
                    $totalAvoirs = intval($avoirs->first()->totalAvoirs);
                    $data[] = [
                        "id" => $customer->id,
                        "code" => $customer->code,
                        "photo" => $img,
                        "name" => $customer->name,
                        "phone" => $customer->phone,
                        "adresse" => $customer->adresse,
                        "city" => $customer->zone->city->title,
                        "zone" => ["id" => $customer->zone->id, "title" => $customer->zone->title],
                        "customertype" => ["id" => $customer->customertype->id, "title" => $customer->customertype->title],
                        "avoirs" => $totalAvoirs,
                        "longitude" => $longitude,
                        "latitude" => $latitude,
                        "loyaltypoints" => $totalLoyaltyPoints,
                        "credit" => $totalOrderPayments,
                        "proximite" => 0,
                        "ice" => $ice,
                        "statut" => $customer->statut,
                    ];
                }
            } else {
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
                        'Customers.customertype_id',
                        'Customertypes.id',
                        'Customers.longitude',
                        'Customers.latitude',
                        'Customers.referral',
                        'Customers.referred',
                        'Customers.ice',
                        'Customers.statut',
                        'distance' => $distanceField
                    ])
                    ->where(["$distanceField < " => $distance])
                    ->bind(':latitude', $latitude, 'float')
                    ->bind(':longitude', $longitude, 'float')
                    ->contain(['Zones.Cities', 'Customertypes'])
                    ->order(["distance" => "ASC"]);
                $empQuery->where(['Customers.customertype_id' => $customerType, 'Customers.statut' => 1]);

                if ($searchText) {
                    $empQuery->where([
                        "OR" => [
                            ['Customers.code LIKE' => '%' . $searchText . '%'],
                            ['lower(Customers.code) LIKE' => '%' . $searchText . '%'],
                            ['Customers.name LIKE' => '%' . $searchText . '%'],
                            ['Customers.phone LIKE' => '%' . $searchText . '%'],
                            ['lower(Customers.name) LIKE' => '%' . $searchText . '%'],
                            ['Customers.adresse LIKE' => '%' . $searchText . '%'],
                            ['lower(Customers.adresse) LIKE' => '%' . $searchText . '%'],
                        ]
                    ]);
                }
                $zoneusers = $this->Customers->Zones->Zoneusers->find('all')->contain(['Zones.Subzones'])->where(['Zoneusers.user_id' => $user_id, 'Zoneusers.statut' => 1]);
                $q = [];

                foreach ($zoneusers as $key => $zoneuser) {
                    foreach ($zoneuser->zone->subzones as $subzone) {
                        $q['OR'][$subzone->id] = [['Customers.zone_id' => $subzone->id]];
                    }
                }
                if ($q) {
                    $empQuery->where([$q]);
                }
                $data = [];
                $empQuery->limit($limit);
                $empQuery->page($page);
                foreach ($empQuery as $key => $customer) {
                    $photo = $this->Customers->Photos->find('all')->where(['controleur' => 'customers', 'objectid' => $customer->id])->order(['created' => 'ASC'])->last();
                    $img = Router::Url('/') . 'webroot/img/unvailable.jpg';
                    if ($photo) {
                        $img = Router::Url('/') . $photo->dir . '/' . $photo->title;
                    }
                    $referred = ($customer->referred) ? $customer->referred : "";
                    $longitude = ($customer->longitude) ? $customer->longitude : "";
                    $latitude = ($customer->latitude) ? $customer->latitude : "";
                    $phone = ($customer->phone) ? $customer->phone : "";
                    $ice = ($customer->ice) ? $customer->ice : "";
                    $orderpacks = $this->Customers->Orders->Orderpacks->find('all')
                        ->where(['Orderpacks.loyaltypointgift_id IS ' => NULL, 'Orders.customer_id' => $customer->id, 'Orders.ordertype_id' => 1, 'Orderpacks.loyalityvalidation' => 1])
                        ->contain(['Orders'])
                        ->select(['totalLoyaltyPoints' => 'SUM(Orderpacks.loyaltypoints*Orderpacks.quantity)']);
                    $orderpackAvoirs = $this->Customers->Orders->Orderpacks->find('all')
                        ->where(['Orderpacks.loyaltypointgift_id IS ' => NULL, 'Orders.customer_id' => $customer->id, 'Orders.ordertype_id' => 2, 'Orderpacks.loyalityvalidation' => 1])
                        ->contain(['Orders'])
                        ->select(['totalLoyaltyPoints' => 'SUM(Orderpacks.loyaltypoints*Orderpacks.quantity)']);
                    $totalLoyaltyPoints = doubleval($orderpacks->first()->totalLoyaltyPoints - $orderpackAvoirs->first()->totalLoyaltyPoints) ? doubleval($orderpacks->first()->totalLoyaltyPoints - $orderpackAvoirs->first()->totalLoyaltyPoints) : 0;
                    $orderPayments = $this->Customers->Orders->OrderPayments->find('all')
                        ->where(['Orders.customer_id' => $customer->id, 'OrderPayments.payment_method_id' => 5])
                        ->contain(['Orders'])
                        ->select(['credit' => 'SUM(OrderPayments.amount)']);
                    $totalOrderPayments = doubleval($orderPayments->first()->credit) ? doubleval($orderPayments->first()->credit) : 0;
                    $avoirs = $this->Customers->Orders->find('all')->where(['Orders.customer_id' => $customer->id, 'Orders.ordertype_id' => 2, 'Orders.shipping_id IS' => null])
                        ->select(['totalAvoirs' => 'count(Orders.id)']);
                    $totalAvoirs = intval($avoirs->first()->totalAvoirs);
                    $data[] = [
                        "id" => $customer->id,
                        "code" => $customer->code,
                        "photo" => $img,
                        "name" => $customer->name,
                        "phone" => $customer->phone,
                        "adresse" => $customer->adresse,
                        "city" => $customer->zone->city->title,
                        "zone" => ["id" => $customer->zone->id, "title" => $customer->zone->title],
                        "customertype" => ["id" => $customer->customertype->id, "title" => $customer->customertype->title],
                        "longitude" => $longitude,
                        "latitude" => $latitude,
                        "proximite" => $customer->distance * 1000,
                        "ice" => $ice,
                        "avoirs" => $totalAvoirs,
                        "loyaltypoints" => $totalLoyaltyPoints,
                        "credit" => $totalOrderPayments,
                        "statut" => $customer->statut,
                    ];
                }
            }
        } else {
            $data[] = 'merci de revoir le lien envoyée';
        }

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize', 'data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    public function zones($user_id)
    {
        $this->loadModel('Zoneusers');
        $zoneusers = $this->Zoneusers->find('all')->where(['Zoneusers.user_id' => $user_id])->contain(['Zones.Subzones']);

        $data = [];
        foreach ($zoneusers as $zoneuser) {
            if ($zoneuser->zone->statut == 1) {
                foreach ($zoneuser->zone->subzones as $subzone) {
                    if ($subzone->statut == 1) {
                        $data[] = ['id' => $subzone->id, 'title' => $subzone->title];
                    }
                }
            }
        }

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize', 'data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    public function customerTypes()
    {
        $this->loadModel('Customertypes');
        $types = $this->Customertypes->find('all')->where(['Customertypes.statut' => 1]);
        $data = [];
        foreach ($types as $customertype) {
            $data[] = ['id' => $customertype->id, 'title' => $customertype->title];
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize', 'data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    public function customerPhoto()
    {
        $this->loadModel('Photos');
        $photo = $this->Photos->newEntity();
        if ($this->request->is('post')) {
            $customerId = $this->request->getData('customer_id');
            $customerPhoto = ($this->request->getData('photo')) ? base64_decode($this->request->getData("photo")) : null;
            $filename = ($this->request->getData('filename')) ? $this->request->getData("filename") : "defauult.jpg";
            if ($customerPhoto) {
                $temp = explode(".", $filename);
                $extension = end($temp);
                $name = round(microtime(true) * 1000) . '.' . $extension;
                file_put_contents('../webroot/files/Photos/customers/' . $name, $customerPhoto);

                $photoData = ["title" => $name, "controleur" => "customers", "statut" => 1, "company_id" => 1, 'photo' => $name, 'dir' => 'webroot/files/Photos/customers'];
            }
            $photoData["objectid"] = $customerId;
            $photo = $this->Photos->patchEntity($photo, $photoData);
            if ($this->Photos->save($photo)) {
                $msg['statut'] = 1;
                $msg['message'] = 'La photo est ajoutée avec succes';
                $msg['img'] = Router::Url('/') . $photo->dir . '/' . $photo->photo;
            } else {
                $msg['statut'] = 0;
                $msg['message'] = 'Merci de vérifier la photo';
            }
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        echo json_encode($msg);
        exit;
    }
    public function paymentPhoto()
    {
        $this->loadModel('Photos');
        $photo = $this->Photos->newEntity();
        if ($this->request->is('post')) {
            $orderPaymentId = 1;
            // $orderPaymentId = $this->request->getData('order_payment_id');
            $orderPaymentPhoto = ($this->request->getData('image_path')) ? base64_decode($this->request->getData("image_path")) : null;
            $filename = ($this->request->getData('filename')) ? $this->request->getData("filename") : "$orderPaymentId.jpg";
            if ($orderPaymentPhoto) {
                $temp = explode(".", $filename);
                $extension = end($temp);
                $name = round(microtime(true) * 1000) . '.' . $extension;
                file_put_contents('../webroot/files/Photos/orderpayments/' . $name, $orderPaymentPhoto);

                $photoData = ["title" => $name, "controleur" => "orderpayments", "statut" => 1, "company_id" => 1, 'photo' => $name, 'dir' => 'webroot/files/Photos/orderpayments'];
            }
            $photoData["objectid"] = $orderPaymentId;
            $photo = $this->Photos->patchEntity($photo, $photoData);
            if ($this->Photos->save($photo)) {
                $msg['statut'] = 1;
                $msg['message'] = 'La photo est ajoutée avec succes';
                $msg['img'] = Router::Url('/') . $photo->dir . '/' . $photo->photo;
            } else {
                $msg['statut'] = 0;
                $msg['message'] = 'Merci de vérifier la photo';
            }

        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        echo json_encode($msg);
        exit;
    }
    public function orderPhoto()
    {
        $this->loadModel('Photos');
        $photo = $this->Photos->newEntity();
        if ($this->request->is('post')) {
            $orderId = $this->request->getData('order_id');
            $customerPhoto = ($this->request->getData('photo')) ? base64_decode($this->request->getData("photo")) : null;
            $filename = ($this->request->getData('filename')) ? $this->request->getData("filename") : "defauult.jpg";
            if ($customerPhoto) {
                $temp = explode(".", $filename);
                $extension = end($temp);
                $name = round(microtime(true) * 1000) . '.' . $extension;
                file_put_contents('../webroot/files/Photos/orders/' . $name, $customerPhoto);

                $photoData = ["title" => $name, "controleur" => "orders", "statut" => 1, "company_id" => 1, 'photo' => $name, 'dir' => 'webroot/files/Photos/orders'];
            }
            $photoData["objectid"] = $orderId;
            $photo = $this->Photos->patchEntity($photo, $photoData);
            if ($this->Photos->save($photo)) {
                $msg['statut'] = 1;
                $msg['message'] = 'La photo est ajoutée avec succes';
                $msg['img'] = Router::Url('/') . $photo->dir . '/' . $photo->photo;
            } else {
                $msg['statut'] = 0;
                $msg['message'] = 'Merci de vérifier la photo';
            }
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        echo json_encode($msg);
        exit;
    }
    public function customerAdd()
    {
        $this->loadModel('Customers');
        $customer = $this->Customers->newEntity();
        if ($this->request->is('post')) {
            $customerName = $this->request->getData('name');
            $customerPhone = $this->request->getData('phone');
            $customerAdresse = $this->request->getData('adresse');
            $customerLatitude = $this->request->getData('latitude');
            $customerLongitude = $this->request->getData('longitude');
            $customerZone = $this->request->getData('zone_id');

            // Validate that latitude and longitude are provided
            if (empty($customerLatitude) || empty($customerLongitude)) {
                $msg['statut'] = 0;
                $msg['message'] = 'La localisation est obligatoire pour ajouter un client';
                header("Access-Control-Allow-Origin: *");
                header("Access-Control-Allow-Credentials: true");
                header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
                header("Content-Type: application/json; charset=UTF-8");
                header("Access-Control-Allow-Methods: POST, OPTIONS");
                echo json_encode($msg);
                exit;
            }
            $user = $this->Customers->Companies->Users->find("all")->where(['id' => $this->request->getData('user_id')])->first();
            if ($user && $user->role_id == 5) {
                $customerType = 3;
            } else {
                $customerType = 2;
            }
            $customerPhoto = ($this->request->getData('photo') !== "nothing") ? base64_decode($this->request->getData("photo")) : null;
            $filename = ($this->request->getData('filename')) ? $this->request->getData("filename") : "defauult.jpg";
            $photoData = [];
            if ($customerPhoto) {
                $temp = explode(".", $filename);
                $extension = end($temp);
                $name = round(microtime(true) * 1000) . '.' . $extension;
                file_put_contents('../webroot/files/Photos/customers/' . $name, $customerPhoto);
                $photoData = ["title" => $name, "controleur" => "customers", "statut" => 1, "company_id" => 1, 'photo' => $name, 'dir' => 'webroot/files/Photos/customers'];
            }
            $code = $this->Customers->Companies->Companycodes->find('all')->where(['controleur' => 'Customers', 'company_id' => 1])->last();
            $customerCode = 'DO' . $code->prefixe . ($code->compteur + 1);
            $hasher = new DefaultPasswordHasher();
            $customerdata = [
                "code" => $customerCode,
                "name" => $customerName,
                "phone" => $customerPhone,
                "adresse" => $customerAdresse,
                "zone_id" => $customerZone,
                "customertype_id" => $customerType,
                "latitude" => $customerLatitude,
                "longitude" => $customerLongitude,
                "statut" => 1,
                "company_id" => 1,
                "referral" => $customerName,
                "password" => $hasher->hash($customerPhone),
            ];
            $customer = $this->Customers->patchEntity($customer, $customerdata);


            if ($this->Customers->save($customer)) {
                if ($customerPhoto) {
                    $photo = $this->Customers->Photos->newEntity();
                    $photoData["objectid"] = $customer->id;
                    $photo = $this->Customers->Photos->patchEntity($photo, $photoData);
                    $this->Customers->Photos->save($photo);
                }
                $code->compteur = $code->compteur + 1;
                $this->Customers->Companies->Companycodes->save($code);
                $msg['statut'] = 1;
                $msg['message'] = 'Le client est ajouté avec succes';
                $msg['customerId'] = $customer->id;
            } else {
                $msg['statut'] = 0;
                $msg['message'] = 'Merci de vérifier vos informations avant de valider votre inscription';
            }

            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
            header("Content-Type: application/json; charset=UTF-8");
            header("Access-Control-Allow-Methods: POST, OPTIONS");
            echo json_encode($msg);
            exit;
        }
    }
    public function customerEdit()
    {
        $this->loadModel('Customers');
        $customerId = $this->request->getData('id');
        $customer = $this->Customers->get($customerId);
        $customerName = $this->request->getData('name');
        $customerPhone = $this->request->getData('phone');
        $customerAdresse = $this->request->getData('adresse');
        $customerLatitude = $this->request->getData('latitude');
        $customerLongitude = $this->request->getData('longitude');
        if ($this->request->is('post')) {
            $customerdata = [
                "name" => $customerName,
                "phone" => $customerPhone,
                "adresse" => $customerAdresse,
                "latitude" => $customerLatitude,
                "longitude" => $customerLongitude,
                "statut" => 1,
                "company_id" => 1,
            ];
            $customer = $this->Customers->patchEntity($customer, $customerdata);
            if ($this->Customers->save($customer)) {
                $msg['statut'] = 1;
                $msg['message'] = 'Le client este modifié avec succés';
                $longitude = ($customer->longitude) ? $customer->longitude : "";
                $latitude = ($customer->latitude) ? $customer->latitude : "";
                $phone = ($customer->phone) ? $customer->phone : "";
                $ice = ($customer->ice) ? $customer->ice : "";
                $msg["client"] = [
                    "name" => $customer->name,
                    "phone" => $phone,
                    "adresse" => $customer->adresse,
                    "longitude" => $longitude,
                    "latitude" => $latitude,
                    "ice" => $ice,
                ];
            } else {
                $msg['statut'] = 0;
                $msg['message'] = 'Merci de vérifier vos informations avant de valider la modification';
            }
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
            header("Content-Type: application/json; charset=UTF-8");
            header("Access-Control-Allow-Methods: POST, OPTIONS");
            echo json_encode($msg);
            exit;
        }
    }
    public function homeCategories($orderId = 0, $user_id)
    {
        $this->loadModel('Users');
        $user = $this->Users->get($user_id);
        $categoryData = [];
        if ($orderId != 0 && $user->role_id == 6) {
            $order = $this->Users->Orders->get($orderId, ['contain' => ['Orderpacks.Packs']]);
            foreach ($order->orderpacks as $orderPack) {
                $categoryData['OR'][$orderPack->pack->category_id] = [['Categories.id' => $orderPack->pack->category_id]];
            }
        }

        $this->loadModel('Categories');
        $categories = $this->Categories->find('all')->where(['Categories.category_id IS NOT ' => NULL]);
        $categories->where(['Categories.id !=' => 9]); // excluding Cadeaux
        if ($categoryData) {
            $categories->where([$categoryData]);
        }

        foreach ($categories as $key => $category) {
            $photo = $this->Categories->Photos->find('all')->where(['controleur' => 'categories', 'objectid' => $category->id])->order(['created' => 'ASC'])->last();
            $img = Router::Url('/') . 'webroot/img/unvailable.jpg';
            if ($photo) {
                $img = Router::Url('/') . $photo->dir . '/' . 'thumbnail160-' . $photo->photo;
            }

            $data[] = [
                "id" => $category->id,
                "code" => $category->code,
                "title" => $category->title,
                "image" => $img
            ];
        }
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('data'));
        $this->set('_serialize', 'data');
        $this->RequestHandler->renderAs($this, 'json');
    }
    //Products
    public function products($isDelivery = 0, $warehouse_id, $customertype, $customerId = 0)
    {
        //hna ra khassni ncharger les produits li deja kaynine f les commandes a voir men be3d
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $skip = isset($_GET['skip']) ? $_GET['skip'] : 1;
        $searchText = isset($_GET['search']) ? $_GET['search'] : "";
        $categories = isset($_GET['categories']) ? explode(',', $_GET['categories']) : null;
        $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;
        $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
        $ordertype_id = isset($_GET['ordertype_id']) ? $_GET['ordertype_id'] : 1;
        $isDepot = isset($_GET['is_depot']) ? $_GET['is_depot'] : 0;
        $this->loadModel('Warehouses');
        $user = $this->Warehouses->Companies->Users->find("all")->where(['id' => $user_id])->first();

        if ($user && ($user->role_id == 5 || $user->role_id == 6)) {
            $customerType = 3;
        } else {
            $customerType = 2;
        }
        $data = [];

        // Load all tranches with their remisetype, gift pack, and trancheprices
        $this->loadModel('Tranches');
        $allTranches = $this->Tranches->find('all')
            ->contain(['Remisetypes', 'Packs', 'Trancheprices' => ['Prices']])
            ->toArray();

        // Helper function to format tranches - filters by product ID
        // If tranche has no trancheprices, it applies to ALL products
        // If tranche has trancheprices, it only applies if a price matches this product
        $formatTranches = function ($tranchesArray, $productId, $customerType, $priceApplies) {
            $tranches = [];
            foreach ($tranchesArray as $tranche) {
                // Check if tranche applies to this product
                $appliesToProduct = false;

                // If tranche has no trancheprices, it applies to all products
                if (empty($tranche->trancheprices)) {
                    $appliesToProduct = true;
                } else {
                    // Check if any trancheprice links to a price for this product
                    foreach ($tranche->trancheprices as $tp) {
                        if (!empty($tp->price) && $tp->price->pack_id == $productId && $tp->price->customertype_id == $customerType) {
                            $appliesToProduct = true;
                            break;
                        }
                    }
                }

                // If this tranche applies to the product, include it
                if ($appliesToProduct) {
                    $trancheData = [
                        'id' => $tranche->id,
                        'title' => $tranche->title,
                        'apply_type' => $tranche->apply_type,
                        'quantity_unit_type' => $tranche->quantity_unit_type,
                        'min' => $tranche->min,
                        'max' => $tranche->max,
                        //hadi zedtha 7ta ndir mise a jour l'application mobile
                        'remise' => $tranche->remisetype_id == 2 ? ($tranche->remise / $priceApplies * 100) : $tranche->remise,
                        //'remise' => $tranche->remise,
                        'remisetype_id' => $tranche->remisetype_id,
                        'remisetype' => [
                            'id' => $tranche->remisetype->id,
                            'title' => $tranche->remisetype->title
                        ]
                    ];

                    // Add gift if pack_id is not null
                    if ($tranche->pack_id !== null && !empty($tranche->pack)) {
                        $trancheData['gift'] = [
                            'id' => $tranche->pack->id,
                            'title' => $tranche->pack->title
                        ];
                    }

                    $tranches[] = $trancheData;
                }
            }
            return $tranches;
        };

        $pofsale = $this->Warehouses->Pofsales->find('all')->where(['warehouse_id' => $warehouse_id])->last();
        if ($warehouse_id) {
            $warehouse = $this->Warehouses->get($warehouse_id, [
                'contain' => [
                    'Subwarehouses' => function ($q) {
                        return $q->where(['whnature_id' => 1, 'whtype_id' => 2]);
                    }
                ]
            ]);
            if ($warehouse) {
                if ($customerId != 0) {
                    if ($ordertype_id == 4) {
                        $this->loadModel('Customers');
                        $empQuery = $this->Customers->find();
                        $empQuery->contain(['Zones.Cities', 'Zones.Parentzones', 'Customertypes'])
                            ->leftJoinWith('Orders.Orderpacks')
                            ->where(['Customers.id' => $customerId])
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
                                'Zones.title',
                                'Parentzones.title',
                                'loyaltypoints_sum' => $empQuery->newExpr(
                                    'SUM(CASE WHEN Orders.ordertype_id = 1 THEN Orderpacks.loyaltypoints * Orderpacks.quantity ELSE 0 END) - '
                                    . 'SUM(CASE WHEN Orders.ordertype_id = 2 THEN Orderpacks.loyaltypoints * Orderpacks.quantity ELSE 0 END)'
                                )
                            ])->group(['Customers.id'])->first();
                        $this->loadModel('Categoryuserpacks');
                        $user = $this->Categoryuserpacks->Categoryusers->Users->get($user_id);
                        $packsCondition = [];
                        $categoryuserpacks = $this->Categoryuserpacks->find('all')->where(['Categoryuserpacks.categoryuser_id' => $user->categoryuser_id]);
                        foreach ($categoryuserpacks as $categoryuserpack) {
                            $packsCondition[] = $categoryuserpack->pack_id;
                        }
                        $this->loadModel('Packs');
                        $packs = $this->Packs->find('all')->contain([
                            'Brands',
                            'MeasurementUnits',
                            'Turnovers',
                            'Categories',
                            'Tranches' => ['Packs', 'Remisetypes'],
                            'Packunites.Unites.Parentunites',
                            'Whproducts' => function ($q) use ($warehouse) {
                                return $q->where(['Whproducts.warehouse_id' => $warehouse->subwarehouses[0]->id]);
                            },
                            'Prices' => function ($q) use ($customerType) {
                                return $q->where(['Prices.customertype_id' => $customerType]);
                            }
                        ]);
                        if ($categories) {
                            $packs->where(['Packs.category_id IN' => $categories]);
                        }
                        $packs->where(['Packs.statut' => 1, 'Packs.loyaltypoints <=' => $empQuery->loyaltypoints_sum]);
                        $packs->order(['Packs.created' => 'DESC']);

                        $this->loadModel('Users');
                        $user = $this->Users->get($user_id);
                        if ($order_id != 0 && $user->role_id == 6) {
                            $order = $this->Users->Orders->get($order_id, ['contain' => ['Orderpacks']]);
                            $packsCondition = [];
                            foreach ($order->orderpacks as $key => $orderPack) {
                                $packsCondition[] = $orderPack->pack_id;
                            }
                        }
                        $packs->where(['Packs.category_id !=' => 9]); // excluding Cadeaux
                        $packs->where(['Packs.id IN' => $packsCondition]);
                        if ($isDelivery == 0) {
                            $packs->where(['Packs.statut' => 1]);
                        }
                        if ($searchText !== NULL) {
                            $packs->where([
                                "OR" => [
                                    ['Packs.title LIKE' => '%' . $searchText . '%'],
                                    ['lower(Packs.title) LIKE' => '%' . $searchText . '%'],
                                    ['lower(Packs.code) LIKE' => '%' . $searchText . '%'],
                                    ['Packs.code LIKE' => '%' . $searchText . '%']
                                ]
                            ]);
                        }
                        $packs->limit($limit);
                        $packs->page($page);
                        foreach ($packs as $pack) {
                            $quantityInInstance = 0;
                            $orderpacks = $this->Packs->Orderpacks->find('all')->contain(['Orders'])->where(['Orders.pofsale_id' => $pofsale->id, 'Orders.statut' => 1, 'Orderpacks.pack_id' => $pack->id]);
                            foreach ($orderpacks as $orderpack) {
                                $quantityInInstance += $orderpack->quantity;
                            }
                            $photo = $this->Packs->Photos->find('all')->where(['controleur' => 'packs', 'objectid' => $pack->id])->order(['created' => 'ASC'])->last();
                            $img = Router::Url('/') . 'webroot/img/unvailable.jpg';
                            $images = [];
                            if ($photo) {
                                $img = Router::Url('/') . $photo->dir . '/thumbnail700-' . $photo->photo;
                                $images[] = Router::Url('/') . $photo->dir . '/thumbnail700-' . $photo->photo;
                            } else {
                                $images[] = $img;
                            }
                            $variants = [];
                            //Sac & Unité

                            if ($pack->saletype_id == 1) {
                                $variants[0] = [
                                    'id' => $pack->packunites[0]->unite->id,
                                    'title' => $pack->packunites[0]->unite->abrev,
                                    'quantity' => $pack->packunites[0]->quantity,
                                    'statut' => 1,
                                ];
                                $variants[1] = [
                                    'id' => $pack->packunites[0]->unite->parentunite->id,
                                    'title' => $pack->packunites[0]->unite->parentunite->abrev,
                                    'quantity' => 1,
                                    'statut' => 1,
                                ];
                                // Sac
                            } elseif ($pack->saletype_id == 2) {

                                $variants[0] = [
                                    'id' => $pack->packunites[0]->unite->id,
                                    'title' => $pack->packunites[0]->unite->abrev,
                                    'quantity' => $pack->packunites[0]->quantity,
                                    'statut' => 1,
                                ];
                                $variants[1] = [
                                    'id' => $pack->packunites[0]->unite->parentunite->id,
                                    'title' => $pack->packunites[0]->unite->parentunite->abrev,
                                    'quantity' => 1,
                                    'statut' => 0,
                                ];
                                //Unité
                            } elseif ($pack->saletype_id == 4) {
                                if ($pack->measurement_unit->conversion_factor == 1) {
                                    $pack->prices[0]->price = $pack->prices[0]->price * $pack->measurement_quantity;
                                    $variants[0] = [
                                        'id' => $pack->id,
                                        'title' => $pack->measurement_unit->abbreviation,
                                        'quantity' => 1,
                                        'statut' => 1,
                                    ];
                                } else {
                                    $getParent = $this->Packs->MeasurementUnits->find('all')->where(['type' => $pack->measurement_unit->type, 'conversion_factor' => ($pack->measurement_unit->conversion_factor * 1000)]);
                                    if ($getParent) {
                                        $pack->prices[0]->price = $pack->prices[0]->price * $getParent->first()->conversion_factor / ($pack->measurement_quantity * $pack->measurement_unit->conversion_factor);
                                        $variants[0] = [
                                            'id' => $pack->id,
                                            'title' => $getParent->first()->abbreviation,
                                            'quantity' => 1,
                                            'statut' => 1,
                                        ];
                                    } else {
                                        $pack->prices[0]->price = $pack->prices[0]->price * $pack->packunites[0]->unite->parentunite->abrev;
                                        $variants[0] = [
                                            'id' => $pack->packunites[0]->unite->parentunite->id,
                                            'title' => $pack->packunites[0]->unite->parentunite->abrev,
                                            'quantity' => 1,
                                            'statut' => 1,
                                        ];
                                    }
                                }

                            } else {
                                $variants[0] = [
                                    'id' => $pack->packunites[0]->unite->id,
                                    'title' => $pack->packunites[0]->unite->abrev,
                                    'quantity' => $pack->packunites[0]->quantity,
                                    'statut' => 0,
                                ];
                                $variants[1] = [
                                    'id' => $pack->packunites[0]->unite->parentunite->id,
                                    'title' => $pack->packunites[0]->unite->parentunite->abrev,
                                    'quantity' => 1,
                                    'statut' => 1,
                                ];
                            }
                            if ($pack->measurement_unit->conversion_factor == 1) {
                                $loyaltypoints = $pack->measurement_quantity . $pack->measurement_unit->abbreviation;
                            } else {
                                $getParent = $this->Packs->MeasurementUnits->find('all')->where(['type' => $pack->measurement_unit->type, 'conversion_factor' => ($pack->measurement_unit->conversion_factor * 1000)]);
                                $loyaltypoints = (($getParent->first()->conversion_factor / ($pack->measurement_quantity * $pack->measurement_unit->conversion_factor)) * $pack->loyaltypoints) . "/" . $getParent->first()->conversion_factor . $getParent->first()->abbreviation;
                            }
                            // Format tranches for client-side calculation - include ALL system tranches
                            $tranches = $formatTranches($allTranches, $pack->id, $customerType, $pack->prices[0]->price);
                            $data[] = [
                                "id" => $pack->id,
                                "code" => $pack->code,
                                "title" => $pack->title,
                                "price" => $pack->prices[0]->price,
                                "type" => $pack->packunites[0]->statut,
                                "quantity" => 100000,
                                "image" => $img,
                                "images" => $images,
                                "statut" => $pack->statut,
                                "turnover" => $loyaltypoints,
                                "loyalty" => $pack->loyaltypoints,
                                "variants" => $variants,
                                "tranches" => $tranches,
                                "brand" => ["id" => $pack->brand->id, "title" => $pack->brand->title],
                                "category" => ["id" => $pack->category->id, "title" => $pack->category->title],
                            ];
                        }

                    } else {
                        $this->loadModel('Customers');
                        $packsCondition = [];
                        //7ta yjib yrado control 3la les packs li 3ndou f les commandes
                        /*$customer = $this->Customers->get($customerId, ['contain' => ['Orders.Orderpacks']]);
                        foreach ($customer->orders as $order) {
                            foreach ($order->orderpacks as $orderPack) {
                                $packsCondition[] = $orderPack->pack_id;
                            }
                        }*/
                        $this->loadModel('Packs');
                        $packs = $this->Packs->find('all')->contain([
                            'Brands',
                            'MeasurementUnits',
                            'Turnovers',
                            'Categories',
                            'Tranches' => ['Packs', 'Remisetypes'],
                            'Packunites.Unites.Parentunites',
                            'Whproducts' => function ($q) use ($warehouse) {
                                return $q->where(['Whproducts.warehouse_id' => $warehouse->subwarehouses[0]->id]);
                            },
                            'Prices' => function ($q) use ($customerType) {
                                return $q->where(['Prices.customertype_id' => $customerType]);
                            }
                        ]);

                        if ($categories) {
                            $packs->where(['Packs.category_id IN' => $categories]);
                        }
                        $packs->where(['Packs.statut' => 1]);
                        $packs->where(['Packs.category_id !=' => 9]); // excluding Cadeaux
                        $packs->order(['Packs.created' => 'DESC']);
                        // $packs->where(['Packs.id IN' => $packsCondition]);
                        if ($isDelivery == 0) {
                            $packs->where(['Packs.statut' => 1]);
                        }
                        if ($searchText !== NULL) {
                            $packs->where([
                                "OR" => [
                                    ['Packs.title LIKE' => '%' . $searchText . '%'],
                                    ['lower(Packs.title) LIKE' => '%' . $searchText . '%'],
                                    ['lower(Packs.code) LIKE' => '%' . $searchText . '%'],
                                    ['Packs.code LIKE' => '%' . $searchText . '%']
                                ]
                            ]);
                        }
                        $packs->limit($limit);
                        $packs->page($page);
                        foreach ($packs as $pack) {
                            $quantityInInstance = 0;
                            $orderpacks = $this->Packs->Orderpacks->find('all')->contain(['Orders'])->where(['Orders.customer_id' => $customerId, 'Orderpacks.pack_id' => $pack->id]);
                            foreach ($orderpacks as $orderpack) {
                                $quantityInInstance += $orderpack->quantity;
                            }
                            $photo = $this->Packs->Photos->find('all')->where(['controleur' => 'packs', 'objectid' => $pack->id])->order(['created' => 'ASC'])->last();
                            $img = Router::Url('/') . 'webroot/img/unvailable.jpg';
                            $images = [];
                            if ($photo) {
                                $img = Router::Url('/') . $photo->dir . '/thumbnail700-' . $photo->photo;
                                $images[] = Router::Url('/') . $photo->dir . '/thumbnail700-' . $photo->photo;
                            } else {
                                $images[] = $img;
                            }
                            $variants = [];
                            //Sac & Unité

                            if ($pack->saletype_id == 1) {
                                $variants[0] = [
                                    'id' => $pack->packunites[0]->unite->id,
                                    'title' => $pack->packunites[0]->unite->abrev,
                                    'quantity' => $pack->packunites[0]->quantity,
                                    'statut' => 1,
                                ];
                                $variants[1] = [
                                    'id' => $pack->packunites[0]->unite->parentunite->id,
                                    'title' => $pack->packunites[0]->unite->parentunite->abrev,
                                    'quantity' => 1,
                                    'statut' => 1,
                                ];
                                // Sac
                            } elseif ($pack->saletype_id == 2) {

                                $variants[0] = [
                                    'id' => $pack->packunites[0]->unite->id,
                                    'title' => $pack->packunites[0]->unite->abrev,
                                    'quantity' => $pack->packunites[0]->quantity,
                                    'statut' => 1,
                                ];
                                $variants[1] = [
                                    'id' => $pack->packunites[0]->unite->parentunite->id,
                                    'title' => $pack->packunites[0]->unite->parentunite->abrev,
                                    'quantity' => 1,
                                    'statut' => 0,
                                ];
                                //Unité
                            } elseif ($pack->saletype_id == 4) {
                                if ($pack->measurement_unit->conversion_factor == 1) {
                                    $pack->prices[0]->price = $pack->prices[0]->price * $pack->measurement_quantity;
                                    $quantityInInstance = $quantityInInstance / $pack->measurement_quantity;
                                    $variants[0] = [
                                        'id' => $pack->id,
                                        'title' => $pack->measurement_unit->abbreviation,
                                        'quantity' => 1,
                                        'statut' => 1,
                                    ];
                                } else {
                                    $getParent = $this->Packs->MeasurementUnits->find('all')->where(['type' => $pack->measurement_unit->type, 'conversion_factor' => ($pack->measurement_unit->conversion_factor * 1000)]);
                                    if ($getParent) {
                                        $pack->prices[0]->price = $pack->prices[0]->price * $getParent->first()->conversion_factor / ($pack->measurement_quantity * $pack->measurement_unit->conversion_factor);
                                        $quantityInInstance = $quantityInInstance / ($getParent->first()->conversion_factor / ($pack->measurement_quantity * $pack->measurement_unit->conversion_factor));
                                        $variants[0] = [
                                            'id' => $pack->id,
                                            'title' => $getParent->first()->abbreviation,
                                            'quantity' => 1,
                                            'statut' => 1,
                                        ];
                                    } else {
                                        $pack->prices[0]->price = $pack->prices[0]->price * $pack->packunites[0]->unite->parentunite->abrev;
                                        $quantityInInstance = $quantityInInstance / $pack->packunites[0]->unite->parentunite->abrev;
                                        $variants[0] = [
                                            'id' => $pack->packunites[0]->unite->parentunite->id,
                                            'title' => $pack->packunites[0]->unite->parentunite->abrev,
                                            'quantity' => 1,
                                            'statut' => 1,
                                        ];
                                    }
                                }

                            } else {
                                $variants[0] = [
                                    'id' => $pack->packunites[0]->unite->id,
                                    'title' => $pack->packunites[0]->unite->abrev,
                                    'quantity' => $pack->packunites[0]->quantity,
                                    'statut' => 0,
                                ];
                                $variants[1] = [
                                    'id' => $pack->packunites[0]->unite->parentunite->id,
                                    'title' => $pack->packunites[0]->unite->parentunite->abrev,
                                    'quantity' => 1,
                                    'statut' => 1,
                                ];
                            }
                            if ($pack->measurement_unit->conversion_factor == 1) {
                                $loyaltypoints = $pack->measurement_quantity . $pack->measurement_unit->abbreviation;
                            } else {
                                $getParent = $this->Packs->MeasurementUnits->find('all')->where(['type' => $pack->measurement_unit->type, 'conversion_factor' => ($pack->measurement_unit->conversion_factor * 1000)]);
                                $loyaltypoints = (($getParent->first()->conversion_factor / ($pack->measurement_quantity * $pack->measurement_unit->conversion_factor)) * $pack->loyaltypoints) . "/" . $getParent->first()->conversion_factor . $getParent->first()->abbreviation;
                            }
                            // Format tranches for client-side calculation - include ALL system tranches
                            $tranches = $formatTranches($allTranches, $pack->id, $customerType, $pack->prices[0]->price);
                            $data[] = [
                                "id" => $pack->id,
                                "code" => $pack->code,
                                "title" => $pack->title,
                                "price" => $pack->prices[0]->price,
                                "type" => $pack->packunites[0]->statut,
                                "quantity" => 100000,
                                "image" => $img,
                                "images" => $images,
                                "statut" => $pack->statut,
                                "turnover" => $loyaltypoints,
                                "loyalty" => $pack->loyaltypoints,
                                "variants" => $variants,
                                "tranches" => $tranches,
                                "brand" => ["id" => $pack->brand->id, "title" => $pack->brand->title],
                                "category" => ["id" => $pack->category->id, "title" => $pack->category->title],
                            ];
                        }
                    }
                } else {
                    $this->loadModel('Categoryuserpacks');
                    $user = $this->Categoryuserpacks->Categoryusers->Users->get($user_id);
                    $packsCondition = [];
                    $categoryuserpacks = $this->Categoryuserpacks->find('all')->where(['Categoryuserpacks.categoryuser_id' => $user->categoryuser_id]);
                    foreach ($categoryuserpacks as $categoryuserpack) {
                        $packsCondition[] = $categoryuserpack->pack_id;
                    }
                    $this->loadModel('Packs');
                    $packs = $this->Packs->find('all')->contain([
                        'Brands',
                        'MeasurementUnits',
                        'Turnovers',
                        'Categories',
                        'Tranches' => ['Packs', 'Remisetypes'],
                        'Packunites.Unites.Parentunites',
                        'Whproducts' => function ($q) use ($warehouse) {
                            return $q->where(['Whproducts.warehouse_id' => $warehouse->subwarehouses[0]->id]);
                        },
                        'Prices' => function ($q) use ($customerType) {
                            return $q->where(['Prices.customertype_id' => $customerType]);
                        }
                    ]);

                    if ($categories) {
                        $packs->where(['Packs.category_id IN' => $categories]);
                    }
                    $packs->where(['Packs.category_id !=' => 9]);
                    $packs->where(['Packs.statut' => 1]);
                    $packs->where(['Packs.category_id !=' => 9]); // excluding Cadeaux
                    $packs->order(['Packs.created' => 'DESC']);

                    $this->loadModel('Users');

                    $user = $this->Users->get($user_id);
                    $packsCondition = [];
                    if ($order_id != 0 && $user->role_id == 6) {
                        $order = $this->Users->Orders->get($order_id, ['contain' => ['Orderpacks']]);
                        foreach ($order->orderpacks as $key => $orderPack) {
                            $packsCondition[] = $orderPack->pack_id;
                        }
                    }
                    if ($packsCondition) {
                        $packs->where(['Packs.id IN' => $packsCondition]);
                    }
                    if ($isDelivery == 0) {
                        $packs->where(['Packs.statut' => 1]);
                    }
                    if ($searchText !== NULL) {
                        $packs->where([
                            "OR" => [
                                ['Packs.title LIKE' => '%' . $searchText . '%'],
                                ['lower(Packs.title) LIKE' => '%' . $searchText . '%'],
                                ['lower(Packs.code) LIKE' => '%' . $searchText . '%'],
                                ['Packs.code LIKE' => '%' . $searchText . '%']
                            ]
                        ]);
                    }
                    $packs->limit($limit);
                    $packs->page($page);
                    foreach ($packs as $pack) {
                        $quantityInInstance = $pack->whproducts[0]->quantity;
                        $orderpacks = $this->Packs->Orderpacks->find('all')->contain(['Orders'])->where(['Orders.customer_id' => $customerId, 'Orderpacks.pack_id' => $pack->id]);
                        foreach ($orderpacks as $orderpack) {
                            $quantityInInstance -= $orderpack->quantity;
                        }
                        $photo = $this->Packs->Photos->find('all')->where(['controleur' => 'packs', 'objectid' => $pack->id])->order(['created' => 'ASC'])->last();
                        $img = Router::Url('/') . 'webroot/img/unvailable.jpg';
                        $images = [];
                        if ($photo) {
                            $img = Router::Url('/') . $photo->dir . '/thumbnail700-' . $photo->photo;
                            $images[] = Router::Url('/') . $photo->dir . '/thumbnail700-' . $photo->photo;
                        } else {
                            $images[] = $img;
                        }
                        $variants = [];
                        //Sac & Unité

                        if ($pack->saletype_id == 1) {
                            $variants[0] = [
                                'id' => $pack->packunites[0]->unite->id,
                                'title' => $pack->packunites[0]->unite->abrev,
                                'quantity' => $pack->packunites[0]->quantity,
                                'statut' => 1,
                            ];
                            $variants[1] = [
                                'id' => $pack->packunites[0]->unite->parentunite->id,
                                'title' => $pack->packunites[0]->unite->parentunite->abrev,
                                'quantity' => 1,
                                'statut' => 1,
                            ];
                            // Sac
                        } elseif ($pack->saletype_id == 2) {

                            $variants[0] = [
                                'id' => $pack->packunites[0]->unite->id,
                                'title' => $pack->packunites[0]->unite->abrev,
                                'quantity' => $pack->packunites[0]->quantity,
                                'statut' => 1,
                            ];
                            $variants[1] = [
                                'id' => $pack->packunites[0]->unite->parentunite->id,
                                'title' => $pack->packunites[0]->unite->parentunite->abrev,
                                'quantity' => 1,
                                'statut' => 0,
                            ];
                            //Unité
                        } elseif ($pack->saletype_id == 4) {
                            if ($pack->measurement_unit->conversion_factor == 1) {
                                $pack->prices[0]->price = $pack->prices[0]->price * $pack->measurement_quantity;
                                $quantityInInstance = $quantityInInstance / $pack->measurement_quantity;
                                $variants[0] = [
                                    'id' => $pack->id,
                                    'title' => $pack->measurement_unit->abbreviation,
                                    'quantity' => 1,
                                    'statut' => 1,
                                ];
                            } else {
                                $getParent = $this->Packs->MeasurementUnits->find('all')->where(['type' => $pack->measurement_unit->type, 'conversion_factor' => ($pack->measurement_unit->conversion_factor * 1000)]);
                                if ($getParent) {
                                    $pack->prices[0]->price = $pack->prices[0]->price * $getParent->first()->conversion_factor / ($pack->measurement_quantity * $pack->measurement_unit->conversion_factor);
                                    $quantityInInstance = $quantityInInstance / ($getParent->first()->conversion_factor / ($pack->measurement_quantity * $pack->measurement_unit->conversion_factor));
                                    $variants[0] = [
                                        'id' => $pack->id,
                                        'title' => $getParent->first()->abbreviation,
                                        'quantity' => 1,
                                        'statut' => 1,
                                    ];
                                } else {
                                    $pack->prices[0]->price = $pack->prices[0]->price * $pack->packunites[0]->unite->parentunite->abrev;
                                    $quantityInInstance = $quantityInInstance / $pack->packunites[0]->unite->parentunite->abrev;
                                    $variants[0] = [
                                        'id' => $pack->packunites[0]->unite->parentunite->id,
                                        'title' => $pack->packunites[0]->unite->parentunite->abrev,
                                        'quantity' => 1,
                                        'statut' => 1,
                                    ];
                                }
                            }

                        } else {
                            $variants[0] = [
                                'id' => $pack->packunites[0]->unite->id,
                                'title' => $pack->packunites[0]->unite->abrev,
                                'quantity' => $pack->packunites[0]->quantity,
                                'statut' => 0,
                            ];
                            $variants[1] = [
                                'id' => $pack->packunites[0]->unite->parentunite->id,
                                'title' => $pack->packunites[0]->unite->parentunite->abrev,
                                'quantity' => 1,
                                'statut' => 1,
                            ];
                        }
                        if ($pack->measurement_unit->conversion_factor == 1) {
                            $loyaltypoints = $pack->measurement_quantity . $pack->measurement_unit->abbreviation;
                        } else {
                            $getParent = $this->Packs->MeasurementUnits->find('all')->where(['type' => $pack->measurement_unit->type, 'conversion_factor' => ($pack->measurement_unit->conversion_factor * 1000)]);
                            $loyaltypoints = (($getParent->first()->conversion_factor / ($pack->measurement_quantity * $pack->measurement_unit->conversion_factor)) * $pack->loyaltypoints) . "/" . $getParent->first()->conversion_factor . $getParent->first()->abbreviation;
                        }
                        // Format tranches for client-side calculation - include ALL system tranches
                        $tranches = $formatTranches($allTranches, $pack->id, $customerType, $pack->prices[0]->price);
                        $data[] = [
                            "id" => $pack->id,
                            "code" => $pack->code,
                            "title" => $pack->title,
                            "price" => $pack->prices[0]->price,
                            "type" => $pack->packunites[0]->statut,
                            "quantity" => ($isDepot == 1) ? 1000000 : (($warehouse_id == 1) ? 100000 : $quantityInInstance),
                            "image" => $img,
                            "images" => $images,
                            "statut" => $pack->statut,
                            "turnover" => $loyaltypoints,
                            "loyalty" => $pack->loyaltypoints,
                            "variants" => $variants,
                            "tranches" => $tranches,
                            "brand" => ["id" => $pack->brand->id, "title" => $pack->brand->title],
                            "category" => ["id" => $pack->category->id, "title" => $pack->category->title],
                        ];
                    }
                }

            }
        }
        $response = ["status" => 200, "msg" => "Success", "data" => $data];

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $this->set(compact('response'));
        $this->set('_serialize', 'response');
        $this->RequestHandler->renderAs($this, 'json');
    }

    public function addPayment()
    {
        $this->loadModel('Payments');
        if ($this->request->is('post')) {
            $data = ['statut' => 0, 'message' => 'Error processing payment'];
            try {
                $requestData = $this->request->getData();


                // Validate required fields
                if (!isset($requestData['user_id'], $requestData['customer_id'], $requestData['order_payments'])) {
                    throw new \Exception('Missing required fields');
                }
                // Find all credit payments (method_id = 5) without a payment assigned
                $creditPayments = $this->Payments->OrderPayments->find('all')
                    ->contain(['Orders'])
                    ->where([
                        'Orders.customer_id' => $requestData['customer_id'],
                        'OrderPayments.payment_id IS NULL',
                        'OrderPayments.payment_method_id' => 5
                    ])
                    ->order(['OrderPayments.created' => 'ASC'])
                    ->toArray();

                if (empty($creditPayments)) {
                    throw new \Exception('No credit payments found for this customer');
                }

                // Create new payment record
                $newPayment = $this->Payments->newEntity();
                $codePay = $this->Payments->OrderPayments->Orders->Companies->Companycodes->find('all')
                    ->where(['controleur' => 'Payments', 'company_id' => 1])
                    ->last();

                $newPayment->code = "APP" . $codePay->prefixe . ($codePay->compteur + 1);
                $newPayment->user_id = $requestData['user_id'];
                $newPayment->amount = 0;

                if (!$this->Payments->save($newPayment)) {
                    throw new \Exception('Failed to create new payment');
                }

                // Update counter
                $codePay->compteur += 1;
                $this->Payments->OrderPayments->Orders->Companies->Companycodes->save($codePay);

                $totalPayment = 0;
                $creditIndex = 0;

                // Process each payment in the request
                foreach ($requestData['order_payments'] as $paymentData) {
                    if (!isset($paymentData['payment_method_id'], $paymentData['amount'])) {
                        continue;
                    }

                    $remainingAmount = $paymentData['amount'];

                    // Skip credit payments
                    if ($paymentData['payment_method_id'] == 5) {
                        continue;
                    }
                    // Process against available credit payments
                    while ($remainingAmount > 0 && $creditIndex < count($creditPayments)) {
                        $creditPayment = $creditPayments[$creditIndex];

                        if ($creditPayment->amount <= $remainingAmount) {
                            // Full credit payment conversion
                            $creditPayment->payment_method_id = $paymentData['payment_method_id'];
                            $creditPayment->payment_id = $newPayment->id;
                            $totalPayment += $creditPayment->amount;
                            $remainingAmount -= $creditPayment->amount;

                            if (!$this->Payments->OrderPayments->save($creditPayment)) {
                                throw new \Exception('Failed to update credit payment');
                            }

                            $creditIndex++;
                        } else {
                            // Split credit payment
                            $originalAmount = $creditPayment->amount;
                            $creditPayment->amount = $remainingAmount;
                            $creditPayment->payment_method_id = $paymentData['payment_method_id'];
                            $creditPayment->payment_id = $newPayment->id;

                            if (!$this->Payments->OrderPayments->save($creditPayment)) {
                                throw new \Exception('Failed to update split credit payment');
                            }

                            // Create remaining credit payment
                            $newCredit = $this->Payments->OrderPayments->newEntity([
                                'amount' => $originalAmount - $remainingAmount,
                                'order_id' => $creditPayment->order_id,
                                'payment_method_id' => 5,
                                'statut' => 1
                            ]);

                            if (!$this->Payments->OrderPayments->save($newCredit)) {
                                throw new \Exception('Failed to create new credit payment');
                            }

                            $totalPayment += $remainingAmount;
                            $remainingAmount = 0;
                        }
                    }
                }

                // Update total payment amount
                $newPayment->amount = $totalPayment;
                if (!$this->Payments->save($newPayment)) {
                    throw new \Exception('Failed to update payment total');
                }

                // Process payment images if any
                if (!empty($requestData['payment_images'])) {
                    $this->loadModel('Photos');
                    foreach ($requestData['payment_images'] as $paymentImage) {
                        if (empty($paymentImage['image']))
                            continue;

                        $paymentPhoto = base64_decode($paymentImage['image']);
                        if (!$paymentPhoto)
                            continue;

                        $filename = !empty($paymentImage['image_path']) ?
                            $paymentImage['image_path'] :
                            $newPayment->id . '.jpg';

                        $temp = explode(".", $filename);
                        $extension = end($temp);
                        $name = round(microtime(true) * 1000) . '.' . $extension;

                        if (!file_put_contents('../webroot/files/Photos/payments/' . $name, $paymentPhoto)) {
                            continue;
                        }

                        $photo = $this->Photos->newEntity([
                            "title" => $name,
                            "controleur" => "payments",
                            "statut" => 1,
                            "company_id" => 1,
                            'photo' => $name,
                            'dir' => 'webroot/files/Photos/payments',
                            "objectid" => $newPayment->id
                        ]);

                        $this->Photos->save($photo);
                    }
                }

                $data = [
                    'statut' => 1,
                    'message' => 'Payment processed successfully'
                ];

            } catch (\Exception $e) {
                $data = [
                    'statut' => 0,
                    'message' => $e->getMessage()
                ];
            }

            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
            header("Content-Type: application/json; charset=UTF-8");
            header("Access-Control-Allow-Methods: POST, OPTIONS");

            $this->set(compact('data'));
            $this->set('_serialize', 'data');
            $this->RequestHandler->renderAs($this, 'json');
        }
    }
}