<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Cake\Auth\DefaultPasswordHasher;

class B2bCustomerApiController extends AppController
{
    private $jwtKey = 'super_secret_b2b_app_key_2026_very_long_secure_string';

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->Auth->allow(); // We handle auth manually via JWT
        $this->viewBuilder()->setClassName('Json');
    }

    private function _getBearerToken()
    {
        $headers = $this->request->getHeaders();
        if (isset($headers['Authorization'][0])) {
            if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'][0], $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    private function _checkAuth()
    {
        $token = $this->_getBearerToken();
        if (!$token) {
            return false;
        }
        try {
            $decoded = JWT::decode($token, new Key($this->jwtKey, 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function _requireAuth()
    {
        $user = $this->_checkAuth();
        if (!$user) {
            $this->response = $this->response->withStatus(401);
            $this->set([
                'success' => false,
                'message' => 'Unauthorized or token expired',
                '_serialize' => ['success', 'message']
            ]);
            return false;
        }
        return $user;
    }

    // 1. /init endpoint
    public function init()
    {
        $this->request->allowMethod(['get']);
        $this->loadModel('AppSettings');

        $settings = $this->AppSettings->find('all')
            ->select(['key_name', 'key_value'])
            ->toArray();

        $formattedSettings = [];
        foreach ($settings as $setting) {
            $formattedSettings[$setting->key_name] = $setting->key_value;
        }

        if (empty($formattedSettings)) {
            $formattedSettings = [
                'maintenance_mode' => 'false',
                'min_app_version' => '1.0.0',
                'support_phone' => '+123456789'
            ];
        }

        $this->set([
            'success' => true,
            'data' => $formattedSettings,
            '_serialize' => ['success', 'data']
        ]);
    }

    // 2. /login endpoint
    public function login()
    {
        $this->request->allowMethod(['post']);
        $phone = $this->request->getData('phone');
        $password = $this->request->getData('password');

        // Fallback for JSON body if RequestHandler didn't parse it
        if (empty($phone) && empty($password)) {
            $json = $this->request->input('json_decode', true);
            if (is_array($json)) {
                $phone = $json['phone'] ? $json['phone'] : null;
                $password = $json['password'] ? $json['password'] : null;
            }
        }

        \Cake\Log\Log::debug('B2B Login Attempt - Phone: ' . json_encode($phone));

        $this->loadModel('Customers');
        $customer = $this->Customers->find()
            ->where(['phone' => $phone])
            ->first();

        if ($customer) {
            \Cake\Log\Log::debug('B2B Login - Customer found. Statut: ' . $customer->statut);
            $hasher = new DefaultPasswordHasher();
            $isValidPassword = $hasher->check($password, $customer->password);
            \Cake\Log\Log::debug('B2B Login - Password valid: ' . ($isValidPassword ? 'true' : 'false'));

            if ($isValidPassword && $customer->statut == 1) {

                $payload = [
                    'iss' => 'moqa_backend',
                    'sub' => $customer->id,
                    'iat' => time(),
                    'exp' => time() + (60 * 60 * 24 * 30), // 30 days
                    'role' => 'customer'
                ];
                $token = JWT::encode($payload, $this->jwtKey, 'HS256');

                $this->set([
                    'success' => true,
                    'data' => [
                        'token' => $token,
                        'customer' => [
                            'id' => $customer->id,
                            'name' => $customer->name,
                            'phone' => $customer->phone,
                            'wallet_balance' => (float)$customer->wallet_balance,
                        ],
                    ],
                    '_serialize' => ['success', 'data'],
                ]);
                return;
            }
        }

        $this->response = $this->response->withStatus(401);
        $this->set([
            'success' => false,
            'message' => 'Invalid phone number or password',
            '_serialize' => ['success', 'message']
        ]);
    }

    // Example protected endpoint
    public function profile()
    {
        $user = $this->_requireAuth();
        if (!$user)
            return; // _requireAuth already set the 401 response

        $this->loadModel('Customers');
        $customer = $this->Customers->get($user->sub);

        $this->set([
            'success' => true,
            'data' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'adresse' => $customer->adresse,
                'wallet_balance' => (float)$customer->wallet_balance,
            ],
            '_serialize' => ['success', 'data'],
        ]);
    }

    public function myLoyaltyPoints()
    {
        $this->request->allowMethod(['get']);
        $user = $this->_requireAuth();
        if (!$user)
            return;

        $this->loadModel('Loyaltypoints');
        $myloyaltypoints = $this->Loyaltypoints->find('all')
            ->where([
                'Loyaltypoints.customer_id' => $user->sub,
                'Loyaltypoints.statut' => 1,
                'Loyaltypoints.order_id IS ' => NULL
            ])
            ->toArray();

        $data = [];
        $total = 0;
        foreach ($myloyaltypoints as $loyaltypoint) {
            $data[] = [
                'id' => $loyaltypoint->id,
                'code' => $loyaltypoint->code,
                'points' => (float) $loyaltypoint->points,
                'statut' => $loyaltypoint->statut,
                'valeur' => (float) $loyaltypoint->valeur
            ];
            $total += (float) $loyaltypoint->points;
        }

        $this->set([
            'success' => true,
            'data' => [
                'total_points' => $total,
                'history' => $data
            ],
            '_serialize' => ['success', 'data']
        ]);
    }

    public function categories()
    {
        $this->request->allowMethod(['get']);
        $user = $this->_requireAuth();
        if (!$user)
            return;

        $this->loadModel('Categories');
        $categories = $this->Categories->find('all')
            ->where(['Categories.category_id IS NOT ' => NULL])
            ->toArray();

        $data = [];
        foreach ($categories as $category) {
            $photo = $this->Categories->Photos->find('all')
                ->where(['controleur' => 'categories', 'objectid' => $category->id])
                ->order(['created' => 'ASC'])
                ->last();

            $img = \Cake\Routing\Router::url('/', true) . 'webroot/img/unvailable.jpg';
            if ($photo) {
                $img = \Cake\Routing\Router::url('/', true) . $photo->dir . '/' . 'thumbnail160-' . $photo->photo;
            }

            $data[] = [
                "id" => $category->id,
                "code" => $category->code,
                "title" => $category->title,
                "image" => $img
            ];
        }

        $this->set([
            'success' => true,
            'data' => $data,
            '_serialize' => ['success', 'data']
        ]);
    }

    public function products()
    {
        $this->request->allowMethod(['get']);
        $user = $this->_requireAuth();
        if (!$user)
            return;

        $this->loadModel('Customers');
        $customer = $this->Customers->get($user->sub);
        $customerId = $customer->id;
        $customerType = $customer->customertype_id ? $customer->customertype_id : 2;
        $warehouse_id = 1; // Default to main warehouse

        $limit = $this->request->getQuery('limit', 10);
        $page = $this->request->getQuery('page', 1);
        $searchText = $this->request->getQuery('search');
        $categoryId = $this->request->getQuery('category_id');
        $categories = $categoryId ? [$categoryId] : null;

        $data = [];

        // Load all tranches with their remisetype, gift pack, and trancheprices
        $this->loadModel('Tranches');
        $allTranches = $this->Tranches->find('all')
            ->contain(['Remisetypes', 'Packs', 'Trancheprices' => ['Prices']])
            ->toArray();

        $formatTranches = function ($tranchesArray, $productId, $customerType, $priceApplies) {
            $tranches = [];
            foreach ($tranchesArray as $tranche) {
                $appliesToProduct = false;
                if (empty($tranche->trancheprices)) {
                    $appliesToProduct = true;
                } else {
                    foreach ($tranche->trancheprices as $tp) {
                        if (!empty($tp->price) && $tp->price->pack_id == $productId && $tp->price->customertype_id == $customerType) {
                            $appliesToProduct = true;
                            break;
                        }
                    }
                }
                if ($appliesToProduct) {
                    $trancheData = [
                        'id' => $tranche->id,
                        'title' => $tranche->title,
                        'apply_type' => $tranche->apply_type,
                        'quantity_unit_type' => $tranche->quantity_unit_type,
                        'min' => $tranche->min,
                        'max' => $tranche->max,
                        'remise' => $tranche->remisetype_id == 2 ? ($tranche->remise / $priceApplies * 100) : $tranche->remise,
                        'remisetype_id' => $tranche->remisetype_id,
                        'remisetype' => [
                            'id' => $tranche->remisetype->id,
                            'title' => $tranche->remisetype->title
                        ]
                    ];
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

        $this->loadModel('Warehouses');
        $warehouse = $this->Warehouses->get($warehouse_id, [
            'contain' => [
                'Subwarehouses' => function ($q) {
                    return $q->where(['whnature_id' => 1, 'whtype_id' => 2]);
                }
            ]
        ]);

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
            $img = \Cake\Routing\Router::url('/', true) . 'webroot/img/unvailable.jpg';
            $images = [];
            if ($photo) {
                $img = \Cake\Routing\Router::url('/', true) . $photo->dir . '/thumbnail700-' . $photo->photo;
                $images[] = \Cake\Routing\Router::url('/', true) . $photo->dir . '/thumbnail700-' . $photo->photo;
            } else {
                $images[] = $img;
            }
            $variants = [];

            if ($pack->saletype_id == 1) {
                $variants[0] = ['id' => $pack->packunites[0]->unite->id, 'title' => $pack->packunites[0]->unite->abrev, 'quantity' => $pack->packunites[0]->quantity, 'statut' => 1];
                $variants[1] = ['id' => $pack->packunites[0]->unite->parentunite->id, 'title' => $pack->packunites[0]->unite->parentunite->abrev, 'quantity' => 1, 'statut' => 1];
            } elseif ($pack->saletype_id == 2) {
                $variants[0] = ['id' => $pack->packunites[0]->unite->id, 'title' => $pack->packunites[0]->unite->abrev, 'quantity' => $pack->packunites[0]->quantity, 'statut' => 1];
                $variants[1] = ['id' => $pack->packunites[0]->unite->parentunite->id, 'title' => $pack->packunites[0]->unite->parentunite->abrev, 'quantity' => 1, 'statut' => 0];
            } elseif ($pack->saletype_id == 4) {
                if ($pack->measurement_unit->conversion_factor == 1) {
                    $pack->prices[0]->price = $pack->prices[0]->price * $pack->measurement_quantity;
                    $quantityInInstance = $quantityInInstance / $pack->measurement_quantity;
                    $variants[0] = ['id' => $pack->id, 'title' => $pack->measurement_unit->abbreviation, 'quantity' => 1, 'statut' => 1];
                } else {
                    $getParent = $this->Packs->MeasurementUnits->find('all')->where(['type' => $pack->measurement_unit->type, 'conversion_factor' => ($pack->measurement_unit->conversion_factor * 1000)]);
                    if ($getParent) {
                        $pack->prices[0]->price = $pack->prices[0]->price * $getParent->first()->conversion_factor / ($pack->measurement_quantity * $pack->measurement_unit->conversion_factor);
                        $quantityInInstance = $quantityInInstance / ($getParent->first()->conversion_factor / ($pack->measurement_quantity * $pack->measurement_unit->conversion_factor));
                        $variants[0] = ['id' => $pack->id, 'title' => $getParent->first()->abbreviation, 'quantity' => 1, 'statut' => 1];
                    } else {
                        $pack->prices[0]->price = $pack->prices[0]->price * $pack->packunites[0]->unite->parentunite->abrev;
                        $quantityInInstance = $quantityInInstance / $pack->packunites[0]->unite->parentunite->abrev;
                        $variants[0] = ['id' => $pack->packunites[0]->unite->parentunite->id, 'title' => $pack->packunites[0]->unite->parentunite->abrev, 'quantity' => 1, 'statut' => 1];
                    }
                }
            } else {
                $variants[0] = ['id' => $pack->packunites[0]->unite->id, 'title' => $pack->packunites[0]->unite->abrev, 'quantity' => $pack->packunites[0]->quantity, 'statut' => 0];
                $variants[1] = ['id' => $pack->packunites[0]->unite->parentunite->id, 'title' => $pack->packunites[0]->unite->parentunite->abrev, 'quantity' => 1, 'statut' => 1];
            }
            if ($pack->measurement_unit->conversion_factor == 1) {
                $loyaltypoints = $pack->measurement_quantity . $pack->measurement_unit->abbreviation;
            } else {
                $getParent = $this->Packs->MeasurementUnits->find('all')->where(['type' => $pack->measurement_unit->type, 'conversion_factor' => ($pack->measurement_unit->conversion_factor * 1000)]);
                $loyaltypoints = (($getParent->first()->conversion_factor / ($pack->measurement_quantity * $pack->measurement_unit->conversion_factor)) * $pack->loyaltypoints) . "/" . $getParent->first()->conversion_factor . $getParent->first()->abbreviation;
            }
            $price = !empty($pack->prices) ? $pack->prices[0]->price : 0;
            $tranches = $formatTranches($allTranches, $pack->id, $customerType, $price);

            $data[] = [
                "id" => $pack->id,
                "code" => $pack->code,
                "title" => $pack->title,
                "price" => $price,
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
                'bonus_amount' => (float)$pack->bonus_amount,
                'bonus_unit_threshold' => (float)$pack->bonus_unit_threshold,
                'measurement_unit_abbreviation' => $pack->measurement_unit ? $pack->measurement_unit->abbreviation : '',
            ];
        }

        $response = ["status" => 200, "msg" => "Success", "data" => $data];
        header("Content-Type: application/json; charset=UTF-8");
        $this->set(compact('response'));
        $this->set('_serialize', 'response');
        $this->RequestHandler->renderAs($this, 'json');
    }

    public function addOrder()
    {
        $this->request->allowMethod(['post']);
        $user = $this->_requireAuth();
        if (!$user)
            return;

        $this->loadModel('Orders');
        $order = $this->Orders->newEntity();

        $customer_id = $user->sub;
        $datas = $this->request->getData();

        $datas["customer_id"] = $customer_id;
        $datas["company_id"] = 1;
        $datas["pofsale_id"] = 1;
        $datas["statut"] = 1; // Pending
        $datas["user_id"] = 1; // System user
        $datas["ordertype_id"] = 2; // Pre-commande

        $customer = $this->Orders->Customers->get($customer_id);
        $customertype_id = $customer->customertype_id ? $customer->customertype_id : 2;
        $warehouse_id = 1;

        $pricingService = new \App\Service\OrderPricingService();

        $totalPrice = 0;
        $totalOrderAmount = 0;

        if (!empty($datas['cartItems'])) {
            $datas['orderpacks'] = [];
            // First pass to calculate total order amount for tranches
            foreach ($datas['cartItems'] as $item) {
                $pack = $this->Orders->Orderpacks->Packs->get($item['pack_id'], ['contain' => ['Prices']]);
                $price = !empty($pack->prices) ? $pack->prices[0]->price : 0;
                $totalOrderAmount += $price * $item['quantity'];
            }

            // Second pass to apply pricing
            foreach ($datas['cartItems'] as $item) {
                $qty = (int) $item['quantity'];
                $pack = $this->Orders->Orderpacks->Packs->get($item['pack_id'], ['contain' => ['Prices', 'MeasurementUnits', 'Packunites.Unites.Parentunites']]);

                try {
                    $priceResult = $pricingService->priceLine(
                        $item['pack_id'],
                        $qty,
                        $customertype_id,
                        $warehouse_id,
                        1,
                        $totalOrderAmount
                    );
                    $price = $priceResult['final_unit_price'];
                } catch (\Exception $e) {
                    $price = !empty($pack->prices) ? $pack->prices[0]->price : 0;
                }

                if ($pack->measurement_unit && $pack->measurement_unit->conversion_factor == 1) {
                    $loyaltypoints = round($pack->loyaltypoints * $pack->measurement_quantity, 2);
                } else if ($pack->measurement_unit) {
                    $getParent = $this->Orders->Orderpacks->Packs->MeasurementUnits->find('all')->where(['type' => $pack->measurement_unit->type, 'conversion_factor' => ($pack->measurement_unit->conversion_factor * 1000)]);
                    if ($getParent && !$getParent->isEmpty()) {
                        $loyaltypoints = round($pack->loyaltypoints * $getParent->first()->conversion_factor / ($pack->measurement_quantity * $pack->measurement_unit->conversion_factor), 2);
                    } else {
                        $loyaltypoints = round($pack->loyaltypoints, 2);
                    }
                } else {
                    $loyaltypoints = round($pack->loyaltypoints, 2);
                }

                $totalPrice += ($qty * $price);

                $datas['orderpacks'][] = [
                    'pack_id' => $item['pack_id'],
                    'quantity' => $qty,
                    'price' => $price,
                    'initialprice' => $price,
                    'loyaltypoints' => $loyaltypoints,
                    'commissionpack' => $pack->commission,
                    'turnover_id' => $pack->turnover_id,
                    'statut' => 1,
                    'company_id' => 1,
                    'user_id' => 1
                ];
            }
        }
        $datas["total"] = $totalPrice;

        $loyaltypoints = $this->Orders->Orderpacks->find('all')->contain(["Orders"])->where(['Orders.customer_id' => $customer_id, 'Orderpacks.loyalityvalidation' => 1]);
        $datas['loyaltypoints'] = 0;
        foreach ($loyaltypoints as $loyaltypoint) {
            $datas['loyaltypoints'] += ($loyaltypoint->loyaltypoints * $loyaltypoint->quantity);
        }
        $datas['loyaltypoints'] = round($datas['loyaltypoints'], 2);

        $code = $this->Orders->Companies->Companycodes->find('all')->where(['controleur' => 'Orders', 'company_id' => 1])->last();
        if ($code) {
            $datas['code'] = "APP" . $code->prefixe . ($code->compteur + 1);
            $code->compteur += 1;
            $this->Orders->Companies->Companycodes->save($code);
        } else {
            $datas['code'] = "APP" . time();
        }

        $order = $this->Orders->patchEntity($order, $datas, ['associated' => ['Orderpacks']]);

        if ($this->Orders->save($order)) {
            // Calculate total earned bonus and update customer's wallet balance
            $totalBonus = 0.0;
            $savedOrder = $this->Orders->get($order->id, [
                'contain' => ['Orderpacks.Packs'],
            ]);
            if (!empty($savedOrder->orderpacks)) {
                foreach ($savedOrder->orderpacks as $orderpack) {
                    $pack = $orderpack->pack;
                    if ($pack && $pack->bonus_amount > 0 && $pack->bonus_unit_threshold > 0) {
                        $itemQty = (float)$orderpack->quantity;
                        $measQty = (float)$pack->measurement_quantity;
                        if ($measQty <= 0) {
                            $measQty = 1.0;
                        }
                        $totalUnits = $itemQty * $measQty;
                        $itemBonus = ($totalUnits / (float)$pack->bonus_unit_threshold) * (float)$pack->bonus_amount;
                        $totalBonus += $itemBonus;
                    }
                }
            }
            if ($totalBonus > 0) {
                $customer = $this->Orders->Customers->get($customer_id);
                $customer->wallet_balance = (float)$customer->wallet_balance + $totalBonus;
                $this->Orders->Customers->save($customer);
            }
            $response = ["status" => 200, "msg" => "Order added successfully", "data" => $order];
        } else {
            $response = ["status" => 400, "msg" => "Failed to add order", "errors" => $order->getErrors()];
        }

        header("Content-Type: application/json; charset=UTF-8");
        $this->set(compact('response'));
        $this->set('_serialize', 'response');
        $this->RequestHandler->renderAs($this, 'json');
    }

    public function myorders()
    {
        $this->request->allowMethod(['get']);
        $user = $this->_requireAuth();
        if (!$user)
            return;
        $customerId = $user->sub;

        $this->loadModel('Orders');
        $orders = $this->Orders->find('all')->contain(['Users', 'OrderPayments.PaymentMethods', 'Shippings', 'Orderpacks.Packs.MeasurementUnits', 'Orderpacks.Turnovers', 'Orderpacks.Packs.Turnovers', 'Orderpacks.Packs.Packunites.Unites.Parentunites', 'Customers.Zones.Cities', 'Customers.Customertypes', 'Orderpacks.Packs.Brands', 'Orderpacks.Packs.Prices', 'Orderpacks.Packs.Categories']);
        $orders->where(['Orders.customer_id' => $customerId]);
        $orders->order(['Orders.created' => 'DESC']);
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
            $img = \Cake\Routing\Router::url('/', true) . 'webroot/img/unvailable.jpg';
            if ($photo) {
                $img = \Cake\Routing\Router::url('/', true) . $photo->dir . '/' . $photo->photo;
            }
            $orderPayments = [];
            foreach ($order->order_payments as $keyOrderP => $orderPayment) {
                $image = "";
                $photo = $this->Orders->Photos->find('all')->where(['controleur' => 'orderpayments', 'objectid' => $orderPayment->id])->order(['created' => 'ASC'])->last();
                if ($photo) {
                    $image = \Cake\Routing\Router::url('/', true) . $photo->dir . '/' . $photo->title;
                }
                $orderPayments[] = [
                    "id" => $orderPayment->id,
                    "amount" => $orderPayment->amount,
                    "date" => $orderPayment->cheque_date ? $orderPayment->cheque_date->i18nFormat('dd/MM/yyyy', 'Africa/Casablanca') : $orderPayment->created->i18nFormat('dd/MM/yyyy ', 'Africa/Casablanca'),
                    "photo" => $image,
                    "created_at" => $orderPayment->created->i18nFormat('dd/MM/yyyy (HH:mm)', 'Africa/Casablanca'),
                    "payment_method" => [
                        "id" => $orderPayment->payment_method->id ? $orderPayment->payment_method->id : 0,
                        "name" => $orderPayment->payment_method->name ? $orderPayment->payment_method->name : '',
                        "code" => $orderPayment->payment_method->code ? $orderPayment->payment_method->code : '',
                        "requires_cheque_date" => $orderPayment->payment_method->requires_cheque_date ? $orderPayment->payment_method->requires_cheque_date : 0,
                    ]
                ];
            }
            $data[$key]["order_payments"] = $orderPayments;

            foreach ($order->orderpacks as $key1 => $orderpack) {
                if ($orderpack->pack->measurement_unit && $orderpack->pack->measurement_unit->conversion_factor == 1) {
                    $loyaltypoints = $orderpack->pack->measurement_quantity . $orderpack->pack->measurement_unit->abbreviation;
                } else if ($orderpack->pack->measurement_unit) {
                    $getParent = $this->Orders->Orderpacks->Packs->MeasurementUnits->find('all')->where(['type' => $orderpack->pack->measurement_unit->type, 'conversion_factor' => ($orderpack->pack->measurement_unit->conversion_factor * 1000)]);
                    if ($getParent && !$getParent->isEmpty()) {
                        $loyaltypoints = (($getParent->first()->conversion_factor / ($orderpack->pack->measurement_quantity * $orderpack->pack->measurement_unit->conversion_factor)) * $orderpack->loyaltypoints) . "/" . $getParent->first()->conversion_factor . $getParent->first()->abbreviation;
                    } else {
                        $loyaltypoints = $orderpack->loyaltypoints;
                    }
                } else {
                    $loyaltypoints = $orderpack->loyaltypoints;
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

                $photo = $this->Orders->Orderpacks->Packs->Photos->find('all')->where(['controleur' => 'packs', 'objectid' => $orderpack->pack->id])->order(['created' => 'ASC'])->last();
                $img = \Cake\Routing\Router::url('/', true) . 'webroot/img/unvailable.jpg';
                $images = [];
                if ($photo) {
                    $img = \Cake\Routing\Router::url('/', true) . $photo->dir . '/' . 'thumbnail160-' . $photo->photo;
                    $images[] = \Cake\Routing\Router::url('/', true) . $photo->dir . '/' . $photo->photo;
                } else {
                    $images[] = $img;
                }
                $variants = [];
                if ($orderpack->pack->packunites && isset($orderpack->pack->packunites[0])) {
                    if ($orderpack->pack->packunites[0]->statut == 1) {
                        $variants[0] = [
                            'id' => $orderpack->pack->packunites[0]->unite->id,
                            'title' => $orderpack->pack->packunites[0]->unite->title,
                            'quantity' => $orderpack->pack->packunites[0]->quantity,
                            'statut' => 1,
                        ];
                    }
                }
                $data[$key]["orderpacks"][$key1]["pack"] = [
                    "id" => $orderpack->pack->id,
                    "title" => $orderpack->pack->title,
                    "code" => $orderpack->pack->code,
                    "image" => $img,
                    "images" => $images,
                    "variants" => $variants
                ];
            }
        }

        $response = ["status" => 200, "msg" => "Success", "data" => $data];
        header("Content-Type: application/json; charset=UTF-8");
        $this->set(compact('response'));
        $this->set('_serialize', 'response');
        $this->RequestHandler->renderAs($this, 'json');
    }

    public function customerSignup()
    {
        $this->request->allowMethod(['post', 'put']);
        $this->loadModel('Customers');

        $data = $this->request->getData();

        if (empty($data['phone']) || empty($data['password'])) {
            $this->set([
                'success' => false,
                'message' => 'Phone and password are required',
                '_serialize' => ['success', 'message']
            ]);
            return;
        }

        $existing = $this->Customers->find()->where(['phone' => $data['phone']])->first();
        if ($existing) {
            $this->set([
                'success' => false,
                'message' => 'Ce numéro de téléphone existe déjà',
                '_serialize' => ['success', 'message']
            ]);
            return;
        }

        $customer = $this->Customers->newEntity();

        // Hash password
        $data['password'] = (new \Cake\Auth\DefaultPasswordHasher())->hash($data['password']);
        $data['statut'] = 1; // active

        $customer = $this->Customers->patchEntity($customer, $data);

        if ($this->Customers->save($customer)) {
            $this->set([
                'success' => true,
                'message' => 'Inscription réussie',
                '_serialize' => ['success', 'message']
            ]);
        } else {
            $this->set([
                'success' => false,
                'message' => 'Erreur lors de l\'inscription',
                'errors' => $customer->getErrors(),
                '_serialize' => ['success', 'message', 'errors']
            ]);
        }
    }

    public function homeSliders()
    {
        $this->request->allowMethod(['get']);
        if (!$this->_requireAuth())
            return;

        $baseUrl = \Cake\Routing\Router::url('/', true);
        $data = [
            ['id' => 1, 'image' => $baseUrl . 'webroot/onboarding_1.png'],
            ['id' => 2, 'image' => $baseUrl . 'webroot/onboarding_2.png']
        ];
        $this->set(['success' => true, 'data' => $data, '_serialize' => ['success', 'data']]);
    }

    public function homecategories()
    {
        $this->request->allowMethod(['get']);
        if (!$this->_requireAuth())
            return;

        $this->loadModel('Categories');
        $categories = $this->Categories->find()->limit(8)->toArray();
        $this->set(['success' => true, 'data' => $categories, '_serialize' => ['success', 'data']]);
    }

    public function homebrands()
    {
        $this->request->allowMethod(['get']);
        if (!$this->_requireAuth())
            return;

        $baseUrl = \Cake\Routing\Router::url('/', true);
        $data = [
            ['id' => 1, 'title' => 'Brand A', 'image' => $baseUrl . 'webroot/logo.png'],
            ['id' => 2, 'title' => 'Brand B', 'image' => $baseUrl . 'webroot/logo.png']
        ];
        $this->set(['success' => true, 'data' => $data, '_serialize' => ['success', 'data']]);
    }

    public function newhomeproducts()
    {
        $this->request->allowMethod(['get']);
        $user = $this->_requireAuth();
        if (!$user)
            return;

        $this->loadModel('Customers');
        $customer = $this->Customers->get($user->sub);
        $customerId = $customer->id;
        $customerType = $customer->customertype_id ? $customer->customertype_id : 2;
        $warehouse_id = 1;

        $data = [];

        $this->loadModel('Tranches');
        $allTranches = $this->Tranches->find('all')
            ->contain(['Remisetypes', 'Packs', 'Trancheprices' => ['Prices']])
            ->toArray();

        $formatTranches = function ($tranchesArray, $productId, $customerType, $priceApplies) {
            $tranches = [];
            foreach ($tranchesArray as $tranche) {
                $appliesToProduct = false;
                if (empty($tranche->trancheprices)) {
                    $appliesToProduct = true;
                } else {
                    foreach ($tranche->trancheprices as $tp) {
                        if (!empty($tp->price) && $tp->price->pack_id == $productId && $tp->price->customertype_id == $customerType) {
                            $appliesToProduct = true;
                            break;
                        }
                    }
                }
                if ($appliesToProduct) {
                    $trancheData = [
                        'id' => $tranche->id,
                        'title' => $tranche->title,
                        'apply_type' => $tranche->apply_type,
                        'quantity_unit_type' => $tranche->quantity_unit_type,
                        'min' => $tranche->min,
                        'max' => $tranche->max,
                        'remise' => $tranche->remisetype_id == 2 ? ($tranche->remise / $priceApplies * 100) : $tranche->remise,
                        'remisetype_id' => $tranche->remisetype_id,
                        'remisetype' => ['id' => $tranche->remisetype->id, 'title' => $tranche->remisetype->title]
                    ];
                    if ($tranche->pack_id !== null && !empty($tranche->pack)) {
                        $trancheData['gift'] = ['id' => $tranche->pack->id, 'title' => $tranche->pack->title];
                    }
                    $tranches[] = $trancheData;
                }
            }
            return $tranches;
        };

        $this->loadModel('Warehouses');
        $warehouse = $this->Warehouses->get($warehouse_id, [
            'contain' => [
                'Subwarehouses' => function ($q) {
                    return $q->where(['whnature_id' => 1, 'whtype_id' => 2]);
                }
            ]
        ]);

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

        $packs->where(['Packs.statut' => 1]);
        $packs->where(['Packs.category_id !=' => 9]);
        $packs->where(['Packs.is_new' => 1]);
        $packs->order(['Packs.created' => 'DESC']);
        $packs->limit(5);

        foreach ($packs as $pack) {
            $quantityInInstance = 0;
            $orderpacks = $this->Packs->Orderpacks->find('all')->contain(['Orders'])->where(['Orders.customer_id' => $customerId, 'Orderpacks.pack_id' => $pack->id]);
            foreach ($orderpacks as $orderpack) {
                $quantityInInstance += $orderpack->quantity;
            }
            $photo = $this->Packs->Photos->find('all')->where(['controleur' => 'packs', 'objectid' => $pack->id])->order(['created' => 'ASC'])->last();
            $img = \Cake\Routing\Router::url('/', true) . 'webroot/img/unvailable.jpg';
            $images = [];
            if ($photo) {
                $img = \Cake\Routing\Router::url('/', true) . $photo->dir . '/thumbnail700-' . $photo->photo;
                $images[] = \Cake\Routing\Router::url('/', true) . $photo->dir . '/thumbnail700-' . $photo->photo;
            } else {
                $images[] = $img;
            }
            $variants = [];
            if ($pack->saletype_id == 1) {
                $variants[0] = ['id' => $pack->packunites[0]->unite->id, 'title' => $pack->packunites[0]->unite->abrev, 'quantity' => $pack->packunites[0]->quantity, 'statut' => 1];
                $variants[1] = ['id' => $pack->packunites[0]->unite->parentunite->id, 'title' => $pack->packunites[0]->unite->parentunite->abrev, 'quantity' => 1, 'statut' => 1];
            } elseif ($pack->saletype_id == 2) {
                $variants[0] = ['id' => $pack->packunites[0]->unite->id, 'title' => $pack->packunites[0]->unite->abrev, 'quantity' => $pack->packunites[0]->quantity, 'statut' => 1];
                $variants[1] = ['id' => $pack->packunites[0]->unite->parentunite->id, 'title' => $pack->packunites[0]->unite->parentunite->abrev, 'quantity' => 1, 'statut' => 0];
            } elseif ($pack->saletype_id == 4) {
                if ($pack->measurement_unit->conversion_factor == 1) {
                    $pack->prices[0]->price = $pack->prices[0]->price * $pack->measurement_quantity;
                    $variants[0] = ['id' => $pack->id, 'title' => $pack->measurement_unit->abbreviation, 'quantity' => 1, 'statut' => 1];
                } else {
                    $getParent = $this->Packs->MeasurementUnits->find('all')->where(['type' => $pack->measurement_unit->type, 'conversion_factor' => ($pack->measurement_unit->conversion_factor * 1000)]);
                    if ($getParent) {
                        $pack->prices[0]->price = $pack->prices[0]->price * $getParent->first()->conversion_factor / ($pack->measurement_quantity * $pack->measurement_unit->conversion_factor);
                        $variants[0] = ['id' => $pack->id, 'title' => $getParent->first()->abbreviation, 'quantity' => 1, 'statut' => 1];
                    } else {
                        $pack->prices[0]->price = $pack->prices[0]->price * $pack->packunites[0]->unite->parentunite->abrev;
                        $variants[0] = ['id' => $pack->packunites[0]->unite->parentunite->id, 'title' => $pack->packunites[0]->unite->parentunite->abrev, 'quantity' => 1, 'statut' => 1];
                    }
                }
            } else {
                $variants[0] = ['id' => $pack->packunites[0]->unite->id, 'title' => $pack->packunites[0]->unite->abrev, 'quantity' => $pack->packunites[0]->quantity, 'statut' => 0];
                $variants[1] = ['id' => $pack->packunites[0]->unite->parentunite->id, 'title' => $pack->packunites[0]->unite->parentunite->abrev, 'quantity' => 1, 'statut' => 1];
            }
            if ($pack->measurement_unit->conversion_factor == 1) {
                $loyaltypoints = $pack->measurement_quantity . $pack->measurement_unit->abbreviation;
            } else {
                $getParent = $this->Packs->MeasurementUnits->find('all')->where(['type' => $pack->measurement_unit->type, 'conversion_factor' => ($pack->measurement_unit->conversion_factor * 1000)]);
                $loyaltypoints = (($getParent->first()->conversion_factor / ($pack->measurement_quantity * $pack->measurement_unit->conversion_factor)) * $pack->loyaltypoints) . "/" . $getParent->first()->conversion_factor . $getParent->first()->abbreviation;
            }
            $price = !empty($pack->prices) ? $pack->prices[0]->price : 0;
            $tranches = $formatTranches($allTranches, $pack->id, $customerType, $price);

            $data[] = [
                "id" => $pack->id,
                "code" => $pack->code,
                "title" => $pack->title,
                "price" => $price,
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
                'bonus_amount' => (float)$pack->bonus_amount,
                'bonus_unit_threshold' => (float)$pack->bonus_unit_threshold,
                'measurement_unit_abbreviation' => $pack->measurement_unit ? $pack->measurement_unit->abbreviation : '',
            ];
        }
        $response = ["status" => 200, "msg" => "Success", "data" => $data];
        header("Content-Type: application/json; charset=UTF-8");
        $this->set(compact('response'));
        $this->set('_serialize', 'response');
        $this->RequestHandler->renderAs($this, 'json');
    }

    public function recommendedhomeproducts()
    {
        $this->request->allowMethod(['get']);
        if (!$this->_requireAuth())
            return;

        $this->loadModel('Products');
        $products = $this->Products->find()
            ->where(['statut' => 1])
            ->limit(10)
            ->toArray();
        $this->set(['success' => true, 'data' => $products, '_serialize' => ['success', 'data']]);
    }
}
