<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Tariftypes Controller
 *
 * @property \App\Model\Table\TariftypesTable $Tariftypes
 *
 * @method \App\Model\Entity\Tariftype[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TariftypesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $tariftypes = $this->paginate($this->Tariftypes);

        $this->set(compact('tariftypes'));
    }

    /**
     * View method
     *
     * @param string|null $id Tariftype id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $tariftype = $this->Tariftypes->get($id, [
            'contain' => ['Tarifs'],
        ]);

        $this->set('tariftype', $tariftype);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $tariftype = $this->Tariftypes->newEntity();
        if ($this->request->is('post')) {
            $tariftype = $this->Tariftypes->patchEntity($tariftype, $this->request->getData());
            if ($this->Tariftypes->save($tariftype)) {
                $this->Flash->success(__('The tariftype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The tariftype could not be saved. Please, try again.'));
        }
        $this->set(compact('tariftype'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Tariftype id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $tariftype = $this->Tariftypes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $tariftype = $this->Tariftypes->patchEntity($tariftype, $this->request->getData());
            if ($this->Tariftypes->save($tariftype)) {
                $this->Flash->success(__('The tariftype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The tariftype could not be saved. Please, try again.'));
        }
        $this->set(compact('tariftype'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Tariftype id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $tariftype = $this->Tariftypes->get($id);
        if ($this->Tariftypes->delete($tariftype)) {
            $this->Flash->success(__('The tariftype has been deleted.'));
        } else {
            $this->Flash->error(__('The tariftype could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
