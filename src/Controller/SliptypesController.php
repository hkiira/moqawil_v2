<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Sliptypes Controller
 *
 * @property \App\Model\Table\SliptypesTable $Sliptypes
 *
 * @method \App\Model\Entity\Sliptype[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])

 0: innactif
 1: actif
 
  */
class SliptypesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $sliptypes = $this->paginate($this->Sliptypes);

        $this->set(compact('sliptypes'));
    }

    /**
     * View method
     *
     * @param string|null $id Sliptype id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $sliptype = $this->Sliptypes->get($id, [
            'contain' => ['Slips'],
        ]);

        $this->set('sliptype', $sliptype);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $sliptype = $this->Sliptypes->newEntity();
        if ($this->request->is('post')) {
            $sliptype = $this->Sliptypes->patchEntity($sliptype, $this->request->getData());
            if ($this->Sliptypes->save($sliptype)) {
                $this->Flash->success(__('The sliptype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The sliptype could not be saved. Please, try again.'));
        }
        $this->set(compact('sliptype'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Sliptype id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $sliptype = $this->Sliptypes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $sliptype = $this->Sliptypes->patchEntity($sliptype, $this->request->getData());
            if ($this->Sliptypes->save($sliptype)) {
                $this->Flash->success(__('The sliptype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The sliptype could not be saved. Please, try again.'));
        }
        $this->set(compact('sliptype'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Sliptype id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $sliptype = $this->Sliptypes->get($id);
        if ($this->Sliptypes->delete($sliptype)) {
            $this->Flash->success(__('The sliptype has been deleted.'));
        } else {
            $this->Flash->error(__('The sliptype could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
