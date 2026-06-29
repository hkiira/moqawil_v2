<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router; // For URL generation if needed

/**
 * StockMovements Controller
 *
 * @property \App\Model\Table\StockMovementsTable $StockMovements
 * @method \App\Model\Entity\StockMovement[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StockMovementsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        // Data for filters
        $this->loadModel('Warehouses');
        $this->loadModel('Users');
        $this->loadModel('Products');
        $this->loadModel('Packs');

        $warehouses = $this->Warehouses->find('list', [
            'conditions' => ['Warehouses.company_id' => $this->Auth->user('company_id'), 'Warehouses.statut' => 1]
        ])->toArray();
        
        $users = $this->Users->find('list', [
            'keyField' => 'id',
            'valueField' => 'username', // or firstname, lastname, etc.
            'conditions' => ['Users.company_id' => $this->Auth->user('company_id')]
        ])->toArray();

        $itemTypes = ['Product' => 'Produit', 'Pack' => 'Pack'];
        
        $movementTypes = [
            'initial_stock' => 'Stock Initial',
            'adjustment_positive' => 'Ajustement Positif',
            'adjustment_negative' => 'Ajustement Négatif',
            'purchase_receipt' => 'Réception Achat',
            'sale' => 'Vente',
            'return_customer' => 'Retour Client',
            'return_supplier' => 'Retour Fournisseur',
            'damaged_goods' => 'Marchandise Endommagée',
            'pack_assembly_consumption' => 'Consommation Assemblage Pack',
            'pack_assembly_production' => 'Production Assemblage Pack',
            'transfer_in' => 'Transfert Entrant',
            'transfer_out' => 'Transfert Sortant',
        ];
        
        // Products and Packs for item filter - might be too many for a simple dropdown
        // Consider an autocomplete or type-specific dropdowns if needed in the template
        // For now, just passing the types and movement types for filtering.

        $this->set(compact('warehouses', 'users', 'itemTypes', 'movementTypes'));
        // The actual data will be fetched by the datatable via the search() action.
    }

    /**
     * Search method for server-side datatable processing.
     *
     * @return \Cake\Http\Response|null
     */
    public function search()
    {
        $this->request->allowMethod(['ajax', 'get', 'post']); // Allow GET for datatable initial load

        $this->loadModel('Products');
        $this->loadModel('Packs');

        $page = $this->request->getData('pagination.page', 1);
        $perpage = $this->request->getData('pagination.perpage', 10);
        
        $columnName = $this->request->getData('sort.field', 'StockMovements.created'); 
        $columnSort = $this->request->getData('sort.sort', 'desc'); 
        
        $searchValue = strtolower((string)$this->request->getData('query.generalSearch')); 
        $searchItemType = $this->request->getData('query.ItemType');
        $searchWarehouse = $this->request->getData('query.Warehouse');
        $searchUser = $this->request->getData('query.User');
        $searchMovementType = $this->request->getData('query.MovementType');
        // Add date range filters if needed: $searchDateFrom, $searchDateTo

        $query = $this->StockMovements->find()
            ->contain(['Warehouses', 'Users', 'Companies']) // Basic associations
            ->where(['StockMovements.company_id' => $this->Auth->user('company_id')]);

        // Apply filters
        if ($searchItemType) {
            $query->where(['StockMovements.item_type' => $searchItemType]);
        }
        if ($searchWarehouse) {
            $query->where(['StockMovements.warehouse_id' => $searchWarehouse]);
        }
        if ($searchUser) {
            $query->where(['StockMovements.user_id' => $searchUser]);
        }
        if ($searchMovementType) {
            $query->where(['StockMovements.movement_type' => $searchMovementType]);
        }

        if ($searchValue != '') {
            // General search can be tricky with polymorphic item_id.
            // Search on notes, movement_type, user name, warehouse name.
            // For item name, it would require a more complex subquery or joining based on item_type.
            $query->where(function ($exp, $q) use ($searchValue) {
                return $exp->or_([
                    'StockMovements.notes LIKE' => '%' . $searchValue . '%',
                    'StockMovements.movement_type LIKE' => '%' . $searchValue . '%',
                    'Users.username LIKE' => '%' . $searchValue . '%', // Assuming username field
                    'Warehouses.title LIKE' => '%' . $searchValue . '%' // Assuming title field
                ]);
            });
        }
        
        $total = $query->count();
        
        $query->order([$columnName => $columnSort]);
        $query->limit($perpage)->page($page);

        $data = [];
        $itemCache = ['Product' => [], 'Pack' => []];

        foreach ($query as $movement) {
            $itemName = 'N/A';
            if (!empty($movement->item_id) && !empty($movement->item_type)) {
                if (!isset($itemCache[$movement->item_type][$movement->item_id])) {
                    if ($movement->item_type === 'Product') {
                        $item = $this->Products->findById($movement->item_id)->select(['title'])->first();
                        $itemCache[$movement->item_type][$movement->item_id] = $item ? $item->title : 'Produit Inconnu (ID: '.$movement->item_id.')';
                    } elseif ($movement->item_type === 'Pack') {
                        $item = $this->Packs->findById($movement->item_id)->select(['title'])->first();
                        $itemCache[$movement->item_type][$movement->item_id] = $item ? $item->title : 'Pack Inconnu (ID: '.$movement->item_id.')';
                    }
                }
                $itemName = $itemCache[$movement->item_type][$movement->item_id];
            }

            $data[] = [
                "id" => $movement->id,
                "created" => $movement->created ? $movement->created->format('Y-m-d H:i:s') : 'N/A',
                "item_type" => $movement->item_type,
                "item_name" => $itemName,
                "warehouse" => $movement->has('warehouse') ? $movement->warehouse->title : 'N/A',
                "quantity_change" => $this->Number->format($movement->quantity_change),
                "balance_after_movement" => $this->Number->format($movement->balance_after_movement),
                "movement_type" => $movement->movement_type,
                "user" => $movement->has('user') ? $movement->user->username : 'N/A', // Assuming username
                "notes" => h($movement->notes),
            ];
        }
        
        $response = [
            "meta"=>[
                'page' => $page,
                'pages' => ceil($total / $perpage),
                'perpage' => $perpage,
                'total' => $total,
            ],
            'data' => $data,
        ];

        $this->autoRender = false; 
        $this->response = $this->response->withType('application/json');
        $this->response = $this->response->withStringBody(json_encode($response));
        return $this->response;
    }

    // View, Add, Edit, Delete methods for StockMovements are typically not directly used by users.
    // Movements are results of other actions (adjustStock, assemblePack, sales, purchases etc.)
    // You can keep them if you need an admin interface to manually edit/delete logs, but it's often discouraged.
    // For now, I will comment them out. If you need them, they can be uncommented and adjusted.

    /*
    public function view($id = null)
    {
        $stockMovement = $this->StockMovements->get($id, [
            'contain' => ['Warehouses', 'Users', 'Companies'], // Add ValidatedByUsers if used
        ]);
        // Manually fetch item
        // ...
        $this->set(compact('stockMovement'));
    }
    // Add, Edit, Delete are generally not recommended for movement logs.
    */
}
