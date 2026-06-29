<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Unites Controller
 *
 * @property \App\Model\Table\UnitesTable $Unites
 *
 * @method \App\Model\Entity\Unite[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 
 0: Innactif
 1: actif

 */
class UnitesController extends AppController
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
     * @param string|null $id Unite id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $unite = $this->Unites->get($id, [
            'contain' => ['Unites'],
        ]);

        $this->set('unite', $unite);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $unite = $this->Unites->newEntity();
        if ($this->request->is('post')) {
            $unite = $this->Unites->patchEntity($unite, $this->request->getData());
            if ($this->request->getData('statut')=='on') {
                $unite->statut=1;
            }else{
                $unite->statut=0;
            }
            $code=$this->Unites->Companies->Companycodes->find('all')->where(['controleur'=>'Unites','company_id'=>$this->Auth->user('company_id')])->last();
            $unite->code=$code->prefixe.($code->compteur+1);
            $unite->company_id=$this->Auth->user('company_id');
            if ($this->Unites->save($unite)) {
                $code->compteur=$code->compteur+1;
                $this->Unites->Companies->Companycodes->save($code);
                $this->Flash->success(__('L\'unité a été enregistrée.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('L\'unité n\'a pas pu être enregistrée. Veuillez réessayer.'));
        }
        $this->set(compact('unite'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Unite id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $unite = $this->Unites->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $unite = $this->Unites->patchEntity($unite, $this->request->getData());
            if ($this->Unites->save($unite)) {
                $this->Flash->success(__('L\'unité a été modifiée.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('L\'unité n\'a pas pu être modifiée. Veuillez réessayer.'));
        }
        $this->set(compact('unite'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Unite id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $unite = $this->Unites->get($id);
        if ($this->Unites->delete($unite)) {
            $this->Flash->success(__('The unite has been deleted.'));
        } else {
            $this->Flash->error(__('The unite could not be deleted. Please, try again.'));
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
        $sel=$this->Unites->find('all')->where(['Unites.company_id'=>$this->Auth->user('company_id')]);

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery=$this->Unites->find('all')->order([$columnName => $columnSortOrder])->where(['Unites.company_id'=>$this->Auth->user('company_id')]);
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
        foreach ($empQuery as $key => $unite) {
            
            $data[] = [
                "Code"=> $unite->code,
                "Title"=>$unite->title,
                "Status"=> $unite->statut,
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
