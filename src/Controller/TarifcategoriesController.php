<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Tarifcategories Controller
 *
 * @property \App\Model\Table\TarifcategoriesTable $Tarifcategories
 *
 * @method \App\Model\Entity\Tarifcategory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TarifcategoriesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Tarifs', 'Categories', 'Companies'],
        ];
        $tarifcategories = $this->paginate($this->Tarifcategories);

        $this->set(compact('tarifcategories'));
    }

    /**
     * View method
     *
     * @param string|null $id Tarifcategory id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $tarifcategory = $this->Tarifcategories->get($id, [
            'contain' => ['Tarifs', 'Categories', 'Companies'],
        ]);

        $this->set('tarifcategory', $tarifcategory);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $tarifcategory = $this->Tarifcategories->newEntity();
        if ($this->request->is('post')) {
            $tarifcategory = $this->Tarifcategories->patchEntity($tarifcategory, $this->request->getData());
            if ($this->Tarifcategories->save($tarifcategory)) {
                $this->Flash->success(__('The tarifcategory has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The tarifcategory could not be saved. Please, try again.'));
        }
        $tarifs = $this->Tarifcategories->Tarifs->find('list', ['limit' => 200]);
        $categories = $this->Tarifcategories->Categories->find('list', ['limit' => 200]);
        $companies = $this->Tarifcategories->Companies->find('list', ['limit' => 200]);
        $this->set(compact('tarifcategory', 'tarifs', 'categories', 'companies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Tarifcategory id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $tarifcategory = $this->Tarifcategories->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $tarifcategory = $this->Tarifcategories->patchEntity($tarifcategory, $this->request->getData());
            if ($this->Tarifcategories->save($tarifcategory)) {
                $this->Flash->success(__('The tarifcategory has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The tarifcategory could not be saved. Please, try again.'));
        }
        $tarifs = $this->Tarifcategories->Tarifs->find('list', ['limit' => 200]);
        $categories = $this->Tarifcategories->Categories->find('list', ['limit' => 200]);
        $companies = $this->Tarifcategories->Companies->find('list', ['limit' => 200]);
        $this->set(compact('tarifcategory', 'tarifs', 'categories', 'companies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Tarifcategory id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $tarifcategory = $this->Tarifcategories->get($id);
        if ($this->Tarifcategories->delete($tarifcategory)) {
            $this->Flash->success(__('The tarifcategory has been deleted.'));
        } else {
            $this->Flash->error(__('The tarifcategory could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
