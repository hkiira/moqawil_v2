<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Orders Model
 *
 * @property \App\Model\Table\CustomersTable&\Cake\ORM\Association\BelongsTo $Customers
 * @property \App\Model\Table\ShippingsTable&\Cake\ORM\Association\BelongsTo $Shippings
 * @property \App\Model\Table\PaymentsTable&\Cake\ORM\Association\BelongsTo $Payments
 * @property \App\Model\Table\OrdertypesTable&\Cake\ORM\Association\BelongsTo $Ordertypes
 * @property \App\Model\Table\ReportsTable&\Cake\ORM\Association\BelongsTo $Reports
 * @property \App\Model\Table\SlipsTable&\Cake\ORM\Association\BelongsTo $Slips
 * @property \App\Model\Table\PofsalesTable&\Cake\ORM\Association\BelongsTo $Pofsales
 * @property \App\Model\Table\CommissionsTable&\Cake\ORM\Association\BelongsTo $Commissions
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 * @property &\Cake\ORM\Association\HasMany $Loyaltypoints
 * @property \App\Model\Table\OrderpacksTable&\Cake\ORM\Association\HasMany $Orderpacks
 * @property \App\Model\Table\OrderPaymentsTable&\Cake\ORM\Association\HasMany $OrderPayments
 * @property \App\Model\Table\PaymentMethodsTable&\Cake\ORM\Association\BelongsToMany $PaymentMethods
 *
 * @method \App\Model\Entity\Order get($primaryKey, $options = [])
 * @method \App\Model\Entity\Order newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Order[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Order|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Order saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Order patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Order[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Order findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class OrdersTable extends Table
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

        $this->setTable('orders');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Customers', [
            'foreignKey' => 'customer_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Shippings', [
            'foreignKey' => 'shipping_id',
        ]);
        $this->belongsTo('Compensations', [
            'foreignKey' => 'compensation_id',
        ]);
        $this->belongsTo('Ordertypes', [
            'foreignKey' => 'ordertype_id',
        ]);
        $this->belongsTo('Reports', [
            'foreignKey' => 'report_id',
        ]);
        $this->belongsTo('Slips', [
            'foreignKey' => 'slip_id',
        ]);
        $this->belongsTo('Pofsales', [
            'foreignKey' => 'pofsale_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Commissions', [
            'foreignKey' => 'commission_id',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Orderpacks', [
            'foreignKey' => 'order_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
        $this->hasMany('OrderPayments', [
            'foreignKey' => 'order_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
        $this->belongsToMany('PaymentMethods', [
            'through' => 'OrderPayments',
            'foreignKey' => 'order_id',
            'targetForeignKey' => 'payment_method_id'
        ]);
        
        $this->hasOne('Photos', [
            'foreignKey' => 'objectid',
        ]);

        $this->hasMany('Loyaltypointgifts', [
            'foreignKey' => 'order_id',
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
            ->maxLength('code', 100)
            ->notEmptyString('code');

        $validator
            ->integer('statut')
            ->allowEmptyString('statut');

        $validator
            ->numeric('loyaltypoints')
            ->allowEmptyString('loyaltypoints');

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
        $rules->add($rules->existsIn(['customer_id'], 'Customers'));
        $rules->add($rules->existsIn(['shipping_id'], 'Shippings'));
        $rules->add($rules->existsIn(['compensation_id'], 'Compensations'));
        $rules->add($rules->existsIn(['ordertype_id'], 'Ordertypes'));
        $rules->add($rules->existsIn(['report_id'], 'Reports'));
        $rules->add($rules->existsIn(['slip_id'], 'Slips'));
        $rules->add($rules->existsIn(['pofsale_id'], 'Pofsales'));
        $rules->add($rules->existsIn(['commission_id'], 'Commissions'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['company_id'], 'Companies'));

        return $rules;
    }
}
