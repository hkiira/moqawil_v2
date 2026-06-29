<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Billingpacks Controller
 *
 * @property \App\Model\Table\BillingpacksTable $Billingpacks
 *
 * @method \App\Model\Entity\Billingpack[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BillingpacksController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Billings', 'Packs', 'Companies', 'Users'],
        ];
        $billingpacks = $this->paginate($this->Billingpacks);

        $this->set(compact('billingpacks'));
    }

    /**
     * View method
     *
     * @param string|null $id Billingpack id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $billingpack = $this->Billingpacks->get($id, [
            'contain' => ['Billings', 'Packs', 'Companies', 'Users'],
        ]);

        $this->set('billingpack', $billingpack);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $billingpack = $this->Billingpacks->newEntity();
        if ($this->request->is('post')) {
            $billingpack = $this->Billingpacks->patchEntity($billingpack, $this->request->getData());
            if ($this->Billingpacks->save($billingpack)) {
                $this->Flash->success(__('The billingpack has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The billingpack could not be saved. Please, try again.'));
        }
        $billings = $this->Billingpacks->Billings->find('list', ['limit' => 200]);
        $packs = $this->Billingpacks->Packs->find('list', ['limit' => 200]);
        $companies = $this->Billingpacks->Companies->find('list', ['limit' => 200]);
        $users = $this->Billingpacks->Users->find('list', ['limit' => 200]);
        $this->set(compact('billingpack', 'billings', 'packs', 'companies', 'users'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Billingpack id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $billingpack = $this->Billingpacks->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $billingpack = $this->Billingpacks->patchEntity($billingpack, $this->request->getData());
            if ($this->Billingpacks->save($billingpack)) {
                $this->Flash->success(__('The billingpack has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The billingpack could not be saved. Please, try again.'));
        }
        $billings = $this->Billingpacks->Billings->find('list', ['limit' => 200]);
        $packs = $this->Billingpacks->Packs->find('list', ['limit' => 200]);
        $companies = $this->Billingpacks->Companies->find('list', ['limit' => 200]);
        $users = $this->Billingpacks->Users->find('list', ['limit' => 200]);
        $this->set(compact('billingpack', 'billings', 'packs', 'companies', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Billingpack id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $billingpack = $this->Billingpacks->get($id);
        if ($this->Billingpacks->delete($billingpack)) {
            $this->Flash->success(__('The billingpack has been deleted.'));
        } else {
            $this->Flash->error(__('The billingpack could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
