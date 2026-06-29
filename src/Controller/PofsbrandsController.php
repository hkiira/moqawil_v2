<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Pofsbrands Controller
 *
 * @property \App\Model\Table\PofsbrandsTable $Pofsbrands
 *
 * @method \App\Model\Entity\Pofsbrand[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])

 0: innactif
 1: actif
 
  */
class PofsbrandsController extends AppController
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
     * @param string|null $id Pofsbrand id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $pofsbrand = $this->Pofsbrands->get($id, [
            'contain' => ['Companies', 'Pofsmodeles'],
        ]);

        $this->set('pofsbrand', $pofsbrand);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $pofsbrand = $this->Pofsbrands->newEntity();
        if ($this->request->is('post')) {
            $pofsbrand = $this->Pofsbrands->patchEntity($pofsbrand, $this->request->getData());
            if ($this->request->getData('statut')=='on') {
                $pofsbrand->statut=1;
            }else{
                $pofsbrand->statut=0;
            }
            $pofsbrand->company_id=$this->Auth->user('company_id');
            $code=$this->Pofsbrands->Companies->Companycodes->find('all')->where(['controleur'=>'Pofsbrands','company_id'=>$this->Auth->user('company_id')])->last();
            $pofsbrand->code=$code->prefixe.($code->compteur+1);
            if ($this->Pofsbrands->save($pofsbrand)) {
                $code->compteur=$code->compteur+1;
                $this->Pofsbrands->Companies->Companycodes->save($code);
                $this->Flash->success(__('La marque a été enregistrée.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('La marque n\'a pas pu être enregistrée. Veuillez réessayer.'));
        }
        $this->set(compact('pofsbrand'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Pofsbrand id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $pofsbrand = $this->Pofsbrands->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $pofsbrand = $this->Pofsbrands->patchEntity($pofsbrand, $this->request->getData());
            if ($pofsbrand->statut=='on') {
                $pofsbrand->statut=1;
            }else{
                $pofsbrand->statut=0;
            }
            if ($this->Pofsbrands->save($pofsbrand)) {
                $this->Flash->success(__('La marque a été enregistrée.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('La marque n\'a pas pu être enregistrée. Veuillez réessayer.'));
        }
        $this->set(compact('pofsbrand'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Pofsbrand id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $pofsbrand = $this->Pofsbrands->get($id);
        if ($this->Pofsbrands->delete($pofsbrand)) {
            $this->Flash->success(__('The pofsbrand has been deleted.'));
        } else {
            $this->Flash->error(__('The pofsbrand could not be deleted. Please, try again.'));
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
                $columnName="Pofsbrands.code";
                break;
            case 'Title':
                $columnName="Pofsbrands.title";
                break;
            case 'Status':
                $columnName="Pofsbrands.statut";
                break;
            default:
                $columnName="Pofsbrands.title";
                break;
        }
        ## Total number of records with filtering
        $sel=$this->Pofsbrands->find('all')->where(['Pofsbrands.company_id'=>$this->Auth->user('company_id')]);

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery=$this->Pofsbrands->find('all')->order([$columnName => $columnSortOrder])->where(['Pofsbrands.company_id'=>$this->Auth->user('company_id')]);
        if ($row==0) {
            $empQuery->limit($rowperpage);
        }else{
            $empQuery->limit($rowperpage);
            $empQuery->page(($row/$rowperpage)+1);
        }
        
        
        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['Pofsbrands.title LIKE' => '%'.$searchValue.'%'],
                ['lower(Pofsbrands.title) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Pofsbrands.code) LIKE'=>'%'.$searchValue.'%'],
                ['Pofsbrands.code LIKE' => '%'.$searchValue.'%']]]);
            $empQuery->where(["OR"=>[
                ['Pofsbrands.title LIKE' => '%'.$searchValue.'%'],
                ['lower(Pofsbrands.title) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Pofsbrands.code) LIKE'=>'%'.$searchValue.'%'],
                ['Pofsbrands.code LIKE' => '%'.$searchValue.'%']]]);
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
        foreach ($empQuery as $key => $pofsbrand) {
            
            $data[] = [
                "Code"=> $pofsbrand->code,
                "Title"=>$pofsbrand->title,
                "Status"=> $pofsbrand->statut,
                "Actions"=> '<div class="dropdown dropdown-inline">
                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                                    <i class="la la-cog"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <ul class="nav nav-hoverable flex-column">
                                        <li class="nav-item"><a class="nav-link" href="'.Router::Url('/pofsbrands/edit/'.$pofsbrand->id).'"><i class="nav-icon la la-edit"></i><span class="nav-text">Modifier</span></a></li>
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
