<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Whuserproducts Controller
 *
 * @property \App\Model\Table\WhuserproductsTable $Whuserproducts
 *
 * @method \App\Model\Entity\Whuserproduct[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 
 0: Innactif
 1: actif

 */
class WhuserproductsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'Warehouses', 'Whproducts', 'Companies'],
        ];
        $whuserproducts = $this->paginate($this->Whuserproducts);

        $this->set(compact('whuserproducts'));
    }

    /**
     * View method
     *
     * @param string|null $id Whuserproduct id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $whuserproduct = $this->Whuserproducts->get($id, [
            'contain' => ['Users', 'Warehouses', 'Whproducts', 'Companies'],
        ]);

        $this->set('whuserproduct', $whuserproduct);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $whuserproduct = $this->Whuserproducts->newEntity();
        if ($this->request->is('post')) {
            $whuserproduct = $this->Whuserproducts->patchEntity($whuserproduct, $this->request->getData());
            if ($this->Whuserproducts->save($whuserproduct)) {
                $this->Flash->success(__('The whuserproduct has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The whuserproduct could not be saved. Please, try again.'));
        }
        $users = $this->Whuserproducts->Users->find('list', ['limit' => 200]);
        $warehouses = $this->Whuserproducts->Warehouses->find('list', ['limit' => 200]);
        $whproducts = $this->Whuserproducts->Whproducts->find('list', ['limit' => 200]);
        $companies = $this->Whuserproducts->Companies->find('list', ['limit' => 200]);
        $this->set(compact('whuserproduct', 'users', 'warehouses', 'whproducts', 'companies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Whuserproduct id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $whuserproduct = $this->Whuserproducts->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $whuserproduct = $this->Whuserproducts->patchEntity($whuserproduct, $this->request->getData());
            if ($this->Whuserproducts->save($whuserproduct)) {
                $this->Flash->success(__('The whuserproduct has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The whuserproduct could not be saved. Please, try again.'));
        }
        $users = $this->Whuserproducts->Users->find('list', ['limit' => 200]);
        $warehouses = $this->Whuserproducts->Warehouses->find('list', ['limit' => 200]);
        $whproducts = $this->Whuserproducts->Whproducts->find('list', ['limit' => 200]);
        $companies = $this->Whuserproducts->Companies->find('list', ['limit' => 200]);
        $this->set(compact('whuserproduct', 'users', 'warehouses', 'whproducts', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Whuserproduct id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $whuserproduct = $this->Whuserproducts->get($id);
        if ($this->Whuserproducts->delete($whuserproduct)) {
            $this->Flash->success(__('The whuserproduct has been deleted.'));
        } else {
            $this->Flash->error(__('The whuserproduct could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
