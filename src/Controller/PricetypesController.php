<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Pricetypes Controller
 *
 * @property \App\Model\Table\PricetypesTable $Pricetypes
 *
 * @method \App\Model\Entity\Pricetype[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PricetypesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $pricetypes = $this->paginate($this->Pricetypes);

        $this->set(compact('pricetypes'));
    }

    /**
     * View method
     *
     * @param string|null $id Pricetype id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $pricetype = $this->Pricetypes->get($id, [
            'contain' => [],
        ]);

        $this->set('pricetype', $pricetype);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $pricetype = $this->Pricetypes->newEntity();
        if ($this->request->is('post')) {
            $pricetype = $this->Pricetypes->patchEntity($pricetype, $this->request->getData());
            if ($this->Pricetypes->save($pricetype)) {
                $this->Flash->success(__('The pricetype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The pricetype could not be saved. Please, try again.'));
        }
        $this->set(compact('pricetype'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Pricetype id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $pricetype = $this->Pricetypes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $pricetype = $this->Pricetypes->patchEntity($pricetype, $this->request->getData());
            if ($this->Pricetypes->save($pricetype)) {
                $this->Flash->success(__('The pricetype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The pricetype could not be saved. Please, try again.'));
        }
        $this->set(compact('pricetype'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Pricetype id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $pricetype = $this->Pricetypes->get($id);
        if ($this->Pricetypes->delete($pricetype)) {
            $this->Flash->success(__('The pricetype has been deleted.'));
        } else {
            $this->Flash->error(__('The pricetype could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
