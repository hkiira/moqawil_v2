<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Tarifs Controller
 *
 * @property \App\Model\Table\TarifsTable $Tarifs
 *
 * @method \App\Model\Entity\Tarif[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TarifsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(){}
    
    public function search($type=null)
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
                $columnName="Tarifs.code";
                break;
            case 'Title':
                $columnName="Tarifs.title";
                break;
            case 'Status':
                $columnName="Tarifs.statut";
                break;
            default:
                $columnName="Tarifs.title";
                break;
        }
        ## Total number of records with filtering
        $sel=$this->Tarifs->find('all')->where(['Tarifs.company_id'=>$this->Auth->user('company_id')]);

        $sel->select(['count' => $sel->func()->count('*')]);
        $totalRecords = $sel->last()->count;

        ## Search 
        $empQuery=$this->Tarifs->find('all')->where(['Tarifs.company_id'=>$this->Auth->user('company_id')]);
        $empQuery->order([$columnName => $columnSortOrder]);
        if ($row==0) {
            $empQuery->limit($rowperpage);
        }else{
            $empQuery->limit($rowperpage);
            $empQuery->page(($row/$rowperpage)+1);
        }
        
        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['Tarifs.title LIKE' => '%'.$searchValue.'%'],
                ['Tarifs.code LIKE' => '%'.$searchValue.'%'],
                ['lower(Tarifs.code) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Tarifs.title) LIKE'=>'%'.$searchValue.'%']]]);
           $empQuery->where(["OR"=>[
                ['Tarifs.title LIKE' => '%'.$searchValue.'%'],
                ['Tarifs.code LIKE' => '%'.$searchValue.'%'],
                ['lower(Tarifs.code) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Tarifs.title) LIKE'=>'%'.$searchValue.'%']]]);
            $empQuery->page(1);
        }
        
        if ($draw=0) {
            $empQuery->page(1);
        }
        ## Total number of records with filtering
        $totalRecordwithFilter = $sel->last()->count;
        ## Fetch records
        $data =[];
        foreach ($empQuery as $key => $tarif) {
            $data[] = [
                "Code"=> $tarif->code,
                "Title"=>$tarif->title,
                "Status"=> $tarif->statut,
                "Actions"=> '<div class="dropdown dropdown-inline">
                                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                                    <i class="la la-cog"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <ul class="nav nav-hoverable flex-column">
                                        <li class="nav-item"><a class="nav-link" href="'.Router::Url('/tarifs/edit/'.$tarif->id).'"><i class="nav-icon la la-edit"></i><span class="nav-text">Modifier</span></a></li>
                                        <li class="nav-item"><a class="nav-link" href="'.Router::Url('/tarifs/view/'.$tarif->id).'"><i class="nav-icon la la-edit"></i><span class="nav-text">Afficher</span></a></li>
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
     * @param string|null $id Tarif id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $tarif = $this->Tarifs->get($id, [
            'contain' => ['Companies', 'Orders', 'Prices'],
        ]);

        $this->set('tarif', $tarif);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $tarif = $this->Tarifs->newEntity();
        if ($this->request->is('post')) {
            $tarifdata=$this->request->getData();
            $tarifdata['company_id']=$this->Auth->user('company_id');
            $tarifdata['statut']=1;

            foreach ($this->request->getData('category_id') as $key => $categoryid) {
                $tarifdata['tarifcategories'][$key]['category_id']=$categoryid;
            }
            $code=$this->Tarifs->Companies->Companycodes->find('all')->where(['controleur'=>'Tarifs','company_id'=>$this->Auth->user('company_id')])->last();
            $tarifdata['code']=$code->prefixe.($code->compteur+1);
            
            $tarif = $this->Tarifs->patchEntity($tarif, $tarifdata,['associated'=>'Tarifcategories']);
            
            if ($this->Tarifs->save($tarif)) {
                $code->compteur=($code->compteur+1);
                if ($this->Tarifs->Companies->Companycodes->save($code)) {
                    $this->Flash->success(__('Le tarif a été enregistré.'));
                    return $this->redirect(['action' => 'index']);
                }
            }
            $this->Flash->error(__('Le tarif n\'a pas pu être enregistré. Veuillez réessayer.'));
        }
        $categories=$this->Tarifs->Tarifcategories->Categories->find('list')->where(['category_id IS  '=>NULL,'statut'=>1]);
        $tariftypes=$this->Tarifs->Tariftypes->find('list')->where(['statut'=>1]);
        $tarifways=$this->Tarifs->Tarifways->find('list')->where(['statut'=>1]);

        $this->set(compact('tarif','categories','tariftypes','tarifways'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Tarif id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function update($id = null)
    {
        $tarif=$this->Tarifs->get($id,['contain'=>['Tarifcategories']]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $datas=['id'=>$tarif->id];
            $existedatas=[];
            
            foreach ($tarif->tarifcategories as $key => $tarifcategorie) {
                $existedatas[$tarifcategorie->id]=$tarifcategorie->category_id;
            }
            foreach ($this->request->getData('tarifcategories') as $key => $value) {
                if(in_array($key, $existedatas)){
                    foreach ($existedatas as $key1 => $exist) {
                        if($exist==$key){
                            $datas['tarifcategories'][$key]['id']=$key1;
                        }
                    }
                }else{
                    $datas['tarifcategories'][$key]['category_id']=$key;
                }
            }
            $tarif=$this->Tarifs->patchEntity($tarif,$datas,['associated'=>['Tarifcategories']]);
            if ($this->Tarifs->save($tarif)) {
                $this->Flash->success(__('Le tarif a été enregistré.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Le tarif n\'a pas pu être enregistré. Veuillez réessayer.'));
        }

        $categories=$this->Tarifs->Tarifcategories->Categories->find('all')->where(['statut'=>1,'category_id IS NOT NULL']);
        $this->set(compact('tarif','categories'));
    }
    public function edit($id = null)
    {
        $tarif = $this->Tarifs->get($id, [
            'contain' => ['Tarifcategories.Categories.Packs.Prices.Warehouses','Tarifcategories.Categories.Packs.Prices.Customertypes','Tarifcategories.Categories.Packs.Prices'=>function($q)use($id){return $q->where(['Prices.tarif_id '=>$id]);}],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $datas=$this->request->getData();
            foreach ($this->request->getData('prices') as $key => $price) {
            	$datas['prices'][$key]['company_id']=$this->Auth->user('company_id');
            }
            $tarifupdate=$this->Tarifs->get($id,['contain'=>['Prices']]);
            $tarifupdate = $this->Tarifs->patchEntity($tarifupdate, $datas);
            
            if ($this->Tarifs->save($tarifupdate)) {
                $this->Flash->success(__('Le tarif a été enregistré.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Le tarif n\'a pas pu être enregistré. Veuillez réessayer.'));
        }

        $warehouses = $this->Tarifs->Prices->Warehouses->find('list')->where(['Warehouses.whtype_id'=>1]);
        $pricesnormale=$this->Tarifs->Prices->find('all')->contain(['Customertypes','Warehouses'])->where(['Prices.tarif_id IS '=>NULL]);
        
        $pricesn=[];
        foreach($pricesnormale as $normaleprice){
            $pricesn[$normaleprice->pack_id][$normaleprice->warehouse_id][$normaleprice->customertype->id]['price']=$normaleprice->price;
            $pricesn[$normaleprice->pack_id][$normaleprice->warehouse_id][$normaleprice->customertype->id]['warehouse']=$normaleprice->warehouse->title;
            $pricesn[$normaleprice->pack_id][$normaleprice->warehouse_id][$normaleprice->customertype->id]['warehouseid']=$normaleprice->warehouse->id;
            $pricesn[$normaleprice->pack_id][$normaleprice->warehouse_id][$normaleprice->customertype->id]['customertype']=$normaleprice->customertype->title;
            $pricesn[$normaleprice->pack_id][$normaleprice->warehouse_id][$normaleprice->customertype->id]['customertypeid']=$normaleprice->customertype->id;
        }
        $this->set(compact('tarif','warehouses','pricesn'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Tarif id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $tarif = $this->Tarifs->get($id);
        if ($this->Tarifs->delete($tarif)) {
            $this->Flash->success(__('The tarif has been deleted.'));
        } else {
            $this->Flash->error(__('The tarif could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
