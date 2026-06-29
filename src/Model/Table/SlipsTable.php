<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Slips Model
 *
 * @property &\Cake\ORM\Association\BelongsTo $Commissions
 * @property \App\Model\Table\WarehousesTable&\Cake\ORM\Association\BelongsTo $Warehouses
 * @property \App\Model\Table\WhnaturesTable&\Cake\ORM\Association\BelongsTo $Whnatures
 * @property \App\Model\Table\ReportsTable&\Cake\ORM\Association\BelongsTo $Reports
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\SliptypesTable&\Cake\ORM\Association\BelongsTo $Sliptypes
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 * @property \App\Model\Table\ExitslipsTable&\Cake\ORM\Association\BelongsTo $Exitslips
 * @property \App\Model\Table\OrdersTable&\Cake\ORM\Association\HasMany $Orders
 * @property \App\Model\Table\ShippingsTable&\Cake\ORM\Association\HasMany $Shippings
 * @property \App\Model\Table\SlipproductsTable&\Cake\ORM\Association\HasMany $Slipproducts
 *
 * @method \App\Model\Entity\Slip get($primaryKey, $options = [])
 * @method \App\Model\Entity\Slip newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Slip[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Slip|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Slip saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Slip patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Slip[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Slip findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SlipsTable extends Table
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

        $this->setTable('slips');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Commissions', [
            'foreignKey' => 'commission_id',
        ]);
        $this->belongsTo('Warehouses', [
            'foreignKey' => 'warehouse_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Whnatures', [
            'foreignKey' => 'whnature_id',
        ]);
        $this->belongsTo('Reports', [
            'foreignKey' => 'report_id',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Sliptypes', [
            'foreignKey' => 'sliptype_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Exitslips', [
            'foreignKey' => 'exitslip_id',
        ]);
        $this->hasMany('Orders', [
            'foreignKey' => 'slip_id',
        ]);
        $this->hasMany('Shippings', [
            'foreignKey' => 'slip_id',
        ]);
        $this->hasMany('Slipproducts', [
            'foreignKey' => 'slip_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
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
            ->maxLength('code', 200)
            ->allowEmptyString('code');

        $validator
            ->scalar('raison')
            ->maxLength('raison', 255)
            ->allowEmptyString('raison');

        $validator
            ->integer('statut')
            ->allowEmptyString('statut');

        $validator
            ->integer('warehoused')
            ->allowEmptyString('warehoused');

        $validator
            ->integer('whnatured')
            ->allowEmptyString('whnatured');

        $validator
            ->integer('uservalidate')
            ->allowEmptyString('uservalidate');

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
        $rules->add($rules->existsIn(['commission_id'], 'Commissions'));
        $rules->add($rules->existsIn(['warehouse_id'], 'Warehouses'));
        $rules->add($rules->existsIn(['whnature_id'], 'Whnatures'));
        $rules->add($rules->existsIn(['report_id'], 'Reports'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['sliptype_id'], 'Sliptypes'));
        $rules->add($rules->existsIn(['company_id'], 'Companies'));
        $rules->add($rules->existsIn(['exitslip_id'], 'Exitslips'));

        return $rules;
    }
}
