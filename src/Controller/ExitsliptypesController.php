<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Exitsliptypes Controller
 *
 * @property \App\Model\Table\ExitsliptypesTable $Exitsliptypes
 *
 * @method \App\Model\Entity\Exitsliptype[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ExitsliptypesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $exitsliptypes = $this->paginate($this->Exitsliptypes);

        $this->set(compact('exitsliptypes'));
    }

    /**
     * View method
     *
     * @param string|null $id Exitsliptype id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $exitsliptype = $this->Exitsliptypes->get($id, [
            'contain' => ['Exitslips'],
        ]);

        $this->set('exitsliptype', $exitsliptype);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $exitsliptype = $this->Exitsliptypes->newEntity();
        if ($this->request->is('post')) {
            $exitsliptype = $this->Exitsliptypes->patchEntity($exitsliptype, $this->request->getData());
            if ($this->Exitsliptypes->save($exitsliptype)) {
                $this->Flash->success(__('The exitsliptype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exitsliptype could not be saved. Please, try again.'));
        }
        $this->set(compact('exitsliptype'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Exitsliptype id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $exitsliptype = $this->Exitsliptypes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $exitsliptype = $this->Exitsliptypes->patchEntity($exitsliptype, $this->request->getData());
            if ($this->Exitsliptypes->save($exitsliptype)) {
                $this->Flash->success(__('The exitsliptype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The exitsliptype could not be saved. Please, try again.'));
        }
        $this->set(compact('exitsliptype'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Exitsliptype id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $exitsliptype = $this->Exitsliptypes->get($id);
        if ($this->Exitsliptypes->delete($exitsliptype)) {
            $this->Flash->success(__('The exitsliptype has been deleted.'));
        } else {
            $this->Flash->error(__('The exitsliptype could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
