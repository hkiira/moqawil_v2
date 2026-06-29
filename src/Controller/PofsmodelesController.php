<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Pofsmodeles Controller
 *
 * @property \App\Model\Table\PofsmodelesTable $Pofsmodeles
 *
 * @method \App\Model\Entity\Pofsmodele[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])

 0: innactif
 1: actif
 
  */
class PofsmodelesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(){}

    /**
     * View method
     *
     * @param string|null $id Pofsmodele id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $pofsmodele = $this->Pofsmodeles->get($id, [
            'contain' => ['Pofsbrands', 'Companies', 'Pofsales'],
        ]);

        $this->set('pofsmodele', $pofsmodele);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $pofsmodele = $this->Pofsmodeles->newEntity();
        if ($this->request->is('post')) {
            $pofsmodele = $this->Pofsmodeles->patchEntity($pofsmodele, $this->request->getData());
            if ($this->request->getData('statut')=='on') {
                $pofsmodele->statut=1;
            }else{
                $pofsmodele->statut=0;
            }

            $pofsmodele->company_id=$this->Auth->user('company_id');
            $code=$this->Pofsmodeles->Companies->Companycodes->find('all')->where(['controleur'=>'Pofsmodeles','company_id'=>$this->Auth->user('company_id')])->last();
            $pofsmodele->code=$code->prefixe.($code->compteur+1);
            
            if ($this->Pofsmodeles->save($pofsmodele)) {
                $code->compteur=$code->compteur+1;
                $this->Pofsmodeles->Companies->Companycodes->save($code);
                $this->Flash->success(__('Le modèle a été enregistré.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Le modèle n\'a pas pu être enregistré. Veuillez réessayer.'));
        }
        $pofsbrands = $this->Pofsmodeles->Pofsbrands->find('list')->where(['statut'=>1,'company_id'=>$this->Auth->user('company_id')]);
        $this->set(compact('pofsmodele', 'pofsbrands'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Pofsmodele id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $pofsmodele = $this->Pofsmodeles->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $pofsmodele = $this->Pofsmodeles->patchEntity($pofsmodele, $this->request->getData());
            if ($pofsmodele->statut=='on') {
                $pofsmodele->statut=1;
            }else{
                $pofsmodele->statut=0;
            }
            if ($this->Pofsmodeles->save($pofsmodele)) {
                $this->Flash->success(__('Le modèle a été enregistré.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Le modèle n\'a pas pu être enregistré. Veuillez réessayer.'));
        }
        $pofsbrands = $this->Pofsmodeles->Pofsbrands->find('list', ['limit' => 200]);
        $companies = $this->Pofsmodeles->Companies->find('list', ['limit' => 200]);
        $this->set(compact('pofsmodele', 'pofsbrands', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Pofsmodele id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $pofsmodele = $this->Pofsmodeles->get($id);
        if ($this->Pofsmodeles->delete($pofsmodele)) {
            $this->Flash->success(__('The pofsmodele has been deleted.'));
        } else {
            $this->Flash->error(__('The pofsmodele could not be deleted. Please, try again.'));
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
                $columnName="Pofsmodeles.code";
                break;
            case 'Title':
                $columnName="Pofsmodeles.title";
                break;
            case 'Brand':
                $columnName="Pofsbrands.title";
                break;
            case 'Status':
                $columnName="Pofsmodeles.statut";
                break;
            default:
                $columnName="Pofsmodeles.title";
                break;
        }
        ## Total number of records with filtering
        $sel=$this->Pofsmodeles->find('all')->contain(['Pofsbrands'])->where(['Pofsmodeles.company_id'=>$this->Auth->user('company_id')]);

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery=$this->Pofsmodeles->find('all')->order([$columnName => $columnSortOrder])->contain(['Pofsbrands'])->where(['Pofsmodeles.company_id'=>$this->Auth->user('company_id')]);
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
                ['Pofsmodeles.title LIKE' => '%'.$searchValue.'%'],
                ['lower(Pofsmodeles.title) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Pofsmodeles.code) LIKE'=>'%'.$searchValue.'%'],
                ['Pofsmodeles.code LIKE' => '%'.$searchValue.'%']]]);
            $empQuery->where(["OR"=>[
                ['Pofsbrands.title LIKE' => '%'.$searchValue.'%'],
                ['lower(Pofsbrands.title) LIKE'=>'%'.$searchValue.'%'],
                ['Pofsmodeles.title LIKE' => '%'.$searchValue.'%'],
                ['lower(Pofsmodeles.title) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Pofsmodeles.code) LIKE'=>'%'.$searchValue.'%'],
                ['Pofsmodeles.code LIKE' => '%'.$searchValue.'%']]]);
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
        foreach ($empQuery as $key => $pofsmodele) {
            
            $data[] = [
                "Code"=> $pofsmodele->code,
                "Title"=>$pofsmodele->title,
                "Brand"=> $pofsmodele->pofsbrand->title,
                "Status"=> $pofsmodele->statut,
                "Actions"=> '<div class="dropdown dropdown-inline">
                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                                    <i class="la la-cog"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <ul class="nav nav-hoverable flex-column">
                                        <li class="nav-item"><a class="nav-link" href="'.Router::Url('/pofsmodeles/edit/'.$pofsmodele->id).'"><i class="nav-icon la la-edit"></i><span class="nav-text">Modifier</span></a></li>
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
