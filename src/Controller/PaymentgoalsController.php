<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Paymentgoals Controller
 *
 * @property \App\Model\Table\PaymentgoalsTable $Paymentgoals
 *
 * @method \App\Model\Entity\Paymentgoal[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PaymentgoalsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Goals', 'Payments'],
        ];
        $paymentgoals = $this->paginate($this->Paymentgoals);

        $this->set(compact('paymentgoals'));
    }

    /**
     * View method
     *
     * @param string|null $id Paymentgoal id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $paymentgoal = $this->Paymentgoals->get($id, [
            'contain' => ['Goals', 'Payments'],
        ]);

        $this->set('paymentgoal', $paymentgoal);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $paymentgoal = $this->Paymentgoals->newEntity();
        if ($this->request->is('post')) {
            $paymentgoal = $this->Paymentgoals->patchEntity($paymentgoal, $this->request->getData());
            if ($this->Paymentgoals->save($paymentgoal)) {
                $this->Flash->success(__('The paymentgoal has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The paymentgoal could not be saved. Please, try again.'));
        }
        $goals = $this->Paymentgoals->Goals->find('list', ['limit' => 200]);
        $payments = $this->Paymentgoals->Payments->find('list', ['limit' => 200]);
        $this->set(compact('paymentgoal', 'goals', 'payments'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Paymentgoal id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $paymentgoal = $this->Paymentgoals->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $paymentgoal = $this->Paymentgoals->patchEntity($paymentgoal, $this->request->getData());
            if ($this->Paymentgoals->save($paymentgoal)) {
                $this->Flash->success(__('The paymentgoal has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The paymentgoal could not be saved. Please, try again.'));
        }
        $goals = $this->Paymentgoals->Goals->find('list', ['limit' => 200]);
        $payments = $this->Paymentgoals->Payments->find('list', ['limit' => 200]);
        $this->set(compact('paymentgoal', 'goals', 'payments'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Paymentgoal id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $paymentgoal = $this->Paymentgoals->get($id);
        if ($this->Paymentgoals->delete($paymentgoal)) {
            $this->Flash->success(__('The paymentgoal has been deleted.'));
        } else {
            $this->Flash->error(__('The paymentgoal could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
