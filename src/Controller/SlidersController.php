<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Sliders Controller
 *
 * @property \App\Model\Table\SlidersTable $Sliders
 *
 * @method \App\Model\Entity\Slider[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SlidersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index($type=null){
        if($type=="brands"){
            $type="Marques";
            $marques=$this->Sliders->Brands->find('list')->where(['statut'=>1]);
            $this->set(compact('type','marques'));
        }elseif($type=="categories"){
            $type="Catégories";
            $categories=$this->Sliders->Categories->find('list')->where(['category_id IS NOT'=>NULL,'statut'=>1]);
            $this->set(compact('type','categories'));
        }else{
            $type="Page principale";
            $this->set(compact('type'));

        }
    }

    public function search($type=null)
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
        $searchBrand = strtolower($this->request->getData('query.brand')); // Search value
        $searchCategory = strtolower($this->request->getData('query.category')); // Search value
        
        switch($columnName) {
            case 'code':
                $columnName="Sliders.title";
                break;
            case 'title':
                $columnName="Sliders.title";
                break;
            default:
                $columnName="Sliders.title";
                $columnSort="asc";
                break;
        }
        ## Total number of records with filtering
        $sel=$this->Sliders->find('all')->contain(['Categories','Brands']);
        $sel->select(['count' => $sel->func()->count('*')]);

        ## Search 
        $empQuery=$this->Sliders->find('all')->contain(['Categories','Brands','Slides']);
        $empQuery->order([$columnName => $columnSort]);
        
        
        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['Categories.title LIKE' => '%'.$searchValue.'%'],
                ['Categories.code LIKE' => '%'.$searchValue.'%'],
                ['Brands.title LIKE' => '%'.$searchValue.'%'],
                ['Brands.code LIKE' => '%'.$searchValue.'%'],
                ['Sliders.title LIKE' => '%'.$searchValue.'%'],
                ['lower(Sliders.title) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Categories.title) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Categories.code) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Brands.title) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Brands.code) LIKE'=>'%'.$searchValue.'%']]]);
           $empQuery->where(["OR"=>[
                ['Categories.title LIKE' => '%'.$searchValue.'%'],
                ['Categories.code LIKE' => '%'.$searchValue.'%'],
                ['Brands.title LIKE' => '%'.$searchValue.'%'],
                ['Brands.code LIKE' => '%'.$searchValue.'%'],
                ['Sliders.title LIKE' => '%'.$searchValue.'%'],
                ['lower(Sliders.title) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Categories.title) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Categories.code) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Brands.title) LIKE'=>'%'.$searchValue.'%'],
                ['lower(Brands.code) LIKE'=>'%'.$searchValue.'%']]]);
        }
        
        if ($searchBrand) {
            $empQuery->where(['Brands.id'=>$searchBrand]);
            $sel->where(['Brands.id'=>$searchBrand]);
        }
        if ($searchCategory) {
            $empQuery->where(['Categories.id'=>$searchCategory]);
            $sel->where(['Categories.id'=>$searchCategory]);
        }
        if($type=="Marques"){
            $sel->where(['Sliders.category_id IS '=>NULL,'Sliders.brand_id IS NOT'=>NULL]);
            $empQuery->where(['Sliders.category_id IS '=>NULL,'Sliders.brand_id IS NOT'=>NULL]);
        }elseif($type=="Catégories"){
            $empQuery->where(['Sliders.category_id IS NOT'=>NULL,'Sliders.brand_id IS '=>NULL]);
            $sel->where(['Sliders.category_id IS NOT'=>NULL,'Sliders.brand_id IS '=>NULL]);
        }else{
            $empQuery->where(['Sliders.category_id IS '=>NULL,'Sliders.brand_id IS '=>NULL]);
            $sel->where(['Sliders.category_id IS '=>NULL,'Sliders.brand_id IS '=>NULL]);
        }
        $total = $sel->last()->count;

        $data =[];
        foreach ($empQuery as $key => $slider) {
            $data[] = [
                "id"=>$slider->id,
                "title"=>$slider->title,
                "slides"=> count($slider->slides),
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
     * @param string|null $id Slider id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $slider = $this->Sliders->get($id, [
            'contain' => ['Categories', 'Brands', 'Companies', 'Slides'],
        ]);

        $this->set('slider', $slider);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $slider = $this->Sliders->newEntity();
        if ($this->request->is('post')) {
            $slider = $this->Sliders->patchEntity($slider, $this->request->getData());
            if ($this->Sliders->save($slider)) {
                $this->Flash->success(__('The slider has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The slider could not be saved. Please, try again.'));
        }
        $categories = $this->Sliders->Categories->find('list', ['limit' => 200]);
        $brands = $this->Sliders->Brands->find('list', ['limit' => 200]);
        $companies = $this->Sliders->Companies->find('list', ['limit' => 200]);
        $this->set(compact('slider', 'categories', 'brands', 'companies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Slider id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $slider = $this->Sliders->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $slider = $this->Sliders->patchEntity($slider, $this->request->getData());
            if ($this->Sliders->save($slider)) {
                $this->Flash->success(__('The slider has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The slider could not be saved. Please, try again.'));
        }
        $categories = $this->Sliders->Categories->find('list', ['limit' => 200]);
        $brands = $this->Sliders->Brands->find('list', ['limit' => 200]);
        $companies = $this->Sliders->Companies->find('list', ['limit' => 200]);
        $this->set(compact('slider', 'categories', 'brands', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Slider id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $slider = $this->Sliders->get($id);
        if ($this->Sliders->delete($slider)) {
            $this->Flash->success(__('The slider has been deleted.'));
        } else {
            $this->Flash->error(__('The slider could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
