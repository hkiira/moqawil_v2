<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Slipproducts Controller
 *
 * @property \App\Model\Table\SlipproductsTable $Slipproducts
 *
 * @method \App\Model\Entity\Slipproduct[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SlipproductsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Products', 'Slips', 'Users', 'Companies'],
        ];
        $slipproducts = $this->paginate($this->Slipproducts);

        $this->set(compact('slipproducts'));
    }

    /**
     * View method
     *
     * @param string|null $id Slipproduct id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $slipproduct = $this->Slipproducts->get($id, [
            'contain' => ['Products', 'Slips', 'Users', 'Companies'],
        ]);

        $this->set('slipproduct', $slipproduct);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $slipproduct = $this->Slipproducts->newEntity();
        if ($this->request->is('post')) {
            $slipproduct = $this->Slipproducts->patchEntity($slipproduct, $this->request->getData());
            if ($this->Slipproducts->save($slipproduct)) {
                $this->Flash->success(__('The slipproduct has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The slipproduct could not be saved. Please, try again.'));
        }
        $products = $this->Slipproducts->Products->find('list', ['limit' => 200]);
        $slips = $this->Slipproducts->Slips->find('list', ['limit' => 200]);
        $users = $this->Slipproducts->Users->find('list', ['limit' => 200]);
        $companies = $this->Slipproducts->Companies->find('list', ['limit' => 200]);
        $this->set(compact('slipproduct', 'products', 'slips', 'users', 'companies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Slipproduct id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $slipproduct = $this->Slipproducts->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $slipproduct = $this->Slipproducts->patchEntity($slipproduct, $this->request->getData());
            if ($this->Slipproducts->save($slipproduct)) {
                $this->Flash->success(__('The slipproduct has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The slipproduct could not be saved. Please, try again.'));
        }
        $products = $this->Slipproducts->Products->find('list', ['limit' => 200]);
        $slips = $this->Slipproducts->Slips->find('list', ['limit' => 200]);
        $users = $this->Slipproducts->Users->find('list', ['limit' => 200]);
        $companies = $this->Slipproducts->Companies->find('list', ['limit' => 200]);
        $this->set(compact('slipproduct', 'products', 'slips', 'users', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Slipproduct id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $slipproduct = $this->Slipproducts->get($id);
        if ($this->Slipproducts->delete($slipproduct)) {
            $this->Flash->success(__('The slipproduct has been deleted.'));
        } else {
            $this->Flash->error(__('The slipproduct could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
