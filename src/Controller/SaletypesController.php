<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Saletypes Controller
 *
 * @property \App\Model\Table\SaletypesTable $Saletypes
 *
 * @method \App\Model\Entity\Saletype[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SaletypesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Companies'],
        ];
        $saletypes = $this->paginate($this->Saletypes);

        $this->set(compact('saletypes'));
    }

    /**
     * View method
     *
     * @param string|null $id Saletype id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $saletype = $this->Saletypes->get($id, [
            'contain' => ['Companies', 'Packs'],
        ]);

        $this->set('saletype', $saletype);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $saletype = $this->Saletypes->newEntity();
        if ($this->request->is('post')) {
            $saletype = $this->Saletypes->patchEntity($saletype, $this->request->getData());
            if ($this->Saletypes->save($saletype)) {
                $this->Flash->success(__('The saletype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The saletype could not be saved. Please, try again.'));
        }
        $companies = $this->Saletypes->Companies->find('list', ['limit' => 200]);
        $this->set(compact('saletype', 'companies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Saletype id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $saletype = $this->Saletypes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $saletype = $this->Saletypes->patchEntity($saletype, $this->request->getData());
            if ($this->Saletypes->save($saletype)) {
                $this->Flash->success(__('The saletype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The saletype could not be saved. Please, try again.'));
        }
        $companies = $this->Saletypes->Companies->find('list', ['limit' => 200]);
        $this->set(compact('saletype', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Saletype id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $saletype = $this->Saletypes->get($id);
        if ($this->Saletypes->delete($saletype)) {
            $this->Flash->success(__('The saletype has been deleted.'));
        } else {
            $this->Flash->error(__('The saletype could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
