<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Goals Controller
 *
 * @property \App\Model\Table\GoalsTable $Goals
 *
 * @method \App\Model\Entity\Goal[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GoalsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Goaltypes'],
        ];
        $goals = $this->paginate($this->Goals);

        $this->set(compact('goals'));
    }

    /**
     * View method
     *
     * @param string|null $id Goal id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $goal = $this->Goals->get($id, [
            'contain' => ['Goaltypes'],
        ]);

        $this->set('goal', $goal);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $goal = $this->Goals->newEntity();
        if ($this->request->is('post')) {
            $goal = $this->Goals->patchEntity($goal, $this->request->getData());
            if ($this->Goals->save($goal)) {
                $this->Flash->success(__('The goal has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The goal could not be saved. Please, try again.'));
        }
        $goaltypes = $this->Goals->Goaltypes->find('list', ['limit' => 200]);
        $this->set(compact('goal', 'goaltypes'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Goal id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $goal = $this->Goals->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $goal = $this->Goals->patchEntity($goal, $this->request->getData());
            if ($this->Goals->save($goal)) {
                $this->Flash->success(__('The goal has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The goal could not be saved. Please, try again.'));
        }
        $goaltypes = $this->Goals->Goaltypes->find('list', ['limit' => 200]);
        $this->set(compact('goal', 'goaltypes'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Goal id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $goal = $this->Goals->get($id);
        if ($this->Goals->delete($goal)) {
            $this->Flash->success(__('The goal has been deleted.'));
        } else {
            $this->Flash->error(__('The goal could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
