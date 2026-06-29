<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * OrderPayments Model
 *
 * @property \App\Model\Table\OrdersTable&\Cake\ORM\Association\BelongsTo $Orders
 * @property \App\Model\Table\PaymentMethodsTable&\Cake\ORM\Association\BelongsTo $PaymentMethods
 *
 * @method \App\Model\Entity\OrderPayment get($primaryKey, $options = [])
 * @method \App\Model\Entity\OrderPayment newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\OrderPayment[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\OrderPayment|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\OrderPayment saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\OrderPayment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\OrderPayment[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\OrderPayment findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class OrderPaymentsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('order_payments');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Orders', [
            'foreignKey' => 'order_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Reports', [
            'foreignKey' => 'report_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('PaymentMethods', [
            'foreignKey' => 'payment_method_id',
            'joinType' => 'INNER',
        ]);
        $this->hasOne('Photos', [
            'foreignKey' => 'objectid',
        ]);
        
        $this->belongsTo('Payments', [
            'foreignKey' => 'payment_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('order_id')
            ->notEmptyString('order_id');

        $validator
            ->integer('payment_method_id')
            ->requirePresence('payment_method_id', 'create')
            ->notEmptyString('payment_method_id');

        $validator
            ->numeric('amount')
            ->requirePresence('amount', 'create')
            ->notEmptyString('amount')
            ->greaterThan('amount', 0, 'Amount must be greater than 0');

        $validator
            ->date('cheque_date')
            ->allowEmptyDate('cheque_date');

        $validator
            ->integer('statut')
            ->notEmptyString('statut');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['order_id'], 'Orders'));
        $rules->add($rules->existsIn(['report_id'], 'Reports'));
        $rules->add($rules->existsIn(['payment_id'], 'Payments'));
        $rules->add($rules->existsIn(['payment_method_id'], 'PaymentMethods'));

        return $rules;
    }

    /**
     * Calculate the total amount paid for an order
     *
     * @param int $orderId The order ID
     * @return float The total amount paid
     */
    public function calculateTotalPaid($orderId)
    {
        $payments = $this->find()
            ->where([
                'order_id' => $orderId,
                'statut' => 1 // Assuming 1 means paid
            ]);

        $totalPaid = 0;
        foreach ($payments as $payment) {
            $totalPaid += $payment->amount;
        }

        return $totalPaid;
    }

    /**
     * Calculate the remaining debt for an order
     *
     * @param int $orderId The order ID
     * @param float $totalAmount The total order amount
     * @return float The remaining debt amount
     */
    public function calculateRemainingDebt($orderId, $totalAmount)
    {
        $totalPaid = $this->calculateTotalPaid($orderId);
        return max(0, $totalAmount - $totalPaid);
    }

    /**
     * Get upcoming cheque payments
     *
     * @param int $daysAhead Number of days to look ahead
     * @return \Cake\ORM\Query
     */
    public function getUpcomingChequePayments($daysAhead = 7)
    {
        $today = new \Cake\I18n\Date();
        $endDate = $today->addDays($daysAhead);

        return $this->find()
            ->where([
                'cheque_date IS NOT NULL',
                'cheque_date >=' => $today,
                'cheque_date <=' => $endDate,
                'statut' => 0 // Assuming 0 means unpaid
            ])
            ->contain(['Orders', 'PaymentMethods'])
            ->order(['cheque_date' => 'ASC']);
    }
} 