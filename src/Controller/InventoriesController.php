<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Inventories Controller
 *
 * @property \App\Model\Table\InventoriesTable $Inventories
 *
 * @method \App\Model\Entity\Inventory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class InventoriesController extends AppController
{

     public function print ($id = null)
    {
        $inventory = $this->Inventories->get($id, [
            'contain' => ['Users', 'Warehouses', 'Whnatures','Invproducts.Packs.MeasurementUnits','Invproducts.Packs.Categories','Invproducts.Packs','Invproducts.Packs.Packunites.Unites.Parentunites','Invproducts.Packs.Prices'=>function($q){return $q->where(['Prices.tarif_id IS '=>NULL]);}],
        ]);
        ini_set('max_execution_time', '300');
        ini_set("pcre.backtrack_limit", "5000000");
        $this->set(compact('inventory'));

    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'Warehouses', 'Whnatures', 'Companies'],
        ];
        $inventories = $this->paginate($this->Inventories);

        $this->set(compact('inventories'));
    }

    /**
     * View method
     *
     * @param string|null $id Inventory id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $inventory = $this->Inventories->get($id, [
            'contain' => ['Users', 'Warehouses', 'Whnatures', 'Companies', 'Invproducts'],
        ]);

        $this->set('inventory', $inventory);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $inventory = $this->Inventories->newEntity();
        if ($this->request->is('post')) {
            $datas=$this->request->getData();
            $warehouse=$this->Inventories->Warehouses->find('all')->where(['warehouse_id'=>$datas['warehouse_id'],'whnature_id'=>$datas['whnature_id'],'whtype_id'=>2])->last();
            $packs=$this->Inventories->Invproducts->Packs->find('all')->where(['Packs.statut'=>1]);
            if($datas['categories']){
                $packcategories=[];
                foreach ($datas['categories'] as $key => $category) {
                    $packcategories['OR'][$category]=['Packs.category_id'=>$category];
                }
                $packs->where([$packcategories]);
            }
            $packproducts=[];
            foreach ($packs as $key => $pack) {
                $packproducts['OR'][$pack->id]=['pack_id'=>$pack->id];
            }
            $whproducts=$this->Inventories->Invproducts->Packs->Whproducts->find('all')->where(['warehouse_id'=>$warehouse->id,[$packproducts]]);
            if($datas['categories']==null){
                $whproducts=$this->Inventories->Invproducts->Packs->Whproducts->find('all')->where(['warehouse_id'=>$warehouse->id]);
            }
           
            foreach ($whproducts as $key => $whproduct) {
                $pack=$this->Inventories->Invproducts->Packs->find('all')->where(['Packs.id'=>$whproduct->pack_id])->first();
                if ($pack) {
                    $datas['invproducts'][$whproduct->id]['pack_id']=$whproduct->pack_id;
                    $datas['invproducts'][$whproduct->id]['quantity']=$whproduct->quantity;
                    $datas['invproducts'][$whproduct->id]['statut']=1;
                }
            }
            $inventory = $this->Inventories->patchEntity($inventory, $datas,['associated'=>['Invproducts']]);

            $inventory->statut=1;
            $code=$this->Inventories->Companies->Companycodes->find('all')->where(['controleur'=>'Inventories','company_id'=>$this->Auth->user('company_id')])->last();
            $inventory->code=$code->prefixe.($code->compteur+1);
            $inventory->company_id=$this->Auth->user('company_id');
            $inventory->user_id=$this->Auth->user('id');
            
            if ($this->Inventories->save($inventory)) {
                $code->compteur+=1;
                if ($this->Inventories->Companies->Companycodes->save($code)) {
                    $this->Flash->success(__('L\'inventaire a été sauvegardé.'));
                    return $this->redirect(['action' => 'index']);
                }

            }
            $this->Flash->error(__('L\'inventaire n\'a pas pu être enregistré. Veuillez réessayer.'));
        }
        
        // Custom query for warehouses to show user names for whtype_id = 3
        $warehouses = $this->Inventories->Warehouses->find('all')
            ->where(['OR'=>['whtype_id'=>1,'whtype_id'=>3]])
            ->contain(['Pofsales.Pofsusers.Users'])
            ->formatResults(function($results) {
                return $results->map(function($row) {
                    if ($row->whtype_id == 1) {
                        $row->display_title = $row->title;
                    }elseif ($row->whtype_id == 3) {
                        // Get the first pofsale's first pofsuser's user
                        $pofsale = $row->pofsales[0];
                        if (!empty($pofsale->pofsusers)) {
                            $user = $pofsale->pofsusers[0]->user;
                            $row->display_title = $row->title . ' - ' . $user->firstname . ' ' . $user->lastname;
                        } else {
                            $row->display_title = $row->title;
                        }
                    } else {
                        $row->display_title = $row->title;
                    }
                    return $row;
                });
            })
            ->combine('id', 'display_title');
            
        $whnatures = $this->Inventories->Whnatures->find('list');
        $categories = $this->Inventories->Invproducts->Packs->Categories->find('list')->where(['Categories.category_id IS NOT '=>NULL]);
        $this->set(compact('inventory', 'warehouses', 'whnatures', 'categories'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Inventory id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $inventory = $this->Inventories->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $inventory = $this->Inventories->patchEntity($inventory, $this->request->getData());
            if ($this->Inventories->save($inventory)) {
                $this->Flash->success(__('The inventory has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The inventory could not be saved. Please, try again.'));
        }
        $users = $this->Inventories->Users->find('list', ['limit' => 200]);
        $warehouses = $this->Inventories->Warehouses->find('list', ['limit' => 200]);
        $whnatures = $this->Inventories->Whnatures->find('list', ['limit' => 200]);
        $companies = $this->Inventories->Companies->find('list', ['limit' => 200]);
        $this->set(compact('inventory', 'users', 'warehouses', 'whnatures', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Inventory id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $inventory = $this->Inventories->get($id);
        if ($this->Inventories->delete($inventory)) {
            $this->Flash->success(__('The inventory has been deleted.'));
        } else {
            $this->Flash->error(__('The inventory could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function search($ordertype=null)
    {  
        $draw = $_GET['draw'];
        $row = $_GET['start'];
        $rowperpage = $_GET['length']; // Rows display per page
        $columnIndex = $_GET['order'][0]['column']; // Column index
        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc
        $searchValue = $_GET['search']['value']; // Search value
        switch($columnName) {
            case 'User':
                $columnName="Inventories.Code";
                break;
            case 'Code':      
                $columnName="Inventories.Code";
                break;
            case 'Customer':
                $columnName="Inventories.Code";
                break;
            case 'Created':
                $columnName="Inventories.created";
                break;
            case 'Status':
                $columnName="Inventories.statut";
                break;
            default:
                $columnName="Inventories.created";
                break;
        }
        ## Total number of records with filtering
        $sel=$this->Inventories->find('all')->where(['Inventories.company_id'=>$this->Auth->user('company_id')]);

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;


        $empQuery=$this->Inventories->find('all')->contain(['Users','Warehouses','Invproducts','Whnatures'])->where(['Inventories.company_id'=>$this->Auth->user('company_id')]);
        $empQuery->order([$columnName => $columnSortOrder]);
        if ($row==0) {
            $empQuery->limit($rowperpage);
        }else{
            $empQuery->limit($rowperpage);
            $empQuery->page(($row/$rowperpage)+1);
        }
       
        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['lower(Inventories.code) LIKE'=>'%'.$searchValue.'%'],
                ['Inventories.code LIKE' => '%'.$searchValue.'%']]]);
            $empQuery->where(["OR"=>[
                ['lower(Inventories.code) LIKE'=>'%'.$searchValue.'%'],
                ['Inventories.code LIKE' => '%'.$searchValue.'%']]]);
            $empQuery->page(1);
        }
        if ($draw=0) {
            $empQuery->page(1);
        }
        ## Total number of records with filtering
        $totalRecordwithFilter = $sel->last()->count;
        ## Fetch records
        $data =[];
        //"statut"=>'',
        foreach ($empQuery as $key => $inventory) {
            $action='<div class="dropdown dropdown-inline">
                        <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                            <i class="la la-cog"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                            <ul class="nav nav-hoverable flex-column">';
                $action.='<li class="nav-item"><a class="nav-link" href="'.Router::Url('/inventories/print/'.$inventory->id).'.pdf" target="_blank"><span class="nav-text">Imprimer</span></a></li>';
            $action.='</ul></div></div>';
            $data[] = [
                "User"=> $inventory->user->firstname,
                "Code"=> $inventory->code,
                "Warehouse"=>$inventory->warehouse->title,
                "Products"=> count($inventory->invproducts),
                "Created"=> $inventory->created->nice('Europe/Paris', 'fr-FR'),
                "Whnature"=> $inventory->whnature->title,
                "Actions"=> $action
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

    public function adjustStock()
    {
       
        // Load necessary models
        $this->loadModel('Whproducts');
        $this->loadModel('Products');
        $this->loadModel('Packs');
        $this->loadModel('Warehouses');
        $this->loadModel('StockMovements'); // Assuming you created this Table class

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            $itemId = $data['item_id'];
            $itemType = $data['item_type']; // 'Product' or 'Pack'
            $warehouseId = (int)$data['warehouse_id'];
            $quantityChange = (int)$data['quantity_change'];
            $movementType = $data['movement_type']; // e.g., 'adjustment_positive', 'initial_stock'
            $notes = isset($data['notes']) ? $data['notes'] : null;
            $userId = $this->Auth->user('id');
            $companyId = $this->Auth->user('company_id'); // Assuming company context
           
            if (empty($itemId) || empty($itemType) || empty($warehouseId) || !isset($data['quantity_change'])) {
                $this->Flash->error(__('Please fill all required fields for stock adjustment.'));
            } else {
                $connection = $this->Whproducts->getConnection();
                try {
                    $connection->begin();

                    // Find or create Whproduct record
                    $whproduct = $this->Whproducts->find()
                        ->where([
                            'item_id' => $itemId,
                            'item_type' => $itemType,
                            'warehouse_id' => $warehouseId,
                            'company_id' => $companyId 
                        ])
                        ->first();
                    if (!$whproduct) {
                        if ($quantityChange < 0) {
                            throw new \Exception(__('Cannot make a negative adjustment for a non-existing stock record.'));
                        }
                        $whproduct = $this->Whproducts->newEntity([
                            'item_id' => $itemId,
                            'item_type' => $itemType,
                            'warehouse_id' => $warehouseId,
                            'quantity' => 0, // Initial quantity before adjustment
                            'statut' => 1, 
                            'company_id' => $companyId,
                        ]);
                    }
                    
                    $balanceBefore = $whproduct->quantity;
                    $whproduct->quantity += $quantityChange; // Apply the change
                    $balanceAfter = $whproduct->quantity;

                    if ($whproduct->quantity < 0) {
                        throw new \Exception(__('Stock quantity cannot go below zero with this adjustment.'));
                    }

                    if (!$this->Whproducts->save($whproduct)) {
                        $errors = $whproduct->getErrors();
                        $this->Flash->error(__('Could not update stock. Errors: ') . json_encode($errors));
                        throw new \Exception('Whproduct save failed.');
                    }

                    // Log the stock movement
                    $stockMovement = $this->StockMovements->newEntity([
                        'item_id' => $itemId,
                        'item_type' => $itemType,
                        'warehouse_id' => $warehouseId,
                        'quantity_change' => $quantityChange,
                        'balance_after_movement' => $balanceAfter,
                        'movement_type' => $movementType,
                        'user_id' => $userId,
                        'notes' => $notes,
                        'company_id' => $companyId, // Assuming StockMovements also has company_id
                        // 'validation_status' => 'approved', // Or 'pending' if workflow needed
                    ]);

                    if (!$this->StockMovements->save($stockMovement)) {
                         $errors = $stockMovement->getErrors();
                         $this->Flash->error(__('Stock updated, but failed to log movement. Errors: ') . json_encode($errors));
                        throw new \Exception('StockMovement save failed.');
                    }

                    $connection->commit();
                    $this->Flash->success(__('Stock adjusted successfully and movement logged.'));
                    return $this->redirect(['action' => 'adjustStock']); // Or to an inventory overview page

                } catch (\Exception $e) {
                    $connection->rollback();
                    $this->Flash->error(__('Error during stock adjustment: ') . $e->getMessage());
                }
            }
        }

        // Data for the form
        $productsList = $this->Products->find('list', [
            'conditions' => ['Products.statut' => 1, 'Products.company_id' => $this->Auth->user('company_id')],
            'keyField' => 'id',
            'valueField' => 'title'
        ])->toArray();

        $packsList = $this->Packs->find('list', [
             'conditions' => ['Packs.statut IN' => [0,1], 'Packs.company_id' => $this->Auth->user('company_id')], // Active/Inactive packs
            'keyField' => 'id',
            'valueField' => 'title'
        ])->toArray();
        
        $warehousesList = $this->Warehouses->find('list', [
            'conditions' => ['Warehouses.company_id' => $this->Auth->user('company_id'), 'Warehouses.statut' => 1],
            'keyField' => 'id',
            'valueField' => 'title'
        ])->toArray();

        $itemTypes = ['Product' => 'Produit', 'Pack' => 'Pack'];
        // Define some common movement types
        $movementTypes = [
            'initial_stock' => 'Stock Initial',
            'adjustment_positive' => 'Ajustement Positif',
            'adjustment_negative' => 'Ajustement Négatif',
            'purchase_receipt' => 'Réception Achat',
            'return_customer' => 'Retour Client',
            'return_supplier' => 'Retour Fournisseur',
            'damaged_goods' => 'Marchandise Endommagée',
            // 'pack_assembly_consumption' and 'pack_assembly_production' would be used by assemblePack action
        ];

        $this->set(compact('productsList', 'packsList', 'warehousesList', 'itemTypes', 'movementTypes'));
        // Explicitly render the view, though convention should find it.
        // This is primarily for troubleshooting if the convention isn't working.
        // Ensure the template file exists at src/Template/Inventories/adjust_stock.ctp
        try {
            $this->render('adjust_stock');
        } catch (\Cake\View\Exception\MissingTemplateException $e) {
            // Log this or handle, but for now, this ensures if it's truly missing,
            // the error source is clearer. If it's found, it just renders.
            // In a production scenario, you might not need this explicit render call.
            throw $e; 
        }
    }
}
