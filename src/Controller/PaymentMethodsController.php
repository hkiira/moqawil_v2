<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Routing\Router;

/**
 * PaymentMethods Controller
 *
 * @property \App\Model\Table\PaymentMethodsTable $PaymentMethods
 */
class PaymentMethodsController extends AppController
{
    /**
     * Initialize method
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    /**
     * BeforeFilter method
     *
     * @param \Cake\Event\Event $event Event object
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['index', 'view']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => [],
            'order' => ['PaymentMethods.name' => 'ASC']
        ];
        $paymentMethods = $this->paginate($this->PaymentMethods);

        $this->set(compact('paymentMethods'));
        $this->set('_serialize', ['paymentMethods']);
    }

    /**
     * Search method for datatable
     *
     * @return \Cake\Http\Response|null
     */
    public function search()
    {  

        $draw = $_GET['draw'];
        $row = $_GET['start'];
        $rowperpage = $_GET['length']; // Rows display per page
        $columnIndex = $_GET['order'][0]['column']; // Column index
        $columnName = $_GET['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_GET['order'][0]['dir']; // asc or desc
        $searchValue = $_GET['search']['value']; // Search value
        switch($columnName) {
            case 'Title':
                $columnName="PaymentMethods.name";
                break;
            default:
                $columnName="PaymentMethods.name";
                break;
        }
        ## Total number of records with filtering
        $sel=$this->PaymentMethods->find('all');

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery=$this->PaymentMethods->find('all');
        $empQuery->order([$columnName => $columnSortOrder]);
        if ($row==0) {
            $empQuery->limit($rowperpage);
        }else{
            $empQuery->limit($rowperpage);
            $empQuery->page(($row/$rowperpage)+1);
        }
        
        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['PaymentMethods.name LIKE' => '%'.$searchValue.'%'],
                ['lower(PaymentMethods.code) LIKE'=>'%'.$searchValue.'%']]]);
            $empQuery->where(["OR"=>[
                ['PaymentMethods.name LIKE' => '%'.$searchValue.'%'],
                ['lower(PaymentMethods.code) LIKE'=>'%'.$searchValue.'%']]]);
           $empQuery->page(1);
        }
        
        if ($draw=0) {
            $empQuery->page(1);
        }
        
        ## Total number of records with filtering
        $totalRecordwithFilter = $sel->last()->count;
        ## Fetch records
        $data =[];
        foreach ($empQuery as $key => $paymentMethod) {
            $data[] = [
                "Title"=>$paymentMethod->name,
                "Status"=> $paymentMethod->active,
                "Actions"=> '<div class="dropdown dropdown-inline">
                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                                    <i class="la la-cog"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <ul class="nav nav-hoverable flex-column">
                                        <li class="nav-item"><a class="nav-link" href="'.Router::Url('/payment-methods/edit/'.$paymentMethod->id).'"><i class="nav-icon la la-edit"></i><span class="nav-text">Modifier</span></a></li>
                                        <li class="nav-item"><a class="nav-link" href="'.Router::Url('/payment-methods/update/'.$paymentMethod->id).'"><i class="nav-icon la la-edit"></i><span class="nav-text">Modifier les accés</span></a></li>
                                    </ul>
                                </div>
                            </div>'
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

    /**
     * View method
     *
     * @param string|null $id Payment Method id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $paymentMethod = $this->PaymentMethods->get($id, [
            'contain' => ['OrderPayments']
        ]);

        $this->set('paymentMethod', $paymentMethod);
        $this->set('_serialize', ['paymentMethod']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $paymentMethod = $this->PaymentMethods->newEntity();
        if ($this->request->is('post')) {
            $paymentMethod = $this->PaymentMethods->patchEntity($paymentMethod, $this->request->getData());
            if ($this->PaymentMethods->save($paymentMethod)) {
                $this->Flash->success(__('The payment method has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The payment method could not be saved. Please, try again.'));
        }
        $this->set(compact('paymentMethod'));
        $this->set('_serialize', ['paymentMethod']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Payment Method id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $paymentMethod = $this->PaymentMethods->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $paymentMethod = $this->PaymentMethods->patchEntity($paymentMethod, $this->request->getData());
            if ($this->PaymentMethods->save($paymentMethod)) {
                $this->Flash->success(__('The payment method has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The payment method could not be saved. Please, try again.'));
        }
        $this->set(compact('paymentMethod'));
        $this->set('_serialize', ['paymentMethod']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Payment Method id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $paymentMethod = $this->PaymentMethods->get($id);
        
        // Check if payment method is in use
        if ($this->PaymentMethods->OrderPayments->exists(['payment_method_id' => $id])) {
            $this->Flash->error(__('This payment method cannot be deleted because it is being used in order payments.'));
            return $this->redirect(['action' => 'index']);
        }

        if ($this->PaymentMethods->delete($paymentMethod)) {
            $this->Flash->success(__('The payment method has been deleted.'));
        } else {
            $this->Flash->error(__('The payment method could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Get payment method details
     *
     * @param string|null $id Payment Method id.
     * @return \Cake\Http\Response|null
     */
    public function getPaymentMethod($id = null)
    {
        $this->autoRender = false;
        $this->response = $this->response->withType('json');
        
        $paymentMethod = $this->PaymentMethods->get($id);
        
        $response = [
            'id' => $paymentMethod->id,
            'name' => $paymentMethod->name,
            'code' => $paymentMethod->code,
            'requires_cheque_date' => $paymentMethod->requires_cheque_date,
            'active' => $paymentMethod->active
        ];
        
        echo json_encode($response);
        exit;
    }
} 