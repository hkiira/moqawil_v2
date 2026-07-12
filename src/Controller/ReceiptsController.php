<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Receipts Controller
 *
 * @property \App\Model\Table\ReceiptsTable $Receipts
 *
 * @method \App\Model\Entity\Receipt[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])

 1: En attente de confirmation
 2: En attente de validation
 3: Validée

  */
class ReceiptsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $whusers = $this->Receipts->Users->Whusers->find('all')->contain(['Users' => function ($q) {
            return $q->where(['Users.statut' => 1, ['OR' => [['Users.role_id' => 1], ['Users.role_id' => 2], ['Users.role_id' => 7], ['Users.role_id' => 8]]]]); }])->where(['Whusers.warehouse_id' => $this->Auth->user('defaultwh')]);
        $users = [];
        foreach ($whusers as $whuser) {
            $users[$whuser->user->id] = $whuser->user->firstname . ' ' . $whuser->user->lastname;
        }
        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id Receipt id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function validate($id = null, $validate = null)
    {

        $receipt = $this->Receipts->get($id, [
            'contain' => ['Supporderproducts.Supplierorders', 'Supporderproducts.Products.Whproducts', 'Supporderproducts.Products.MeasurementUnits', 'Supporderproducts.Productunites.Unites.Parentunites'],
        ]);
        if ($receipt->statut == 2) {
            if ($this->request->is(['patch', 'post', 'put'])) {
                $datas = $this->request->getData('supporderproducts');

                $newsupporderproducts = [];
                foreach ($datas as $key => $upporpro) {
                    $data['id'] = $receipt->id;
                    $data['statut'] = 3;
                    $supporderproduct = $this->Receipts->Supporderproducts->get($upporpro['id'], ['contain' => ['Supplierorders']]);

                    if ($supporderproduct->quantity < ($upporpro['quantity'] * $upporpro['qtepercs'])) {
                        $this->Flash->success(__('Merci de vérifier les quantités avant de valider.'));
                        return $this->redirect(['action' => 'validate', $receipt->id]);
                    } elseif ($supporderproduct->quantity > ($upporpro['quantity'] * $upporpro['qtepercs'])) {

                        $oldsupporderproduct = $this->Receipts->Supporderproducts->find('all')->where(['Supporderproducts.product_id' => $supporderproduct->product_id, 'Supporderproducts.productunite_id' => $supporderproduct->productunite_id, 'Supporderproducts.supplierorder_id' => $supporderproduct->supplierorder_id, 'Supporderproducts.statut' => 1])->last();
                        if ($oldsupporderproduct) {
                            $newsupporderproducts[] = [
                                'id' => $oldsupporderproduct->id,
                                'quantity' => $oldsupporderproduct->quantity + ($supporderproduct->quantity - $upporpro['quantity'] * $upporpro['qtepercs']),
                            ];

                        } else {
                            $newsupporderproducts[] = [
                                'supplierorder_id' => $supporderproduct->supplierorder_id,
                                'product_id' => $supporderproduct->product_id,
                                'productunite_id' => $supporderproduct->productunite_id,
                                'price' => $supporderproduct->price,
                                'supplier_id' => $supporderproduct->supplier_id,
                                'company_id' => $supporderproduct->company_id,
                                'user_id' => $supporderproduct->user_id,
                                'quantity' => $supporderproduct->quantity - ($upporpro['quantity'] * $upporpro['qtepercs']),
                            ];
                        }
                        $supporderproduct->quantity = ($upporpro['quantity'] * $upporpro['qtepercs']);

                    }
                    $data['supporderproducts'][$key]['id'] = $supporderproduct->id;
                    $data['supporderproducts'][$key]['statut'] = 3;
                    $data['supporderproducts'][$key]['product']['id'] = $supporderproduct->product_id;

                    // rechercher le stock du produit
                    $warehouse = $this->Receipts->Supplierorders->Warehouses->find('all')->where(['whnature_id' => 1, 'warehouse_id' => $supporderproduct->supplierorder->warehouse_id, 'whtype_id' => 2])->last();
                    $whproducts = $this->Receipts->Supporderproducts->Products->Whproducts->find('all')->where(['warehouse_id' => $warehouse->id, 'item_id' => $supporderproduct->product_id, 'item_type' => 'Product'])->last();
                    $data['supporderproducts'][$key]['product']['whproducts'][0]['id'] = $whproducts->id;
                    $data['supporderproducts'][$key]['product']['whproducts'][0]['quantity'] = $whproducts->quantity + $supporderproduct->quantity;

                }
                $receipt = $this->Receipts->patchEntity($receipt, $data, ['associated' => ['Supporderproducts.Products.Whproducts']]);

                if ($receipt->supporderproducts) {

                    if ($this->Receipts->save($receipt)) {
                        // Log stock movements for the receipt
                        $this->loadModel('Whproducts');
                        $this->loadModel('StockMovements');
                        foreach ($receipt->supporderproducts as $sop) {
                            if ($sop->statut == 3 && $sop->supplierorder) {
                                // Find warehouse
                                $warehouse = $this->Receipts->Supplierorders->Warehouses->find('all')
                                    ->where(['whnature_id' => 1, 'warehouse_id' => $sop->supplierorder->warehouse_id, 'whtype_id' => 2])
                                    ->last();
                                if ($warehouse) {
                                    $whproduct = $this->Whproducts->find('all')
                                        ->where(['warehouse_id' => $warehouse->id, 'item_id' => $sop->product_id, 'item_type' => 'Product'])
                                        ->last();
                                    if ($whproduct) {
                                        $stockMovement = $this->StockMovements->newEntity([
                                            'item_id' => $sop->product_id,
                                            'item_type' => 'Product',
                                            'warehouse_id' => $warehouse->id,
                                            'quantity_change' => $sop->quantity,
                                            'balance_after_movement' => $whproduct->quantity,
                                            'movement_type' => 'supplier_receipt',
                                            'user_id' => $this->Auth->user('id'),
                                            'company_id' => $this->Auth->user('company_id'),
                                            'notes' => 'Stock increment from supplier receipt validation (Receipt ID: ' . $receipt->id . ')',
                                        ]);
                                        $this->StockMovements->save($stockMovement);
                                    }
                                }
                            }
                        }
                        foreach ($newsupporderproducts as $key => $orderproduct) {
                            if (isset($orderproduct['id'])) {
                                $newsorderpack = $this->Receipts->Supporderproducts->get($orderproduct['id']);
                                $newsorderpack = $this->Receipts->Supporderproducts->patchEntity($newsorderpack, $orderproduct);
                                $this->Receipts->Supporderproducts->save($newsorderpack);

                            } else {
                                $newsorderpack = $this->Receipts->Supporderproducts->newEntity();
                                $newsorderpack = $this->Receipts->Supporderproducts->patchEntity($newsorderpack, $orderproduct);
                                $this->Receipts->Supporderproducts->save($newsorderpack);
                            }
                        }
                        $supplierorderpro = $this->Receipts->Supporderproducts->find('all')->where(['supplierorder_id' => $receipt->supplierorder_id, 'statut' => 1])->last();
                        $supplierorder = $this->Receipts->Supplierorders->get($receipt->supplierorder_id);
                        if (empty($supplierorderpro)) {
                            $supplierorder->statut = 3;
                        } else {
                            $supplierorder->statut = 2;
                        }
                        if ($this->Receipts->Supplierorders->save($supplierorder)) {
                            $this->Flash->success(__('Le bon de réception a été validé.'));
                            return $this->redirect(['action' => 'index']);
                        }
                    }
                } else {
                    $this->Flash->error(__('Le bon de réception doit avoir au moins 1 article. Veuillez réessayer.'));
                }
                if ($this->Receipts->save($receipt)) {
                    $this->Flash->success(__('Le bon de réception a été modifié.'));
                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('Le bon de réception n\'a pas pu être modifié. Veuillez réessayer.'));
            }
            $this->set(compact('receipt'));
        } else {
            $this->Flash->error(__('Vous n\'avez pas les droits pour modifier ce bon de réception. Veuillez réessayer.'));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function view($id = null)
    {
        $receipt = $this->Receipts->get($id, [
            'contain' => ['Suppliers', 'Users', 'Supplierorders', 'Companies', 'Supporderproducts.Packs.Packunites.Unites.Parentunites'],
        ]);

        $this->set('receipt', $receipt);
    }

    public function print($id = null)
    {
        $receipt = $this->Receipts->get($id, [
            'contain' => ['Suppliers.Adresses.Cities', 'Users', 'Supplierorders', 'Companies', 'Supporderproducts.Products.MeasurementUnits', 'Supporderproducts.Productunites.Unites.Parentunites'],
        ]);

        $this->set('receipt', $receipt);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $receipt = $this->Receipts->newEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();

            $supporderproducts = [];
            foreach ($this->request->getData('supporderproducts') as $key => $supporderproduct) {
                if ($supporderproduct['quantity'] == 0) {
                    unset($datas['supporderproducts'][$key]);
                }
            }
            foreach ($datas['supporderproducts'] as $key => $orderproduct) {
                $supporderproduct = $this->Receipts->Supporderproducts->get($orderproduct['id']);
                if ($supporderproduct->quantity <= ($orderproduct['quantity'])) {
                    $datas['supporderproducts'][$key]['statut'] = 2;
                    $supporderproducts[] = [
                        'id' => $orderproduct['id'],
                        'quantity' => ($orderproduct['quantity']),
                        'statut' => 2
                    ];
                } elseif (($orderproduct['quantity']) < $supporderproduct->quantity) {
                    $supporderproducts[] = [
                        'supplierorder_id' => $supporderproduct->supplierorder_id,
                        'pack_id' => $supporderproduct->pack_id,
                        'price' => $supporderproduct->price,
                        'supplier_id' => $supporderproduct->supplier_id,
                        'company_id' => $supporderproduct->company_id,
                        'user_id' => $supporderproduct->user_id,
                        'quantity' => $supporderproduct->quantity - ($orderproduct['quantity']),
                    ];
                    $supporderproducts[] = [
                        'id' => $orderproduct['id'],
                        'quantity' => $orderproduct['quantity'],
                        'statut' => 2
                    ];

                }
            }
            unset($datas['supporderproducts']);
            $receipt = $this->Receipts->patchEntity($receipt, $datas);

            $supplierid = $this->Receipts->Supplierorders->get($receipt->supplierorder_id);

            $receipt->supplier_id = $supplierid->supplier_id;
            $code = $this->Receipts->Companies->Companycodes->find('all')->where(['controleur' => 'Receipts', 'company_id' => $this->Auth->user('company_id')])->last();
            $receipt->code = $code->prefixe . ($code->compteur + 1);
            $receipt->user_id = $this->Auth->user('id');
            $receipt->company_id = $this->Auth->user('company_id');
            $receipt->statut = 2;

            if ($this->Receipts->save($receipt)) {

                $code->compteur = $code->compteur + 1;
                if ($this->Receipts->Companies->Companycodes->save($code)) {
                    foreach ($supporderproducts as $key => $orderpack) {
                        if (isset($orderpack['id'])) {
                            $supporderproduct = $this->Receipts->Supporderproducts->get($orderpack['id']);
                            $supporderproduct = $this->Receipts->Supporderproducts->patchEntity($supporderproduct, $orderpack);
                            $supporderproduct->receipt_id = $receipt->id;
                            $this->Receipts->Supporderproducts->save($supporderproduct);
                        } else {
                            $supporderproduct = $this->Receipts->Supporderproducts->newEntity();
                            $supporderproduct = $this->Receipts->Supporderproducts->patchEntity($supporderproduct, $orderpack);
                            $this->Receipts->Supporderproducts->save($supporderproduct);
                        }
                    }
                }

                $this->Flash->success(__('Le bon de réception a été enregistré.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Le bon de réception n\'a pas pu être enregistré. Veuillez réessayer.'));
        }
        $warehouses = $this->Receipts->Supplierorders->warehouses->find('list')->where(['id' => $this->Auth->user('defaultwh'), 'whtype_id' => 1, 'company_id ' => $this->Auth->user('company_id')]);
        $this->set(compact('receipt', 'warehouses'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Receipt id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null, $validate = null)
    {
        $receipt = $this->Receipts->get($id, [
            'contain' => ['Supporderproducts.Supplierorders', 'Supporderproducts.Packs.Packunites.Unites.Parentunites'],
        ]);
        if ($receipt->statut == 1) {
            if ($validate == 'validation') {
                if ($receipt->supporderproducts) {
                    $receipt->statut = 2;
                    if ($this->Receipts->save($receipt)) {
                        $this->Flash->success(__('Le bon de réception est en attente de validation.'));
                        return $this->redirect(['action' => 'index']);
                    }
                } else {
                    $this->Flash->error(__('Le bon de réception doit avoir au moins 1 article. Veuillez réessayer.'));
                }
                $this->Flash->error(__('Le bon de réception n\'a pas pu être validé. Veuillez réessayer.'));
            }
            $this->set(compact('receipt'));
        } else {
            $this->Flash->error(__('Vous n\'avez pas les droits pour modifier ce bon de réception. Veuillez réessayer.'));
            return $this->redirect(['action' => 'index']);
        }
    }
    public function supplierorder($select = null)
    {
        //$this->request->allowMethod('ajax');
        $keyword = $this->request->getQuery('keyword');
        $supplierorders = $this->Receipts->Suppliers->Supplierorders->find('all')->contain(['Suppliers', 'Supporderproducts' => function ($q) {
            return $q->where(['Supporderproducts.statut' => 1]); }])->where(['Supplierorders.warehouse_id' => $keyword]);
        $this->set(compact('supplierorders', 'select'));
    }
    /**
     * Delete method
     *
     * @param string|null $id Receipt id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $receipt = $this->Receipts->get($id);
        if ($this->Receipts->delete($receipt)) {
            $this->Flash->success(__('The receipt has been deleted.'));
        } else {
            $this->Flash->error(__('The receipt could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function search($start = null, $end = null)
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
        $searchStatus = ($this->request->getData('query.status') !== NULL) ? $this->request->getData('query.status') : -1; // Search value
        $searchDate = strtolower($this->request->getData('query.date')); // Search value

        switch ($columnName) {
            case 'user':
                $columnName = "Users.firstname";
                break;
            case 'code':
                $columnName = "Receipts.code";
                break;
            case 'supplier':
                $columnName = "Suppliers.created";
                break;
            case 'created':
                $columnName = "Receipts.created";
                break;
            case 'status':
                $columnName = "Receipts.statut";
                break;
            default:
                $columnName = "Receipts.created";
                $columnSort = "desc";
                break;
        }
        $pos = stripos($searchDate, ";");
        $dateend = substr($searchDate, $pos + 1);
        $datestart = substr($searchDate, 0, $pos);

        $sel = $this->Receipts->find('all')->contain(['Suppliers', 'Supporderproducts', 'Users', 'Supplierorders.Warehouses']);
        $sel->where(['Receipts.warehouse_id' => $this->Auth->user('defaultwh')]);

        ## Search 
        $empQuery = $this->Receipts->find('all')->contain(['Suppliers', 'Supporderproducts', 'Users', 'Supplierorders.Warehouses']);
        $empQuery->where(['Receipts.warehouse_id' => $this->Auth->user('defaultwh')]);
        if ($start && $end) {
            $sel->where(['AND' => ['DATE(Receipts.created) <= ' => $end, 'DATE(Receipts.created) >= ' => $start]]);
            $empQuery->where(['AND' => ['DATE(Receipts.created) <= ' => $end, 'DATE(Receipts.created) >= ' => $start]]);
        }
        $empQuery->order([$columnName => $columnSort]);

        if ($searchValue != '') {
            $sel->where([
                "OR" => [
                    ['Receipts.code LIKE' => '%' . $searchValue . '%'],
                    ['lower(Receipts.code) LIKE' => '%' . $searchValue . '%'],
                    ['Suppliers.name LIKE' => '%' . $searchValue . '%'],
                    ['Users.firstname LIKE' => '%' . $searchValue . '%'],
                    ['Users.lastname LIKE' => '%' . $searchValue . '%'],
                    ['lower(Users.firstname) LIKE' => '%' . $searchValue . '%'],
                    ['lower(Users.lastname) LIKE' => '%' . $searchValue . '%'],
                    ['lower(Suppliers.name) LIKE' => '%' . $searchValue . '%'],
                    ['lower(Users.lastname) LIKE' => '%' . $searchValue . '%'],
                    ['Users.lastname LIKE' => '%' . $searchValue . '%']
                ]
            ]);
            $empQuery->where([
                "OR" => [
                    ['Receipts.code LIKE' => '%' . $searchValue . '%'],
                    ['lower(Receipts.code) LIKE' => '%' . $searchValue . '%'],
                    ['Suppliers.name LIKE' => '%' . $searchValue . '%'],
                    ['lower(Suppliers.name) LIKE' => '%' . $searchValue . '%'],
                    ['Users.firstname LIKE' => '%' . $searchValue . '%'],
                    ['Users.lastname LIKE' => '%' . $searchValue . '%'],
                    ['lower(Users.firstname) LIKE' => '%' . $searchValue . '%'],
                    ['lower(Users.lastname) LIKE' => '%' . $searchValue . '%'],
                    ['lower(Users.lastname) LIKE' => '%' . $searchValue . '%'],
                    ['Users.lastname LIKE' => '%' . $searchValue . '%']
                ]
            ]);
        }
        if ($datestart && $dateend) {
            $empQuery->where(['DATE(Receipts.created) <= ' => $dateend, 'DATE(Receipts.created) >= ' => $datestart]);
            $sel->where(['DATE(Receipts.created) <= ' => $dateend, 'DATE(Receipts.created) >= ' => $datestart]);

        }
        if ($searchUser) {
            $empQuery->where(['Receipts.user_id' => $searchUser]);
            $sel->where(['Receipts.user_id' => $searchUser]);
        }
        if ($searchStatus > -1) {
            $empQuery->where(['Receipts.statut' => $searchStatus]);
            $sel->where(['Receipts.statut' => $searchStatus]);
        }

        $empQuery->limit($perpage);
        $empQuery->page($page);
        $sel->select(['count' => $sel->func()->count('*')]);
        $total = $sel->last()->count;

        $data = [];
        foreach ($empQuery as $key => $receipt) {
            $photo = $this->Receipts->Suppliers->Photos->find('all')->where(['controleur' => 'suppliers', 'objectid' => $receipt->supplier->id])->order(['created' => 'ASC'])->last();
            $img = Router::Url('/') . 'webroot/img/unvailable.jpg';
            if ($photo) {
                $img = Router::Url('/') . $photo->dir . '/' . $photo->title;
            }
            $date = $receipt->created->nice('Europe/Paris', 'fr-FR');
            $data[] = [
                "id" => $receipt->id,
                "code" => $receipt->code,
                "user" => $receipt->user->firstname . ' ' . $receipt->user->lastname,
                "img" => $img,
                "name" => $receipt->supplier->name,
                "phone" => $receipt->supplier->phone,
                "products" => count($receipt->supporderproducts),
                "created" => $date,
                "status" => $receipt->statut,
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

    public function instanceord()
    {
        $supplierorderid = $this->request->getQuery('keyword');

        $supplierorder = $this->Receipts->Supporderproducts->Supplierorders->get($supplierorderid, ['contain' => ['Supporderproducts' => function ($q) {
            return $q->where(['Supporderproducts.statut' => 1]); }, 'Supporderproducts.Products.MeasurementUnits', 'Supporderproducts.Productunites.Unites.Parentunites']]);
        $productselects = [];
        foreach ($supplierorder->supporderproducts as $key => $supporderproduct) {
            $productselect['id'] = $supporderproduct->id;
            $productselect['productunite_id'] = $supporderproduct->productunite->id;
            $productselect['title'] = $supporderproduct->product->title . ' (' . $supporderproduct->productunite->unite->parentunite->abrev . ')';
            $productselect['quantity'] = $supporderproduct->product->measurement_quantity * $supporderproduct->quantity;
            $productselect['disponible'] = $supporderproduct->quantity;
            $productselect['qtepercs'] = $supporderproduct->productunite->quantity;
            $productselect['carsac'] = $supporderproduct->productunite->unite->abrev;
            $productselect['piecekg'] = $supporderproduct->product->measurement_unit->abbreviation;
            $productselect['unit'] = $supporderproduct->productunite->unite->parentunite->abrev;

            $productselects[] = $productselect;
        }
        $this->set(compact('productselects'));

    }

    public function addedord($receiptid = null)
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
        $sel = $this->Receipts->Supporderproducts->find('all')->contain('Products');
        $sel->where(['Supporderproducts.receipt_id' => $receiptid]);
        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery = $this->Receipts->Supporderproducts->find('all')->contain('Products');
        $empQuery->where(['Supporderproducts.receipt_id' => $receiptid]);
        if ($row == 0) {
            $empQuery->limit($rowperpage);
        } else {
            $empQuery->limit($rowperpage);
            $empQuery->page(($row / $rowperpage) + 1);
        }

        if ($searchValue != '') {
            $or = [
                ['Products.title LIKE' => '%' . $searchValue . '%'],
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
        foreach ($empQuery as $key => $supporderproduct) {

            $action = '<button data-id="' . $supporderproduct->id . '" class="rmvord btn btn-sm btn-danger waves-effect waves-light" >-</button>';

            $data[] = [
                "product" => $supporderproduct->product->title,
                "quantity" => $supporderproduct->quantity,
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

    public function addord($receiptid = null)
    {
        $ordid = json_decode($_GET['ordid'], true);
        $qte = intval(json_decode($_GET['qte'], true));

        $supporderproduct = $this->Receipts->Supporderproducts->get($ordid);

        if ($qte == $supporderproduct->quantity) {
            $oldsupporderproduct = $this->Receipts->Supporderproducts->find('all')->where(['receipt_id' => $receiptid, 'product_id' => $supporderproduct->product_id])->last();

            if (empty($oldsupporderproduct)) {
                $supporderproduct->statut = 2;
                $supporderproduct->receipt_id = $receiptid;
                $this->Receipts->Supporderproducts->save($supporderproduct);
            } else {
                $updateoldersuppprod = $this->Receipts->Supporderproducts->get($oldsupporderproduct->id);
                $updateoldersuppprod->quantity += $qte;
                if ($this->Receipts->Supporderproducts->save($updateoldersuppprod)) {
                    $this->Receipts->Supporderproducts->delete($supporderproduct);
                }
            }
        } elseif ($qte < $supporderproduct->quantity) {
            $oldsupporderproduct = $this->Receipts->Supporderproducts->find('all')->where(['receipt_id' => $receiptid, 'product_id' => $supporderproduct->product_id])->last();

            if (empty($oldsupporderproduct)) {
                $newsupporderproduct = $this->Receipts->Supporderproducts->newEntity();
                $newsupporderproduct->supplierorder_id = $supporderproduct->supplierorder_id;
                $newsupporderproduct->product_id = $supporderproduct->product_id;
                $newsupporderproduct->price = $supporderproduct->price;
                $newsupporderproduct->supplier_id = $supporderproduct->supplier_id;
                $newsupporderproduct->company_id = $supporderproduct->company_id;
                $newsupporderproduct->user_id = $supporderproduct->user_id;
                $newsupporderproduct->quantity = $qte;
                $newsupporderproduct->receipt_id = $receiptid;
                $newsupporderproduct->statut = 2;
                if ($this->Receipts->Supporderproducts->save($newsupporderproduct)) {
                    $supporderproduct->quantity -= $qte;
                    $this->Receipts->Supporderproducts->save($supporderproduct);
                }
            } else {
                $updateoldersuppprod = $this->Receipts->Supporderproducts->get($oldsupporderproduct->id);
                $updateoldersuppprod->quantity += $qte;
                if ($this->Receipts->Supporderproducts->save($updateoldersuppprod)) {
                    $supporderproduct->quantity -= $qte;
                    $this->Receipts->Supporderproducts->save($supporderproduct);
                }
            }
        }
        $this->autoRender = false;
    }

    public function rmvord($receiptid = null)
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
}
