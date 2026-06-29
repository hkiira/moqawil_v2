<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Packtaxes Controller
 *
 * @property \App\Model\Table\PacktaxesTable $Packtaxes
 *
 * @method \App\Model\Entity\Packtax[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PacktaxesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $packtaxes = $this->paginate($this->Packtaxes);

        $this->set(compact('packtaxes'));
    }

    /**
     * View method
     *
     * @param string|null $id Packtax id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $packtax = $this->Packtaxes->get($id, [
            'contain' => [],
        ]);

        $this->set('packtax', $packtax);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $packtax = $this->Packtaxes->newEntity();
        if ($this->request->is('post')) {
            $packtax = $this->Packtaxes->patchEntity($packtax, $this->request->getData());
            if ($this->Packtaxes->save($packtax)) {
                $this->Flash->success(__('The packtax has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The packtax could not be saved. Please, try again.'));
        }
        $this->set(compact('packtax'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Packtax id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $packtax = $this->Packtaxes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $packtax = $this->Packtaxes->patchEntity($packtax, $this->request->getData());
            if ($this->Packtaxes->save($packtax)) {
                $this->Flash->success(__('The packtax has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The packtax could not be saved. Please, try again.'));
        }
        $this->set(compact('packtax'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Packtax id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $packtax = $this->Packtaxes->get($id);
        if ($this->Packtaxes->delete($packtax)) {
            $this->Flash->success(__('The packtax has been deleted.'));
        } else {
            $this->Flash->error(__('The packtax could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
