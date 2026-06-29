<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;

/**
 * Companies Controller
 *
 * @property \App\Model\Table\CompaniesTable $Companies
 *
 * @method \App\Model\Entity\Company[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 
 0: innactif
 1: actif
 
 */
class CompaniesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $companies = $this->paginate($this->Companies);

        $this->set(compact('companies'));
    }

    /**
     * View method
     *
     * @param string|null $id Company id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $company = $this->Companies->get($id, [
            'contain' => ['Packs', 'Unites'],
        ]);

        $this->set('company', $company);
    }
    
    public function dashboard(){
        // Get date parameters from query string or use defaults
        $keyword = $this->request->getQuery('keyword');
        
        // Set default dates to current month if not provided
        $startDate = $keyword['start'] ?? date('Y-m-01');
        $endDate = $keyword['end'] ?? date('Y-m-t');
        
        // Build the vrb array for backward compatibility with cells
        $vrb = [
            'start' => $startDate,
            'end' => $endDate
        ];
        
        $datetime1 = new Time($vrb['start']);
        $datetime2 = new Time($vrb['end']);
        
        // Calculate stock metrics
        $quantite = 0;
        $price = 0;
        $prixdachat = 0;
        
        $this->loadModel('Whproducts');
        
        // Get warehouses for the user's default warehouse
        $warehouses = $this->Whproducts->Warehouses->find('all')
            ->where([
                'Warehouses.id' => $this->Auth->user('defaultwh'),
                'Warehouses.whtype_id' => 2
            ]);
        
        $warehouseIds = [];
        foreach($warehouses as $warehouse){
            $warehouseIds[] = $warehouse->id;
        }

        // Get warehouse products with related data
        $whproducts = $this->Whproducts->find('all')
            ->contain([
                'Packs.Categories',
                'Packs',
                'Packs.Prices' => function($q){
                    return $q->where(['Prices.tarif_id IS' => null]);
                }
            ])
            ->where(['Whproducts.warehouse_id IN' => $warehouseIds]);

        foreach($whproducts as $whproduct){
            $quantite += $whproduct->quantity;
            if (!empty($whproduct->pack->prices[0])) {
                $price += ($whproduct->pack->prices[0]->price * $whproduct->quantity);
            }
            $prixdachat += ($whproduct->pack->buyingprice * $whproduct->quantity);
        }
        
        $this->set(compact('vrb','datetime1','datetime2','quantite', 'price','prixdachat'));
    }
    
    public function mouvements(){
        
    }
    public function mouvementdata(){
        $vrb = $_GET['keyword'];

        $this->set(compact('vrb'));
    }
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $company = $this->Companies->newEntity();
        if ($this->request->is('post')) {
            debug($this->request->getData());
            die();
            $company = $this->Companies->patchEntity($company, $this->request->getData());
            if ($this->Companies->save($company)) {
                $this->Flash->success(__('The company has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The company could not be saved. Please, try again.'));
        }
        $this->set(compact('company'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Company id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit()
    {
        $id=$this->Auth->user('company_id');
        $company = $this->Companies->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $company = $this->Companies->patchEntity($company, $this->request->getData());
            if ($this->Companies->save($company)) {
                $this->Flash->success(__('La société a été modifée.'));

                return $this->redirect(['controller'=>'Pages','action' => 'home']);
            }
            $this->Flash->error(__('La société n`\'a pas pu être modifiée. Veuillez réessayer.'));
        }
        $this->set(compact('company'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Company id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    
}
