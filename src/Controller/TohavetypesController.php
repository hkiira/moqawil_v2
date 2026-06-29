<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Tohavetypes Controller
 *
 * @property \App\Model\Table\TohavetypesTable $Tohavetypes
 *
 * @method \App\Model\Entity\Tohavetype[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 
 0: Innactif
 1: actif

 */
class TohavetypesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $tohavetypes = $this->paginate($this->Tohavetypes);

        $this->set(compact('tohavetypes'));
    }

    /**
     * View method
     *
     * @param string|null $id Tohavetype id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $tohavetype = $this->Tohavetypes->get($id, [
            'contain' => ['Tohaves'],
        ]);

        $this->set('tohavetype', $tohavetype);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $tohavetype = $this->Tohavetypes->newEntity();
        if ($this->request->is('post')) {
            $tohavetype = $this->Tohavetypes->patchEntity($tohavetype, $this->request->getData());
            if ($this->Tohavetypes->save($tohavetype)) {
                $this->Flash->success(__('The tohavetype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The tohavetype could not be saved. Please, try again.'));
        }
        $this->set(compact('tohavetype'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Tohavetype id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $tohavetype = $this->Tohavetypes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $tohavetype = $this->Tohavetypes->patchEntity($tohavetype, $this->request->getData());
            if ($this->Tohavetypes->save($tohavetype)) {
                $this->Flash->success(__('The tohavetype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The tohavetype could not be saved. Please, try again.'));
        }
        $this->set(compact('tohavetype'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Tohavetype id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $tohavetype = $this->Tohavetypes->get($id);
        if ($this->Tohavetypes->delete($tohavetype)) {
            $this->Flash->success(__('The tohavetype has been deleted.'));
        } else {
            $this->Flash->error(__('The tohavetype could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
