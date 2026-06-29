<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Charges Controller
 *
 * @property \App\Model\Table\ChargesTable $Charges
 *
 * @method \App\Model\Entity\Charge[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ChargesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Reports'],
        ];
        $charges = $this->paginate($this->Charges);

        $this->set(compact('charges'));
    }

    /**
     * View method
     *
     * @param string|null $id Charge id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $charge = $this->Charges->get($id, [
            'contain' => ['Reports'],
        ]);

        $this->set('charge', $charge);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $charge = $this->Charges->newEntity();
        if ($this->request->is('post')) {
            $charge = $this->Charges->patchEntity($charge, $this->request->getData());
            if ($this->Charges->save($charge)) {
                $this->Flash->success(__('The charge has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The charge could not be saved. Please, try again.'));
        }
        $reports = $this->Charges->Reports->find('list', ['limit' => 200]);
        $this->set(compact('charge', 'reports'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Charge id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $charge = $this->Charges->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $charge = $this->Charges->patchEntity($charge, $this->request->getData());
            if ($this->Charges->save($charge)) {
                $this->Flash->success(__('The charge has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The charge could not be saved. Please, try again.'));
        }
        $reports = $this->Charges->Reports->find('list', ['limit' => 200]);
        $this->set(compact('charge', 'reports'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Charge id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $charge = $this->Charges->get($id);
        if ($this->Charges->delete($charge)) {
            $this->Flash->success(__('The charge has been deleted.'));
        } else {
            $this->Flash->error(__('The charge could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
