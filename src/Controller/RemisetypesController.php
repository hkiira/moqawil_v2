<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Remisetypes Controller
 *
 * @property \App\Model\Table\RemisetypesTable $Remisetypes
 *
 * @method \App\Model\Entity\Remisetype[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])

 0: innactif
 1: actif
 
  */
class RemisetypesController extends AppController
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
     * @param string|null $id Remisetype id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $remisetype = $this->Remisetypes->newEntity();
        if ($this->request->is('post')) {
            $remisetype = $this->Remisetypes->patchEntity($remisetype, $this->request->getData());
            if ($this->request->getData('statut')=='on') {
                $remisetype->statut=1;
            }else{
                $remisetype->statut=0;
            }
            if ($this->Remisetypes->save($remisetype)) {
                $this->Flash->success(__('The remisetype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The remisetype could not be saved. Please, try again.'));
        }
        $this->set(compact('remisetype'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Remisetype id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $remisetype = $this->Remisetypes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $remisetype = $this->Remisetypes->patchEntity($remisetype, $this->request->getData());
            if ($this->request->getData('statut')=='on') {
                $remisetype->statut=1;
            }else{
                $remisetype->statut=0;
            }
            if ($this->Remisetypes->save($remisetype)) {
                $this->Flash->success(__('The remisetype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The remisetype could not be saved. Please, try again.'));
        }
        $this->set(compact('remisetype'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Remisetype id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $remisetype = $this->Remisetypes->get($id);
        if ($this->Remisetypes->delete($remisetype)) {
            $this->Flash->success(__('The remisetype has been deleted.'));
        } else {
            $this->Flash->error(__('The remisetype could not be deleted. Please, try again.'));
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
            case 'code':
                $columnName="code";
                break;
            case 'statut':
                $columnName="statut";
                break;
            default:
                $columnName="title";
                break;
        }
        ## Total number of records with filtering
        $sel=$this->Remisetypes->find('all');

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery=$this->Remisetypes->find('all')->order([$columnName => $columnSortOrder]);
        if ($row==0) {
            $empQuery->limit($rowperpage);
        }else{
            $empQuery->limit($rowperpage);
            $empQuery->page(($row/$rowperpage)+1);
        }
        
        
        if($searchValue != ''){
            $sel->where(['title LIKE' => '%'.$searchValue.'%'],['code LIKE' => '%'.$searchValue.'%']);
            $empQuery->where(['title LIKE' => '%'.$searchValue.'%'],['code LIKE' => '%'.$searchValue.'%']);
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
        foreach ($empQuery as $key => $remisetype) {
            
            $data[] = [
                "Code"=> $remisetype->code,
                "Title"=>$remisetype->title,
                "Status"=> $remisetype->statut,
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
