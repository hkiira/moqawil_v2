<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router; // Added for search if needed

/**
 * Packproducts Controller
 *
 * @property \App\Model\Table\PackproductsTable $Packproducts
 *
 * @method \App\Model\Entity\Packproduct[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 
 0: innactif
 1: actif
 
 */
class PackproductsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        // Fetch lists for filters, similar to ProductsController
        $packs = $this->Packproducts->Packs->find('list', ['limit' => 200, 'conditions' => ['Packs.statut IN' => [0, 1]]]); // Active/Inactive packs
        $products = $this->Packproducts->Products->find('list', ['limit' => 200, 'conditions' => ['Products.statut IN' => [0, 1]]]); // Active/Inactive products
        // No direct pagination here, search() will handle it.
        $this->set(compact('packs', 'products'));
    }

    /**
     * View method
     *
     * @param string|null $id Packproduct id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $packproduct = $this->Packproducts->get($id, [
            'contain' => ['Packs', 'Products', 'Companies'],
        ]);

        $this->set('packproduct', $packproduct);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $packproduct = $this->Packproducts->newEntity();
        if ($this->request->is('post')) {
            $packproduct = $this->Packproducts->patchEntity($packproduct, $this->request->getData());
            if ($this->Packproducts->save($packproduct)) {
                $this->Flash->success(__('The packproduct has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The packproduct could not be saved. Please, try again.'));
        }
        $packs = $this->Packproducts->Packs->find('list', ['limit' => 200]);
        $products = $this->Packproducts->Products->find('list', ['limit' => 200]);
        $companies = $this->Packproducts->Companies->find('list', ['limit' => 200]);
        $this->set(compact('packproduct', 'packs', 'products', 'companies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Packproduct id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $packproduct = $this->Packproducts->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $packproduct = $this->Packproducts->patchEntity($packproduct, $this->request->getData());
            if ($this->Packproducts->save($packproduct)) {
                $this->Flash->success(__('The packproduct has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The packproduct could not be saved. Please, try again.'));
        }
        $packs = $this->Packproducts->Packs->find('list', ['limit' => 200]);
        $products = $this->Packproducts->Products->find('list', ['limit' => 200]);
        $companies = $this->Packproducts->Companies->find('list', ['limit' => 200]);
        $this->set(compact('packproduct', 'packs', 'products', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Packproduct id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $packproduct = $this->Packproducts->get($id);
        // Change to soft delete by setting statut to -1 (or a different conventional value for deleted)
        $packproduct->statut = -1; // Assuming -1 is the convention for deleted/inactive
        if ($this->Packproducts->save($packproduct)) {
            $this->Flash->success(__('The packproduct has been marked as deleted.'));
        } else {
            $this->Flash->error(__('The packproduct could not be marked as deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function search() // No categoryid like in Products/Packs for now
    {  
        $page = $this->request->getData('pagination.page', 1);
        $perpage = $this->request->getData('pagination.perpage', 10);
        
        $columnName = $this->request->getData('sort.field', 'Packproducts.id'); 
        $columnSort = $this->request->getData('sort.sort', 'asc'); 
        $searchValue = strtolower((string)$this->request->getData('query.generalSearch')); 
        $searchPack = $this->request->getData('query.Pack'); 
        $searchProduct = $this->request->getData('query.Product');
        $searchStatus = ($this->request->getData('query.Status') !== null) ? (int)$this->request->getData('query.Status') : -1 ;

        // Define sortable columns for Packproducts
        switch($columnName) {
            case 'pack':
                $columnName = "Packs.title";
                break;
            case 'product':
                $columnName = "Products.title";
                break;
            case 'quantity':
                $columnName = "Packproducts.quantity";
                break;
            case 'status':
                $columnName = "Packproducts.statut";
                break;
            default:
                $columnName = "Packproducts.id"; // Default sort
                $columnSort = "asc";
                break;
        }

        $query = $this->Packproducts->find()
            ->contain(['Packs', 'Products', 'Companies']);

        if ($this->Auth->user('company_id')) {
            $query->where(['Packproducts.company_id' => $this->Auth->user('company_id')]);
        }
        
        // Base status filter: only show active (1), inactive (0), or deleted (-1) if explicitly requested
        if ($searchStatus === -1) { // Default: show active and inactive
            $query->where(['Packproducts.statut IN' => [0, 1]]);
        } elseif ($searchStatus >= -1) { // Allow specific status search including -1
             $query->where(['Packproducts.statut' => $searchStatus]);
        }


        if($searchValue != ''){
            $query->where(function ($exp, $q) use ($searchValue) {
                return $exp->or_([
                    'Packs.title LIKE' => '%' . $searchValue . '%',
                    'Products.title LIKE' => '%' . $searchValue . '%',
                    'Packproducts.quantity LIKE' => '%' . $searchValue . '%' 
                ]);
            });
        }

        if ($searchPack) {
            $query->where(['Packproducts.pack_id' => $searchPack]);
        }
        if ($searchProduct) {
            $query->where(['Packproducts.product_id' => $searchProduct]);
        }
        
        $total = $query->count();
        
        $query->order([$columnName => $columnSort]);
        $query->limit($perpage);
        $query->page($page);

        $data =[];
        foreach ($query as $packproduct) {
            $edit = 0;
            if ($this->Auth->user('role_id') == 1 || $this->Auth->user('role_id') == 2 || $this->Auth->user('role_id') == 7 || $this->Auth->user('role_id') == 8) {
                $edit = 1;
            }

            $data[] = [
                "id" => $packproduct->id,
                "pack" => $packproduct->has('pack') ? $packproduct->pack->title : 'N/A',
                "product" => $packproduct->has('product') ? $packproduct->product->title : 'N/A',
                "quantity" => $packproduct->quantity,
                "status" => $packproduct->statut,
                "company" => $packproduct->has('company') ? $packproduct->company->name : 'N/A',
                "edit" => $edit,
                "actions" => null 
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
}
