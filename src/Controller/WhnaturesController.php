<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Whnatures Controller
 *
 * @property \App\Model\Table\WhnaturesTable $Whnatures
 *
 * @method \App\Model\Entity\Whnature[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 
 0: Innactif
 1: actif

 */
class WhnaturesController extends AppController
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
        $whnatures = $this->paginate($this->Whnatures);

        $this->set(compact('whnatures'));
    }

    /**
     * View method
     *
     * @param string|null $id Whnature id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $whnature = $this->Whnatures->get($id, [
            'contain' => ['Companies', 'Warehouses'],
        ]);

        $this->set('whnature', $whnature);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $whnature = $this->Whnatures->newEntity();
        if ($this->request->is('post')) {
            $whnature = $this->Whnatures->patchEntity($whnature, $this->request->getData());
            if ($this->request->getData('statut')=='on') {
                $whnature->statut=1;
            }else{
                $whnature->statut=0;
            }
            $whnature->company_id=1;
            if ($this->Whnatures->save($whnature)) {
                $this->Flash->success(__('The whnature has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The whnature could not be saved. Please, try again.'));
        }
        $companies = $this->Whnatures->Companies->find('list', ['limit' => 200]);
        $this->set(compact('whnature', 'companies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Whnature id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $whnature = $this->Whnatures->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $whnature = $this->Whnatures->patchEntity($whnature, $this->request->getData());
            if ($this->Whnatures->save($whnature)) {
                $this->Flash->success(__('The whnature has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The whnature could not be saved. Please, try again.'));
        }
        $companies = $this->Whnatures->Companies->find('list', ['limit' => 200]);
        $this->set(compact('whnature', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Whnature id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $whnature = $this->Whnatures->get($id);
        if ($this->Whnatures->delete($whnature)) {
            $this->Flash->success(__('The whnature has been deleted.'));
        } else {
            $this->Flash->error(__('The whnature could not be deleted. Please, try again.'));
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
        $sel=$this->Whnatures->find('all');

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery=$this->Whnatures->find('all')->order([$columnName => $columnSortOrder]);
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
        foreach ($empQuery as $key => $whnature) {
            
            $data[] = [
                "Code"=> $whnature->code,
                "Title"=>$whnature->title,
                "Status"=> $whnature->statut,
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
