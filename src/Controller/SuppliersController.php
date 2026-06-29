<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Suppliers Controller
 *
 * @property \App\Model\Table\SuppliersTable $Suppliers
 *
 * @method \App\Model\Entity\Supplier[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SuppliersController extends AppController
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
     * @param string|null $id Supplier id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $supplier = $this->Suppliers->get($id, [
            'contain' => ['Companies'],
        ]);

        $this->set('supplier', $supplier);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $supplier = $this->Suppliers->newEntity();
        if ($this->request->is('post')) {
            $data=$this->request->getData();
            $data['adress']['controleur']='suppliers';
            
            $supplier = $this->Suppliers->patchEntity($supplier, $data);
            if ($this->request->getData('statut')=='on') {
                $supplier->statut=1;
            }else{
                $supplier->statut=0;
            }
            $code=$this->Suppliers->Companies->Companycodes->find('all')->where(['controleur'=>'Suppliers','company_id'=>$this->Auth->user('company_id')])->last();
            $supplier->code=$code->prefixe.($code->compteur+1);
            $supplier->company_id=$this->Auth->user('company_id');
            if ($this->Suppliers->save($supplier)) {
                $code->compteur=$code->compteur+1;
                $this->Suppliers->Companies->Companycodes->save($code);
                $this->Flash->success(__('Le fournisseur a été enregistré.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Le fournisseur n\'a pas pu être enregistré. Veuillez réessayer.'));
        }
        $cities = $this->Suppliers->Adresses->Cities->find('list')->where(['statut'=>1]);
        $this->set(compact('supplier', 'cities'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Supplier id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null,$amodifier = null)
    {
        /*  $amodifier 
            1: modifier le fournisseur
            2: modifier la photo
        */ 
        $supplier = $this->Suppliers->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            
            $supplier = $this->Suppliers->patchEntity($supplier, $this->request->getData());
            $statut=$this->request->getData('statut');
            $supplier->company_id=$this->Auth->user('company_id');
            if($this->request->getData('statut') || $amodifier==1){
                if ($statut) {
                    $supplier->statut=1;
                } else {
                    $supplier->statut=0;
                }
            }
            if($amodifier==2){
                $supplier->photo->title=$supplier->name;
                $supplier->photo->controleur='suppliers';
                $supplier->photo->company_id=$this->Auth->user('company_id');
            }
            if ($this->Suppliers->save($supplier)) {
                $this->Flash->success(__('Le fournisseur a été enregistré.'));
                return $this->redirect(['action' => 'index']);
            }
            
            $this->Flash->error(__('Le fournisseur n\'a pas pu être enregistré. Veuillez réessayer.'));
        }
        if($amodifier==1){
            $cities = $this->Suppliers->Adresses->Cities->find('list')->where(['statut'=>1]);
            $this->set(compact('supplier', 'cities','amodifier'));
        }else{
            $this->set(compact('supplier','amodifier'));
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Supplier id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $supplier = $this->Suppliers->get($id);
        if ($this->Suppliers->delete($supplier)) {
            $this->Flash->success(__('The supplier has been deleted.'));
        } else {
            $this->Flash->error(__('The supplier could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    */


    public function search()
    {  

        $page = $this->request->getData('pagination.page');
        $pages = $this->request->getData('pagination.pages');
        $perpage = $this->request->getData('pagination.perpage');
        $total = $this->request->getData('pagination.total');
        $field = $this->request->getData('sort.field'); // Column name
        $sort = $this->request->getData('sort.sort'); // Column name
        
        $columnName = $this->request->getData('sort.field'); // Column name
        $columnSort = $this->request->getData('sort.sort'); // Column name
        $searchValue = strtolower($this->request->getData('query.generalSearch')); // Search value
        $searchSecteurs = $this->request->getData('query.Secteur'); // Search value
        $searchStatus = ($this->request->getData('query.Status')!==NULL) ? $this->request->getData('query.Status') : -1 ;
        switch($columnName) {
            case 'code':
                $columnName="Suppliers.code";
                break;
            case 'name':
                $columnName="Suppliers.name";
                break;
            case 'status':
                $columnName="Suppliers.statut";
                break;
            default:
                $columnName="Suppliers.name";
                $columnSort="asc";
                break;
        }
        ## Total number of records with filtering
        $sel=$this->Suppliers->find('all')->contain(['Adresses.Cities'=>function($q){ return $q->where(['Adresses.controleur'=>'suppliers']);}])->where(['Suppliers.company_id'=>$this->Auth->user('company_id')])->group('Suppliers.id');

        ## Search 
        $empQuery=$this->Suppliers->find('all')->contain(['Adresses.Cities'=>function($q){ return $q->where(['Adresses.controleur'=>'suppliers']);}])->order([$columnName => $columnSort])->where(['Suppliers.company_id'=>$this->Auth->user('company_id')])->group('Suppliers.id');
        
        
        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['Suppliers.name LIKE' => '%'.$searchValue.'%'],
                ['lower(Suppliers.name) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Suppliers.phone) LIKE'=>'%'.$searchValue.'%'],
                ['Suppliers.phone LIKE'=>'%'.$searchValue.'%'],
                ['Suppliers.code LIKE' => '%'.$searchValue.'%']]]);
            $empQuery->where(["OR"=>[
                ['Suppliers.name LIKE' => '%'.$searchValue.'%'],
                ['lower(Suppliers.name) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Suppliers.phone) LIKE'=>'%'.$searchValue.'%'],
                ['Suppliers.phone LIKE'=>'%'.$searchValue.'%'],
                ['Suppliers.code LIKE' => '%'.$searchValue.'%']]]);
        }
        if ($searchStatus>-1) {
            $empQuery->where(['Suppliers.statut'=>$searchStatus]);
            $sel->where(['Suppliers.statut'=>$searchStatus]);
        }
        $empQuery->limit($perpage);
        $empQuery->page($page);
        $sel->select(['count' => $sel->func()->count('*')]);
        $total = $sel->last()->count;

        $data =[];
        foreach ($empQuery as $key => $supplier) {
            $photo=$this->Suppliers->Photos->find('all')->where(['controleur'=>'suppliers','objectid'=>$supplier->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            if ($photo) {
                $img=Router::Url('/').$photo->dir.'/'.$photo->photo;
            }
            $adresse = ($supplier->adress->title) ? $supplier->adress->title.'-'.$supplier->adress->city->title : "Indisponible" ;
            $data[] = [
                "id"=> $supplier->id,
                "img"=> $img,
                "code"=> $supplier->code,
                "name"=>$supplier->name,
                "phone"=>$supplier->phone,
                "adresse"=>$adresse,
                "status"=> $supplier->statut,
                "Actions"=> null
            ];
        }

        $response = [
            "meta"=>[
                'page' => $page,
                'pages' => $pages,
                'perpage' => $perpage,
                'total' => $total,
                'sort'=> $sort
            ],
            'data' => $data,
        ];
        $this->autoRender = false; 
        echo json_encode($response);
        exit;
    }
}
