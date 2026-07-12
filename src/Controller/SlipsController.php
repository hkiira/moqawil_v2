<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Slips Controller
 *
 * @property \App\Model\Table\SlipsTable $Slips
 *
 * @method \App\Model\Entity\Slip[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])

 1: confirmé
 2: en attente de validation
 3: Validé
 4: Récupéré
 7: Encaissé
 */
class SlipsController extends AppController
{
    protected function getStockReportUsers($companyId, $warehouseId)
    {
        $users = [];
        $selectedUsers = $this->Slips->Users->Whusers->find('all')
            ->contain([
                'Users.Roles',
                'Users' => function ($q) {
                    return $q->where(['Users.statut' => 1, 'Users.role_id' => 3]);
                }
            ])
            ->where(['Whusers.warehouse_id' => $warehouseId]);

        foreach ($selectedUsers as $whuser) {
            if (!$whuser->user) {
                continue;
            }

            $roleTitle = $whuser->user->role->title ?? 'Rôle inconnu';
            $fullName = trim($whuser->user->firstname . ' ' . $whuser->user->lastname);
            $users[$whuser->user->id] = $fullName . ' (' . $roleTitle . ')';
        }

        $deliveryUsers = $this->Slips->Users->find('all')
            ->contain(['Roles'])
            ->where([
                'Users.company_id' => $companyId,
                'Users.statut' => 1,
                'Users.role_id IN' => [5, 6],
            ]);

        foreach ($deliveryUsers as $deliveryUser) {
            $roleTitle = $deliveryUser->role->title ?? 'Rôle inconnu';
            $fullName = trim($deliveryUser->firstname . ' ' . $deliveryUser->lastname);
            $users[$deliveryUser->id] = $fullName . ' (' . $roleTitle . ')';
        }

        asort($users);

        return $users;
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index($id = null)
    {
        if ($id) {
            $sliptype = $this->Slips->Sliptypes->get($id);
            if ($id == 1) {
                $whusers = $this->Slips->Users->Whusers->find('all')->contain([
                    'Users.Roles',
                    'Users' => function ($q) {
                        return $q->where(['Users.statut' => 1, 'Users.role_id' => 3]);
                    }
                ])->where(['Whusers.warehouse_id' => $this->Auth->user('defaultwh')]);
                $users = [];
                foreach ($whusers as $whuser) {
                    $users[$whuser->user->id] = $whuser->user->firstname . ' ' . $whuser->user->lastname . ' (' . $whuser->user->role->title . ')';
                }
            } elseif ($id == 2) {
                $whusers = $this->Slips->Users->Whusers->find('all')->contain([
                    'Users.Roles',
                    'Users' => function ($q) {
                        return $q->where(['Users.statut' => 1, ['OR' => [['Users.role_id' => 5], ['Users.role_id' => 3]]]]);
                    }
                ])->where(['Whusers.warehouse_id' => $this->Auth->user('defaultwh')]);

                $users = [];
                foreach ($whusers as $whuser) {
                    $users[$whuser->user->id] = $whuser->user->firstname . ' ' . $whuser->user->lastname . ' (' . $whuser->user->role->title . ')';
                }
            } else {
                $users = null;
            }

            $this->set(compact('sliptype', 'id', 'users'));
        }
    }

    public function print($id = null)
    {

        $slip = $this->Slips->get($id, [
            'contain' => [
                'Users',
                'Slipproducts.Whnatures',
                'Sliptypes',
                'Slipproducts.Packs.Saletypes',
                'Slipproducts.Packs.MeasurementUnits',
                'Slipproducts.Packs.Packunites.Unites.Parentunites',

            ],
        ]);
        if ($slip->sliptype_id == 1) {
            $warehouse = $this->Slips->Warehouses->get($slip->warehouse_id);
            $warehousetitle = $warehouse->title;
            $warehoused = $this->Slips->Warehouses->get($slip->warehoused, ['contain' => ['Pofsales.Pofsusers.Users']]);
            $warehousedtitle = $warehoused->pofsales[0]->pofsusers[0]->user->firstname . ' ' . $warehoused->pofsales[0]->pofsusers[0]->user->lastname;
        } elseif ($slip->sliptype_id == 2) {
            $warehoused = $this->Slips->Warehouses->get($slip->warehoused);
            $warehousedtitle = $warehoused->title;
            $warehouse = $this->Slips->Warehouses->get($slip->warehouse_id, ['contain' => ['Pofsales.Pofsusers.Users']]);
            $warehousetitle = $warehouse->pofsales[0]->pofsusers[0]->user->firstname . ' ' . $warehouse->pofsales[0]->pofsusers[0]->user->lastname;
        } elseif ($slip->sliptype_id == 3) {
            $warehouse = $this->Slips->Warehouses->get($slip->warehouse_id);
            $warehousetitle = $warehouse->title;
            $warehoused = $this->Slips->Warehouses->get($slip->warehoused);
            $warehousedtitle = $warehoused->title;
        } elseif ($slip->sliptype_id == 4) {
            $warehouse = $this->Slips->Warehouses->get($slip->warehouse_id);
            $warehousetitle = $warehouse->title;
            $warehoused = $this->Slips->Warehouses->get($slip->warehoused);
            $warehousedtitle = $warehoused->title;
        } elseif ($slip->sliptype_id == 5) {
            $warehouse = $this->Slips->Warehouses->get($slip->warehouse_id);
            $warehousetitle = $warehouse->title;
            $warehoused = $this->Slips->Warehouses->get($slip->warehoused);
            $warehousedtitle = $warehoused->title;
        } elseif ($slip->sliptype_id == 6) {
            $slip = $this->Slips->get($id, [
                'contain' => [
                    'Users',
                    'Slipproducts.Whnatures',
                    'Sliptypes',
                    'Slipproducts.Productunites.Unites.Parentunites',
                    'Slipproducts.Products'

                ],
            ]);
            $warehouse = $this->Slips->Warehouses->get($slip->warehouse_id);
            $warehousetitle = $warehouse->title;
            $warehoused = $this->Slips->Warehouses->get($slip->warehoused);
            $warehousedtitle = $warehoused->title;
        }
        if ($slip->uservalidate) {
            $uservalidate = $this->Slips->Users->get($slip->uservalidate);
        } else {
            $uservalidate = null;
        }
        $slippackquantites = [];
        if ($slip->sliptype_id == 6) {
            foreach ($slip->slipproducts as $key => $slipproduct) {
                $slippackquantites[$slipproduct->id]['title'] = $slipproduct->product->title;
                $slippackquantites[$slipproduct->id]['code'] = $slipproduct->product->reference;
                $slippackquantites[$slipproduct->id]['statut'] = $slipproduct->statut;
                $slippackquantites[$slipproduct->id]['whnature'] = $slipproduct->whnature->title;
                $slippackquantites[$slipproduct->id]['price'] = $slipproduct->price;
                $slippackquantites[$slipproduct->id]['cartsac'] = $slipproduct->productunite->unite->title;
                $slippackquantites[$slipproduct->id]['kgunite'] = $slipproduct->productunite->unite->parentunite->abrev;
                $slippackquantites[$slipproduct->id]['qtcarsac'] = $slipproduct->productunite->quantity;
                $slippackquantites[$slipproduct->id]['quantity'] = ($slipproduct->quantity);
                $slippackquantites[$slipproduct->id]['saletype']['id'] = 1;
                $slippackquantites[$slipproduct->id]['saletype']['title'] = 'Traditionnel';
                $slippackquantites[$slipproduct->id]['measurement']['quantity'] = $slipproduct->quantity;
                $slippackquantites[$slipproduct->id]['measurement']['title'] = '';
                $slippackquantites[$slipproduct->id]['measurement']['price'] = $slipproduct->price;
            }
        } else {

            foreach ($slip->slipproducts as $key => $slipproduct) {
                $slippackquantites[$slipproduct->id]['title'] = $slipproduct->pack->title;
                $slippackquantites[$slipproduct->id]['code'] = $slipproduct->pack->reference;
                $slippackquantites[$slipproduct->id]['statut'] = $slipproduct->statut;
                $slippackquantites[$slipproduct->id]['whnature'] = $slipproduct->whnature->title;
                $slippackquantites[$slipproduct->id]['price'] = $slipproduct->price;
                $slippackquantites[$slipproduct->id]['cartsac'] = $slipproduct->pack->packunites[0]->unite->title;
                $slippackquantites[$slipproduct->id]['kgunite'] = $slipproduct->pack->packunites[0]->unite->parentunite->abrev;
                $slippackquantites[$slipproduct->id]['qtcarsac'] = $slipproduct->pack->packunites[0]->quantity;
                $slippackquantites[$slipproduct->id]['quantity'] = ($slipproduct->quantity);
                $slippackquantites[$slipproduct->id]['saletype']['id'] = ($slipproduct->pack->saletype->id);
                $slippackquantites[$slipproduct->id]['saletype']['title'] = ($slipproduct->pack->saletype->title);

                // Always convert to base unit (conversion_factor = 1)
                $factorOne = $this->Slips->Slipproducts->Packs->MeasurementUnits->find('all')
                    ->where(['conversion_factor' => 1, 'type' => $slipproduct->pack->measurement_unit->type])
                    ->first();

                if ($factorOne) {
                    // Convert quantity to base unit
                    $slippackquantites[$slipproduct->id]['measurement']['quantity'] = $slipproduct->quantity * $slipproduct->pack->measurement_quantity * $slipproduct->pack->measurement_unit->conversion_factor;
                    $slippackquantites[$slipproduct->id]['measurement']['title'] = $factorOne->abbreviation;
                    // Convert price to base unit price
                    $slippackquantites[$slipproduct->id]['measurement']['price'] = $slipproduct->price / ($slipproduct->pack->measurement_quantity * $slipproduct->pack->measurement_unit->conversion_factor);
                } else {
                    // Fallback if no base unit found
                    $slippackquantites[$slipproduct->id]['measurement']['quantity'] = $slipproduct->quantity;
                    $slippackquantites[$slipproduct->id]['measurement']['title'] = $slipproduct->pack->measurement_unit->abbreviation;
                    $slippackquantites[$slipproduct->id]['measurement']['price'] = $slipproduct->price;
                }
            }
        }
        $this->set(compact('slip', 'slippackquantites', 'uservalidate', 'warehousetitle', 'warehousedtitle'));

    }

    /**
     * View method
     *
     * @param string|null $id Slip id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $slip = $this->Slips->get($id, [
            'contain' => ['Warehouses', 'Users', 'Sliptypes', 'Companies', 'Slipproducts'],
        ]);

        $this->set('slip', $slip);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function addsortie()
    {

        $slip = $this->Slips->newEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            if (isset($data['zoneusers']) && $data['zoneusers']) {
                if ($data['user_id']) {
                    $userid = $data['user_id'];

                    $codeexit = $this->Slips->Companies->Companycodes->find('all')->where(['controleur' => 'Slips5', 'company_id' => $this->Auth->user('company_id')])->last();

                    $pofsale = $this->Slips->Users->Pofsusers->find('all')->contain(['Pofsales'])->where(['Pofsusers.user_id' => $userid]);

                    $warehousedepot = $this->Slips->Users->Pofsusers->Pofsales->Warehouses->get($this->Auth->user('defaultwh'), [
                        'contain' => [
                            'Subwarehouses' => function ($q) {
                                return $q->where(['Subwarehouses.whnature_id' => 1, 'Subwarehouses.whtype_id' => 2]);
                            }
                        ]
                    ]);
                    $warehouseuser = $this->Slips->Users->Pofsusers->Pofsales->Warehouses->get($pofsale->toArray()[0]['pofsale']['warehouse_id'], [
                        'contain' => [
                            'Subwarehouses' => function ($q) {
                                return $q->where(['Subwarehouses.whnature_id' => 1, 'Subwarehouses.whtype_id' => 2]);
                            }
                        ]
                    ]);

                    $dataslip = ['user_id' => $userid, 'uservalidate' => $this->Auth->user('id'), 'company_id' => $this->Auth->user('company_id'), 'code' => $codeexit->prefixe . '' . ($codeexit->compteur + 1), 'sliptype_id' => 5, 'warehouse_id' => $warehousedepot->id, 'warehoused' => $warehouseuser->id, 'whnature_id' => 1, 'statut' => 2];

                    $slip = $this->Slips->patchEntity($slip, $dataslip);

                    if ($this->Slips->save($slip)) {
                        $products = [];
                        foreach ($data['zoneusers'] as $zoneuser) {
                            $products = [];
                            //récuperer les bon de livraison en attente
                            $orders = $this->Slips->Orders->find('all')->contain(['Orderpacks.Orderpackproducts', 'Shippings'])->where(['Orders.statut' => 1, 'Orders.user_id' => $zoneuser, 'Orders.shipping_id IS NOT ' => NULL]);
                            $orderdatas = [];
                            //si y a des livraison en attente
                            if ($orders->toArray()) {
                                foreach ($orders as $order) {
                                    $orderupdate = $this->Slips->Orders->get($order->id, ['contain' => ['Shippings']]);

                                    $orderdata['id'] = $order->id;
                                    $orderdata['statut'] = 5;

                                    $orderdata['shipping']['id'] = $order->shipping->id;
                                    $orderdata['shipping']['slip_id'] = $slip->id;
                                    $orderdata['shipping']['statut'] = 3;

                                    $orderupdate = $this->Slips->Orders->patchEntity($orderupdate, $orderdata, ['associated' => ['Shippings']]);
                                    $this->Slips->Orders->save($orderupdate);

                                    foreach ($order->orderpacks as $orderpack) {
                                        $orderpackupdate = $this->Slips->Orders->Orderpacks->get($orderpack->id);
                                        $orderpackupdate->statut = 5;
                                        $this->Slips->Orders->Orderpacks->save($orderpackupdate);

                                        foreach ($orderpack->orderpackproducts as $orderpackproduct) {
                                            $orderproductupdate = $this->Slips->Orders->Orderpacks->Orderpackproducts->get($orderpackproduct->id);
                                            $orderproductupdate->statut = 5;
                                            $this->Slips->Orders->Orderpacks->Orderpackproducts->save($orderproductupdate);

                                            if (isset($products[$orderpackproduct->product_id])) {
                                                $products[$orderpackproduct->product_id] = [
                                                    'product_id' => $orderpackproduct->product_id,
                                                    'quantity' => $orderpackproduct->quantity + $products[$orderpackproduct->product_id]['quantity']
                                                ];
                                            } else {
                                                $products[$orderpackproduct->product_id] = [
                                                    'product_id' => $orderpackproduct->product_id,
                                                    'quantity' => $orderpackproduct->quantity
                                                ];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $this->loadModel('Whproducts');
                        $this->loadModel('StockMovements');
                        foreach ($products as $product) {
                            $whproductd = $this->Whproducts->find('all')
                                ->where(['item_id' => $product['product_id'], 'item_type' => 'Product', 'warehouse_id' => $warehousedepot->subwarehouses[0]->id])
                                ->last();
                            $whproductliv = $this->Whproducts->find('all')
                                ->where(['item_id' => $product['product_id'], 'item_type' => 'Product', 'warehouse_id' => $warehouseuser->subwarehouses[0]->id])
                                ->last();

                            if ($whproductd) {
                                $whproductd->quantity -= $product['quantity'];
                                $this->Whproducts->save($whproductd);

                                $movSource = $this->StockMovements->newEntity([
                                    'item_id' => $product['product_id'],
                                    'item_type' => 'Product',
                                    'warehouse_id' => $warehousedepot->subwarehouses[0]->id,
                                    'quantity_change' => -$product['quantity'],
                                    'balance_after_movement' => $whproductd->quantity,
                                    'movement_type' => 'exit_slip_creation_source',
                                    'user_id' => $this->Auth->user('id'),
                                    'company_id' => $this->Auth->user('company_id'),
                                    'notes' => 'Stock transfer exit slip creation source (Slip ID: ' . $slip->id . ')',
                                ]);
                                $this->StockMovements->save($movSource);
                            }
                            if ($whproductliv) {
                                $whproductliv->quantity += $product['quantity'];
                                $this->Whproducts->save($whproductliv);

                                $movDest = $this->StockMovements->newEntity([
                                    'item_id' => $product['product_id'],
                                    'item_type' => 'Product',
                                    'warehouse_id' => $warehouseuser->subwarehouses[0]->id,
                                    'quantity_change' => $product['quantity'],
                                    'balance_after_movement' => $whproductliv->quantity,
                                    'movement_type' => 'exit_slip_creation_dest',
                                    'user_id' => $this->Auth->user('id'),
                                    'company_id' => $this->Auth->user('company_id'),
                                    'notes' => 'Stock transfer exit slip creation destination (Slip ID: ' . $slip->id . ')',
                                ]);
                                $this->StockMovements->save($movDest);
                            }
                        }
                        $codeexit->compteur += 1;
                        $this->Slips->Companies->Companycodes->save($codeexit);


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

        $orders = $this->Slips->Shippings->Orders->find('all')->contain(['Customers.Zones'])->where(['Orders.statut' => 1, 'Orders.ordertype_id' => 1]);

        //récupérer selement les id des zones pour chercher les livreurs de ces zones

        $qzones = [];

        if ($orders->toArray()) {

            foreach ($orders as $key => $order) {

                $qzones['OR'][$order->customer->zone->zone_id] = ['Zoneusers.zone_id' => $order->customer->zone->zone_id];

            }

            //rechercher les livreurs qui ont les mêmes zones des commande en attente

            $livreurs = $this->Slips->Users->find('all')->contain([
                'Zoneusers' => function ($q) use ($qzones) {
                    return $q->where([$qzones]);
                }
            ])->where(['role_id' => 6, 'company_id' => $this->Auth->user('company_id')]);



            $users = [];

            foreach ($livreurs as $livreur) {

                if ($livreur->zoneusers) {

                    $users[$livreur->id] = $livreur->firstname . ' ' . $livreur->lastname;

                }

            }

        } else {

            $users = null;

        }

        $this->set(compact('slip', 'users'));

    }
    public function addcharge()
    {
        $slip = $this->Slips->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();
            // Validate required fields
            if (empty($datas['whnature_id'])) {
                $this->Flash->error(__('Veuillez sélectionner la nature de transfert.'));
                return $this->redirect(['action' => 'addcharge']);
            }

            if (empty($datas['warehoused'])) {
                $this->Flash->error(__('Veuillez sélectionner l\'entrepôt de réception.'));
                return $this->redirect(['action' => 'addcharge']);
            }
            foreach ($this->request->getData('slipproducts') as $key => $orderpck) {
                if (isset($orderpck[0]) && isset($orderpck[1])) {
                    if (intVal($orderpck[0]['quantity']) == 0 && intVal($orderpck[1]['quantity']) == 0) {
                        unset($datas['slipproducts'][$key]);
                    }
                } elseif (isset($orderpck[0]) && !isset($orderpck[1])) {
                    if (intVal($orderpck[0]['quantity']) == 0) {
                        unset($datas['slipproducts'][$key]);
                    }
                } elseif (!isset($orderpck[0]) && isset($orderpck[1])) {
                    if (intVal($orderpck[1]['quantity']) == 0) {
                        unset($datas['slipproducts'][$key]);
                    }
                }
            }
            // Process slip products
            $hasProducts = false;
            foreach ($datas['slipproducts'] as $key => $orderproduct) {
                $hasProducts = true;
                $datas['slipproducts'][$key]['user_id'] = $this->Auth->user('id');
                $datas['slipproducts'][$key]['company_id'] = $this->Auth->user('company_id');
                $product = $this->Slips->slipproducts->Packs->get($orderproduct['item_id'], ['contain' => ['Packunites']]);
                $packunite = $this->Slips->slipproducts->Packs->Packunites->get($orderproduct['unity_id'], ['contain' => ['Packs']]);
                $datas['slipproducts'][$key]['item_type'] = "Pack";
                if (isset($orderproduct[0]) && isset($orderproduct[1])) {
                    $datas['slipproducts'][$key]['quantity'] = ($orderproduct[0]['quantity'] * $packunite->quantity) + $orderproduct[1]['quantity'];
                    $datas['slipproducts'][$key]['price'] = $orderproduct['price'] / $packunite->quantity;
                    unset($datas['slipproducts'][$key][0]);
                    unset($datas['slipproducts'][$key][1]);
                } elseif (isset($orderproduct[0]) && !isset($orderproduct[1])) {
                    $datas['slipproducts'][$key]['quantity'] = ($orderproduct[0]['quantity'] * $packunite->quantity);
                    $datas['slipproducts'][$key]['price'] = $orderproduct['price'] / $packunite->quantity;
                    unset($datas['slipproducts'][$key][0]);
                } else {
                    $datas['slipproducts'][$key]['quantity'] = $orderproduct[1]['quantity'];
                    $datas['slipproducts'][$key]['price'] = $orderproduct['price'] / $packunite->quantity;
                    unset($datas['slipproducts'][$key][1]);
                }

            }
            // Validate at least one product selected
            if (!$hasProducts) {
                $this->Flash->error(__('Veuillez sélectionner au moins un article avec une quantité.'));
                return $this->redirect(['action' => 'addcharge']);
            }

            $slip = $this->Slips->patchEntity($slip, $datas, ['associated' => ['Slipproducts']]);
            $slip->user_id = $this->Auth->user('id');
            $slip->company_id = $this->Auth->user('company_id');
            $slip->sliptype_id = 1;
            $slip->statut = 2;
            $slip->warehouse_id = $this->Auth->user('defaultwh');

            $code = $this->Slips->Companies->Companycodes->find('all')->where(['controleur' => 'Slips1', 'company_id' => $this->Auth->user('company_id')])->last();
            $slip->code = $code->prefixe . ($code->compteur + 1);

            if ($this->Slips->save($slip)) {
                $code->compteur = $code->compteur + 1;
                $this->Slips->Companies->Companycodes->save($code);
                $this->Flash->success(__('Le bon de charge a été enregistré avec succès.'));

                return $this->redirect(['action' => 'index', $slip->sliptype_id]);
            }
            $this->Flash->error(__('Le bon n\'a pas pu être enregistré. Veuillez réessayer.'));
        }
        $warehouseid = $this->Auth->user('defaultwh');

        $users = $this->Slips->Users->find('all')->contain([
            'Pofsusers.Pofsales.Warehouses' => function ($q) use ($warehouseid) {
                return $q->where(['Warehouses.warehouse_id' => $warehouseid]);
            }
        ])->where(['Users.role_id' => 3]);
        $warehoused = [];
        foreach ($users as $key => $user) {
            if ($user->pofsusers) {
                if ($user->pofsusers[0]->pofsale) {
                    $warehoused[$user->pofsusers[0]->pofsale->warehouse_id] = $user->pofsusers[0]->pofsale->title . ' (' . $user->firstname . ' ' . $user->lastname . ')';
                }
            }
        }

        $whnaturesdsipo = $this->Slips->Warehouses->find('all')->contain([
            'Whnatures',
            'Whproducts' => function ($q) {
                return $q->where(['Whproducts.quantity >' => 0]);
            }
        ])->where(['Warehouses.warehouse_id' => $this->Auth->user('defaultwh'), 'Warehouses.whtype_id' => 2]);
        $whnatures = [];
        foreach ($whnaturesdsipo as $key => $whnature) {
            $whnatures[$whnature->whnature->id] = $whnature->whnature->title;
        }
        $categories = $this->Slips->Slipproducts->Products->Categories->find('all')->where(['company_id' => $this->Auth->user('company_id')]);
        $packselects = [];
        foreach ($categories as $key => $category) {
            $packs = $this->Slips->Slipproducts->Packs->find('all')->contain(['MeasurementUnits', 'Packunites.Unites.Parentunites'])->where(['Packs.category_id' => $category->id, ['OR' => [['Packs.statut' => 1], ['Packs.statut' => 2], ['Packs.statut' => 3]]]]);
            $packselect = [];
            foreach ($packs as $key => $pack) {
                foreach ($pack->packunites as $key2 => $packunite) {
                    $packselect[$packunite->id]['id'] = $packunite->id;
                    $packselect[$packunite->id]['pack_id'] = $pack->id;
                    $packselect[$packunite->id]['title'] = $pack->title . ' (' . $packunite->unite->parentunite->abrev . ')';
                    $packselect[$packunite->id]['qtepercs'] = $packunite->quantity * $pack->measurement_quantity;
                    $packselect[$packunite->id]['carsac'] = $packunite->unite->abrev;
                    $packselect[$packunite->id]['piecekg'] = $pack->measurement_unit->abbreviation;
                    $packselect[$packunite->id][1]['price'] = 0;
                    $packselect[$packunite->id][0]['price'] = 0;
                }
            }

            $packselects[] = ['category' => $category->title, 'packs' => $packselect];
        }
        $this->set(compact('slip', 'whnatures', 'warehoused', 'packselects'));
    }

    public function chargeNew()
    {
        $slip = $this->Slips->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();
            debug($datas);
            exit;
            // Validate required fields
            if (empty($datas['whnature_id'])) {
                $this->Flash->error(__('Veuillez sélectionner la nature de transfert.'));
                return $this->redirect(['action' => 'chargeNew']);
            }

            if (empty($datas['warehoused'])) {
                $this->Flash->error(__('Veuillez sélectionner l\'entrepôt de réception.'));
                return $this->redirect(['action' => 'chargeNew']);
            }

            // Process slip products
            $hasProducts = false;
            foreach ($this->request->getData('slipproducts') as $key => $orderpck) {
                $packageQty = isset($orderpck[0]['quantity']) ? intval($orderpck[0]['quantity']) : 0;
                $unitQty = isset($orderpck[1]['quantity']) ? intval($orderpck[1]['quantity']) : 0;

                if ($packageQty == 0 && $unitQty == 0) {
                    unset($datas['slipproducts'][$key]);
                } else {
                    $hasProducts = true;
                    $datas['slipproducts'][$key]['company_id'] = $this->Auth->user('company_id');
                    $datas['slipproducts'][$key]['user_id'] = $this->Auth->user('id');
                    $datas['slipproducts'][$key]['price'] = 0;
                    $datas['slipproducts'][$key]['statut'] = 2;
                    $datas['slipproducts'][$key]['whnature_id'] = $datas['whnature_id'];
                    $datas['slipproducts'][$key]['quantity'] = $packageQty * $orderpck['qtepercs'] + $unitQty;

                    // Remove temporary fields
                    unset($datas['slipproducts'][$key][0]);
                    unset($datas['slipproducts'][$key][1]);
                    unset($datas['slipproducts'][$key]['qtepercs']);
                }
            }

            // Validate at least one product selected
            if (!$hasProducts) {
                $this->Flash->error(__('Veuillez sélectionner au moins un article avec une quantité.'));
                return $this->redirect(['action' => 'chargeNew']);
            }

            $slip = $this->Slips->patchEntity($slip, $datas, ['associated' => ['Slipproducts']]);
            $slip->user_id = $this->Auth->user('id');
            $slip->company_id = $this->Auth->user('company_id');
            $slip->sliptype_id = 1;
            $slip->statut = 2;
            $slip->warehouse_id = $this->Auth->user('defaultwh');

            $code = $this->Slips->Companies->Companycodes->find('all')->where(['controleur' => 'Slips1', 'company_id' => $this->Auth->user('company_id')])->last();
            $slip->code = $code->prefixe . ($code->compteur + 1);

            if ($this->Slips->save($slip)) {
                $code->compteur = $code->compteur + 1;
                $this->Slips->Companies->Companycodes->save($code);
                $this->Flash->success(__('Le bon de charge a été enregistré avec succès.'));

                return $this->redirect(['action' => 'index', $slip->sliptype_id]);
            }
            $this->Flash->error(__('Le bon n\'a pas pu être enregistré. Veuillez réessayer.'));
        }

        $warehouseid = $this->Auth->user('defaultwh');

        $users = $this->Slips->Users->find('all')->contain([
            'Pofsusers.Pofsales.Warehouses' => function ($q) use ($warehouseid) {
                return $q->where(['Warehouses.warehouse_id' => $warehouseid]);
            }
        ])->where(['Users.role_id' => 3]);
        $warehoused = [];
        foreach ($users as $key => $user) {
            if ($user->pofsusers) {
                if ($user->pofsusers[0]->pofsale) {
                    $warehoused[$user->pofsusers[0]->pofsale->warehouse_id] = $user->pofsusers[0]->pofsale->title . ' (' . $user->firstname . ' ' . $user->lastname . ')';
                }
            }
        }

        $whnaturesdsipo = $this->Slips->Warehouses->find('all')->contain([
            'Whnatures',
            'Whproducts' => function ($q) {
                return $q->where(['Whproducts.quantity >' => 0]);
            }
        ])->where(['Warehouses.warehouse_id' => $this->Auth->user('defaultwh'), 'Warehouses.whtype_id' => 2]);
        $whnatures = [];
        foreach ($whnaturesdsipo as $key => $whnature) {
            $whnatures[$whnature->whnature->id] = $whnature->whnature->title;
        }
        $this->set(compact('slip', 'whnatures', 'warehoused'));
    }

    public function adddecharge()
    {
        $slip = $this->Slips->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();
            if (!isset($datas['warehoused']) || $datas['warehoused'] == NULL) {
                $this->Flash->error(__('Merci de sélectionner le livreur.'));
                return $this->redirect(['action' => 'adddecharge']);
            }
            $userid = $this->Auth->user('id');
            if ($datas['user_id']) {
                $userid = $datas['user_id'];
            }
            foreach ($this->request->getData('slipproducts') as $key => $orderpck) {
                if ($orderpck[0]['quantity'] == 0 && $orderpck[1]['quantity'] == 0) {
                    unset($datas['slipproducts'][$key]);
                } else {
                    $datas['slipproducts'][$key]['company_id'] = $this->Auth->user('company_id');
                    $datas['slipproducts'][$key]['user_id'] = $userid;
                    $datas['slipproducts'][$key]['price'] = $orderpck[1]['price'];
                    $datas['slipproducts'][$key]['statut'] = 2;
                    $datas['slipproducts'][$key]['quantity'] = $orderpck[0]['quantity'] * $orderpck['qtepercs'] + $orderpck[1]['quantity'];
                }

            }
            if (!$datas['slipproducts']) {
                $this->Flash->error(__('Merci de sélectionner les articles avant de valider le bon de décharge.'));
                return $this->redirect(['action' => 'adddecharge']);
            }

            $slip = $this->Slips->patchEntity($slip, $datas, ['associated' => ['Slipproducts']]);

            $slip->company_id = $this->Auth->user('company_id');
            $slip->sliptype_id = 2;
            $slip->statut = 2;
            $slip->user_id = $userid;
            $slip->warehouse_id = $slip->warehoused;
            $slip->whnature_id = 1;
            $slip->warehoused = $this->Slips->Warehouses->get($slip->warehoused)->warehouse_id;
            $code = $this->Slips->Companies->Companycodes->find('all')->where(['controleur' => 'Slips2', 'company_id' => $this->Auth->user('company_id')])->last();
            $slip->code = $code->prefixe . ($code->compteur + 1);
            //$users=$this->Slips->Warehouses->get($slip->warehouse_id,['contain'=>['Pofsales.Pofsusers.Users'=>function($q){return $q->where(['Users.role_id'=>6]);}]]);

            if ($this->Slips->save($slip)) {
                $code->compteur = $code->compteur + 1;
                $this->Slips->Companies->Companycodes->save($code);
                $this->Flash->success(__('Le bon a été ajouté.'));
                return $this->redirect(['action' => 'index', $slip->sliptype_id]);
            }
            $this->Flash->error(__('Le bon n\'a pas pu être ajouté. Veuillez réessayer.'));
        }
        $whnatures = $this->Slips->Whnatures->find('list');
        $warehouses = $this->Slips->Warehouses->find('all')->contain([
            'Pofsales.Pofsusers.Users.Roles',
            'Subwarehouses' => function ($q) {
                return $q->where(['Subwarehouses.whnature_id' => 1]);
            },
            'Subwarehouses.Whproducts' => function ($q) {
                return $q->where(['Whproducts.quantity >' => 0]);
            }
        ])->where(['Warehouses.warehouse_id' => $this->Auth->user('defaultwh'), ['OR' => [['Warehouses.whtype_id' => 3], ['Warehouses.whtype_id' => 4]]]]);

        $warehoused = [];
        foreach ($warehouses as $key => $warehouse) {
            foreach ($warehouse->subwarehouses as $key1 => $subwarehouse) {
                if ($subwarehouse->whproducts) {
                    $warehoused[$warehouse->id] = $warehouse->pofsales[0]->pofsusers[0]->user->firstname . ' ' . $warehouse->pofsales[0]->pofsusers[0]->user->lastname . ' (' . $warehouse->pofsales[0]->pofsusers[0]->user->role->title . ')';
                }
            }
        }

        $this->set(compact('slip', 'whnatures', 'warehoused'));
    }
    public function adddeplace()
    {
        $slip = $this->Slips->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();
            foreach ($this->request->getData('slipproducts') as $key => $orderpck) {
                if ($orderpck[0]['quantity'] == 0 && $orderpck[1]['quantity'] == 0) {
                    unset($datas['slipproducts'][$key]);
                } else {
                    $datas['slipproducts'][$key]['company_id'] = $this->Auth->user('company_id');
                    $datas['slipproducts'][$key]['user_id'] = $this->Auth->user('id');
                    $datas['slipproducts'][$key]['price'] = 0;
                    $datas['slipproducts'][$key]['statut'] = 2;
                    $datas['slipproducts'][$key]['quantity'] = $orderpck[0]['quantity'] * $orderpck['qtepercs'] + $orderpck[1]['quantity'];
                }
            }
            $slip = $this->Slips->patchEntity($slip, $datas, ['associated' => ['Slipproducts']]);
            $slip->user_id = $this->Auth->user('id');
            $slip->company_id = $this->Auth->user('company_id');
            $slip->sliptype_id = 3;
            $slip->statut = 2;
            $slip->warehouse_id = $this->Auth->user('defaultwh');
            $slip->whnatured = $slip->warehoused;
            $slip->warehoused = NULL;

            $code = $this->Slips->Companies->Companycodes->find('all')->where(['controleur' => 'Slips3', 'company_id' => $this->Auth->user('company_id')])->last();
            $slip->code = $code->prefixe . ($code->compteur + 1);

            if ($this->Slips->save($slip)) {
                $code->compteur = $code->compteur + 1;
                $this->Slips->Companies->Companycodes->save($code);
                $this->Flash->success(__('Le bon a été ajouté.'));

                return $this->redirect(['action' => 'index', $slip->sliptype_id]);
            }

            $this->Flash->error(__('Le bon n\'a pas pu être ajouté. Veuillez réessayer.'));
        }
        $sliptype = $this->Slips->Sliptypes->get(3);
        $warehouse_id = $this->Auth->user('defaultwh');
        $warehouses = $this->Slips->Warehouses->find('list')->where(['whtype_id' => 1, 'company_id' => $this->Auth->user('company_id')]);
        $whnaturdispos = $this->Slips->Whnatures->find('all')->contain([
            'Warehouses' => function ($q) use ($warehouse_id) {
                return $q->where(['Warehouses.warehouse_id' => $warehouse_id, 'Warehouses.whtype_id' => 2]);
            },
            'Warehouses.Whproducts' => function ($q) {
                return $q->where(['Whproducts.quantity >' => 0]);
            }
        ]);
        $whnatures = [];
        foreach ($whnaturdispos as $key => $whnature) {
            foreach ($whnature->warehouses as $key1 => $warehouse) {
                if ($warehouse->whproducts) {
                    $whnatures[$whnature->id] = $whnature->title;
                }
            }
        }
        $this->set(compact('slip', 'whnatures', 'warehouses', 'sliptype'));
    }
    public function addtransfert()
    {
        $slip = $this->Slips->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();
            foreach ($this->request->getData('slipproducts') as $key => $orderpck) {
                if ($orderpck[0]['quantity'] == 0 && $orderpck[1]['quantity'] == 0) {
                    unset($datas['slipproducts'][$key]);
                } else {
                    $datas['slipproducts'][$key]['company_id'] = $this->Auth->user('company_id');
                    $datas['slipproducts'][$key]['user_id'] = $this->Auth->user('id');
                    $datas['slipproducts'][$key]['price'] = 0;
                    $datas['slipproducts'][$key]['statut'] = 2;
                    $datas['slipproducts'][$key]['quantity'] = $orderpck[0]['quantity'] * $orderpck['qtepercs'] + $orderpck[1]['quantity'];
                }
            }
            $slip = $this->Slips->patchEntity($slip, $datas, ['associated' => ['Slipproducts']]);
            $slip->user_id = $this->Auth->user('id');
            $slip->company_id = $this->Auth->user('company_id');
            $slip->statut = 2;
            $slip->sliptype_id = 4;

            $code = $this->Slips->Companies->Companycodes->find('all')->where(['controleur' => 'Slips4', 'company_id' => $this->Auth->user('company_id')])->last();
            $slip->code = $code->prefixe . ($code->compteur + 1);

            if ($this->Slips->save($slip)) {
                $code->compteur = $code->compteur + 1;
                $this->Slips->Companies->Companycodes->save($code);
                $this->Flash->success(__('Le bon a été ajouté.'));

                return $this->redirect(['action' => 'index', $slip->sliptype_id]);
            }
            $this->Flash->error(__('Le bon n\'a pas pu être ajouté. Veuillez réessayer.'));
        }
        $warehousedispos = $this->Slips->Warehouses->find('all')->contain([
            'Subwarehouses' => function ($q) {
                return $q->where(['Subwarehouses.whtype_id' => 2]);
            },
            'Subwarehouses.Whproducts' => function ($q) {
                return $q->where(['Whproducts.quantity >' => 0]);
            }
        ])->where(['Warehouses.warehouse_id IS ' => NULL]);

        $warehouses = [];
        foreach ($warehousedispos as $key => $warehouse) {
            foreach ($warehouse->subwarehouses as $key1 => $subwarehouse) {
                if ($subwarehouse->whproducts) {
                    $warehouses[$warehouse->id] = $warehouse->title;
                }
            }
        }
        $this->set(compact('slip', 'warehouses'));
    }

    public function add($sliptypeid = null)
    {
        $datas = $this->request->getData();
        if ($sliptypeid == 1) {
            return $this->redirect(['action' => 'addcharge']);
        } elseif ($sliptypeid == 2) {
            return $this->redirect(['action' => 'adddecharge']);
        } elseif ($sliptypeid == 3) {
            return $this->redirect(['action' => 'adddeplace']);
        } elseif ($sliptypeid == 4) {
            return $this->redirect(['action' => 'addtransfert']);
        } elseif ($sliptypeid == 5) {
            return $this->redirect(['action' => 'addfabrication']);
        } elseif ($sliptypeid == 6) {
            return $this->redirect(['action' => 'addprelevement']);
        } else {
            $this->Flash->error(__('vous n\'avez pas le droit d\'accéder.'));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function addfabrication()
    {
        $slip = $this->Slips->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();

            foreach ($this->request->getData('slipproducts') as $key => $orderpck) {
                if (isset($orderpck[0]) && isset($orderpck[1])) {
                    if (intVal($orderpck[0]['quantity']) == 0 && intVal($orderpck[1]['quantity']) == 0) {
                        unset($datas['slipproducts'][$key]);
                    }
                } elseif (isset($orderpck[0]) && !isset($orderpck[1])) {
                    if (intVal($orderpck[0]['quantity']) == 0) {
                        unset($datas['slipproducts'][$key]);
                    }
                } elseif (!isset($orderpck[0]) && isset($orderpck[1])) {
                    if (intVal($orderpck[1]['quantity']) == 0) {
                        unset($datas['slipproducts'][$key]);
                    }
                }
            }
            if (isset($datas['slipproducts'])) {
                foreach ($datas['slipproducts'] as $key => $orderproduct) {
                    $datas['slipproducts'][$key]['user_id'] = $this->Auth->user('id');
                    $datas['slipproducts'][$key]['company_id'] = $this->Auth->user('company_id');
                    $product = $this->Slips->slipproducts->Packs->get($orderproduct['item_id'], ['contain' => ['Packunites']]);
                    $packunite = $this->Slips->slipproducts->Packs->Packunites->get($orderproduct['unity_id'], ['contain' => ['Packs']]);
                    $datas['slipproducts'][$key]['item_type'] = "Pack";
                    if (isset($orderproduct[0]) && isset($orderproduct[1])) {
                        $datas['slipproducts'][$key]['quantity'] = ($orderproduct[0]['quantity'] * $packunite->quantity) + $orderproduct[1]['quantity'];
                        $datas['slipproducts'][$key]['price'] = $orderproduct['price'] / $packunite->quantity;
                        unset($datas['slipproducts'][$key][0]);
                        unset($datas['slipproducts'][$key][1]);
                    } elseif (isset($orderproduct[0]) && !isset($orderproduct[1])) {
                        $datas['slipproducts'][$key]['quantity'] = ($orderproduct[0]['quantity'] * $packunite->quantity);
                        $datas['slipproducts'][$key]['price'] = $orderproduct['price'] / $packunite->quantity;
                        unset($datas['slipproducts'][$key][0]);
                    } else {
                        $datas['slipproducts'][$key]['quantity'] = $orderproduct[1]['quantity'];
                        $datas['slipproducts'][$key]['price'] = $orderproduct['price'] / $packunite->quantity;
                        unset($datas['slipproducts'][$key][1]);
                    }

                }

                $slip = $this->Slips->patchEntity($slip, $datas, ['associated' => ['slipproducts']]);

                $code = $this->Slips->Companies->Companycodes->find('all')->where(['controleur' => 'Slips5', 'company_id' => $this->Auth->user('company_id')])->last();
                $slip->code = $code->prefixe . ($code->compteur + 1);


                $slip->user_id = $this->Auth->user('id');
                $slip->company_id = $this->Auth->user('company_id');
                $slip->whnature_id = 2;
                $slip->warehouse_id = 1;
                $slip->warehoused = 1;
                $slip->sliptype_id = 5;

                if ($this->Slips->save($slip)) {
                    $code->compteur = $code->compteur + 1;
                    $this->Slips->Companies->Companycodes->save($code);
                    $this->Flash->success(__('Le bon de Prélévement a été enregistré.'));
                    return $this->redirect(['action' => 'index', $slip->sliptype_id]);
                }
            } else {
                $this->Flash->error(__('Merci de charger les produits. Veuillez réessayer.'));
            }

        }
        $categories = $this->Slips->Slipproducts->Products->Categories->find('all')->where(['company_id' => $this->Auth->user('company_id')]);
        $packselects = [];
        foreach ($categories as $key => $category) {
            $packs = $this->Slips->Slipproducts->Packs->find('all')->contain(['MeasurementUnits', 'Packunites.Unites.Parentunites'])->where(['Packs.category_id' => $category->id, ['OR' => [['Packs.statut' => 1], ['Packs.statut' => 2], ['Packs.statut' => 3]]]]);
            $packselect = [];
            foreach ($packs as $key => $pack) {
                foreach ($pack->packunites as $key2 => $packunite) {
                    $packselect[$packunite->id]['id'] = $packunite->id;
                    $packselect[$packunite->id]['pack_id'] = $pack->id;
                    $packselect[$packunite->id]['title'] = $pack->title . ' (' . $packunite->unite->parentunite->abrev . ')';
                    $packselect[$packunite->id]['qtepercs'] = $packunite->quantity * $pack->measurement_quantity;
                    $packselect[$packunite->id]['carsac'] = $packunite->unite->abrev;
                    $packselect[$packunite->id]['piecekg'] = $pack->measurement_unit->abbreviation;
                    $packselect[$packunite->id][1]['price'] = 0;
                    $packselect[$packunite->id][0]['price'] = 0;
                }
            }

            $packselects[] = ['category' => $category->title, 'packs' => $packselect];
        }
        $warehouses = $this->Slips->Warehouses->find('list')->where(['whtype_id' => 1, 'company_id' => $this->Auth->user('company_id')]);
        $this->set(compact('slip', 'categories', 'warehouses', 'packselects'));
    }
    public function addprelevement()
    {
        $slip = $this->Slips->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();

            foreach ($this->request->getData('slipproducts') as $key => $orderpck) {
                if (isset($orderpck[0]) && isset($orderpck[1])) {
                    if (intVal($orderpck[0]['quantity']) == 0 && intVal($orderpck[1]['quantity']) == 0) {
                        unset($datas['slipproducts'][$key]);
                    }
                } elseif (isset($orderpck[0]) && !isset($orderpck[1])) {
                    if (intVal($orderpck[0]['quantity']) == 0) {
                        unset($datas['slipproducts'][$key]);
                    }
                } elseif (!isset($orderpck[0]) && isset($orderpck[1])) {
                    if (intVal($orderpck[1]['quantity']) == 0) {
                        unset($datas['slipproducts'][$key]);
                    }
                }
            }
            if (isset($datas['slipproducts'])) {
                foreach ($datas['slipproducts'] as $key => $orderproduct) {
                    $datas['slipproducts'][$key]['user_id'] = $this->Auth->user('id');
                    $datas['slipproducts'][$key]['company_id'] = $this->Auth->user('company_id');
                    $product = $this->Slips->slipproducts->Products->get($orderproduct['item_id'], ['contain' => ['Productunites']]);
                    $productunite = $this->Slips->slipproducts->Products->Productunites->get($orderproduct['unity_id'], ['contain' => ['Products']]);
                    $datas['slipproducts'][$key]['item_type'] = "Product";
                    if (isset($orderproduct[0]) && isset($orderproduct[1])) {
                        $datas['slipproducts'][$key]['quantity'] = ($orderproduct[0]['quantity'] * $productunite->quantity) + $orderproduct[1]['quantity'];
                        $datas['slipproducts'][$key]['price'] = $product->sellingprice;
                        unset($datas['slipproducts'][$key][0]);
                        unset($datas['slipproducts'][$key][1]);
                    } elseif (isset($orderproduct[0]) && !isset($orderproduct[1])) {
                        $datas['slipproducts'][$key]['quantity'] = ($orderproduct[0]['quantity'] * $productunite->quantity);
                        $datas['slipproducts'][$key]['price'] = $product->sellingprice;
                        unset($datas['slipproducts'][$key][0]);
                    } else {
                        $datas['slipproducts'][$key]['quantity'] = $orderproduct[1]['quantity'];
                        $datas['slipproducts'][$key]['price'] = $product->sellingprice;
                        unset($datas['slipproducts'][$key][1]);
                    }

                }
                $slip = $this->Slips->patchEntity($slip, $datas, ['associated' => ['slipproducts']]);

                $code = $this->Slips->Companies->Companycodes->find('all')->where(['controleur' => 'Slips6', 'company_id' => $this->Auth->user('company_id')])->last();
                $slip->code = $code->prefixe . ($code->compteur + 1);


                $slip->user_id = $this->Auth->user('id');
                $slip->company_id = $this->Auth->user('company_id');
                $slip->whnature_id = 1;
                $slip->warehouse_id = 1;
                $slip->statut = 2;
                $slip->warehoused = 1;
                $slip->sliptype_id = 6;

                if ($this->Slips->save($slip)) {
                    $code->compteur = $code->compteur + 1;
                    $this->Slips->Companies->Companycodes->save($code);
                    $this->Flash->success(__('Le bon de Prélévement a été enregistré.'));
                    return $this->redirect(['action' => 'index', $slip->sliptype_id]);
                }
            } else {
                $this->Flash->error(__('Merci de charger les produits. Veuillez réessayer.'));
            }

        }
        $categories = $this->Slips->Slipproducts->Products->Categories->find('all')->where(['company_id' => $this->Auth->user('company_id')]);
        $productselects = [];
        foreach ($categories as $key => $category) {
            $products = $this->Slips->Slipproducts->Products->find('all')->contain(['MeasurementUnits', 'Productunites.Unites.Parentunites'])->where(['Products.category_id' => $category->id, ['OR' => [['Products.statut' => 1], ['Products.statut' => 2], ['Products.statut' => 3]]]]);
            $productselect = [];
            foreach ($products as $key => $product) {
                foreach ($product->productunites as $key2 => $productunite) {
                    $productselect[$productunite->id]['id'] = $productunite->id;
                    $productselect[$productunite->id]['product_id'] = $product->id;
                    $productselect[$productunite->id]['title'] = $product->title . ' (' . $productunite->unite->parentunite->abrev . ')';
                    $productselect[$productunite->id]['qtepercs'] = $productunite->quantity * $product->measurement_quantity;
                    $productselect[$productunite->id]['carsac'] = $productunite->unite->abrev;
                    $productselect[$productunite->id]['piecekg'] = $product->measurement_unit->abbreviation;
                    $productselect[$productunite->id][1]['price'] = 0;
                    $productselect[$productunite->id][0]['price'] = 0;
                }
            }

            $productselects[] = ['category' => $category->title, 'products' => $productselect];
        }
        $warehouses = $this->Slips->Warehouses->find('list')->where(['whtype_id' => 1, 'company_id' => $this->Auth->user('company_id')]);
        $this->set(compact('slip', 'categories', 'warehouses', 'productselects'));
    }
    public function warehoused($type)
    {
        //$this->request->allowMethod('ajax');
        $keyword = $this->request->getQuery('keyword');
        $whnatured = null;

        if ($type == 2) {
            $warehouses = $this->Slips->Warehouses->find('all')->contain([
                'Subwarehouses',
                'Subwarehouses' => function ($q) use ($keyword) {
                    return $q->where(['Subwarehouses.whnature_id' => $keyword]);
                },
                'Subwarehouses.Whproducts' => function ($q) {
                    return $q->where(['Whproducts.quantity >' => 0]);
                }
            ])->where(['Warehouses.warehouse_id' => $this->Auth->user('defaultwh'), ['OR' => [['Warehouses.whtype_id' => 3], ['Warehouses.whtype_id' => 4]]]]);


            $warehoused = [];
            foreach ($warehouses as $key => $warehouse) {
                foreach ($warehouse->subwarehouses as $key1 => $subwarehouse) {
                    if ($subwarehouse->whproducts) {
                        $warehoused[$warehouse->id] = $warehouse->title;
                    }
                }
            }
        } elseif ($type == 3) {
            $warehoused = $this->Slips->Warehouses->Whnatures->find('list')->where(['id !=' => $keyword]);

        } elseif ($type == 4) {
            $whnaturesdsipo = $this->Slips->Warehouses->find('all')->contain([
                'Whnatures',
                'Whproducts' => function ($q) {
                    return $q->where(['Whproducts.quantity >' => 0]);
                }
            ])->where(['Warehouses.warehouse_id' => $keyword, 'Warehouses.whtype_id' => 2]);
            $warehoused = $this->Slips->Warehouses->find('list')->where(['id !=' => $keyword, 'whnature_id' => 1, 'whtype_id' => 1]);

            $whnatured = [];
            foreach ($whnaturesdsipo as $key => $whnature) {
                if ($whnature->whproducts) {
                    $whnatured[$whnature->whnature->id] = $whnature->whnature->title;
                }
            }

        }

        $this->set(compact('warehoused', 'whnatured', 'type'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Slip id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null, $validate = null)
    {
        $slip = $this->Slips->get($id, [
            'contain' => ['Sliptypes', 'Slipproducts'],
        ]);
        if ($validate == 'validation') {

            if (count($slip->slipproducts) > 0) {
                //si un bon de chargement pour le vente indirect
                if ($slip->sliptype_id == 1 && !$slip->warehoused) {
                    $data = ['id' => $slip->id, 'statut' => 3];
                    foreach ($slip->slipproducts as $key => $slipproduct) {
                        $data['slipproducts'][$key] = ['id' => $slipproduct->id, 'statut' => 3];
                        $data['slipproducts'][$key]['product']['id'] = $slipproduct->product_id;
                    }
                    //si un bon de déchargement
                } else {
                    $data = ['id' => $slip->id, 'statut' => 2];
                    foreach ($slip->slipproducts as $key => $slipproduct) {
                        $data['slipproducts'][$key] = ['id' => $slipproduct->id, 'statut' => 2];
                        $data['slipproducts'][$key]['product']['id'] = $slipproduct->product_id;
                    }
                }

            } else {
                $this->Flash->error(__('Le bon ne contient pas d\'article à confirmé. Veuillez réessayer.'));
                return $this->redirect(['action' => 'edit', $slip->id]);

            }
            $slip = $this->Slips->patchEntity($slip, $data, ['associated' => ['Slipproducts']]);


            if ($this->Slips->save($slip)) {
                //si un bon de chargement pour le vente indirect
                /*if ($slip->sliptype_id==1 && !$slip->warehoused) {
                    $orders=$this->Slips->Users->Orders->find('all')->contain(['Orderpacks.Orderpackproducts'])->where(['Orders.user_id'=>$slip->user_id,'Orders.statut'=>2]);
                    foreach ($orders as $key => $order) {
                        $data=['id'=>$order->id,'statut'=>3];
                        $order->statut=3;
                        foreach ($order->orderpacks as $key1 => $orderpack) {
                            $data['orderpacks'][$key1]=['id'=>$orderpack->id,'statut'=>3];
                            foreach ($orderpack->orderpackproducts as $key2 => $orderpackproduct) {
                                $data['orderpacks'][$key1]['orderpackproducts'][$key2]=['id'=>$orderpackproduct->id,'statut'=>3];
                            }
                        }
                        $order=$this->Slips->Users->Orders->patchEntity($order,$data,['associated'=>['Orderpacks.Orderpackproducts']]);
                        $this->Slips->Users->Orders->save($order);
                    }  
                }*/
                $this->Flash->success(__('Le bon a été confirmé.'));
                return $this->redirect(['action' => 'index', $slip->sliptype_id]);
            }
            $this->Flash->error(__('Le bon n\'a pas pu être confirmé. Veuillez réessayer.'));

        }
        $this->set(compact('slip'));
    }
    public function validation($id)
    {
        $this->loadModel('Whproducts');
        $this->loadModel('StockMovements');
        //récupérer le bon pour les entrepots et les natures
        $slipdepart = $this->Slips->get($id);
        $request = 0;
        $warehouse = $this->Slips->Warehouses->find('all')->where(['warehouse_id' => $slipdepart->warehouse_id, 'whnature_id' => $slipdepart->whnature_id, 'whtype_id' => 2])->last();
        $warehoused = null;

        if ($slipdepart->warehoused) {
            $warehoused = $this->Slips->Warehouses->find('all')->where(['warehouse_id' => $slipdepart->warehoused, 'whnature_id' => $slipdepart->whnature_id, 'whtype_id' => 2])->last();
        } else {
            $warehoused = $this->Slips->Warehouses->find('all')->where(['warehouse_id' => $slipdepart->warehouse_id, 'whnature_id' => $slipdepart->whnatured, 'whtype_id' => 2])->last();
        }

        $request = $warehouse->id;
        $sliptype = $this->Slips->get($id)->sliptype_id;
        if ($sliptype == 6) {
            $slip = $this->Slips->get($id, [
                'contain' => [
                    'Sliptypes',
                    'Slipproducts.Products.Whproducts' => function ($q) use ($request) {
                        return $q->where(['Whproducts.warehouse_id' => $request]);
                    }
                ]
            ]);
        } else {
            $slip = $this->Slips->get($id, [
                'contain' => [
                    'Sliptypes',
                    'Slipproducts.Packs.MeasurementUnits',
                    'Slipproducts.Packs.Whproducts' => function ($q) use ($request) {
                        return $q->where(['Whproducts.warehouse_id' => $request]);
                    },
                ]
            ]);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $requestdatas = $this->request->getData();
            $data = ['id' => $slip->id, 'statut' => 3];
            $increment = 0;

            $whid = $this->Auth->user('defaultwh');

            foreach ($requestdatas['slipproducts'] as $key => $slipprd) {
                //récuperer le le produit commandé
                if ($slip->sliptype_id == 6) {
                    $slipproduct = $this->Slips->Slipproducts->get($slipprd['id']);
                } else {
                    $slipproduct = $this->Slips->Slipproducts->get($slipprd['id'], ['contain' => ['Packs.Prices']]);
                }

                // si le bon est un bon de retour récupérer la nature du produit retourné
                if ($slipproduct->whnature_id == 99) {
                    $data['slipproducts'][$increment]['id'] = $slipproduct->id;
                    $data['slipproducts'][$increment]['statut'] = 3;
                    $data['slipproducts'][$increment]['uservalidate'] = $this->Auth->user('id');
                    $increment++;

                } elseif ($slip->sliptype_id == 6) {

                    //récupérer le produit du stock a décharger
                    $whproduct = $this->Slips->Slipproducts->Products->Whproducts->find('all')->where(['warehouse_id' => $warehouse->id, 'item_id' => $slipproduct->item_id, 'item_type' => $slipproduct->item_type])->first();
                    //récupérer le produit du stock a charger
                    $whproductd = $this->Slips->Slipproducts->Products->Whproducts->find('all')->where(['warehouse_id' => $warehoused->id, 'item_id' => $slipproduct->item_id, 'item_type' => $slipproduct->item_type])->first();
                    //récuperer la quantité par sac/carton du produit
                    $productunite = $this->Slips->Slipproducts->Productunites->get($slipproduct->unity_id);
                    $qtyCarton = isset($slipprd[0]['quantity']) ? intVal($slipprd[0]['quantity']) : 0;
                    $qtyUnite = isset($slipprd[1]['quantity']) ? intVal($slipprd[1]['quantity']) : 0;
                    $validatedQty = ($qtyCarton * $productunite->quantity) + $qtyUnite;

                    //si la quantité validé est la mm quantité déclarer lors de la création du bon
                    if ($validatedQty >= $slipproduct->quantity) {
                        $data['slipproducts'][$increment]['id'] = $slipproduct->id;
                        $data['slipproducts'][$increment]['statut'] = 3;
                        $data['slipproducts'][$increment]['unity_id'] = $slipproduct->unity_id;
                        $data['slipproducts'][$increment]['item_type'] = $slipproduct->item_type;
                        $data['slipproducts'][$increment]['price'] = $slipproduct->price;
                        $data['slipproducts'][$increment]['uservalidate'] = $this->Auth->user('id');
                        if ($whproduct) {
                            $whproduct->quantity -= $slipproduct->quantity;
                            $this->Whproducts->save($whproduct);
                            $movSource = $this->StockMovements->newEntity([
                                'item_id' => $slipproduct->item_id,
                                'item_type' => $slipproduct->item_type,
                                'warehouse_id' => $whproduct->warehouse_id,
                                'quantity_change' => -$slipproduct->quantity,
                                'balance_after_movement' => $whproduct->quantity,
                                'movement_type' => 'slip_validation_source',
                                'user_id' => $this->Auth->user('id'),
                                'company_id' => $this->Auth->user('company_id'),
                                'notes' => 'Stock deduction on slip validation (Slip ID: ' . $slip->id . ')',
                            ]);
                            $this->StockMovements->save($movSource);
                        }
                        if ($whproductd) {
                            $whproductd->quantity += $slipproduct->quantity;
                            $this->Whproducts->save($whproductd);
                            $movDest = $this->StockMovements->newEntity([
                                'item_id' => $slipproduct->item_id,
                                'item_type' => $slipproduct->item_type,
                                'warehouse_id' => $whproductd->warehouse_id,
                                'quantity_change' => $slipproduct->quantity,
                                'balance_after_movement' => $whproductd->quantity,
                                'movement_type' => 'slip_validation_dest',
                                'user_id' => $this->Auth->user('id'),
                                'company_id' => $this->Auth->user('company_id'),
                                'notes' => 'Stock addition on slip validation (Slip ID: ' . $slip->id . ')',
                            ]);
                            $this->StockMovements->save($movDest);
                        }
                        $increment++;

                        //si la quantité validé et inférieur a la quantité commande et supérieur ou égale a 0
                    } elseif ($validatedQty < $slipproduct->quantity && $validatedQty >= 0) {
                        $data['slipproducts'][$increment]['id'] = $slipproduct->id;
                        $data['slipproducts'][$increment]['statut'] = 3;
                        $data['slipproducts'][$increment]['unity_id'] = $slipproduct->unity_id;
                        $data['slipproducts'][$increment]['item_type'] = $slipproduct->item_type;
                        $data['slipproducts'][$increment]['price'] = $slipproduct->price;
                        $data['slipproducts'][$increment]['quantity'] = $validatedQty;
                        $data['slipproducts'][$increment]['uservalidate'] = $this->Auth->user('id');

                        if ($whproduct) {
                            $whproduct->quantity -= $validatedQty;
                            $this->Whproducts->save($whproduct);
                            $movSource = $this->StockMovements->newEntity([
                                'item_id' => $slipproduct->item_id,
                                'item_type' => $slipproduct->item_type,
                                'warehouse_id' => $whproduct->warehouse_id,
                                'quantity_change' => -$validatedQty,
                                'balance_after_movement' => $whproduct->quantity,
                                'movement_type' => 'slip_validation_source',
                                'user_id' => $this->Auth->user('id'),
                                'company_id' => $this->Auth->user('company_id'),
                                'notes' => 'Stock deduction on slip validation (Slip ID: ' . $slip->id . ')',
                            ]);
                            $this->StockMovements->save($movSource);
                        }
                        if ($whproductd) {
                            $whproductd->quantity += $validatedQty;
                            $this->Whproducts->save($whproductd);
                            $movDest = $this->StockMovements->newEntity([
                                'item_id' => $slipproduct->item_id,
                                'item_type' => $slipproduct->item_type,
                                'warehouse_id' => $whproductd->warehouse_id,
                                'quantity_change' => $validatedQty,
                                'balance_after_movement' => $whproductd->quantity,
                                'movement_type' => 'slip_validation_dest',
                                'user_id' => $this->Auth->user('id'),
                                'company_id' => $this->Auth->user('company_id'),
                                'notes' => 'Stock addition on slip validation (Slip ID: ' . $slip->id . ')',
                            ]);
                            $this->StockMovements->save($movDest);
                        }

                        $increment++;

                        $data['slipproducts'][$increment]['statut'] = 4;
                        $data['slipproducts'][$increment]['item_id'] = $slipproduct->item_id;
                        $data['slipproducts'][$increment]['quantity'] = $slipproduct->quantity - $validatedQty;
                        $data['slipproducts'][$increment]['price'] = $slipproduct->price;
                        $data['slipproducts'][$increment]['slip_id'] = $slipproduct->slip_id;
                        $data['slipproducts'][$increment]['unity_id'] = $slipproduct->unity_id;
                        $data['slipproducts'][$increment]['item_type'] = $slipproduct->item_type;
                        $data['slipproducts'][$increment]['user_id'] = $slipproduct->user_id;
                        $data['slipproducts'][$increment]['company_id'] = $slipproduct->company_id;
                        $data['slipproducts'][$increment]['uservalidate'] = $this->Auth->user('id');
                        $increment++;
                    }
                } elseif ($slip->sliptype_id == 5) {

                    //récupérer le produit du stock a décharger
                    $whproduct = $this->Slips->Slipproducts->Packs->Whproducts->find('all')->where(['warehouse_id' => $warehouse->id, 'item_id' => $slipproduct->item_id, 'item_type' => $slipproduct->item_type])->first();
                    //récupérer le produit du stock a charger
                    $whproductd = $this->Slips->Slipproducts->Packs->Whproducts->find('all')->where(['warehouse_id' => $warehoused->id, 'item_id' => $slipproduct->item_id, 'item_type' => $slipproduct->item_type])->first();
                    //récuperer la quantité par sac/carton du produit
                    $packunite = $this->Slips->Slipproducts->Packunites->get($slipproduct->unity_id);


                    //si la quantité validé est la mm quantité déclarer lors de la création du bon
                    if (($slipprd[0]['quantity'] * $packunite->quantity + $slipprd[1]['quantity']) >= $slipproduct->quantity) {
                        $data['slipproducts'][$increment]['id'] = $slipproduct->id;
                        $data['slipproducts'][$increment]['statut'] = 3;
                        $data['slipproducts'][$increment]['price'] = $slipproduct->pack->prices[0]->price;
                        $data['slipproducts'][$increment]['uservalidate'] = $this->Auth->user('id');
                        $data['slipproducts'][$increment]['pack']['id'] = $slipproduct->item_id;
                        if ($whproductd) {
                            $data['slipproducts'][$increment]['pack']['whproducts'][1]['id'] = $whproductd->id;
                            $data['slipproducts'][$increment]['pack']['whproducts'][1]['quantity'] = $whproductd->quantity - $slipproduct->quantity;
                        }
                        $increment++;
                        //si la quantité validé et inférieur a la quantité commande et supérieur ou égale a 0
                    } elseif (($slipprd[0]['quantity'] * $packunite->quantity + $slipprd[1]['quantity']) < $slipproduct->quantity && ($slipprd[0]['quantity'] * $packunite->quantity + $slipprd[1]['quantity']) >= 0) {
                        $validatedQty = ($slipprd[0]['quantity'] * $packunite->quantity + $slipprd[1]['quantity']);
                        $data['slipproducts'][$increment]['id'] = $slipproduct->id;
                        $data['slipproducts'][$increment]['statut'] = 3;
                        $data['slipproducts'][$increment]['price'] = $slipproduct->pack->prices[0]->price;
                        $data['slipproducts'][$increment]['quantity'] = $validatedQty;
                        $data['slipproducts'][$increment]['uservalidate'] = $this->Auth->user('id');

                        if ($whproductd) {
                            $data['slipproducts'][$increment]['pack']['whproducts'][1]['id'] = $whproductd->id;
                            $data['slipproducts'][$increment]['pack']['whproducts'][1]['quantity'] = $whproductd->quantity - $validatedQty;
                        }

                        $increment++;

                        $data['slipproducts'][$increment]['statut'] = 4;
                        $data['slipproducts'][$increment]['item_id'] = $slipproduct->item_id;
                        $data['slipproducts'][$increment]['quantity'] = $slipproduct->quantity - $validatedQty;
                        $data['slipproducts'][$increment]['price'] = $slipproduct->price;
                        $data['slipproducts'][$increment]['slip_id'] = $slipproduct->slip_id;
                        $data['slipproducts'][$increment]['user_id'] = $slipproduct->user_id;
                        $data['slipproducts'][$increment]['unity_id'] = $slipproduct->unity_id;
                        $data['slipproducts'][$increment]['item_type'] = $slipproduct->item_type;
                        $data['slipproducts'][$increment]['company_id'] = $slipproduct->company_id;
                        $data['slipproducts'][$increment]['uservalidate'] = $this->Auth->user('id');
                        $increment++;
                    }
                } else {
                    if ($slip->sliptype_id == 2) {
                        $warehoused = $this->Slips->Warehouses->find('all')->where(['warehouse_id' => $slipdepart->warehoused, 'whnature_id' => $slipproduct->whnature_id, 'whtype_id' => 2])->last();
                    }

                    //récupérer le produit du stock a décharger
                    $whproduct = $this->Slips->Slipproducts->Packs->Whproducts->find('all')->where(['warehouse_id' => $warehouse->id, 'item_id' => $slipproduct->item_id, 'item_type' => $slipproduct->item_type])->first();
                    //récupérer le produit du stock a charger
                    $whproductd = $this->Slips->Slipproducts->Packs->Whproducts->find('all')->where(['warehouse_id' => $warehoused->id, 'item_id' => $slipproduct->item_id, 'item_type' => $slipproduct->item_type])->first();
                    if (isset($slipprd[0]) && isset($slipprd[0])) {
                        //récuperer la quantité par sac/carton du produit
                        $packunite = $this->Slips->Slipproducts->Packs->get($slipproduct->item_id, ['contain' => ['Packunites']]);
                        //si la quantité validé est la mm quantité déclarer lors de la création du bon
                        if (($slipprd[0]['quantity'] * $packunite->packunites[0]->quantity + $slipprd[1]['quantity']) >= $slipproduct->quantity) {
                            $data['slipproducts'][$increment]['id'] = $slipproduct->id;
                            $data['slipproducts'][$increment]['statut'] = 3;
                            $data['slipproducts'][$increment]['price'] = $slipproduct->pack->prices[0]->price;
                            $data['slipproducts'][$increment]['uservalidate'] = $this->Auth->user('id');
                            $data['slipproducts'][$increment]['pack']['id'] = $slipproduct->item_id;
                            if ($whproduct) {
                                $data['slipproducts'][$increment]['pack']['whproducts'][0]['id'] = $whproduct->id;
                                $data['slipproducts'][$increment]['pack']['whproducts'][0]['quantity'] = $whproduct->quantity - $slipproduct->quantity;
                            }
                            if ($whproductd) {
                                $data['slipproducts'][$increment]['pack']['whproducts'][1]['id'] = $whproductd->id;
                                $data['slipproducts'][$increment]['pack']['whproducts'][1]['quantity'] = $whproductd->quantity + $slipproduct->quantity;
                            }
                            $increment++;
                            //si la quantité validé et inférieur a la quantité commande et supérieur ou égale a 0
                        } elseif (($slipprd[0]['quantity'] * $packunite->packunites[0]->quantity + $slipprd[1]['quantity']) < $slipproduct->quantity && ($slipprd[0]['quantity'] * $packunite->packunites[0]->quantity + $slipprd[1]['quantity']) >= 0) {
                            $validatedQty = ($slipprd[0]['quantity'] * $packunite->packunites[0]->quantity + $slipprd[1]['quantity']);
                            $data['slipproducts'][$increment]['id'] = $slipproduct->id;
                            $data['slipproducts'][$increment]['statut'] = 3;
                            $data['slipproducts'][$increment]['price'] = $slipproduct->pack->prices[0]->price;
                            $data['slipproducts'][$increment]['quantity'] = $validatedQty;
                            $data['slipproducts'][$increment]['uservalidate'] = $this->Auth->user('id');

                            if ($whproduct) {
                                $data['slipproducts'][$increment]['pack']['whproducts'][0]['id'] = $whproduct->id;
                                $data['slipproducts'][$increment]['pack']['whproducts'][0]['quantity'] = $whproduct->quantity - $validatedQty;
                            }
                            if ($whproductd) {
                                $data['slipproducts'][$increment]['pack']['whproducts'][1]['id'] = $whproductd->id;
                                $data['slipproducts'][$increment]['pack']['whproducts'][1]['quantity'] = $whproductd->quantity + $validatedQty;
                            }

                            $increment++;

                            $data['slipproducts'][$increment]['statut'] = 4;
                            $data['slipproducts'][$increment]['item_id'] = $slipproduct->item_id;
                            $data['slipproducts'][$increment]['item_type'] = $slipproduct->item_type;
                            $data['slipproducts'][$increment]['quantity'] = $slipproduct->quantity - $validatedQty;
                            $data['slipproducts'][$increment]['price'] = $slipproduct->price;
                            $data['slipproducts'][$increment]['slip_id'] = $slipproduct->slip_id;
                            $data['slipproducts'][$increment]['user_id'] = $slipproduct->user_id;
                            $data['slipproducts'][$increment]['company_id'] = $slipproduct->company_id;
                            $data['slipproducts'][$increment]['uservalidate'] = $this->Auth->user('id');
                            $increment++;
                        }
                    } elseif (isset($slipprd[2])) {
                        $measurementunit = $this->Slips->Slipproducts->Packs->MeasurementUnits->get($slipproduct->pack->measurement_unit_id);
                        if (intVal($slipprd[2]['quantity']) == intVal($slipproduct->quantity)) {

                            $data['slipproducts'][$increment]['id'] = $slipproduct->id;
                            $data['slipproducts'][$increment]['statut'] = 3;
                            $data['slipproducts'][$increment]['price'] = $slipproduct->price;
                            $data['slipproducts'][$increment]['quantity'] = $slipprd[2]['quantity'];
                            $data['slipproducts'][$increment]['uservalidate'] = $this->Auth->user('id');
                            $data['slipproducts'][$increment]['pack']['id'] = $slipproduct->item_id;
                            if ($whproduct) {
                                $data['slipproducts'][$increment]['pack']['whproducts'][0]['id'] = $whproduct->id;
                                $data['slipproducts'][$increment]['pack']['whproducts'][0]['quantity'] = ($measurementunit->conversion_factor == 1) ? intVal($whproduct->quantity - $slipproduct->quantity) : intVal($whproduct->quantity - ($slipproduct->quantity * 1000 / $slipproduct->pack->measurement_quantity));
                            }
                            if ($whproductd) {
                                $data['slipproducts'][$increment]['pack']['whproducts'][1]['id'] = $whproductd->id;
                                $data['slipproducts'][$increment]['pack']['whproducts'][1]['quantity'] = ($measurementunit->conversion_factor == 1) ? intVal($whproductd->quantity + $slipproduct->quantity) : intVal($whproductd->quantity + ($slipproduct->quantity * 1000 / $slipproduct->pack->measurement_quantity));
                            }
                            $increment++;
                        } else {
                            $data['slipproducts'][$increment]['id'] = $slipproduct->id;
                            $data['slipproducts'][$increment]['statut'] = 3;
                            $data['slipproducts'][$increment]['price'] = $slipproduct->price;
                            $data['slipproducts'][$increment]['quantity'] = $slipprd[2]['quantity'];
                            $data['slipproducts'][$increment]['uservalidate'] = $this->Auth->user('id');
                            $data['slipproducts'][$increment]['pack']['id'] = $slipproduct->item_id;
                            if ($whproduct) {
                                $data['slipproducts'][$increment]['pack']['whproducts'][0]['id'] = $whproduct->id;
                                $data['slipproducts'][$increment]['pack']['whproducts'][0]['quantity'] = ($measurementunit->conversion_factor == 1) ? intVal($whproduct->quantity - $slipprd[2]['quantity']) : intVal($whproduct->quantity - ($slipprd[2]['quantity'] * 1000 / $slipproduct->pack->measurement_quantity));
                            }
                            if ($whproductd) {
                                $data['slipproducts'][$increment]['pack']['whproducts'][1]['id'] = $whproductd->id;
                                $data['slipproducts'][$increment]['pack']['whproducts'][1]['quantity'] = ($measurementunit->conversion_factor == 1) ? intVal($whproductd->quantity + $slipprd[2]['quantity']) : intVal($whproductd->quantity + ($slipprd[2]['quantity'] * 1000 / $slipproduct->pack->measurement_quantity));
                            }
                            $increment++;
                            if ($slipproduct->quantity - $slipprd[2]['quantity'] > 0) {
                                $data['slipproducts'][$increment]['statut'] = 4;
                                $data['slipproducts'][$increment]['item_id'] = $slipproduct->item_id;
                                $data['slipproducts'][$increment]['item_type'] = $slipproduct->item_type;
                                $data['slipproducts'][$increment]['quantity'] = $slipproduct->quantity - $slipprd[2]['quantity'];
                                $data['slipproducts'][$increment]['price'] = $slipproduct->price;
                                $data['slipproducts'][$increment]['slip_id'] = $slipproduct->slip_id;
                                $data['slipproducts'][$increment]['user_id'] = $slipproduct->user_id;
                                $data['slipproducts'][$increment]['company_id'] = $slipproduct->company_id;
                                $data['slipproducts'][$increment]['uservalidate'] = $this->Auth->user('id');
                                $increment++;
                            }
                        }

                    } else {
                        $this->Flash->error(__('Merci de renseigner les quantités validées. Veuillez réessayer.'));
                        return $this->redirect(['action' => 'validation', $slip->id]);
                    }
                }
            }
            $slip = $sliptype == 6 ? $this->Slips->patchEntity($slip, $data, ['associated' => ['Slipproducts.Products.Whproducts']]) : $this->Slips->patchEntity($slip, $data, ['associated' => ['Slipproducts.Packs.Whproducts']]);
            $slip->uservalidate = $this->Auth->user('id');
            if ($this->Slips->save($slip)) {
                // Log stock movements for each validated slipproduct
                $this->loadModel('Whproducts');
                $this->loadModel('StockMovements');
                foreach ($slip->slipproducts as $sp) {
                    if ($sp->statut == 3) {
                        // Find the source warehouse product
                        if ($slip->warehouse_id) {
                            $sp_wh = $this->Whproducts->find('all')
                                ->where(['item_id' => $sp->item_id, 'item_type' => $sp->item_type, 'warehouse_id' => $slip->warehouse_id])
                                ->first();
                            if ($sp_wh) {
                                $movSource = $this->StockMovements->newEntity([
                                    'item_id' => $sp->item_id,
                                    'item_type' => $sp->item_type,
                                    'warehouse_id' => $slip->warehouse_id,
                                    'quantity_change' => -$sp->quantity,
                                    'balance_after_movement' => $sp_wh->quantity,
                                    'movement_type' => 'slip_validation_source',
                                    'user_id' => $this->Auth->user('id'),
                                    'company_id' => $this->Auth->user('company_id'),
                                    'notes' => 'Slip validation decrement (Slip ID: ' . $slip->id . ')',
                                ]);
                                $this->StockMovements->save($movSource);
                            }
                        }

                        // Find the destination warehouse product
                        if ($slip->warehoused) {
                            $sp_whd = $this->Whproducts->find('all')
                                ->where(['item_id' => $sp->item_id, 'item_type' => $sp->item_type, 'warehouse_id' => $slip->warehoused])
                                ->first();
                            if ($sp_whd) {
                                $movDestQty = ($slip->sliptype_id == 5) ? -$sp->quantity : $sp->quantity; // Subtract if delivery validation
                                $movDest = $this->StockMovements->newEntity([
                                    'item_id' => $sp->item_id,
                                    'item_type' => $sp->item_type,
                                    'warehouse_id' => $slip->warehoused,
                                    'quantity_change' => $movDestQty,
                                    'balance_after_movement' => $sp_whd->quantity,
                                    'movement_type' => 'slip_validation_destination',
                                    'user_id' => $this->Auth->user('id'),
                                    'company_id' => $this->Auth->user('company_id'),
                                    'notes' => 'Slip validation increment/decrement (Slip ID: ' . $slip->id . ')',
                                ]);
                                $this->StockMovements->save($movDest);
                            }
                        }
                    }
                }

                $this->Flash->success(__('Le bon a été validé.'));
                return $this->redirect(['action' => 'index', $slip->sliptype_id]);
            }
            $this->Flash->error(__('Le bon n\'a pas pu être confirmé. Veuillez réessayer.'));
        }
        if ($slip->sliptype_id == 6) {
            $slippackunties = $this->Slips->get($id, [
                'contain' => [
                    'Slipproducts.Whnatures',
                    'Sliptypes',
                    'Slipproducts.Productunites.Unites.Parentunites',
                    'Slipproducts.Products.MeasurementUnits',
                    'Slipproducts.Products.Whproducts' => function ($q) use ($request) {
                        return $q->where(['Whproducts.warehouse_id' => $request]);
                    }
                ]
            ]);
        } else {
            $slippackunties = $this->Slips->get($id, [
                'contain' => [
                    'Slipproducts.Whnatures',
                    'Sliptypes',
                    'Slipproducts.Packs.Packunites.Unites.Parentunites',
                    'Slipproducts.Packs.MeasurementUnits',
                    'Slipproducts.Packs.Whproducts' => function ($q) use ($request) {
                        return $q->where(['Whproducts.warehouse_id' => $request]);
                    },
                ]
            ]);
        }

        $this->set(compact('slip', 'slippackunties'));
    }

    public function search($sliptype = null)
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
            case 'Code':
                $columnName = "Slips.created";
                break;
            case 'Warehouse':
                $columnName = "Slips.warehouse_id";
                break;
            case 'Warehoused':
                $columnName = "Slips.warehoused";
                break;
            case 'Whnature':
                $columnName = "Slips.whnature_id";
                break;
            case 'Whnatured':
                $columnName = "Slips.whnatured";
                break;
            case 'User':
                $columnName = "Slips.created";
                break;
            default:
                $columnName = "Slips.created";
                $columnSort = "desc";
                break;
        }
        $pos = stripos($searchDate, ";");
        $dateend = substr($searchDate, $pos + 1);
        $datestart = substr($searchDate, 0, $pos);

        if ($sliptype == 1) {
            $sel = $this->Slips->find('all')->where(['Slips.company_id' => $this->Auth->user('company_id'), 'Slips.sliptype_id' => $sliptype]);
            $empQuery = $this->Slips->find('all')->contain(['Sliptypes', 'Warehouses.Subwarehouses', 'Whnatures', 'Users', 'Slipproducts'])->where(['Slips.company_id' => $this->Auth->user('company_id'), 'Slips.sliptype_id' => $sliptype, 'Slips.exitslip_id IS ' => NULL, 'Slips.warehoused IS NOT ' => NULL])->order([$columnName => $columnSort]);
        } elseif ($sliptype == 5) {
            $sel = $this->Slips->find('all')->where(['Slips.company_id' => $this->Auth->user('company_id'), 'Slips.sliptype_id' => $sliptype]);
            $empQuery = $this->Slips->find('all')->contain(['Sliptypes', 'Warehouses.Subwarehouses', 'Whnatures', 'Users', 'Slipproducts'])->where(['Slips.company_id' => $this->Auth->user('company_id'), 'Slips.sliptype_id' => $sliptype, 'Slips.exitslip_id IS ' => NULL])->order([$columnName => $columnSort]);

        } elseif ($sliptype == 6) {
            $sel = $this->Slips->find('all')->where(['Slips.company_id' => $this->Auth->user('company_id'), 'Slips.sliptype_id' => $sliptype]);
            $empQuery = $this->Slips->find('all')->contain(['Sliptypes', 'Warehouses.Subwarehouses', 'Whnatures', 'Users', 'Slipproducts'])->where(['Slips.company_id' => $this->Auth->user('company_id'), 'Slips.sliptype_id' => $sliptype, 'Slips.exitslip_id IS ' => NULL])->order([$columnName => $columnSort]);

        } else {
            $sel = $this->Slips->find('all')->where(['Slips.company_id' => $this->Auth->user('company_id'), 'Slips.sliptype_id' => $sliptype]);
            $empQuery = $this->Slips->find('all')->contain(['Sliptypes', 'Warehouses.Subwarehouses', 'Whnatures', 'Users', 'Slipproducts'])->order([$columnName => $columnSort])->where(['Slips.company_id' => $this->Auth->user('company_id'), 'Slips.sliptype_id' => $sliptype, 'Slips.exitslip_id IS ' => NULL]);
        }


        if ($sliptype && $sliptype != 99) {
            $sel->where(['Slips.sliptype_id' => $sliptype]);
            $empQuery->where(['Slips.sliptype_id' => $sliptype]);
        } else {
            $sel->where(['Slips.statut' => 3]);
            $empQuery->where(['Slips.statut' => 3]);
        }

        if ($searchValue != '') {
            $sel->where([
                "OR" => [
                    ['Slips.code LIKE' => '%' . $searchValue . '%'],
                    ['lower(Slips.code) LIKE' => '%' . $searchValue . '%']
                ]
            ]);
            $empQuery->where([
                "OR" => [
                    ['Slips.code LIKE' => '%' . $searchValue . '%'],
                    ['lower(Slips.code) LIKE' => '%' . $searchValue . '%']
                ]
            ]);
        }
        if ($searchUser) {
            $user = $this->Slips->Users->get($searchUser, ['contain' => ['Pofsusers.Pofsales']]);
            if ($user->role_id == 3) {
                $empQuery->where(['Slips.warehoused' => $user->pofsusers[0]->pofsale->warehouse_id]);
                $sel->where(['Slips.warehoused' => $user->pofsusers[0]->pofsale->warehouse_id]);
            } else {
                $empQuery->where(['Slips.user_id' => $searchUser]);
                $sel->where(['Slips.user_id' => $searchUser]);
            }
        }
        if ($searchStatus) {
            $empQuery->where(['Slips.statut' => $searchStatus]);
            $sel->where(['Slips.statut' => $searchStatus]);
        }
        if ($datestart && $dateend) {
            $empQuery->where(['DATE(Slips.created) <= ' => $dateend, 'DATE(Slips.created) >= ' => $datestart]);
            $sel->where(['DATE(Slips.created) <= ' => $dateend, 'DATE(Slips.created) >= ' => $datestart]);
        }
        $empQuery->limit($perpage);
        $empQuery->page($page);
        $sel->select(['count' => $sel->func()->count('*')]);
        $total = $sel->last()->count;
        $data = [];
        foreach ($empQuery as $key => $slip) {


            $validate = ($slip->uservalidate) ? $this->Slips->Users->get($slip->uservalidate)->firstname : 'en attente';

            if ($sliptype == 1) {
                $warehoused = null;
                if ($slip->warehoused) {
                    $vendeur = $this->Slips->Warehouses->get($slip->warehoused, ['contain' => 'Pofsales.Pofsusers.Users']);
                    $warehoused = $vendeur->pofsales[0]->pofsusers[0]->user->firstname . ' ' . $vendeur->pofsales[0]->pofsusers[0]->user->lastname;
                }
                $data[] = [
                    "id" => $slip->id,
                    "code" => $slip->code,
                    "warehoused" => $warehoused,
                    "products" => count($slip->slipproducts),
                    "user" => $slip->user->firstname,
                    "validate" => $validate,
                    "date" => $slip->created->nice('Europe/Paris', 'fr-FR'),
                    "status" => $slip->statut,
                    "actions" => null
                ];
            } elseif ($sliptype == 2) {
                $warehoused = null;
                if ($slip->warehoused) {
                    $vendeur = $this->Slips->Warehouses->get($slip->warehouse_id, ['contain' => 'Pofsales.Pofsusers.Users']);
                    $warehouse = $vendeur->pofsales[0]->pofsusers[0]->user->firstname . ' ' . $vendeur->pofsales[0]->pofsusers[0]->user->lastname;
                }
                $data[] = [
                    "id" => $slip->id,
                    "code" => $slip->code,
                    "warehouse" => $warehouse,
                    "products" => count($slip->slipproducts),
                    "user" => $slip->user->firstname,
                    "date" => $slip->created->nice('Europe/Paris', 'fr-FR'),
                    "validate" => $validate,
                    "status" => $slip->statut,
                    "actions" => null
                ];
            } elseif ($sliptype == 3) {
                $whnatured = $this->Slips->Whnatures->get($slip->whnatured);

                $data[] = [
                    "id" => $slip->id,
                    "code" => $slip->code,
                    "warehouse" => $slip->warehouse->title,
                    "whnature" => $slip->whnature->title,
                    "whnatured" => $whnatured->title,
                    "products" => count($slip->slipproducts),
                    "user" => $slip->user->firstname,
                    "date" => $slip->created->nice('Europe/Paris', 'fr-FR'),
                    "validate" => $validate,
                    "status" => $slip->statut,
                    "actions" => null
                ];
            } elseif ($sliptype == 4) {
                $warehoused = $this->Slips->Warehouses->get($slip->warehoused);
                $data[] = [
                    "id" => $slip->id,
                    "code" => $slip->code,
                    "warehouse" => $slip->warehouse->title,
                    "warehoused" => $warehoused->title,
                    "whnature" => $slip->whnature->title,
                    "products" => count($slip->slipproducts),
                    "user" => $slip->user->firstname,
                    "date" => $slip->created->nice('Europe/Paris', 'fr-FR'),
                    "validate" => $validate,
                    "status" => $slip->statut,
                    "actions" => null
                ];
            } elseif ($sliptype == 5) {
                $data[] = [
                    "id" => $slip->id,
                    "code" => $slip->code,
                    "warehouse" => $slip->warehouse->title,
                    "whnature" => $slip->whnature->title,
                    "products" => count($slip->slipproducts),
                    "user" => $slip->user->firstname,
                    "date" => $slip->created->nice('Europe/Paris', 'fr-FR'),
                    "validate" => $validate,
                    "status" => $slip->statut,
                    "actions" => null
                ];
            } elseif ($sliptype == 6) {
                $data[] = [
                    "id" => $slip->id,
                    "code" => $slip->code,
                    "warehouse" => $slip->warehouse->title,
                    "whnature" => $slip->whnature->title,
                    "products" => count($slip->slipproducts),
                    "user" => $slip->user->firstname,
                    "date" => $slip->created->nice('Europe/Paris', 'fr-FR'),
                    "validate" => $validate,
                    "status" => $slip->statut,
                    "actions" => null
                ];
            }
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
    public function secteurs()
    {
        $userid = $this->request->getQuery('keyword');
        $userzones = $this->Slips->Users->Zoneusers->find('all')->where(['user_id' => $userid]);
        $qzones = [];
        foreach ($userzones as $key => $userzone) {
            $qzones['OR'][$userzone->zone_id] = ['Zoneusers.zone_id' => $userzone->zone_id];
        }

        $zoneusers = $this->Slips->Users->Zoneusers->find('all')->contain([
            'Zones',
            'Users.Orders' => function ($q) {
                return $q->where(['Orders.statut' => 1]);
            },
            'Users' => function ($q) {
                return $q->where(['Users.role_id' => 5]);
            }
        ])->where([$qzones]);
        $zoneuserdatas = [];
        foreach ($zoneusers as $key => $zoneuser) {
            if ($zoneuser->user->orders) {
                $zoneuserdatas[$zoneuser->user_id] = $zoneuser->zone->title . '(' . $zoneuser->user->firstname . ' ' . $zoneuser->user->lastname . ')';
            }
        }
        $this->set(compact('zoneuserdatas'));

    }
    public function instanceord()
    {
        $warehoused = $this->request->getQuery('keyword');
        $whnature_id = $this->request->getQuery('keyword1');
        $user_id = $this->request->getQuery('keyword2');
        $orders = $this->Slips->Users->Orders->find('all')->contain(['Orderpacks.Packs.Packunites.Unites.Parentunites'])->where(['Orders.statut' => 5, 'Orders.user_id' => $user_id]);

        $packselects = [];
        $warehouse = $this->Slips->Warehouses->find('all')->contain([
            'Whproducts.Packs.Prices' => function ($q) {
                return $q->where(['Prices.warehouse_id' => $this->Auth->user('defaultwh')]);
            },
            'Whproducts.Packs.Packunites.Unites.Parentunites'
        ])->where([
                    'Warehouses.warehouse_id' => $warehoused,
                    'Warehouses.whnature_id' => 1,
                    'Warehouses.whtype_id' => 2
                ])->last();

        $packselects = [];
        foreach ($warehouse->whproducts as $key => $whproduct) {
            $packselect['id'] = $whproduct->pack->id;
            $packselect['title'] = $whproduct->pack->title . ' (' . $whproduct->pack->packunites[0]->unite->parentunite->abrev . ')';
            $packselect['quantity'] = $whproduct->quantity;
            $packselect['price'] = $whproduct->pack->prices[0]->price;
            $packselect['disponible'] = $whproduct->quantity;
            $packselect['qtepercs'] = $whproduct->pack->packunites[0]->quantity;
            $packselect['carsac'] = $whproduct->pack->packunites[0]->unite->abrev;
            $packselect['piecekg'] = $whproduct->pack->packunites[0]->unite->parentunite->abrev;
            $packselects[] = $packselect;
        }

        $whnatures1 = $this->Slips->Warehouses->Whnatures->find('all')->where(['statut' => 1]);
        $whnatures = [];
        foreach ($whnatures1 as $key => $whnat) {
            $whnatures[$whnat->id] = $whnat->title;
        }
        $whnatures[99] = "Rupture";

        $this->set(compact('packselects', 'whnatures'));

    }
    public function dechargestock()
    {
        $warehoused = $this->request->getQuery('keyword');
        $whnature_id = $this->request->getQuery('keyword1');
        $pofsuser = $this->Slips->Warehouses->Pofsales->find('all')->contain(['Pofsusers'])->where(['Pofsales.warehouse_id' => $warehoused]);
        $datauser = $this->Slips->Users->get($pofsuser->last()->pofsusers[0]->user_id, [
            'contain' => [
                'Zoneusers.Zones.Zoneusers' => function ($q) use ($pofsuser) {
                    return $q->where(['Zoneusers.user_id !=' => $pofsuser->last()->pofsusers[0]->user_id]);
                },
                'Zoneusers.Zones.Zoneusers.Users' => function ($q) {
                    return $q->where(['Users.role_id' => 5, 'Users.statut' => 1]);
                }
            ]
        ]);
        $users = [];
        if ($datauser->zoneusers) {
            foreach ($datauser->zoneusers as $key => $zoneuser) {
                if ($zoneuser->zone->zoneusers) {
                    foreach ($zoneuser->zone->zoneusers as $key => $zoneuser) {
                        if ($zoneuser->user) {
                            $users[$zoneuser->user->id] = $zoneuser->user->firstname . ' ' . $zoneuser->user->lastname;
                        }
                    }

                }
            }
        }

        $warehouse = $this->Slips->Warehouses->find('all')->contain([
            'Whproducts.Packs',
            'Whproducts.Packs.Prices' => function ($q) {
                return $q->where(['Prices.warehouse_id' => $this->Auth->user('defaultwh')]);
            },
            'Whproducts.Packs.Packunites.Unites.Parentunites'
        ])->where([
                    'Warehouses.warehouse_id' => $warehoused,
                    'Warehouses.whnature_id' => 1,
                    'Warehouses.whtype_id' => 2
                ])->last();

        $packselects = [];
        foreach ($warehouse->whproducts as $key => $whproduct) {
            $packselect['id'] = $whproduct->pack->id;
            $packselect['title'] = $whproduct->pack->title . ' (' . $whproduct->pack->packunites[0]->unite->parentunite->abrev . ')';
            $packselect['quantity'] = $whproduct->quantity;
            $packselect['disponible'] = $whproduct->quantity;
            $packselect['qtepercs'] = $whproduct->pack->packunites[0]->quantity;
            $packselect['price'] = $whproduct->pack->prices[0]->price;
            $packselect['carsac'] = $whproduct->pack->packunites[0]->unite->abrev;
            $packselect['piecekg'] = $whproduct->pack->packunites[0]->unite->parentunite->abrev;
            $packselects[] = $packselect;
        }

        $whnatures1 = $this->Slips->Warehouses->Whnatures->find('all')->where(['statut' => 1]);
        $whnatures = [];
        foreach ($whnatures1 as $key => $whnat) {
            $whnatures[$whnat->id] = $whnat->title;
        }
        $whnatures[99] = "Rupture";

        $this->set(compact('packselects', 'users', 'whnatures'));

    }
    public function chargestock()
    {
        // Disable layout for AJAX request
        $this->viewBuilder()->setLayout('ajax');

        $warehoused = $this->request->getQuery('keyword');
        $whnature_id = $this->request->getQuery('keyword1');

        // Validate parameters
        if (empty($warehoused) || empty($whnature_id)) {
            $packselects = [];
            $this->set(compact('packselects'));
            return;
        }

        // Get warehouse with products
        $warehouse = $this->Slips->Warehouses->find('all')
            ->contain([
                'Whproducts' => function ($q) {
                    return $q->where(['Whproducts.quantity >' => 0]);
                },
                'Whproducts.Packs',
                'Whproducts.Packs.Packunites.Unites.Parentunites'
            ])
            ->where([
                'Warehouses.warehouse_id' => $this->Auth->user('defaultwh'),
                'Warehouses.whnature_id' => $whnature_id,
                'Warehouses.whtype_id' => 2
            ])
            ->last();

        $packselects = [];

        if ($warehouse && $warehouse->whproducts) {
            foreach ($warehouse->whproducts as $key => $whproduct) {
                // Skip if pack or packunites not available
                if (!$whproduct->pack || empty($whproduct->pack->packunites)) {
                    continue;
                }

                $packselect = [];
                $packselect['id'] = $whproduct->pack->id;
                $packselect['title'] = $whproduct->pack->title . ' (' . $whproduct->pack->packunites[0]->unite->parentunite->abrev . ')';
                $packselect['quantity'] = $whproduct->quantity;
                $packselect['disponible'] = $whproduct->quantity;
                $packselect['qtepercs'] = $whproduct->pack->packunites[0]->quantity;
                $packselect['carsac'] = $whproduct->pack->packunites[0]->unite->abrev;
                $packselect['piecekg'] = $whproduct->pack->packunites[0]->unite->parentunite->abrev;

                // Calculate available quantity (subtract pending orders)
                $pofsale = $this->Slips->Orders->Pofsales->find('all')
                    ->where([
                        'warehouse_id' => $this->Auth->user('defaultwh'),
                        'pofstype_id' => 3
                    ])
                    ->last();

                if ($pofsale) {
                    $orders = $this->Slips->Orders->find('all')
                        ->contain([
                            'Orderpacks' => function ($q) use ($whproduct) {
                                return $q->where(['Orderpacks.pack_id' => $whproduct->pack_id]);
                            }
                        ])
                        ->where([
                            'Orders.statut' => 1,
                            'Orders.pofsale_id' => $pofsale->id
                        ]);

                    foreach ($orders as $order) {
                        foreach ($order->orderpacks as $orderpack) {
                            $packselect['disponible'] -= $orderpack->quantity;
                            $packselect['quantity'] -= $orderpack->quantity;
                        }
                    }
                }

                // Subtract pending slips
                $slips = $this->Slips->find('all')
                    ->contain([
                        'Slipproducts' => function ($q) use ($whproduct) {
                            return $q->where(['Slipproducts.pack_id' => $whproduct->pack_id]);
                        }
                    ])
                    ->where([
                        'Slips.statut' => 1,
                        'Slips.warehouse_id' => $this->Auth->user('defaultwh')
                    ]);

                foreach ($slips as $slip) {
                    foreach ($slip->slipproducts as $slipproduct) {
                        $packselect['disponible'] -= $slipproduct->quantity;
                        $packselect['quantity'] -= $slipproduct->quantity;
                    }
                }

                // Only add items with available quantity
                if ($packselect['disponible'] > 0) {
                    $packselects[] = $packselect;
                }
            }
        }

        $this->set(compact('packselects'));
    }
    public function transferstock()
    {
        $warehoused = $this->request->getQuery('keyword');
        $whnature_id = $this->request->getQuery('keyword1');

        $warehouse = $this->Slips->Warehouses->find('all')->contain(['Whproducts.Packs', 'Whproducts.Packs.Packunites.Unites.Parentunites'])->where(['Warehouses.warehouse_id' => $this->request->getQuery('keyword'), 'Warehouses.whnature_id' => $whnature_id, 'Warehouses.whtype_id' => 2])->last();

        $packselects = [];
        foreach ($warehouse->whproducts as $key => $whproduct) {
            if ($whproduct->quantity > 0) {
                $packselect['id'] = $whproduct->pack->id;
                $packselect['title'] = $whproduct->pack->title . ' (' . $whproduct->pack->packunites[0]->unite->parentunite->abrev . ')';
                $packselect['quantity'] = $whproduct->quantity;
                $packselect['disponible'] = $whproduct->quantity;
                $packselect['qtepercs'] = $whproduct->pack->packunites[0]->quantity;
                $packselect['carsac'] = $whproduct->pack->packunites[0]->unite->abrev;
                $packselect['piecekg'] = $whproduct->pack->packunites[0]->unite->parentunite->abrev;
                $pofsale = $this->Slips->Orders->Pofsales->find('all')->where(['warehouse_id' => $warehoused, 'pofstype_id' => 3])->last();
                $orders = $this->Slips->Orders->find('all')->contain([
                    'Orderpacks' => function ($q) use ($whproduct) {
                        return $q->where(['Orderpacks.pack_id' => $whproduct->pack_id]);
                    }
                ])->where(['Orders.statut' => 1, 'Orders.pofsale_id' => $pofsale->id]);
                foreach ($orders as $order) {
                    foreach ($order->orderpacks as $orderpack) {
                        $packselect['disponible'] -= $orderpack->quantity;
                        $packselect['quantity'] -= $orderpack->quantity;
                    }
                }
                $slips = $this->Slips->find('all')->contain([
                    'Slipproducts' => function ($q) use ($whproduct) {
                        return $q->where(['Slipproducts.pack_id' => $whproduct->pack_id]);
                    }
                ])->where(['Slips.statut' => 1, 'Slips.warehouse_id' => $warehoused]);
                foreach ($slips as $slip) {
                    foreach ($slip->slipproducts as $slipproduct) {
                        $packselect['disponible'] -= $slipproduct->quantity;
                        $packselect['quantity'] -= $slipproduct->quantity;
                    }
                }
                $packselects[] = $packselect;
            }
        }
        $this->set(compact('packselects'));

    }
    public function deplacestock()
    {
        $warehoused = $this->request->getQuery('keyword');
        $whnature_id = $this->request->getQuery('keyword1');

        $warehouse = $this->Slips->Warehouses->find('all')->contain(['Whproducts.Packs', 'Whproducts.Packs.Packunites.Unites.Parentunites'])->where(['Warehouses.warehouse_id' => $this->Auth->user('defaultwh'), 'Warehouses.whnature_id' => $whnature_id, 'Warehouses.whtype_id' => 2])->last();

        $packselects = [];
        foreach ($warehouse->whproducts as $key => $whproduct) {
            if ($whproduct->quantity > 0) {
                $packselect['id'] = $whproduct->pack->id;
                $packselect['title'] = $whproduct->pack->title . ' (' . $whproduct->pack->packunites[0]->unite->parentunite->abrev . ')';
                $packselect['quantity'] = $whproduct->quantity;
                $packselect['disponible'] = $whproduct->quantity;
                $packselect['qtepercs'] = $whproduct->pack->packunites[0]->quantity;
                $packselect['carsac'] = $whproduct->pack->packunites[0]->unite->abrev;
                $packselect['piecekg'] = $whproduct->pack->packunites[0]->unite->parentunite->abrev;

                $packselects[] = $packselect;
            }
        }
        $this->set(compact('packselects'));

    }
    /*public function instanceord($slipid=null)
    {  

        $slip=$this->Slips->get($slipid,['contain'=>['Slipproducts']]);


         $empQuery=$this->Slips->Warehouses->Whproducts->find('all')->contain(['Products'])->where(['Whproducts.quantity >'=>0]);
        $empQuery->where(['Whproducts.warehouse_id'=>$warehouse->id]);

        //"statut"=>'',
        foreach ($empQuery as $key => $supporderproduct) {
            $quantity=$supporderproduct->quantity;
            foreach ($slip->slipproducts as $key1 => $slipproduct) {
                if ($slipproduct->product_id==$supporderproduct->product_id) {
                    $quantity-=$slipproduct->quantity;
                }
            }
            $action='<button data-id="'.$supporderproduct->product->id.'" class="addo btn btn-sm btn-success waves-effect waves-light" >+</button>';

            $data[] = [
                "product"=>$supporderproduct->product->title,
                "quantity"=>'<input type="number" name="'.$supporderproduct->product->id.'" class="form-control" value="'.$quantity.'" id="'.$supporderproduct->product->id.'">',
                "order"=>'',
                "action"=>$action
            ];
        }

    }*/

    public function addedord($slipid = null)
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
        ## Total number of records with filtering
        $sel = $this->Slips->Slipproducts->find('all')->contain('Products');
        $sel->where(['Slipproducts.slip_id' => $slipid]);
        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery = $this->Slips->Slipproducts->find('all')->contain('Products');
        $empQuery->where(['Slipproducts.slip_id' => $slipid]);
        if ($row == 0) {
            $empQuery->limit($rowperpage);
        } else {
            $empQuery->limit($rowperpage);
            $empQuery->page(($row / $rowperpage) + 1);
        }

        if ($searchValue != '') {
            $or = [
                ['Products.title LIKE' => '%' . $searchValue . '%'],
                ['Products.reference LIKE' => '%' . $searchValue . '%'],
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
        foreach ($empQuery as $key => $slipproduct) {

            $action = '<button data-id="' . $slipproduct->id . '" class="rmvord btn btn-sm btn-danger waves-effect waves-light" >-</button>';

            $data[] = [
                "product" => $slipproduct->product->title,
                "quantity" => $slipproduct->quantity,
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

    public function addord($slipid = null)
    {
        $productid = json_decode($_GET['ordid'], true);
        $qte = intval(json_decode($_GET['qte'], true));
        $slipproduct = $this->Slips->Slipproducts->find('all')->where(['slip_id' => $slipid, 'product_id' => $productid])->last();

        $slip = $this->Slips->get($slipid);
        $subwarehouse = $this->Slips->Warehouses->find('all')->where(['warehouse_id' => $slip->warehouse_id, 'whnature_id' => $slip->whnature_id, 'whtype_id' => 2])->last();
        $whproduct = $this->Slips->Warehouses->Whproducts->find('all')->where(['product_id' => $productid, 'warehouse_id' => $subwarehouse->id])->last();
        $slipproduct = $this->Slips->Slipproducts->find('all')->where(['slip_id' => $slipid, 'product_id' => $productid])->last();


        if ($qte <= $whproduct->quantity && $qte >= 0) {
            if ($slipproduct) {
                if (($qte + $slipproduct->quantity) <= $whproduct->quantity) {
                    $updateslipproduct = $this->Slips->Slipproducts->get($slipproduct->id);
                    $updateslipproduct->quantity += intval($qte);
                    $this->Slips->Slipproducts->save($updateslipproduct);
                }
            } else {
                $newslipproduct = $this->Slips->Slipproducts->newEntity();
                $newslipproduct->slip_id = $slipid;
                $newslipproduct->product_id = $productid;
                $newslipproduct->quantity = intval($qte);
                $newslipproduct->user_id = $this->Auth->user('id');
                $newslipproduct->company_id = $this->Auth->user('company_id');
                $this->Slips->Slipproducts->save($newslipproduct);
            }
        }
        $this->autoRender = false;
    }

    public function rmvord($receiptid = null)
    {
        $productid = json_decode($_GET['ordid'], true);
        $slipproduct = $this->Slips->Slipproducts->get($productid);
        $this->Slips->Slipproducts->delete($slipproduct);

        $this->autoRender = false;
    }

    public function delete($id)
    {
        $slip = $this->Slips->get($id);
        $type = $slip->sliptype_id;
        if ($slip->statut == 2) {
            if ($this->Slips->delete($slip)) {
                $this->Flash->success(__('le bon est supprimer avec succés.'));
                return $this->redirect(['action' => 'index', $type]);
            }
            $this->Flash->error(__('un probléme est survenue lors de la suppression du bon.'));
            return $this->redirect(['action' => 'index', $type]);
        }
        $this->Flash->error(__('Vous n\'avez pas les droits nécessaires pour supprimer ce bon.'));
        return $this->redirect(['action' => 'index', $type]);
    }

    public function instancebn($slipid = null)
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
        $slip = $this->Slips->get($slipid, ['contain' => ['Orders']]);

        $q = [];
        foreach ($slip->orders as $key => $order) {
            $q['OR'] = [$order->id => ['Orderpacks.order_id' => $order->id]];
        }
        $warehouse = $this->Slips->Warehouses->find('all')->where(['warehouse_id' => $slip->warehouse_id, 'whnature_id' => $slip->whnature_id])->last();

        $sel = $this->Slips->Orders->Orderpacks->find('all')->contain([
            'Orders.Customers',
            'Packs' => function ($q) {
                return $q->where(['Orderpacks.statut' => 3]);
            }
        ])->where(['Orderpacks.statut' => 3, 'Orderpacks.user_id' => $slip->user_id]);

        $empQuery = $this->Slips->Orders->Orderpacks->find('all')->contain([
            'Orders.Customers',
            'Packs' => function ($q) {
                return $q->where(['Orderpacks.statut' => 3]);
            }
        ])->where(['Orderpacks.statut' => 3, 'Orderpacks.user_id' => $slip->user_id]);
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
        //"statut"=>'',
        foreach ($empQuery as $key => $orderpack) {
            $packproduct = $this->Slips->Users->Orders->Orderpacks->Packs->Packproducts->find('all')->contain([
                'Products.Whproducts' => function ($q) use ($warehouse) {
                    return $q->where(['Whproducts.warehouse_id' => $warehouse->id]);
                }
            ])->where(['Packproducts.pack_id' => $orderpack->pack->id])->last();
            $quantity = 0;
            foreach ($packproduct->product->whproducts as $key => $whproduct) {
                $product1 = intval($whproduct->quantity / $packproduct->quantity);
                if ($product1 < $quantity || $quantity == null) {
                    $quantity = $product1;
                }
            }
            $action = '<a data-id="' . $orderpack->id . '" class="addo btn btn-sm btn-success waves-effect waves-light" >+</a>';

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

    public function addedbn($slipid = null)
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
        $slip = $this->Slips->get($slipid);

        $warehouse = $this->Slips->Warehouses->find('all')->where(['warehouse_id' => $slip->warehouse_id, 'whnature_id' => $slip->whnature_id])->last();

        $sel = $this->Slips->Users->Orders->Orderpacks->find('all');
        $sel->where(['Orderpacks.statut' => 9, 'Orderpacks.user_id' => $slip->user_id]);

        $empQuery = $this->Slips->Users->Orders->find('all')->contain([
            'Customers',
            'Orderpacks.Packs' => function ($q) {
                return $q->where(['Orderpacks.statut' => 9]);
            }
        ]);
        $empQuery->where(['Orders.user_id' => $slip->user_id]);

        $empQuery->order(['Customers.name']);


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
                ['Orders.title LIKE' => '%' . $searchValue . '%'],
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
        foreach ($empQuery as $key => $order) {
            //$quantity=$supporderproduct->quantity;
            /*foreach ($slip->slipproducts as $key1 => $slipproduct) {
                if ($slipproduct->product_id==$supporderproduct->product_id) {
                    $quantity-=$slipproduct->quantity;
                }
            }*/
            foreach ($order->orderpacks as $key => $orderpack) {
                $packproduct = $this->Slips->Users->Orders->Orderpacks->Packs->Packproducts->find('all')->contain([
                    'Products.Whproducts' => function ($q) use ($warehouse) {
                        return $q->where(['Whproducts.warehouse_id' => $warehouse->id]);
                    }
                ])->where(['Packproducts.pack_id' => $orderpack->pack->id])->last();
                $quantity = null;
                foreach ($packproduct->product->whproducts as $key => $whproduct) {
                    $product1 = intval($whproduct->quantity / $packproduct->quantity);
                    if ($product1 < $quantity || $quantity == null) {
                        $quantity = $product1;
                    }
                }
                $action = '<button data-id="' . $orderpack->id . '" class="rmvord btn btn-sm btn-danger waves-effect waves-light" >-</button>';

                $data[] = [
                    "product" => $orderpack->pack->title,
                    "productdis" => $quantity,
                    "customer" => $order->customer->name . ' CMD:' . $order->code,
                    "quantity" => $orderpack->quantity,
                    "action" => $action
                ];
            }
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

    public function addbn($slipid = null)
    {
        // récuperer lidentifiant du pack et la quantité
        $productid = json_decode($_GET['ordid'], true);
        $qte = intval(json_decode($_GET['qte'], true));
        //récupérer le bon de chargement et les produits pour les actualisés
        $slip = $this->Slips->get($slipid, ['contain' => ['Slipproducts']]);
        // tableau pour traiter les données du bon
        $slipdata = ['id' => $slip->id];
        //récupérer le pack avec ses produits
        $orderpack = $this->Slips->Users->Orderpacks->get($productid, ['contain' => ['Orderpackproducts']]);
        //vérifié si le produit est déja disponible dans le bon
        $hasvldorderpack = $this->Slips->Users->Orderpacks->find('all')->contain(['Orderpackproducts'])->where(['order_id' => $orderpack->order_id, 'pack_id' => $orderpack->pack_id, 'statut' => 9])->last();
        if ($hasvldorderpack) {
            //vérifier si la quantité récupérer égale la quantité commandé
            if ($orderpack->quantity == $qte) {
                foreach ($orderpack->orderpackproducts as $key => $orderpackproduct) {

                    // verifier si y a un produit dans le bon avec le meme identifiant
                    $samesliprdcts = $this->Slips->Slipproducts->find('all')->where(['slip_id' => $slip->id, 'product_id' => $orderpackproduct->product_id])->last();

                    // la quantité des produits dans le pack
                    $qtypackproduct = $orderpackproduct->quantity / $orderpack->quantity;



                    // si le produit est disponible on ajoute la quantité au ancien produit
                    if ($samesliprdcts) {
                        $slipdata['slipproducts'][$key] =
                            [
                                'id' => $samesliprdcts->id,
                                'quantity' => $samesliprdcts->quantity + ($qtypackproduct * $qte),
                            ];


                    } else {

                        $slipdata['slipproducts'][$key] =
                            [
                                'product_id' => $orderpackproduct->product_id,
                                'quantity' => ($qtypackproduct * $qte),
                                'user_id' => $this->Auth->user('id'),
                                'company_id' => $this->Auth->user('company_id'),
                                'statut' => 1,
                            ];

                    }

                    $orderpackproduct->quantity += $hasvldorderpack->orderpackproducts[$key]->quantity;
                    $orderpackproduct->statut = 2;

                }
                $orderpack->statut = 2;
                $deletorderpack = $this->Slips->Users->Orderpacks->get($hasvldorderpack->id);

                $slip = $this->Slips->patchEntity($slip, $slipdata, ['associated' => ['Slipproducts']]);
                if ($this->Slips->Users->Orderpacks->delete($deletorderpack)) {
                    if ($this->Slips->Users->Orderpacks->save($orderpack)) {
                        $this->Slips->save($slip);
                    }
                }

            }
            //si le produit n'est pas disponible dans le bon
        } else {
            //vérifier si la quantité récupérer égale la quantité commandé
            if ($orderpack->quantity == $qte) {
                $orderpack->statut = 9;
                foreach ($orderpack->orderpackproducts as $key => $orderpackproduct) {
                    // verifier si y a un produit dans le bon avec le meme identifiant
                    $samesliprdcts = $this->Slips->Slipproducts->find('all')->where(['slip_id' => $slip->id, 'product_id' => $orderpackproduct->product_id])->last();

                    // la quantité des produits dans le pack
                    $qtypackproduct = $orderpackproduct->quantity / $orderpack->quantity;

                    // si le produit est disponible on ajoute la quantité au ancien produit

                    if ($samesliprdcts) {
                        $slipdata['slipproducts'][$key] =
                            [
                                'id' => $samesliprdcts->id,
                                'quantity' => $samesliprdcts->quantity + ($qtypackproduct * $qte),
                            ];

                    } else {

                        $slipdata['slipproducts'][$key] =
                            [
                                'product_id' => $orderpackproduct->product_id,
                                'quantity' => ($qtypackproduct * $qte),
                                'user_id' => $this->Auth->user('id'),
                                'company_id' => $this->Auth->user('company_id'),
                                'statut' => 1,
                            ];

                    }
                    $orderpackproduct->statut = 9;
                }
                $slip = $this->Slips->patchEntity($slip, $slipdata, ['associated' => ['Slipproducts']]);
                if ($this->Slips->Users->Orderpacks->save($orderpack)) {
                    $this->Slips->save($slip);
                }
            } elseif ($orderpack->quantity > 0 && $qte < $orderpack->quantity) {

                $neworderpack = $this->Slips->Users->Orderpacks->newEntity();
                $newordpackdatas = [
                    'order_id' => $orderpack->order_id,
                    'quantity' => ($orderpack->quantity - $qte),
                    'user_id' => $this->Auth->user('id'),
                    'company_id' => $this->Auth->user('company_id'),
                    'tohave_id' => $orderpack->tohave_id,
                    'price' => $orderpack->price,
                    'tranche_id' => $orderpack->tranche_id,
                    'commission' => $orderpack->commission,
                    'pack_id' => $orderpack->pack_id,
                    'statut' => 7,
                ];

                foreach ($orderpack->orderpackproducts as $key => $orderpackproduct) {
                    // la quantité des produits dans le pack
                    $qtypackproduct = $orderpackproduct->quantity / $orderpack->quantity;

                    $newordpackdatas['orderpackproducts'][$key] = [
                        'orderpack_id' => $orderpackproduct->orderpack_id,
                        'product_id' => $orderpackproduct->product_id,
                        'slipproduct_id' => $orderpackproduct->slipproduct_id,
                        'quantity' => (($orderpack->quantity - $qte) * $qtypackproduct),
                        'buyingprice' => $orderpackproduct->buyingprice,
                        'statut' => 7,
                        'company_id' => $orderpackproduct->company_id,
                        'user_id' => $orderpackproduct->user_id,
                    ];
                    //verifier si y a un produit dans le bon avec le meme identifiant
                    $samesliprdcts = $this->Slips->Slipproducts->find('all')->where(['slip_id' => $slip->id, 'product_id' => $orderpackproduct->product_id])->last();


                    // si le produit est disponible on ajoute la quantité au ancien produit
                    if ($samesliprdcts) {
                        $slipdata['slipproducts'][$key] =
                            [
                                'id' => $samesliprdcts->id,
                                'quantity' => $samesliprdcts->quantity + ($qtypackproduct * $qte),
                            ];

                    } else {

                        $slipdata['slipproducts'][$key] =
                            [
                                'product_id' => $orderpackproduct->product_id,
                                'quantity' => ($qtypackproduct * $qte),
                                'user_id' => $this->Auth->user('id'),
                                'company_id' => $this->Auth->user('company_id'),
                                'statut' => 1,
                            ];
                    }
                    $orderpackproduct->statut = 9;
                }

                $orderpack->statut = 9;
                $orderpack->quantity = $qte;

                $slip = $this->Slips->patchEntity($slip, $slipdata, ['associated' => ['Slipproducts']]);
                $neworderpack = $this->Slips->Users->Orderpacks->patchEntity($neworderpack, $newordpackdatas);
                if ($this->Slips->Users->Orderpacks->save($orderpack)) {
                    if ($this->Slips->Users->Orderpacks->save($neworderpack)) {
                        $this->Slips->save($slip);
                    }
                }
            }
        }

        $this->autoRender = false;
    }

    public function rmvbn($receiptid = null)
    {
        $ordid = json_decode($_GET['ordid'], true);
        $supporderproduct = $this->Receipts->Supporderproducts->get($ordid);
        $instanceproduct = $this->Receipts->Supporderproducts->find('all')->where(['product_id' => $supporderproduct->product_id, 'supplierorder_id' => $supporderproduct->supplierorder_id, 'receipt_id IS' => NULL])->last();
        $supporderproduct->statut = 1;
        $supporderproduct->receipt_id = NULL;
        if (empty($instanceproduct)) {
            $this->Receipts->Supporderproducts->save($supporderproduct);
        } else {
            $deletproduct = $this->Receipts->Supporderproducts->get($instanceproduct->id);
            $supporderproduct->quantity += $deletproduct->quantity;
            if ($this->Receipts->Supporderproducts->save($supporderproduct)) {
                $this->Receipts->Supporderproducts->delete($deletproduct);
            }
        }
        $this->autoRender = false;
    }

    /**
     * Stock report between two dates
     * Shows products charged (from slips and purchase orders) vs products sold
     */
    public function stockreport()
    {
        $companyId = $this->Auth->user('company_id');
        $warehouseId = $this->Auth->user('defaultwh');

        // Default dates: current month
        $startDate = date('Y-m-01 00:00:00');
        $endDate = date('Y-m-t 23:59:59');
        $userId = null;
        $selectedUser = null;

        if ($this->request->is('post') || $this->request->is('get')) {
            $data = $this->request->getData();
            if (empty($data)) {
                $data = $this->request->getQuery();
            }
            if (!empty($data['start_date'])) {
                $startDate = $data['start_date'];
            }
            if (!empty($data['end_date'])) {
                $endDate = $data['end_date'];
            }
            if (!empty($data['user_id'])) {
                $userId = (int) $data['user_id'];
            }
        }

        // Ensure date fields have time components
        if (strlen($startDate) <= 10) {
            $startDate .= ' 00:00:00';
        }
        if (strlen($endDate) <= 10) {
            $endDate .= ' 23:59:59';
        }

        $users = $this->getStockReportUsers($companyId, $warehouseId);
        if ($userId) {
            $selectedUser = $this->Slips->Users->find()
                ->contain(['Roles'])
                ->where([
                    'Users.id' => $userId,
                    'Users.statut' => 1,
                    'Users.company_id' => $companyId,
                ])
                ->first();
        }

        $useExitslipOrders = $selectedUser && (int) $selectedUser->role_id === 6;

        // Get all slips (charges) in date range
        $slipsQuery = $this->Slips->find()
            ->contain([
                'Slipproducts' => [
                    'Packs' => [
                        'Packunites' => ['Unites' => ['Parentunites']],
                        'Saletypes',
                        'MeasurementUnits'
                    ]
                ]
            ])
            ->where([
                'Slips.company_id' => $companyId,
                'Slips.created >=' => $startDate,
                'Slips.created <=' => $endDate,
                'Slips.sliptype_id' => 1,
            ]);

        if ($userId) {
            $slipsQuery->where(['Slips.user_id' => $userId]);
        }

        $slips = $slipsQuery->all();

        // Get purchase orders
        $Orders = $this->loadModel('Orders');
        $purchaseQuery = $Orders->find()
            ->contain([
                'Orderpacks' => [
                    'Packs' => [
                        'Packunites' => ['Unites' => ['Parentunites']],
                        'Saletypes',
                        'MeasurementUnits'
                    ]
                ]
            ])
            ->where([
                'Orders.company_id' => $companyId,
                'Orders.ordertype_id' => 2,
                'Orders.created >=' => $startDate,
                'Orders.created <=' => $endDate,
                'Orders.statut >=' => 1,
            ]);
        if ($userId) {
            $purchaseQuery->where(['Orders.user_id' => $userId]);
        }
        $purchaseOrders = $purchaseQuery->all();

        // Get sales orders
        if ($useExitslipOrders) {
            $Exitslips = $this->loadModel('Exitslips');
            $exitslips = $Exitslips->find()
                ->contain([
                    'Shippings.Orders' => function ($q) {
                        return $q->where(['Orders.statut >=' => 1]);
                    },
                    'Shippings.Orders.Orderpacks' => [
                        'Packs' => [
                            'Packunites' => ['Unites' => ['Parentunites']],
                            'Saletypes',
                            'MeasurementUnits'
                        ]
                    ],
                    'Shippings.Orders.Customers',
                    'Shippings.Orders.Users'
                ])
                ->where([
                    'Exitslips.company_id' => $companyId,
                    'Exitslips.user_id' => $userId,
                    'Exitslips.created >=' => $startDate,
                    'Exitslips.created <=' => $endDate,
                ])
                ->all();

            $salesOrders = [];
            foreach ($exitslips as $exitslip) {
                foreach ($exitslip->shippings as $shipping) {
                    foreach ($shipping->orders as $order) {
                        if ((int) $order->ordertype_id === 1) {
                            $salesOrders[] = $order;
                        }
                    }
                }
            }
        } else {
            $salesQuery = $Orders->find()
                ->contain([
                    'Orderpacks' => [
                        'Packs' => [
                            'Packunites' => ['Unites' => ['Parentunites']],
                            'Saletypes',
                            'MeasurementUnits'
                        ]
                    ]
                ])
                ->where([
                    'Orders.company_id' => $companyId,
                    'Orders.ordertype_id' => 1,
                    'Orders.created >=' => $startDate,
                    'Orders.created <=' => $endDate,
                    'Orders.statut >=' => 1,
                ]);

            if ($userId) {
                $salesQuery->where(['Orders.user_id' => $userId]);
            }

            $salesOrders = $salesQuery->all();
        }

        $Prices = $this->loadModel('Prices');
        $MeasurementUnits = $this->loadModel('MeasurementUnits');
        $baseUnitByType = [];
        $basePriceByPack = [];

        $resolveBaseUnitForType = function ($type) use ($MeasurementUnits, &$baseUnitByType) {
            if (!$type) {
                return null;
            }
            if (!array_key_exists($type, $baseUnitByType)) {
                $baseUnitByType[$type] = $MeasurementUnits->find('all')
                    ->where(['conversion_factor' => 1, 'type' => $type])
                    ->first();
            }
            return $baseUnitByType[$type];
        };

        $resolvePackBasePrice = function ($pack) use ($Prices, $companyId, $warehouseId, $resolveBaseUnitForType, &$basePriceByPack) {
            if (!$pack || empty($pack->id)) {
                return 0.0;
            }

            $packId = (int) $pack->id;
            if (array_key_exists($packId, $basePriceByPack)) {
                return $basePriceByPack[$packId];
            }

            $price = $Prices->find('all')
                ->where([
                    'Prices.pack_id' => $packId,
                    'Prices.customertype_id' => 2,
                    'Prices.company_id' => $companyId,
                    'OR' => [
                        ['Prices.warehouse_id' => $warehouseId],
                        ['Prices.warehouse_id IS' => null],
                    ],
                ])
                ->order(['Prices.warehouse_id' => 'DESC', 'Prices.id' => 'ASC'])
                ->first();

            if (!$price) {
                $price = $Prices->find('all')
                    ->where([
                        'Prices.pack_id' => $packId,
                        'Prices.customertype_id' => 2,
                        'Prices.company_id' => $companyId,
                    ])
                    ->order(['Prices.warehouse_id' => 'DESC', 'Prices.id' => 'ASC'])
                    ->first();
            }

            $normalizedPrice = $price ? (float) $price->price : 0.0;

            if (
                isset($pack->measurement_unit)
                && $pack->measurement_unit
                && isset($pack->measurement_unit->type)
                && isset($pack->measurement_quantity)
            ) {
                $factorOne = $resolveBaseUnitForType($pack->measurement_unit->type);
                $conversionFactor = (float) ($pack->measurement_unit->conversion_factor ?: 1);
                $measurementQuantity = (float) ($pack->measurement_quantity ?: 1);
                $divider = $measurementQuantity * $conversionFactor;
                if ($factorOne && $divider > 0) {
                    $normalizedPrice = $normalizedPrice / $divider;
                }
            }

            $basePriceByPack[$packId] = $normalizedPrice;
            return $basePriceByPack[$packId];
        };

        $resolveRealLinePrice = function ($orderpack) use ($resolvePackBasePrice) {
            $pack = $orderpack->pack ?? null;
            if ($pack && (int) ($pack->saletype_id ?? 0) === 4) {
                return $resolvePackBasePrice($pack);
            }
            return (float) $orderpack->price;
        };

        // Calculate totals by product
        $productData = [];

        foreach ($slips as $slip) {
            foreach ($slip->slipproducts as $slipproduct) {
                $packId = $slipproduct->item_id;
                if (!isset($productData[$packId])) {
                    $pack = $slipproduct->pack;

                    $factorOne = null;
                    if ($pack->measurement_unit) {
                        $factorOne = $MeasurementUnits->find('all')
                            ->where(['conversion_factor' => 1, 'type' => $pack->measurement_unit->type])
                            ->first();
                    }

                    $productData[$packId] = [
                        'pack' => $pack,
                        'charged_slips' => 0,
                        'charged_purchases' => 0,
                        'sold' => 0,
                        'sold_amount' => 0,
                        'real_amount' => 0,
                        'measurement_base_unit' => $factorOne,
                    ];
                }
                $productData[$packId]['charged_slips'] += (float) $slipproduct->quantity;
            }
        }

        foreach ($purchaseOrders as $order) {
            foreach ($order->orderpacks as $orderpack) {
                $packId = $orderpack->pack_id;
                if (!isset($productData[$packId])) {
                    $pack = $orderpack->pack;

                    $factorOne = null;
                    if ($pack->measurement_unit) {
                        $factorOne = $MeasurementUnits->find('all')
                            ->where(['conversion_factor' => 1, 'type' => $pack->measurement_unit->type])
                            ->first();
                    }

                    $productData[$packId] = [
                        'pack' => $pack,
                        'charged_slips' => 0,
                        'charged_purchases' => 0,
                        'sold' => 0,
                        'sold_amount' => 0,
                        'real_amount' => 0,
                        'measurement_base_unit' => $factorOne,
                    ];
                }
                $productData[$packId]['charged_purchases'] += (float) $orderpack->quantity;
            }
        }

        foreach ($salesOrders as $order) {
            foreach ($order->orderpacks as $orderpack) {
                $packId = $orderpack->pack_id;
                $orderpackStatut = isset($orderpack->statut) ? (int) $orderpack->statut : null;
                if (!isset($productData[$packId])) {
                    $pack = $orderpack->pack;

                    $factorOne = null;
                    if ($pack->measurement_unit) {
                        $factorOne = $resolveBaseUnitForType($pack->measurement_unit->type);
                    }
                    $productData[$packId] = [
                        'pack' => $pack,
                        'charged_slips' => 0,
                        'charged_purchases' => 0,
                        'sold' => 0,
                        'sold_amount' => 0,
                        'real_amount' => 0,
                        'measurement_base_unit' => $factorOne,
                    ];
                }
                if ($useExitslipOrders) {
                    $productData[$packId]['charged_slips'] += (float) $orderpack->quantity;
                }
                $productData[$packId]['sold'] += (float) $orderpack->quantity;
                $productData[$packId]['sold_amount'] += ((float) $orderpack->quantity * (float) $orderpack->price);
                $productData[$packId]['real_amount'] += ((float) $orderpack->quantity * $resolveRealLinePrice($orderpack));
            }
        }

        // Calculate order amounts
        $slipOrderAmount = 0;
        $purchaseOrderAmount = 0;
        $salesOrderAmount = 0;
        $salesRealAmount = 0;

        foreach ($slips as $slip) {
            $total_amount = 0;
            foreach ($slip->slipproducts as $slipproduct) {
                $total_amount += ((float) $slipproduct->quantity * (float) $slipproduct->price);
            }
            $slip->total_amount = $total_amount;
            $slipOrderAmount += (float) $slip->total_amount;
        }

        foreach ($purchaseOrders as $order) {
            $total_amount = 0;
            foreach ($order->orderpacks as $orderpack) {
                $total_amount += ((float) $orderpack->quantity * (float) $orderpack->price);
            }
            $order->total_amount = $total_amount;
            $purchaseOrderAmount += (float) $order->total_amount;
        }

        foreach ($salesOrders as $order) {
            $total_amount = 0;
            $real_amount = 0;
            $purchase_amount_for_sales = 0;
            foreach ($order->orderpacks as $orderpack) {
                $lineAmount = ((float) $orderpack->quantity * $orderpack->price);
                $lineRealAmount = ((float) $orderpack->quantity * $resolveRealLinePrice($orderpack));
                if ($useExitslipOrders) {
                    $orderpackStatut = isset($orderpack->statut) ? (int) $orderpack->statut : null;
                    if ($orderpackStatut === 6) {
                        $total_amount += $lineAmount;
                        $real_amount += $lineRealAmount;
                        continue;
                    }
                    if ($orderpackStatut === 8) {
                        $purchase_amount_for_sales += $lineAmount;
                    }
                    continue;
                }
                $total_amount += $lineAmount;
                $real_amount += $lineRealAmount;
            }
            $order->total_amount = $total_amount;
            $order->real_amount = $real_amount;
            $salesOrderAmount += (float) $order->total_amount;
            $salesRealAmount += (float) $order->real_amount;
            if ($useExitslipOrders) {
                $purchaseOrderAmount += $purchase_amount_for_sales;
                $order->purchase_amount = $purchase_amount_for_sales;
            }
        }

        // Calculate remaining stock amount
        $totalChargedAmount = $slipOrderAmount + $purchaseOrderAmount;
        $remainingStockAmount = $totalChargedAmount - $salesOrderAmount;

        foreach ($productData as $packId => &$data) {
            $data['total_charged'] = $data['charged_slips'] + $data['charged_purchases'];
            $data['remaining_stock'] = $data['total_charged'] - $data['sold'];
        }

        // Get warehouse info
        $warehouse = $this->Slips->Warehouses->get($warehouseId);

        // Get company info
        $Companies = $this->loadModel('Companies');
        $company = $Companies->get($companyId);

        $this->set(compact('productData', 'startDate', 'endDate', 'userId', 'selectedUser', 'users', 'warehouse', 'company', 'slips', 'purchaseOrders', 'salesOrders', 'slipOrderAmount', 'purchaseOrderAmount', 'salesOrderAmount', 'salesRealAmount', 'remainingStockAmount'));
    }

    /**
     * Print stock report
     * Generates a printable PDF view of the stock report
     */
    public function stockreportprint()
    {
        $companyId = $this->Auth->user('company_id');
        $warehouseId = $this->Auth->user('defaultwh');

        // Get dates from query parameters
        $startDate = $this->request->getQuery('start_date', date('Y-m-01 00:00:00'));
        $endDate = $this->request->getQuery('end_date', date('Y-m-t 23:59:59'));
        $userId = $this->request->getQuery('user_id');

        // Ensure date fields have time components
        if (strlen($startDate) <= 10) {
            $startDate .= ' 00:00:00';
        }
        if (strlen($endDate) <= 10) {
            $endDate .= ' 23:59:59';
        }

        $users = $this->getStockReportUsers($companyId, $warehouseId);
        $selectedUser = null;
        if ($userId) {
            $selectedUser = $this->Slips->Users->find()
                ->contain(['Roles'])
                ->where([
                    'Users.id' => $userId,
                    'Users.statut' => 1,
                    'Users.company_id' => $companyId,
                ])
                ->first();
        }

        // Get all slips (charges) in date range
        $slipsQuery = $this->Slips->find()
            ->contain([
                'Slipproducts' => [
                    'Packs' => [
                        'Packunites' => ['Unites' => ['Parentunites']],
                        'Saletypes',
                        'MeasurementUnits'
                    ]
                ]
            ])
            ->where([
                'Slips.created >=' => $startDate,
                'Slips.created <=' => $endDate,
                'Slips.sliptype_id' => 1,
            ]);

        if ($userId) {
            $slipsQuery->where(['Slips.user_id' => $userId]);
        }

        $slips = $slipsQuery->all();
        // Get purchase orders
        $Orders = $this->loadModel('Orders');
        $purchaseQuery = $Orders->find()
            ->contain([
                'Orderpacks' => [
                    'Packs' => [
                        'Packunites' => ['Unites' => ['Parentunites']],
                        'Saletypes',
                        'MeasurementUnits'
                    ]
                ]
            ])
            ->where([
                'Orders.ordertype_id' => 2,
                'Orders.created >=' => $startDate,
                'Orders.created <=' => $endDate,
            ]);
        if ($userId) {
            $purchaseQuery->where(['Orders.user_id' => $userId]);
        }

        $useExitslipOrders = $selectedUser && (int) $selectedUser->role_id === 6;

        // Get sales orders
        if ($useExitslipOrders) {
            $Exitslips = $this->loadModel('Exitslips');
            $exitslips = $Exitslips->find()
                ->contain([
                    'Shippings.Orders' => function ($q) {
                        return $q->where(['Orders.statut >=' => 1]);
                    },
                    'Shippings.Orders.Orderpacks' => [
                        'Packs' => [
                            'Packunites' => ['Unites' => ['Parentunites']],
                            'Saletypes',
                            'MeasurementUnits'
                        ]
                    ],
                    'Shippings.Orders.Customers',
                    'Shippings.Orders.Users'
                ])
                ->where([
                    'Exitslips.company_id' => $companyId,
                    'Exitslips.user_id' => $userId,
                    'Exitslips.created >=' => $startDate,
                    'Exitslips.created <=' => $endDate,
                ])
                ->all();

            $salesOrders = [];
            foreach ($exitslips as $exitslip) {
                foreach ($exitslip->shippings as $shipping) {
                    foreach ($shipping->orders as $order) {
                        if ((int) $order->ordertype_id === 1) {
                            $salesOrders[] = $order;
                        }
                    }
                }
            }
        } else {
            $salesQuery = $Orders->find()
                ->contain([
                    'Orderpacks' => [
                        'Packs' => [
                            'Packunites' => ['Unites' => ['Parentunites']],
                            'Saletypes',
                            'MeasurementUnits'
                        ]
                    ]
                ])
                ->where([
                    'Orders.ordertype_id' => 1,
                    'Orders.created >=' => $startDate,
                    'Orders.created <=' => $endDate,
                ]);

            if ($userId) {
                $salesQuery->where(['Orders.user_id' => $userId]);
            }

            $salesOrders = $salesQuery->all();
        }
        $purchaseOrders = $purchaseQuery->all();

        $Prices = $this->loadModel('Prices');
        $MeasurementUnits = $this->loadModel('MeasurementUnits');
        $baseUnitByType = [];
        $basePriceByPack = [];

        $resolveBaseUnitForType = function ($type) use ($MeasurementUnits, &$baseUnitByType) {
            if (!$type) {
                return null;
            }
            if (!array_key_exists($type, $baseUnitByType)) {
                $baseUnitByType[$type] = $MeasurementUnits->find('all')
                    ->where(['conversion_factor' => 1, 'type' => $type])
                    ->first();
            }
            return $baseUnitByType[$type];
        };

        $resolvePackBasePrice = function ($pack) use ($Prices, $companyId, $warehouseId, $resolveBaseUnitForType, &$basePriceByPack) {
            if (!$pack || empty($pack->id)) {
                return 0.0;
            }

            $packId = (int) $pack->id;
            if (array_key_exists($packId, $basePriceByPack)) {
                return $basePriceByPack[$packId];
            }

            $price = $Prices->find('all')
                ->where([
                    'Prices.pack_id' => $packId,
                    'Prices.customertype_id' => 2,
                    'Prices.company_id' => $companyId,
                    'OR' => [
                        ['Prices.warehouse_id' => $warehouseId],
                        ['Prices.warehouse_id IS' => null],
                    ],
                ])
                ->order(['Prices.warehouse_id' => 'DESC', 'Prices.id' => 'ASC'])
                ->first();

            if (!$price) {
                $price = $Prices->find('all')
                    ->where([
                        'Prices.pack_id' => $packId,
                        'Prices.customertype_id' => 2,
                        'Prices.company_id' => $companyId,
                    ])
                    ->order(['Prices.warehouse_id' => 'DESC', 'Prices.id' => 'ASC'])
                    ->first();
            }

            $normalizedPrice = $price ? (float) $price->price : 0.0;

            // Conversion du prix vers l'unité de base (conversion_factor = 1)
            // pour obtenir un montant réel comparable entre unités.

            if (
                isset($pack->measurement_unit)
                && $pack->measurement_unit
                && isset($pack->measurement_unit->type)
                && isset($pack->measurement_quantity)
            ) {
                $factorOne = $resolveBaseUnitForType($pack->measurement_unit->type);
                $conversionFactor = (float) ($pack->measurement_unit->conversion_factor ?: 1);
                $measurementQuantity = (float) ($pack->measurement_quantity ?: 1);
                $divider = $measurementQuantity * $conversionFactor;
                if ($factorOne && $divider > 0) {
                    $normalizedPrice = $normalizedPrice / $divider;
                }
            }

            $basePriceByPack[$packId] = $normalizedPrice;
            return $basePriceByPack[$packId];
        };

        // Le prix "réel" (prix de base converti) s'applique uniquement aux packs de type mesure (saletype_id = 4).
        $resolveRealLinePrice = function ($orderpack) use ($resolvePackBasePrice) {
            $pack = $orderpack->pack ?? null;
            if ($pack && (int) ($pack->saletype_id ?? 0) === 4) {
                return $resolvePackBasePrice($pack);
            }
            return (float) $orderpack->price;
        };

        // Calculate totals by product
        $productData = [];

        foreach ($slips as $slip) {
            foreach ($slip->slipproducts as $slipproduct) {
                $packId = $slipproduct->item_id;
                if (!isset($productData[$packId])) {
                    $pack = $slipproduct->pack;

                    // Get measurement unit conversion
                    $MeasurementUnits = $this->loadModel('MeasurementUnits');
                    $factorOne = null;
                    if ($pack->measurement_unit) {
                        $factorOne = $MeasurementUnits->find('all')
                            ->where(['conversion_factor' => 1, 'type' => $pack->measurement_unit->type])
                            ->first();
                    }

                    $productData[$packId] = [
                        'pack' => $pack,
                        'charged_slips' => 0,
                        'charged_purchases' => 0,
                        'sold' => 0,
                        'sold_amount' => 0,
                        // Montant réel calculé avec le tarif customertype_id = 2 en unité de base.
                        'real_amount' => 0,
                        'measurement_base_unit' => $factorOne,
                    ];
                }
                $productData[$packId]['charged_slips'] += (float) $slipproduct->quantity;
            }
        }

        foreach ($purchaseOrders as $order) {
            foreach ($order->orderpacks as $orderpack) {
                $packId = $orderpack->pack_id;
                if (!isset($productData[$packId])) {
                    $pack = $orderpack->pack;

                    // Get measurement unit conversion
                    $MeasurementUnits = $this->loadModel('MeasurementUnits');
                    $factorOne = null;
                    if ($pack->measurement_unit) {
                        $factorOne = $MeasurementUnits->find('all')
                            ->where(['conversion_factor' => 1, 'type' => $pack->measurement_unit->type])
                            ->first();
                    }

                    $productData[$packId] = [
                        'pack' => $pack,
                        'charged_slips' => 0,
                        'charged_purchases' => 0,
                        'sold' => 0,
                        'sold_amount' => 0,
                        // Initialisé ici aussi pour éviter "Undefined index: real_amount".
                        'real_amount' => 0,
                        'measurement_base_unit' => $factorOne,
                    ];
                }
                $productData[$packId]['charged_purchases'] += (float) $orderpack->quantity;
            }
        }

        foreach ($salesOrders as $order) {
            foreach ($order->orderpacks as $orderpack) {
                $packId = $orderpack->pack_id;
                $orderpackStatut = isset($orderpack->statut) ? (int) $orderpack->statut : null;
                if (!isset($productData[$packId])) {
                    $pack = $orderpack->pack;

                    // Get measurement unit conversion
                    $factorOne = null;
                    if ($pack->measurement_unit) {
                        $factorOne = $resolveBaseUnitForType($pack->measurement_unit->type);
                    }
                    $productData[$packId] = [
                        'pack' => $pack,
                        'charged_slips' => 0,
                        'charged_purchases' => 0,
                        'sold' => 0,
                        'sold_amount' => 0,
                        'real_amount' => 0,
                        'measurement_base_unit' => $factorOne,
                    ];
                }
                if ($useExitslipOrders) {
                    $productData[$packId]['charged_slips'] += (float) $orderpack->quantity;
                }
                $productData[$packId]['sold'] += (float) $orderpack->quantity;
                // Ancienne logique conservée: montant issu du prix de la ligne de vente.
                $productData[$packId]['sold_amount'] += ((float) $orderpack->quantity * (float) $orderpack->price);
                // Nouveau calcul: montant réel avec prix customertype_id=2 converti en unité de base.
                $productData[$packId]['real_amount'] += ((float) $orderpack->quantity * $resolveRealLinePrice($orderpack));
            }
        }

        // Calculate order amounts
        $slipOrderAmount = 0;
        $purchaseOrderAmount = 0;
        $salesOrderAmount = 0;
        $salesRealAmount = 0;

        foreach ($slips as $slip) {
            $total_amount = 0;
            foreach ($slip->slipproducts as $slipproduct) {
                $total_amount += ((float) $slipproduct->quantity * (float) $slipproduct->price);
            }
            $slip->total_amount = $total_amount;
            $slipOrderAmount += (float) $slip->total_amount;
        }

        foreach ($purchaseOrders as $order) {
            $total_amount = 0;
            foreach ($order->orderpacks as $orderpack) {
                $total_amount += ((float) $orderpack->quantity * (float) $orderpack->price);
            }
            $order->total_amount = $total_amount;
            $purchaseOrderAmount += (float) $order->total_amount;
        }

        foreach ($salesOrders as $order) {
            $total_amount = 0;
            $real_amount = 0;
            $purchase_amount_for_sales = 0;
            foreach ($order->orderpacks as $orderpack) {
                $lineAmount = ((float) $orderpack->quantity * $orderpack->price);
                $lineRealAmount = ((float) $orderpack->quantity * $resolveRealLinePrice($orderpack));
                if ($useExitslipOrders) {
                    $orderpackStatut = isset($orderpack->statut) ? (int) $orderpack->statut : null;
                    if ($orderpackStatut === 6) {
                        $total_amount += $lineAmount;
                        $real_amount += $lineRealAmount;
                        continue;
                    }
                    if ($orderpackStatut === 8) {
                        $purchase_amount_for_sales += $lineAmount;
                    }
                    continue;
                }
                $total_amount += $lineAmount;
                $real_amount += $lineRealAmount;
            }
            $order->total_amount = $total_amount;
            $order->real_amount = $real_amount;
            $salesOrderAmount += (float) $order->total_amount;
            $salesRealAmount += (float) $order->real_amount;
            if ($useExitslipOrders) {
                $purchaseOrderAmount += $purchase_amount_for_sales;
                $order->purchase_amount = $purchase_amount_for_sales;
            }
        }

        // Calculate remaining stock amount
        $totalChargedAmount = $slipOrderAmount + $purchaseOrderAmount;
        $remainingStockAmount = $totalChargedAmount - $salesOrderAmount;

        foreach ($productData as $packId => &$data) {
            $data['total_charged'] = $data['charged_slips'] + $data['charged_purchases'];
            $data['remaining_stock'] = $data['total_charged'] - $data['sold'];
        }
        // Get warehouse info
        $warehouse = $this->Slips->Warehouses->get($warehouseId);

        // Get company info
        $Companies = $this->loadModel('Companies');
        $company = $Companies->get($companyId);
        ini_set('max_execution_time', '300');
        ini_set("pcre.backtrack_limit", "5000000");
        $this->set(compact('productData', 'startDate', 'endDate', 'userId', 'selectedUser', 'warehouse', 'company', 'slips', 'purchaseOrders', 'salesOrders', 'slipOrderAmount', 'purchaseOrderAmount', 'salesOrderAmount', 'salesRealAmount', 'remainingStockAmount'));
    }

    /**
     * Generate inventory PDF for a slip
     *
     * @param string|null $id Slip id.
     * @return \Cake\Http\Response|null
     */
    public function inventory($id = null)
    {
        $slip = $this->Slips->get($id, [
            'contain' => [
                'Users',
                'Warehouses',
                'Whnatures',
                'Slipproducts' => [
                    'Packs' => [
                        'Packunites' => ['Unites' => ['Parentunites']],
                        'MeasurementUnits'
                    ],
                    'Products' => [
                        'Productunites' => ['Unites' => ['Parentunites']],
                        'MeasurementUnits'
                    ]
                ]
            ]
        ]);
        
        ini_set('max_execution_time', '300');
        ini_set("pcre.backtrack_limit", "5000000");

        $this->set(compact('slip'));
    }
}
