<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Goaltypes Controller
 *
 * @property \App\Model\Table\GoaltypesTable $Goaltypes
 *
 * @method \App\Model\Entity\Goaltype[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GoaltypesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $goaltypes = $this->paginate($this->Goaltypes);

        $this->set(compact('goaltypes'));
    }

    /**
     * View method
     *
     * @param string|null $id Goaltype id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $goaltype = $this->Goaltypes->get($id, [
            'contain' => ['Goals'],
        ]);

        $this->set('goaltype', $goaltype);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $goaltype = $this->Goaltypes->newEntity();
        if ($this->request->is('post')) {
            $goaltype = $this->Goaltypes->patchEntity($goaltype, $this->request->getData());
            if ($this->Goaltypes->save($goaltype)) {
                $this->Flash->success(__('The goaltype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The goaltype could not be saved. Please, try again.'));
        }
        $this->set(compact('goaltype'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Goaltype id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $goaltype = $this->Goaltypes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $goaltype = $this->Goaltypes->patchEntity($goaltype, $this->request->getData());
            if ($this->Goaltypes->save($goaltype)) {
                $this->Flash->success(__('The goaltype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The goaltype could not be saved. Please, try again.'));
        }
        $this->set(compact('goaltype'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Goaltype id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $goaltype = $this->Goaltypes->get($id);
        if ($this->Goaltypes->delete($goaltype)) {
            $this->Flash->success(__('The goaltype has been deleted.'));
        } else {
            $this->Flash->error(__('The goaltype could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
