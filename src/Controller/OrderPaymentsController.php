<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\EventInterface;

/**
 * OrderPayments Controller
 *
 * @property \App\Model\Table\OrderPaymentsTable $OrderPayments
 * @property \App\Model\Table\PaymentMethodsTable $PaymentMethods
 */
class OrderPaymentsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('PaymentMethods');
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['index', 'view']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Orders', 'PaymentMethods'],
        ];
        $orderPayments = $this->paginate($this->OrderPayments);

        $this->set(compact('orderPayments'));
    }

    /**
     * View method
     *
     * @param string|null $id Order Payment id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $orderPayment = $this->OrderPayments->get($id, [
            'contain' => ['Orders', 'PaymentMethods'],
        ]);

        $this->set(compact('orderPayment'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $orderPayment = $this->OrderPayments->newEmptyEntity();
        if ($this->request->is('post')) {
            $orderPayment = $this->OrderPayments->patchEntity($orderPayment, $this->request->getData());
            if ($this->OrderPayments->save($orderPayment)) {
                $this->Flash->success(__('The order payment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The order payment could not be saved. Please, try again.'));
        }
        $orders = $this->OrderPayments->Orders->find('list', ['limit' => 200]);
        $paymentMethods = $this->PaymentMethods->find('list', ['limit' => 200]);
        $this->set(compact('orderPayment', 'orders', 'paymentMethods'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Order Payment id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $orderPayment = $this->OrderPayments->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $orderPayment = $this->OrderPayments->patchEntity($orderPayment, $this->request->getData());
            if ($this->OrderPayments->save($orderPayment)) {
                $this->Flash->success(__('The order payment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The order payment could not be saved. Please, try again.'));
        }
        $orders = $this->OrderPayments->Orders->find('list', ['limit' => 200]);
        $paymentMethods = $this->PaymentMethods->find('list', ['limit' => 200]);
        $this->set(compact('orderPayment', 'orders', 'paymentMethods'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Order Payment id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $orderPayment = $this->OrderPayments->get($id);
        if ($this->OrderPayments->delete($orderPayment)) {
            $this->Flash->success(__('The order payment has been deleted.'));
        } else {
            $this->Flash->error(__('The order payment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Split payment method
     *
     * @param int $orderId The order ID
     * @return \Cake\Http\Response|null|void Redirects on successful split, renders view otherwise.
     */
    public function split($orderId)
    {
        $order = $this->OrderPayments->Orders->get($orderId);
        $totalAmount = $order->total_amount;
        $totalPaid = $this->OrderPayments->calculateTotalPaid($orderId);
        $remainingAmount = $totalAmount - $totalPaid;

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $payments = [];

            // Validate total amount matches
            $sum = 0;
            foreach ($data['payments'] as $payment) {
                $sum += $payment['amount'];
            }

            if (abs($sum - $remainingAmount) > 0.01) {
                $this->Flash->error(__('The sum of payment amounts must equal the remaining amount.'));
                return $this->redirect(['action' => 'split', $orderId]);
            }

            // Create payment records
            foreach ($data['payments'] as $payment) {
                $paymentEntity = $this->OrderPayments->newEmptyEntity();
                $paymentEntity = $this->OrderPayments->patchEntity($paymentEntity, [
                    'order_id' => $orderId,
                    'payment_method_id' => $payment['payment_method_id'],
                    'amount' => $payment['amount'],
                    'cheque_date' => $payment['cheque_date'] ?? null,
                    'statut' => 0
                ]);

                if (!$this->OrderPayments->save($paymentEntity)) {
                    $this->Flash->error(__('Error saving payment split.'));
                    return $this->redirect(['action' => 'split', $orderId]);
                }
            }

            $this->Flash->success(__('Payment has been split successfully.'));
            return $this->redirect(['controller' => 'Orders', 'action' => 'view', $orderId]);
        }

        $paymentMethods = $this->PaymentMethods->find('list', ['limit' => 200]);
        $this->set(compact('order', 'totalAmount', 'totalPaid', 'remainingAmount', 'paymentMethods'));
    }

    /**
     * Mark payment as paid
     *
     * @param int $id Payment ID
     * @return \Cake\Http\Response|null|void Redirects on successful update
     */
    public function markAsPaid($id)
    {
        $this->request->allowMethod(['post']);
        $payment = $this->OrderPayments->get($id);
        $payment->statut = 1;
        
        if ($this->OrderPayments->save($payment)) {
            $this->Flash->success(__('Payment has been marked as paid.'));
        } else {
            $this->Flash->error(__('Error marking payment as paid.'));
        }

        return $this->redirect($this->referer());
    }
} 