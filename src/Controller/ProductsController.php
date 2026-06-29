<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;
use Cake\Event\EventInterface;

/**
 * Products Controller
 *
 * @property \App\Model\Table\ProductsTable $Products
 *
 * @method \App\Model\Entity\Product[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProductsController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->loadModel('Categories');
        $this->loadModel('ProductPackages');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Categories', 'Productunites', 'Companies', 'Suppliers'],
        ];
        $products = $this->paginate($this->Products);
        $categories = $this->Products->Categories->find('list', ['limit' => 200, 'conditions' => ['Categories.statut' => 1]]); // Fetch categories for the filter

        $this->set(compact('products', 'categories')); // Add categories to set
    }

    /**
     * View method
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $product = $this->Products->get($id, [
            'contain' => ['Categories', 'Unites','Suppliers', 'Packproducts'],
        ]);

        $this->set('product', $product);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $product = $this->Products->newEntity();
        if ($this->request->is('post')) {
            $datas=$this->request->getData();
            
            $depots=$this->Products->Packproducts->Products->Whproducts->Warehouses->find('all')->where(['whtype_id'=>2,'company_id'=>$this->Auth->user('company_id')]);
            $whproducts=[];
            
            // When creating a new pack, Whproduct entries are for this pack.
            // item_id will be set by the ORM through association if foreignKey is 'item_id'.
            // We must ensure item_type is set.
            foreach ($depots as $key => $depot) {
                $whproducts[$key]=[
                    'item_type' => 'Product', // Specify item_type for pack stock
                    'warehouse_id'=>$depot->id,
                    'quantity'=>0,
                    'statut'=>1,
                    'company_id'=>$this->Auth->user('company_id')
                    // 'item_id' will be the new pack's ID, handled by association.
                ];
            }
            $datas['whproducts']=$whproducts; // This will be part of $datas passed to patchEntity
                   
            if($datas['productunites'][0]['quantity']<=0){
                $this->Flash->error(__('Merci de vérfier la quantité du Carton/Sac.'));
                return $this->redirect(['action' => 'add']);
            }
            $datas['whproducts']=$whproducts;
            
            foreach($datas['productunites'] as $key=>$packun){
                $datas['productunites'][$key]['company_id']=$this->Auth->user('company_id');
                $datas['productunites'][$key]['statut']=1;
            }
              

            $increment=0;
            $packdata=[];
            $datas['sellingprice']=$datas['buyingprice'];
            $datas['reference']='PR';
            $whproducts=$datas ['whproducts'];
            
            $product = $this->Products->patchEntity($product, $datas, ['associated'=>['Photos','Productunites']]);
            
            
            /*$product->photo->title=$product->title;
            $product->photo->controleur='products';
            $product->photo->company_id=$this->Auth->user('company_id');*/
            $product->company_id=$this->Auth->user('company_id');
            if ($this->Products->save($product)) {
                foreach ($whproducts as $key => $whproduct) {
                    $whproduct['item_id']=$product->id;
                    $whp=$this->Products->Whproducts->newEntity($whproduct);
                    $this->Products->Whproducts->save($whp);
                }
                $this->Flash->success(__('L\'article a été enregistré.'));
                return $this->redirect(['action' => 'index']);
            } 
            $this->Flash->error(__('L\'article n\'a pas pu être enregistré. Veuillez réessayer.'));
        }
        
        $categories = $this->Categories->find('list', [
            'keyField' => 'id',
            'valueField' => 'title',
            'conditions' => ['Categories.company_id' => 1]
        ])->toArray();

        $productPackages = $this->ProductPackages->find('list', [
            'keyField' => 'id',
            'valueField' => function ($package) {
                return $package->weight . ' ' . $package->unit;
            },
            'conditions' => ['ProductPackages.company_id' => 1]
        ])->toArray();
        $measurementUnits = $this->Products->MeasurementUnits->find('list', [
            'keyField' => 'id',
            'valueField' => function ($unit) {
                return $unit->title . ' (' . $unit->abbreviation . ')';
            },
            'conditions' => [
                'MeasurementUnits.company_id' => $this->Auth->user('company_id'),
                'MeasurementUnits.statut' => 1
            ],
            'order' => ['MeasurementUnits.title' => 'ASC']
        ]);
        $unites = $this->Products->Productunites->Unites->find('list')->where(['statut'=>1,'unite_id IS NOT'=>NULL]);
        $this->set(compact('product','measurementUnits' ,'categories','unites', 'productPackages'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $product = $this->Products->get($id, [
            'contain' => ['ProductPackages'],
            'conditions' => ['Products.company_id' => 1]
        ]);
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $product = $this->Products->patchEntity($product, $data);
            
            if ($this->Products->save($product)) {
                $this->Flash->success(__('Le produit a été modifié.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Le produit n\'a pas pu être modifié. Veuillez réessayer.'));
        }
        
        $categories = $this->Categories->find('list', [
            'keyField' => 'id',
            'valueField' => 'title',
            'conditions' => ['Categories.company_id' => 1]
        ])->toArray();

        $productPackages = $this->ProductPackages->find('list', [
            'keyField' => 'id',
            'valueField' => function ($package) {
                return $package->weight . ' ' . $package->unit;
            },
            'conditions' => ['ProductPackages.company_id' => 1]
        ])->toArray();
        
        $this->set(compact('product', 'categories', 'productPackages'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $product = $this->Products->get($id);
        // Change to soft delete by setting statut to -1 (or a different conventional value for deleted)
        $product->statut = -1; 
        if ($this->Products->save($product)) {
            $this->Flash->success(__('The product has been marked as deleted.'));
        } else {
            $this->Flash->error(__('The product could not be marked as deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function search() // Removed $categoryid, will handle via request params if needed
    {  
        $this->request->allowMethod(['ajax', 'get', 'post']); // Allow GET for initial load, POST for AJAX

        $draw = $this->request->getData('draw');
        $start = $this->request->getData('start', 0); // Default to 0 for jQuery DataTables
        $length = $this->request->getData('length', 10); // Default to 10 for jQuery DataTables

        $searchValue = strtolower((string)$this->request->getData('search.value'));
        
        // Custom filters from request (if you add them to the AJAX call from DataTables)
        // Example: $searchCategories = $this->request->getData('categories');
        // Example: $searchStatus = $this->request->getData('status');
        // For now, using KTDatatable-like query params if they are still sent by re-wired filters
        $searchCategories = $this->request->getData('query.Category');
        $searchStatusParam = $this->request->getData('query.Status');
        $searchStatus = ($searchStatusParam !== null && $searchStatusParam !== '') ? (int)$searchStatusParam : -1;


        $query = $this->Products->find('all')
            ->contain(['Categories', 'Suppliers']); // Add 'Photos' if needed for image
        
        
        // Initial total records (before filtering by search value)
        $recordsTotal = $query->count();

        // Apply general search value
        if ($searchValue != '') {
            $query->where(function ($exp, $q) use ($searchValue) {
                return $exp->or_([
                    'Products.title LIKE' => '%' . $searchValue . '%',
                    'Products.reference LIKE' => '%' . $searchValue . '%', // Assuming 'reference' is like 'code'
                    'Categories.title LIKE' => '%' . $searchValue . '%',
                    // Add other fields to search if needed
                ]);
            });
        }
        
        // Apply custom filters (Category, Status)
        if (!empty($searchCategories)) { // Assuming $searchCategories is an array of IDs
             if (is_array($searchCategories)) {
                $query->where(['Products.category_id IN' => $searchCategories]);
             } else { // If it's a single value
                $query->where(['Products.category_id' => $searchCategories]);
             }
        }
        if ($searchStatus !== -1) {
            $query->where(['Products.statut' => $searchStatus]);
        }

        // Total records after filtering by search value and custom filters
        $recordsFiltered = $query->count();
        
        // Handle Sorting for jQuery DataTables
        $order = $this->request->getData('order.0'); // First order column
        if ($order) {
            $columnIndex = (int)$order['column'];
            $columnSortDir = strtolower($order['dir']) === 'asc' ? 'ASC' : 'DESC';
            $columnData = $this->request->getData("columns.{$columnIndex}.data");
            
            // Map DataTables column 'data' name to actual database field
            $sortableColumns = [
                'reference' => 'Products.reference', // Assuming 'reference' is your code
                'title' => 'Products.title',
                'category_title' => 'Categories.title', // Requires join/contain
                'statut' => 'Products.statut'
                // Add other sortable columns as defined in DataTables JS
            ];
            if (isset($sortableColumns[$columnData])) {
                $query->order([$sortableColumns[$columnData] => $columnSortDir]);
            } else {
                 $query->order(['Products.title' => 'ASC']); // Default sort
            }
        } else {
            $query->order(['Products.title' => 'ASC']); // Default sort
        } // Debugging SQL query, remove in production
        $query->limit($length)->offset($start);
        $data = [];
        foreach ($query as $product) {
            // Prepare data for each row as expected by DataTables columns
            // This needs to match the 'data' field for each column in JS
            $imageUrl = Router::Url('/img/unvailable.jpg', true); // Default image
            // Add logic to get actual product image if available (e.g., from a Photos association)
            // if ($product->has('photos') && !empty($product->photos[0])) {
            //    $imageUrl = Router::Url('/' . $product->photos[0]->dir . $product->photos[0]->photo, true);
            // }

            $actions = '<div class="dropdown dropdown-inline">
                            <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="la la-cog"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                <ul class="navi navi-hover py-2">
                                    <li class="navi-item">
                                        <a class="navi-link" href="' . Router::url(['action' => 'view', $product->id]) . '">
                                            <span class="navi-icon"><i class="la la-eye"></i></span>
                                            <span class="navi-text">Afficher</span>
                                        </a>
                                    </li>
                                    <li class="navi-item">
                                        <a class="navi-link" href="' . Router::url(['action' => 'edit', $product->id]) . '">
                                            <span class="navi-icon"><i class="la la-edit"></i></span>
                                            <span class="navi-text">Modifier</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>';
            
            $statusBadges = [
                0 => '<span class="label font-weight-bold label-lg label-light-danger label-inline">Innactif</span>',
                1 => '<span class="label font-weight-bold label-lg label-light-success label-inline">Actif</span>',
                // Add other statuses if needed
            ];

            $data[] = [
                // Data for each column, ensure order and 'data' name matches JS config
                'id' => $product->id, // For checkbox selection
                'display_name' => [ // Example for a complex column with image and text
                    'image' => $imageUrl,
                    'title' => h($product->title), 
                    'reference' => h($product->reference),
                    'unite' => $product->has('unite') ? h($product->unite->title) : ''
                ],
                'reference' => h($product->reference), // If 'reference' is a separate column
                'title' => h($product->title),       // If 'title' is a separate column
                'category_title' => $product->has('category') ? h($product->category->title) : 'N/A',
                'statut' => isset($statusBadges[$product->statut]) ? $statusBadges[$product->statut] : 'N/A',
                'actions' => $actions,
                // Add other fields as needed by your JS DataTables column definitions
            ];
        }
        
        $response = [
            "draw" => intval($draw),
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        ];

        $this->autoRender = false; 
        $this->response = $this->response->withType('application/json');
        $this->response = $this->response->withStringBody(json_encode($response));
        return $this->response;
    }

    public function batchAdjustStock()
    {
        $this->loadModel('Whproducts');
        $this->loadModel('Warehouses');
        $this->loadModel('StockMovements');

        if ($this->request->is('post') && $this->request->getData('process_batch_update') === '1') {
            // This is the submission of the batch adjustment form itself
            $data = $this->request->getData();

            // Common fields
            $commonWarehouseId = (int)$data['common_warehouse_id'];
            $commonMovementType = $data['common_movement_type'];
            $commonNotes = isset($data['common_notes']) ? $data['common_notes'] : null;
            
            $productsAdjustments = isset($data['products_adjustments']) ? $data['products_adjustments'] : [];

            $userId = $this->Auth->user('id');
            $companyId = $this->Auth->user('company_id');

            if (empty($commonWarehouseId) || empty($commonMovementType) || empty($productsAdjustments)) {
                $this->Flash->error(__('Please fill all common fields and ensure at least one product adjustment is specified.'));
                return $this->redirect($this->referer());
            }

            $connection = $this->Whproducts->getConnection();
            $successCount = 0;
            $errorCount = 0;
            $errorMessages = [];

            try {
                $connection->begin();
                foreach ($productsAdjustments as $productId => $adjustmentData) {
                    $productId = (int)$productId; // product_id is also in $adjustmentData['product_id']
                    
                    if (empty($adjustmentData['adjustment_type']) || !isset($adjustmentData['quantity']) || !is_numeric($adjustmentData['quantity'])) {
                        $errorCount++;
                        $errorMessages[] = "Ajustement invalide pour Produit ID: {$productId}. Type ou quantité manquant/incorrect.";
                        continue;
                    }

                    $adjustmentType = $adjustmentData['adjustment_type'];
                    $quantity = (float)$adjustmentData['quantity'];

                    $whproduct = $this->Whproducts->find()
                        ->where([
                            'item_id' => $productId,
                            'item_type' => 'Product',
                            'warehouse_id' => $commonWarehouseId,
                            'company_id' => $companyId
                        ])
                        ->first();

                    $quantityChange = 0;
                    $balanceBefore = 0;

                    if (!$whproduct) {
                        if ($adjustmentType === 'decrease_by' || ($adjustmentType === 'set_to' && $quantity < 0)) {
                            $errorCount++;
                            $errorMessages[] = "Ajustement négatif impossible pour stock inexistant (Produit ID: {$productId}).";
                            continue;
                        }
                        $whproduct = $this->Whproducts->newEntity([
                            'item_id' => $productId,
                            'item_type' => 'Product',
                            'warehouse_id' => $commonWarehouseId,
                            'quantity' => 0,
                            'statut' => 1,
                            'company_id' => $companyId,
                        ]);
                        $balanceBefore = 0;
                    } else {
                        $balanceBefore = $whproduct->quantity;
                    }

                    if ($adjustmentType === 'set_to') {
                        $quantityChange = $quantity - $balanceBefore;
                        $whproduct->quantity = $quantity;
                    } elseif ($adjustmentType === 'increase_by') {
                        $quantityChange = $quantity; // The change is the quantity itself
                        $whproduct->quantity += $quantity;
                    } elseif ($adjustmentType === 'decrease_by') {
                        $quantityChange = -$quantity; // The change is the negative of the quantity
                        $whproduct->quantity -= $quantity;
                    }

                    if ($whproduct->quantity < 0) {
                        $errorCount++;
                        $errorMessages[] = "Stock pour Produit ID: {$productId} ne peut pas être négatif. Ajustement ignoré.";
                        continue; 
                    }
                    
                    if (!$this->Whproducts->save($whproduct)) {
                        $errorCount++;
                        $errorMessages[] = "Impossible de mettre à jour le stock pour Produit ID: {$productId}.";
                        continue;
                    }

                    $stockMovement = $this->StockMovements->newEntity([
                        'item_id' => $productId,
                        'item_type' => 'Product',
                        'warehouse_id' => $commonWarehouseId,
                        'quantity_change' => $quantityChange,
                        'balance_after_movement' => $whproduct->quantity,
                        'movement_type' => $commonMovementType, // Use common movement type
                        'user_id' => $userId,
                        'notes' => $commonNotes, // Use common notes
                        'company_id' => $companyId,
                    ]);
                    if (!$this->StockMovements->save($stockMovement)) {
                        $errorCount++;
                        $errorMessages[] = "Stock mis à jour pour Produit ID: {$productId}, mais échec de l'enregistrement du mouvement.";
                    } else {
                        $successCount++;
                    }
                }

                if ($errorCount > 0) {
                    $connection->rollback();
                    $this->Flash->error(implode('<br>', $errorMessages) . '<br>' . $errorCount . __(' erreurs durant l\'ajustement groupé. Aucun stock n\'a été modifié globalement.'));
                } else {
                    $connection->commit();
                    $this->Flash->success($successCount . __(' produits ont eu leur stock ajusté avec succès.'));
                }
                return $this->redirect(['action' => 'index']);

            } catch (\Exception $e) {
                $connection->rollback();
                $this->Flash->error(__('Erreur durant l\'ajustement de stock groupé: ') . $e->getMessage());
                return $this->redirect($this->referer());
            }
        } else { 
            // This is the initial request to display the batch_adjust_stock.ctp form
            // It could be a GET request if URL was bookmarked/typed, or POST from index page's hidden form
            
            $product_ids_json = $this->request->getData('product_ids'); // From POST from index.ctp
            if (empty($product_ids_json)) {
                $product_ids_json = $this->request->getQuery('product_ids'); // Fallback for GET
            }

            if (empty($product_ids_json)) {
                $this->Flash->error(__('No products selected for batch stock adjustment.'));
                return $this->redirect(['action' => 'index']);
            }

            $product_ids = json_decode($product_ids_json, true);
            if (empty($product_ids) || !is_array($product_ids)) {
                $this->Flash->error(__('Invalid product selection format.'));
                return $this->redirect(['action' => 'index']);
            }

            $products = $this->Products->find('list', [
                'conditions' => ['Products.id IN' => $product_ids],
                'keyField' => 'id',
                'valueField' => 'title'
            ])->toArray();

            if (count($product_ids) > 0 && empty($products)) {
                 $this->Flash->error(__('None of the selected products could be found or belong to your company.'));
                return $this->redirect(['action' => 'index']);
            }
             if (empty($products)) { // Handles case where $product_ids was empty array initially
                 $this->Flash->error(__('No valid products found for batch stock adjustment.'));
                return $this->redirect(['action' => 'index']);
            }


            $warehousesList = $this->Warehouses->find('list', [
                'conditions' => ['Warehouses.company_id' => $this->Auth->user('company_id'), 'Warehouses.statut' => 1],
                'keyField' => 'id',
                'valueField' => 'title'
            ])->toArray();
            
            $adjustmentTypes = [
                'set_to' => 'Définir le stock à',
                'increase_by' => 'Augmenter le stock de',
                'decrease_by' => 'Diminuer le stock de'
            ];
            $movementTypes = [
                'batch_adjustment_positive' => 'Ajustement Groupé Positif',
                'batch_adjustment_negative' => 'Ajustement Groupé Négatif',
                'batch_initial_stock' => 'Stock Initial Groupé',
                'batch_correction' => 'Correction Groupée Stock',
            ];

            $this->set(compact('products', 'product_ids_json', 'warehousesList', 'adjustmentTypes', 'movementTypes'));
            $this->render('batch_adjust_stock');
        }
    }
}
