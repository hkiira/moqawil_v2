<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Prices Controller
 *
 * @property \App\Model\Table\PricesTable $Prices
 *
 * @method \App\Model\Entity\Price[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PricesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Packs', 'Customertypes', 'Tranches', 'Companies'],
        ];
        $prices = $this->paginate($this->Prices);

        $this->set(compact('prices'));
    }

    /**
     * View method
     *
     * @param string|null $id Price id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $price = $this->Prices->get($id, [
            'contain' => ['Packs', 'Customertypes', 'Tranches', 'Companies'],
        ]);

        $this->set('price', $price);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $price = $this->Prices->newEntity();
        if ($this->request->is('post')) {
            $price = $this->Prices->patchEntity($price, $this->request->getData());
            if ($this->Prices->save($price)) {
                $this->Flash->success(__('The price has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The price could not be saved. Please, try again.'));
        }
        $packs = $this->Prices->Packs->find('list', ['limit' => 200]);
        $customertypes = $this->Prices->Customertypes->find('list', ['limit' => 200]);
        $tranches = $this->Prices->Tranches->find('list', ['limit' => 200]);
        $companies = $this->Prices->Companies->find('list', ['limit' => 200]);
        $this->set(compact('price', 'packs', 'customertypes', 'tranches', 'companies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Price id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($category_id = null)
    {
        $category=$this->Prices->Packs->Categories->get($category_id,['contain'=>['Packs.Prices.Warehouses','Packs.Prices.Customertypes','Packs.Prices'=>function($q){return $q->where(['Prices.tarif_id IS '=>NULL])->order(['Prices.warehouse_id'=>'ASC']);},'Packs'=>function($q){return $q->where(['Packs.statut'=>1]);}]]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            foreach ($this->request->getData('prices') as $key => $data) {
                $price=$this->Prices->get($data['id']);
                $price = $this->Prices->patchEntity($price, $data);
                $this->Prices->save($price);
            }
            $this->Flash->success(__('Les prix de la catégorie '.$category->title.' ont bien mis à jour'));
            return $this->redirect(['controller'=>'Categories','action' => 'index',$category->category_id]);
        }
        $this->set(compact( 'category'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Price id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $price = $this->Prices->get($id);
        if ($this->Prices->delete($price)) {
            $this->Flash->success(__('The price has been deleted.'));
        } else {
            $this->Flash->error(__('The price could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
