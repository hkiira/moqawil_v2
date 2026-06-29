<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Tarifways Controller
 *
 * @property \App\Model\Table\TarifwaysTable $Tarifways
 *
 * @method \App\Model\Entity\Tarifway[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TarifwaysController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $tarifways = $this->paginate($this->Tarifways);

        $this->set(compact('tarifways'));
    }

    /**
     * View method
     *
     * @param string|null $id Tarifway id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $tarifway = $this->Tarifways->get($id, [
            'contain' => ['Tarifs'],
        ]);

        $this->set('tarifway', $tarifway);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $tarifway = $this->Tarifways->newEntity();
        if ($this->request->is('post')) {
            $tarifway = $this->Tarifways->patchEntity($tarifway, $this->request->getData());
            if ($this->Tarifways->save($tarifway)) {
                $this->Flash->success(__('The tarifway has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The tarifway could not be saved. Please, try again.'));
        }
        $this->set(compact('tarifway'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Tarifway id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $tarifway = $this->Tarifways->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $tarifway = $this->Tarifways->patchEntity($tarifway, $this->request->getData());
            if ($this->Tarifways->save($tarifway)) {
                $this->Flash->success(__('The tarifway has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The tarifway could not be saved. Please, try again.'));
        }
        $this->set(compact('tarifway'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Tarifway id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $tarifway = $this->Tarifways->get($id);
        if ($this->Tarifways->delete($tarifway)) {
            $this->Flash->success(__('The tarifway has been deleted.'));
        } else {
            $this->Flash->error(__('The tarifway could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
