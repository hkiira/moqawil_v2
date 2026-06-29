<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Customertypes Controller
 *
 * @property \App\Model\Table\CustomertypesTable $Customertypes
 *
 * @method \App\Model\Entity\Customertype[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 
 0: innactif
 1: actif
 
 */
class CustomertypesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
    }

    /**
     * View method
     *
     * @param string|null $id Customertype id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $customertype = $this->Customertypes->get($id, [
            'contain' => ['Companies', 'Prices'],
        ]);

        $this->set('customertype', $customertype);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $customertype = $this->Customertypes->newEntity();
        if ($this->request->is('post')) {
            $customertype = $this->Customertypes->patchEntity($customertype, $this->request->getData());
            if ($this->request->getData('statut')=='on') {
                $customertype->statut=1;
            }else{
                $customertype->statut=0;
            }
            $customertype->company_id=$this->Auth->user('company_id');
            $code=$this->Customertypes->Companies->Companycodes->find('all')->where(['controleur'=>'Customertypes','company_id'=>$this->Auth->user('company_id')])->last();
            $customertype->code=$code->prefixe.($code->compteur+1);
            if ($this->Customertypes->save($customertype)) {
                $code->compteur=$code->compteur+1;
                $this->Customertypes->Companies->Companycodes->save($code);

                $this->Flash->success(__('Le type de client a été enregistré.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Le type de client n\'a pas pu être enregistré. Veuillez réessayer.'));
        }
        $this->set(compact('customertype'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Customertype id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $customertype = $this->Customertypes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $customertype = $this->Customertypes->patchEntity($customertype, $this->request->getData());
            if ($this->request->getData('statut')=='on') {
                $customertype->statut=1;
            }else{
                $customertype->statut=0;
            }
            if ($this->Customertypes->save($customertype)) {
                $this->Flash->success(__('Le type de client a été enregistré.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Le type de client n\'a pas pu être enregistré. Veuillez réessayer.'));
        }
        $companies = $this->Customertypes->Companies->find('list', ['limit' => 200]);
        $this->set(compact('customertype', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Customertype id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $customertype = $this->Customertypes->get($id);
        if ($this->Customertypes->delete($customertype)) {
            $this->Flash->success(__('The customertype has been deleted.'));
        } else {
            $this->Flash->error(__('The customertype could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

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
            case 'Code':
                $columnName="Customertypes.code";
                break;
            case 'Title':
                $columnName="Customertypes.title";
                break;
            case 'Status':
                $columnName="Customertypes.statut";
                break;
            default:
                $columnName="Customertypes.title";
                break;
        }
        ## Total number of records with filtering
        $sel=$this->Customertypes->find('all')->order([$columnName => $columnSortOrder])->where(['Customertypes.company_id'=>$this->Auth->user('company_id')]);

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery=$this->Customertypes->find('all')->order([$columnName => $columnSortOrder])->where(['Customertypes.company_id'=>$this->Auth->user('company_id')]);
        if ($row==0) {
            $empQuery->limit($rowperpage);
        }else{
            $empQuery->limit($rowperpage);
            $empQuery->page(($row/$rowperpage)+1);
        }
        
        
        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['Customertypes.title LIKE' => '%'.$searchValue.'%'],
                ['lower(Customertypes.title) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Customertypes.code) LIKE'=>'%'.$searchValue.'%'],
                ['Customertypes.code LIKE' => '%'.$searchValue.'%']]]);
            $empQuery->where(["OR"=>[
                ['Customertypes.title LIKE' => '%'.$searchValue.'%'],
                ['lower(Customertypes.title) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Customertypes.code) LIKE'=>'%'.$searchValue.'%'],
                ['Customertypes.code LIKE' => '%'.$searchValue.'%']]]);
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
        foreach ($empQuery as $key => $customertype) {
            
            $data[] = [
                "Code"=> $customertype->code,
                "Title"=>$customertype->title,
                "Status"=> $customertype->statut,
                "Actions"=> '<div class="dropdown dropdown-inline">
                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                                    <i class="la la-cog"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <ul class="nav nav-hoverable flex-column">
                                        <li class="nav-item"><a class="nav-link" href="#"><i class="nav-icon la la-edit"></i><span class="nav-text">Modifier</span></a></li>
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
}
