<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Payments Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\PaymentMethodsTable&\Cake\ORM\Association\BelongsTo $PaymentMethods
 * @property &\Cake\ORM\Association\HasMany $Orders
 * @property \App\Model\Table\PaymentgoalsTable&\Cake\ORM\Association\HasMany $Paymentgoals
 *
 * @method \App\Model\Entity\Payment get($primaryKey, $options = [])
 * @method \App\Model\Entity\Payment newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Payment[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Payment|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Payment saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Payment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Payment[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Payment findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PaymentsTable extends Table
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

        $this->setTable('payments');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('PaymentMethods', [
            'foreignKey' => 'payment_method_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Orders', [
            'foreignKey' => 'payment_id',
        ]);
        $this->hasMany('OrderPayments', [
            'foreignKey' => 'payment_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
        $this->hasMany('Paymentgoals', [
            'foreignKey' => 'payment_id',
        ]);
        
        $this->hasMany('Photos', [
            'foreignKey' => 'objectid',
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
            ->scalar('code')
            ->maxLength('code', 255)
            ->requirePresence('code', 'create')
            ->notEmptyString('code');

        $validator
            ->integer('statut')
            ->requirePresence('statut', 'create')
            ->notEmptyString('statut');

        $validator
            ->date('datedepart')
            ->allowEmptyDate('datedepart');

        $validator
            ->date('datefin')
            ->allowEmptyDate('datefin');

        $validator
            ->integer('payment_method_id')
            ->requirePresence('payment_method_id', 'create')
            ->notEmptyString('payment_method_id');

        $validator
            ->date('cheque_date')
            ->allowEmptyDate('cheque_date');

        $validator
            ->numeric('amount')
            ->requirePresence('amount', 'create')
            ->notEmptyString('amount');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['payment_method_id'], 'PaymentMethods'));

        return $rules;
    }

    /**
     * Calculate the total debt for a user
     *
     * @param int $userId The user ID
     * @return float The total debt amount
     */
    public function calculateTotalDebt($userId)
    {
        $payments = $this->find()
            ->where([
                'user_id' => $userId,
                'statut' => 0 // Assuming 0 means unpaid
            ])
            ->contain(['PaymentMethods']);

        $totalDebt = 0;
        foreach ($payments as $payment) {
            $totalDebt += $payment->amount;
        }

        return $totalDebt;
    }

    /**
     * Get payments with upcoming cheque dates
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
            ->contain(['Users', 'PaymentMethods'])
            ->order(['cheque_date' => 'ASC']);
    }
}
