<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Visites Controller
 *
 * @property \App\Model\Table\VisitesTable $Visites
 *
 * @method \App\Model\Entity\Visite[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class VisitesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Customers', 'Companies', 'Orders'],
        ];
        $visites = $this->paginate($this->Visites);

        $this->set(compact('visites'));
    }

    /**
     * Map method
     *
     * @return \Cake\Http\Response|null
     */
    public function map()
    {
        $query = $this->Visites->find()
            ->contain(['Customers' => ['Zones', 'Photos'], 'Users'])
            ->order(['Visites.created' => 'DESC']);

        $user_id = $this->request->getQuery('user_id');
        $zone_id = $this->request->getQuery('zone_id');
        $date_start = $this->request->getQuery('date_start');
        $date_end = $this->request->getQuery('date_end');

        if (empty($date_start) && !isset($this->request->query['date_start'])) {
            $date_start = date('Y-m-01');
        }
        if (empty($date_end) && !isset($this->request->query['date_end'])) {
            $date_end = date('Y-m-t');
        }

        if ($user_id) {
            $query->where(['Visites.user_id' => $user_id]);
        }
        if ($zone_id) {
            $query->innerJoinWith('Customers')
                  ->where(['Customers.zone_id' => $zone_id]);
        }
        if ($date_start) {
            $query->where(['Visites.created >=' => $date_start . ' 00:00:00']);
        }
        if ($date_end) {
            $query->where(['Visites.created <=' => $date_end . ' 23:59:59']);
        }

        $allVisites = $query->all();
        $latestVisites = [];
        foreach ($allVisites as $visite) {
            if (!isset($latestVisites[$visite->customer_id])) {
                $latestVisites[$visite->customer_id] = $visite;
            }
        }

        $this->loadModel('Orders');
        foreach ($latestVisites as $key => $visite) {
            $orderCount = $this->Orders->find()->where(['customer_id' => $visite->customer_id])->count();
            
            $lastOrderTotal = 0;
            $lastOrderId = $visite->order_id;
            
            if (!$lastOrderId) {
                $lastOrder = $this->Orders->find()
                    ->where(['customer_id' => $visite->customer_id])
                    ->order(['created' => 'DESC'])
                    ->first();
                if ($lastOrder) {
                    $lastOrderId = $lastOrder->id;
                }
            }
            
            if ($lastOrderId) {
                $orderPacks = $this->Orders->Orderpacks->find()->where(['order_id' => $lastOrderId])->all();
                foreach ($orderPacks as $pack) {
                    $lastOrderTotal += ($pack->price * $pack->quantity);
                }
            }
            
            $querySum = $this->Orders->find()
                ->leftJoinWith('Orderpacks')
                ->where([
                    'Orders.customer_id' => $visite->customer_id,
                    'Orderpacks.loyaltypointgift_id IS' => NULL
                ])
                ->select([
                    'loyaltypoints_sum' => $this->Orders->find()->newExpr(
                        'SUM(CASE WHEN Orders.ordertype_id = 1 THEN Orderpacks.loyaltypoints * Orderpacks.quantity ELSE 0 END) - '
                        . 'SUM(CASE WHEN Orders.ordertype_id = 2 THEN Orderpacks.loyaltypoints * Orderpacks.quantity ELSE 0 END)'
                    )
                ])
                ->first();
                
            $visite->loyaltypoints_sum = $querySum ? (float)$querySum->loyaltypoints_sum : 0;
            
            $visite->order_count = $orderCount;
            $visite->last_order_total = $lastOrderTotal;
        }

        // Fetch last visit date for all customers
        $lastVisits = $this->Visites->find();
        $lastVisits->select([
            'customer_id',
            'max_created' => $lastVisits->func()->max('Visites.created')
        ])->group(['customer_id']);

        $lastVisitDates = [];
        foreach ($lastVisits as $lv) {
            $val = $lv->max_created;
            if ($val instanceof \DateTimeInterface) {
                $lastVisitDates[$lv->customer_id] = $val->format('d/m/Y H:i');
            } elseif (is_string($val)) {
                $lastVisitDates[$lv->customer_id] = date('d/m/Y H:i', strtotime($val));
            } else {
                $lastVisitDates[$lv->customer_id] = null;
            }
        }

        foreach ($latestVisites as $visite) {
            $visite->last_visite_date = isset($lastVisitDates[$visite->customer_id]) ? $lastVisitDates[$visite->customer_id] : null;
        }

        // Collect IDs of customers who were visited in the period
        $visitedCustomerIds = array_keys($latestVisites);

        // Fetch ALL customers with GPS coords who were NOT visited in the date range
        $this->loadModel('Customers');
        $unvisitedQuery = $this->Customers->find()
            ->contain(['Zones', 'Photos'])
            ->where([
                'Customers.latitude IS NOT' => null,
                'Customers.latitude !=' => '',
                'Customers.longitude IS NOT' => null,
                'Customers.longitude !=' => '',
            ]);

        if ($zone_id) {
            $unvisitedQuery->where(['Customers.zone_id' => $zone_id]);
        }

        if (!empty($visitedCustomerIds)) {
            $unvisitedQuery->where(['Customers.id NOT IN' => $visitedCustomerIds]);
        }

        $unvisitedCustomers = $unvisitedQuery->all();

        foreach ($unvisitedCustomers as $customer) {
            $customer->last_visite_date = isset($lastVisitDates[$customer->id]) ? $lastVisitDates[$customer->id] : __('Aucune visite');
        }

        $users = $this->Visites->Users->find('list', [
            'keyField' => 'id',
            'valueField' => function ($e) {
                return trim($e->firstname . ' ' . $e->lastname);
            }
        ]);
        
        $zones = $this->Visites->Customers->Zones->find('list', ['limit' => 200]);

        $this->set(compact('latestVisites', 'unvisitedCustomers', 'users', 'zones', 'user_id', 'zone_id', 'date_start', 'date_end'));
    }

    /**
     * View method
     *
     * @param string|null $id Visite id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $visite = $this->Visites->get($id, [
            'contain' => ['Customers', 'Companies', 'Orders'],
        ]);

        $this->set('visite', $visite);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $visite = $this->Visites->newEntity();
        if ($this->request->is('post')) {
            $visite = $this->Visites->patchEntity($visite, $this->request->getData());
            if ($this->Visites->save($visite)) {
                $this->Flash->success(__('The visite has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The visite could not be saved. Please, try again.'));
        }
        $customers = $this->Visites->Customers->find('list', ['limit' => 200]);
        $companies = $this->Visites->Companies->find('list', ['limit' => 200]);
        $orders = $this->Visites->Orders->find('list', ['limit' => 200]);
        $this->set(compact('visite', 'customers', 'companies', 'orders'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Visite id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $visite = $this->Visites->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $visite = $this->Visites->patchEntity($visite, $this->request->getData());
            if ($this->Visites->save($visite)) {
                $this->Flash->success(__('The visite has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The visite could not be saved. Please, try again.'));
        }
        $customers = $this->Visites->Customers->find('list', ['limit' => 200]);
        $companies = $this->Visites->Companies->find('list', ['limit' => 200]);
        $orders = $this->Visites->Orders->find('list', ['limit' => 200]);
        $this->set(compact('visite', 'customers', 'companies', 'orders'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Visite id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $visite = $this->Visites->get($id);
        if ($this->Visites->delete($visite)) {
            $this->Flash->success(__('The visite has been deleted.'));
        } else {
            $this->Flash->error(__('The visite could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
