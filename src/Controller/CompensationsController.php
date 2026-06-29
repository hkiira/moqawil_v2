<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Compensations Controller
 *
 * @property \App\Model\Table\CompensationsTable $Compensations
 *
 * @method \App\Model\Entity\Compensation[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CompensationsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $query = $this->Compensations->find()
            ->contain([
                'Users',
                'CommissionTiers',
                'Orders' => ['Orderpacks']
            ]);

        // Filter by date range if provided
        $dateStart = $this->request->getQuery('date_start');
        $dateEnd = $this->request->getQuery('date_end');
        $userId = $this->request->getQuery('user_id');
        $statut = $this->request->getQuery('statut');

        if ($dateStart) {
            $query->where(['Compensations.datedepart >=' => $dateStart]);
        }

        if ($dateEnd) {
            $query->where(['Compensations.datefin <=' => $dateEnd]);
        }

        if ($userId) {
            $query->where(['Compensations.user_id' => $userId]);
        }

        if ($statut !== null && $statut !== '') {
            $query->where(['Compensations.statut' => $statut]);
        }

        $this->paginate = [
            'order' => ['Compensations.created' => 'DESC']
        ];
        $compensations = $this->paginate($query);

        $users = $this->Compensations->Users->find('list', [
            'keyField' => 'id',
            'valueField' => function($user) {
                return $user->firstname . ' ' . $user->lastname;
            },
            'conditions' => [
                'Users.role_id IN' => [3, 5]
            ],
            'order' => ['Users.firstname' => 'ASC']
        ]);
        $this->set(compact('compensations', 'users', 'dateStart', 'dateEnd', 'userId', 'statut'));
    }

    /**
     * View method
     *
     * @param string|null $id Compensation id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $compensation = $this->Compensations->get($id, [
            'contain' => [
                'Users',
                'Orders' => [
                    'Users',
                    'Customers',
                    'Orderpacks'
                ]
            ],
        ]);

        $this->set('compensation', $compensation);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $compensation = $this->Compensations->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            // Always generate a unique code
            $data['code'] = 'COMP-' . date('Ymd') . '-' . uniqid();
            
            // Set default status to 1 (Processed)
            $data['statut'] = 1;
            
            $compensation = $this->Compensations->patchEntity($compensation, $data);
            
            // First save to get the ID
            if ($this->Compensations->save($compensation)) {
                // Update selected orders with this compensation_id
                if (!empty($data['order_ids'])) {
                    $this->loadModel('Orders');
                    $orderIds = $data['order_ids'];
                    
                    $this->Orders->updateAll(
                        ['compensation_id' => $compensation->id],
                        ['id IN' => $orderIds]
                    );
                    
                    // Reload compensation with orders to calculate commission
                    $compensation = $this->Compensations->get($compensation->id, [
                        'contain' => [
                            'Orders' => [
                                'Orderpacks' => [
                                    'Packs' => ['MeasurementUnits']
                                ]
                            ]
                        ]
                    ]);
                    
                    // Calculate and save commission
                    $compensation = $this->Compensations->calculateAndSaveCommission($compensation);
                    $this->Compensations->save($compensation);
                }
                
                $this->Flash->success(__('le paiement a été enregistrée avec succès.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Le paiement n\'a pas pu être enregistré. Veuillez réessayer.'));
        }
        
        // Get users with role_id 3 or 5
        $users = $this->Compensations->Users->find('list', [
            'keyField' => 'id',
            'valueField' => function($user) {
                return $user->firstname . ' ' . $user->lastname;
            },
            'conditions' => [
                'Users.role_id IN' => [3, 5]
            ],
            'order' => ['Users.firstname' => 'ASC']
        ]);
        
        // Get orders with null compensation_id for the selected user or all
        $this->loadModel('Orders');
        $ordersQuery = $this->Orders->find()
            ->where([
                'Orders.compensation_id IS' => null,
                'Orders.statut' => 6
            ])
            ->contain([
                'Users', 
                'Customers', 
                'Ordertypes', 
                'Orderpacks' => [
                    'Packs' => ['MeasurementUnits']
                ]
            ])
            ->order(['Orders.created' => 'DESC']);
        
        $orders = $ordersQuery->toArray();
        
        // Load commission tiers for display
        $this->loadModel('CommissionTiers');
        $commissionTiers = $this->CommissionTiers->find('active')->toArray();
        
        $this->set(compact('compensation', 'users', 'orders', 'commissionTiers'));
    }

    /**
     * Print PDF of a compensation with its orders and totals
     *
     * @param int|null $id Compensation id
     * @return \Cake\Http\Response|null
     */
    public function print($id = null)
    {
        $compensation = $this->Compensations->get($id, [
            'contain' => [
                'Users',
                'Orders' => [
                    'Users',
                    'Customers',
                    'Orderpacks',
                    'Ordertypes'
                ]
            ],
        ]);

        $this->viewBuilder()->setLayout(false);
        $this->set(compact('compensation'));

        $response = $this->render('print');
        $html = method_exists($response, 'getBody') ? (string)$response->getBody() : $response->body();

        $filename = 'Compensation_' . $compensation->code . '.pdf';

        // Generate PDF using mPDF
        $mpdf = new \Mpdf\Mpdf(['tempDir' => TMP . 'mpdf']);
        $mpdf->WriteHTML($html);
        return $this->response
            ->withType('pdf')
            ->withHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->withStringBody($mpdf->Output($filename, \Mpdf\Output\Destination::STRING_RETURN));
    }

    /**
     * Edit method
     *
     * @param string|null $id Compensation id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $compensation = $this->Compensations->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $compensation = $this->Compensations->patchEntity($compensation, $this->request->getData());
            if ($this->Compensations->save($compensation)) {
                $this->Flash->success(__('The compensation has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The compensation could not be saved. Please, try again.'));
        }
        $users = $this->Compensations->Users->find('list', [
            'keyField' => 'id',
            'valueField' => function($user) {
                return $user->firstname . ' ' . $user->lastname;
            },
            'conditions' => [
                'Users.role_id IN' => [3, 5]
            ],
            'order' => ['Users.firstname' => 'ASC']
        ]);
        $this->set(compact('compensation', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Compensation id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $compensation = $this->Compensations->get($id);
        if ($this->Compensations->delete($compensation)) {
            $this->Flash->success(__('The compensation has been deleted.'));
        } else {
            $this->Flash->error(__('The compensation could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Calculate compensation for a user based on date range
     *
     * @return \Cake\Http\Response|null
     */
    public function calculate()
    {
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $userId = $data['user_id'];
            $dateStart = $data['datedepart'];
            $dateEnd = $data['datefin'];
            
            // Load Orders table to calculate compensation amount
            $this->loadModel('Orders');
            
            // Find orders for the user within the date range
            $orders = $this->Orders->find()
                ->where([
                    'Orders.user_id' => $userId,
                    'Orders.created >=' => $dateStart,
                    'Orders.created <=' => $dateEnd
                ])
                ->toArray();
            
            // Calculate total (this logic can be customized based on your needs)
            $totalAmount = 0;
            foreach ($orders as $order) {
                // Add your calculation logic here
                // $totalAmount += $order->amount or similar
            }
            
            $this->set(compact('userId', 'dateStart', 'dateEnd', 'orders', 'totalAmount'));
        }
        
        $users = $this->Compensations->Users->find('list', [
            'keyField' => 'id',
            'valueField' => function($user) {
                return $user->firstname . ' ' . $user->lastname;
            },
            'conditions' => [
                'Users.role_id IN' => [3, 5]
            ],
            'order' => ['Users.firstname' => 'ASC']
        ]);
        $this->set(compact('users'));
    }
}
