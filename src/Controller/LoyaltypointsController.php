<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Loyaltypoints Controller
 *
 * @property \App\Model\Table\LoyaltypointsTable $Loyaltypoints
 *
 * @method \App\Model\Entity\Loyaltypoint[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class LoyaltypointsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Orders', 'Companies'],
        ];
        $loyaltypoints = $this->paginate($this->Loyaltypoints);

        $this->set(compact('loyaltypoints'));
    }

    /**
     * View method
     *
     * @param string|null $id Loyaltypoint id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $loyaltypoint = $this->Loyaltypoints->get($id, [
            'contain' => ['Orders', 'Companies', 'Loyaltyorderpacks'],
        ]);

        $this->set('loyaltypoint', $loyaltypoint);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $loyaltypoint = $this->Loyaltypoints->newEntity();
        if ($this->request->is('post')) {
            $loyaltypoint = $this->Loyaltypoints->patchEntity($loyaltypoint, $this->request->getData());
            if ($this->Loyaltypoints->save($loyaltypoint)) {
                $this->Flash->success(__('The loyaltypoint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The loyaltypoint could not be saved. Please, try again.'));
        }
        $orders = $this->Loyaltypoints->Orders->find('list', ['limit' => 200]);
        $companies = $this->Loyaltypoints->Companies->find('list', ['limit' => 200]);
        $this->set(compact('loyaltypoint', 'orders', 'companies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Loyaltypoint id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $loyaltypoint = $this->Loyaltypoints->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $loyaltypoint = $this->Loyaltypoints->patchEntity($loyaltypoint, $this->request->getData());
            if ($this->Loyaltypoints->save($loyaltypoint)) {
                $this->Flash->success(__('The loyaltypoint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The loyaltypoint could not be saved. Please, try again.'));
        }
        $orders = $this->Loyaltypoints->Orders->find('list', ['limit' => 200]);
        $companies = $this->Loyaltypoints->Companies->find('list', ['limit' => 200]);
        $this->set(compact('loyaltypoint', 'orders', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Loyaltypoint id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $loyaltypoint = $this->Loyaltypoints->get($id);
        if ($this->Loyaltypoints->delete($loyaltypoint)) {
            $this->Flash->success(__('The loyaltypoint has been deleted.'));
        } else {
            $this->Flash->error(__('The loyaltypoint could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
