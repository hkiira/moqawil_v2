<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Brands Controller
 *
 * @property \App\Model\Table\BrandsTable $Brands
 *
 * @method \App\Model\Entity\Brand[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BrandsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
    }
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
        $searchStatus = ($this->request->getData('query.Status')!== NULL) ? $this->request->getData('query.Status') : -1 ;
        
        switch($columnName) {
            case 'code':
                $columnName="Brands.code";
                break;
            case 'name':
                $columnName="Brands.title";
                break;
            default:
                $columnName="Brands.title";
                $columnSort="asc";
                break;
        }
        ## Total number of records with filtering
        $sel=$this->Brands->find('all')->where(['Brands.company_id'=>$this->Auth->user('company_id')]);

        ## Search 
        $empQuery=$this->Brands->find('all')->where(['Brands.company_id'=>$this->Auth->user('company_id')]);
        $empQuery->order([$columnName => $columnSort]);
        
        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['Brands.title LIKE' => '%'.$searchValue.'%'],
                ['Brands.code LIKE' => '%'.$searchValue.'%'],
                ['lower(Brands.code) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Brands.title) LIKE'=>'%'.$searchValue.'%']]]);
           $empQuery->where(["OR"=>[
                ['Brands.title LIKE' => '%'.$searchValue.'%'],
                ['Brands.code LIKE' => '%'.$searchValue.'%'],
                ['lower(Brands.code) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Brands.title) LIKE'=>'%'.$searchValue.'%']]]);
            $empQuery->page(1);
        }
        
        if ($searchStatus>-1) {
            $empQuery->where(['Brands.statut'=>$searchStatus]);
            $sel->where(['Brands.statut'=>$searchStatus]);
        }

        $empQuery->limit($perpage);
        $empQuery->page($page);
        $sel->select(['count' => $sel->func()->count('*')]);
        $total = $sel->last()->count;

        ## Fetch records
        $data =[];
        foreach ($empQuery as $key => $brand) {
            $photo=$this->Brands->Photos->find('all')->where(['controleur'=>'brands','objectid'=>$brand->id])->order(['created'=>'ASC'])->last();
            $img=Router::Url('/').'webroot/img/unvailable.jpg';
            if ($photo) {
                $img=Router::Url('/').$photo->dir.'/thumbnail160-'.$photo->photo;
            }
            
            $data[] = [
                "id"=> $brand->id,
                "img"=> $img,
                "code"=> $brand->code,
                "name"=>$brand->title,
                "status"=> $brand->statut,
                "actions"=> null
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
    /**
     * View method
     *
     * @param string|null $id Brand id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $brand = $this->Brands->get($id, [
            'contain' => ['Companies', 'Packs'],
        ]);

        $this->set('brand', $brand);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $brand = $this->Brands->newEntity();
        if ($this->request->is('post')) {
            $datas=$this->request->getData();
            $datas['sliders'][0]=[
                    'title'=>$this->request->getData('title')
                ];
            $brand = $this->Brands->patchEntity($brand, $datas,["Associated"=>"Sliders"]);
            if ($datas['statut']=='on') {
                $brand->statut=1;
            }else{
                $brand->statut=0;
            }

            $code=$this->Brands->Companies->Companycodes->find('all')->where(['controleur'=>'Brands','company_id'=>$this->Auth->user('company_id')])->last();
            $brand->code=$code->prefixe.($code->compteur+1);
            $brand->company_id=$this->Auth->user('company_id');

            if ($this->Brands->save($brand)) {
                $code->compteur=$code->compteur+1;
                $this->Brands->Companies->Companycodes->save($code);
                $this->Flash->success(__('La marque a été enregistrée.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('La marque n\'a pas pu être enregistrée. Veuillez réessayer.'));
        }
        $this->set(compact('brand'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Brand id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */

    public function edit($id = null,$image=null)
    {
        $brand = $this->Brands->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            if($image=="image"){
                $brand = $this->Brands->patchEntity($brand, $this->request->getData(),['Associated'=>['Photos']]);
                $brand->photo->title=$brand->title;
                $brand->photo->controleur='brands';
                $brand->photo->company_id=$this->Auth->user('company_id');
            }else{
                $brand = $this->Brands->patchEntity($brand, $this->request->getData());

                if ($this->request->getData("statut")) {
                    $brand->statut=1;
                } else {
                    $brand->statut=0;
                }
            }
            if ($this->Brands->save($brand)) {
                $this->Flash->success(__('La marque a été enregistrée.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('La marque n\'a pas pu être enregistrée. Veuillez réessayer.'));
        }
        $this->set(compact('brand','image'));
    }
    /**
     * Delete method
     *
     * @param string|null $id Brand id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $brand = $this->Brands->get($id);
        if ($this->Brands->delete($brand)) {
            $this->Flash->success(__('The brand has been deleted.'));
        } else {
            $this->Flash->error(__('The brand could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
