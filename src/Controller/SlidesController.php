<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Slides Controller
 *
 * @property \App\Model\Table\SlidesTable $Slides
 *
 * @method \App\Model\Entity\Slide[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SlidesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index($id=null){
        if($id){
                $slider=$this->Slides->Sliders->get($id);
                if($slider->category_id){
                    $type="Catégorie";
                    $title=$this->Slides->Sliders->Categories->get($slider->category_id)->title;
                }elseif($slider->brand_id){
                    $type="Marque";
                    $title=$this->Slides->Sliders->Brands->get($slider->brand_id)->title;
                }else{
                    $title="Page principale";
                    $type="Page principale";
                }
                $this->set(compact('type','title', 'id'));
        }else{
            $this->Flash->error(__('Vous n\'avez pas les droits pour accéder a cet endroit. Veuillez réessayer.'));
            return $this->redirect(['controller'=>'Pages','action' => 'home']);
        }
    }

    public function search($slider_id=null)
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
        
        switch($columnName) {
            case 'code':
                $columnName="Slides.title";
                break;
            case 'title':
                $columnName="Slides.title";
                break;
            case 'category':
                $columnName="Slides.title";
                break;
            default:
                $columnName="Slides.title";
                $columnSort="asc";
                break;
        }
        ## Total number of records with filtering
        $sel=$this->Slides->find('all')->contain(['Sliders']);

        $sel->select(['count' => $sel->func()->count('*')]);

        $empQuery=$this->Slides->find('all')->contain(['Sliders']);
        $empQuery->order([$columnName => $columnSort]);
        
        if($slider_id){
            $sel->where(['Slides.slider_id'=>$slider_id]);
            $empQuery->where(['Slides.slider_id'=>$slider_id]);
        }
        if($searchValue != ''){
            $sel->where(["OR"=>[
                ['Slides.title LIKE' => '%'.$searchValue.'%'],
                ['lower(Slides.title) LIKE'=>'%'.$searchValue.'%']]]);
           $empQuery->where(["OR"=>[
                ['Slides.title LIKE' => '%'.$searchValue.'%'],
                ['lower(Slides.title) LIKE'=>'%'.$searchValue.'%']]]);
        }
        $total = $sel->last()->count;
        

        $data =[];
        foreach ($empQuery as $key => $slide) {
            $img=Router::Url('/').$slide->dir.'/thumbnail160-'.$slide->photo;

            $data[] = [
                "id"=>$slide->id,
                "photo"=>$img,
                "title"=> $slide->title." N°: ".$slide->id,
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
     * @param string|null $id Slide id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $slide = $this->Slides->get($id, [
            'contain' => ['Sliders', 'Companies'],
        ]);

        $this->set('slide', $slide);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($id)
    {
        $slide = $this->Slides->newEntity();
        if ($this->request->is('post')) {
            $slide = $this->Slides->patchEntity($slide, $this->request->getData());
            $slider=$this->Slides->Sliders->get($id);
            $slide->title=$slider->title;
            $slide->company_id=1;
            $slide->slider_id=$id;
            $slide->statut=1;

            if ($this->Slides->save($slide)) {
                $this->Flash->success(__('The slide has been saved.'));

                return $this->redirect(['action' => 'index',$id]);
            }
            $this->Flash->error(__('The slide could not be saved. Please, try again.'));
        }
        $sliders = $this->Slides->Sliders->find('list', ['limit' => 200]);
        $companies = $this->Slides->Companies->find('list', ['limit' => 200]);
        $this->set(compact('slide', 'sliders', 'companies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Slide id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $slide = $this->Slides->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $slide = $this->Slides->patchEntity($slide, $this->request->getData());
            if ($this->Slides->save($slide)) {
                $this->Flash->success(__('The slide has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The slide could not be saved. Please, try again.'));
        }
        $sliders = $this->Slides->Sliders->find('list', ['limit' => 200]);
        $companies = $this->Slides->Companies->find('list', ['limit' => 200]);
        $this->set(compact('slide', 'sliders', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Slide id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $slide = $this->Slides->get($id);
        $slider_id=$slide->slider_id;
        if ($this->Slides->delete($slide)) {
            $this->Flash->success(__('The slide has been deleted.'));
        } else {
            $this->Flash->error(__('The slide could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index',$slider_id]);
    }
}
