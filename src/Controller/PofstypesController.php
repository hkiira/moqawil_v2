<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Pofstypes Controller
 *
 * @property \App\Model\Table\PofstypesTable $Pofstypes
 *
 * @method \App\Model\Entity\Pofstype[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])

 0: innactif
 1: actif
 
  */
class PofstypesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Companies'],
        ];
        $pofstypes = $this->paginate($this->Pofstypes);

        $this->set(compact('pofstypes'));
    }

    /**
     * View method
     *
     * @param string|null $id Pofstype id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $pofstype = $this->Pofstypes->get($id, [
            'contain' => ['Companies', 'Pofsales'],
        ]);

        $this->set('pofstype', $pofstype);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $pofstype = $this->Pofstypes->newEntity();
        if ($this->request->is('post')) {
            $pofstype = $this->Pofstypes->patchEntity($pofstype, $this->request->getData());
            if ($this->request->getData('statut')=='on') {
                $pofstype->statut=1;
            }else{
                $pofstype->statut=0;
            }
            $pofstype->company_id=1;
            
            if ($this->Pofstypes->save($pofstype)) {
                $this->Flash->success(__('The pofstype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The pofstype could not be saved. Please, try again.'));
        }
        $companies = $this->Pofstypes->Companies->find('list', ['limit' => 200]);
        $this->set(compact('pofstype', 'companies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Pofstype id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $pofstype = $this->Pofstypes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $pofstype = $this->Pofstypes->patchEntity($pofstype, $this->request->getData());
            if ($this->Pofstypes->save($pofstype)) {
                $this->Flash->success(__('The pofstype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The pofstype could not be saved. Please, try again.'));
        }
        $companies = $this->Pofstypes->Companies->find('list', ['limit' => 200]);
        $this->set(compact('pofstype', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Pofstype id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $pofstype = $this->Pofstypes->get($id);
        if ($this->Pofstypes->delete($pofstype)) {
            $this->Flash->success(__('The pofstype has been deleted.'));
        } else {
            $this->Flash->error(__('The pofstype could not be deleted. Please, try again.'));
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
                $columnName="Pofstypes.title";
                break;
            case 'Title':
                $columnName="Pofstypes.code";
                break;
            case 'Status':
                $columnName="Pofstypes.statut";
                break;
            default:
                $columnName="Pofstypes.title";
                break;
        }
        ## Total number of records with filtering
        $sel=$this->Pofstypes->find('all');

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery=$this->Pofstypes->find('all')->order([$columnName => $columnSortOrder]);
        if ($row==0) {
            $empQuery->limit($rowperpage);
        }else{
            $empQuery->limit($rowperpage);
            $empQuery->page(($row/$rowperpage)+1);
        }
        
        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['Pofstypes.title LIKE' => '%'.$searchValue.'%'],
                ['lower(Pofstypes.title) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Pofstypes.code) LIKE'=>'%'.$searchValue.'%'],
                ['Pofstypes.code LIKE' => '%'.$searchValue.'%']]]);
            $empQuery->where(["OR"=>[
                ['Pofstypes.title LIKE' => '%'.$searchValue.'%'],
                ['lower(Pofstypes.title) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Pofstypes.code) LIKE'=>'%'.$searchValue.'%'],
                ['Pofstypes.code LIKE' => '%'.$searchValue.'%']]]);
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
        foreach ($empQuery as $key => $pofstype) {
            
            $data[] = [
                "Code"=> $pofstype->code,
                "Title"=>$pofstype->title,
                "Status"=> $pofstype->statut,
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
