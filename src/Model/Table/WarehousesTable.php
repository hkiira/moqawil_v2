<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Warehouses Model
 *
 * @property \App\Model\Table\WhnaturesTable&\Cake\ORM\Association\BelongsTo $Whnatures
 * @property \App\Model\Table\WhtypesTable&\Cake\ORM\Association\BelongsTo $Whtypes
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 * @property &\Cake\ORM\Association\BelongsTo $Warehouses
 * @property &\Cake\ORM\Association\HasMany $Billings
 * @property &\Cake\ORM\Association\HasMany $Exitslips
 * @property &\Cake\ORM\Association\HasMany $Moneyboxs
 * @property \App\Model\Table\PofsalesTable&\Cake\ORM\Association\HasMany $Pofsales
 * @property &\Cake\ORM\Association\HasMany $Prices
 * @property &\Cake\ORM\Association\HasMany $Receipts
 * @property &\Cake\ORM\Association\HasMany $Reports
 * @property &\Cake\ORM\Association\HasMany $Shippings
 * @property \App\Model\Table\SlipsTable&\Cake\ORM\Association\HasMany $Slips
 * @property &\Cake\ORM\Association\HasMany $Supplierorders
 * @property &\Cake\ORM\Association\HasMany $Warehouses
 * @property \App\Model\Table\WhproductsTable&\Cake\ORM\Association\HasMany $Whproducts
 * @property \App\Model\Table\WhuserproductsTable&\Cake\ORM\Association\HasMany $Whuserproducts
 * @property \App\Model\Table\WhusersTable&\Cake\ORM\Association\HasMany $Whusers
 * @property &\Cake\ORM\Association\HasMany $Zones
 *
 * @method \App\Model\Entity\Warehouse get($primaryKey, $options = [])
 * @method \App\Model\Entity\Warehouse newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Warehouse[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Warehouse|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Warehouse saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Warehouse patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Warehouse[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Warehouse findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class WarehousesTable extends Table
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

        $this->setTable('warehouses');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Whnatures', [
            'foreignKey' => 'whnature_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Whtypes', [
            'foreignKey' => 'whtype_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Subwarehouses', [
            'className' => 'Warehouses',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
        $this->belongsTo('Parentwarehouses', [
            'className' => 'Warehouses',
            'foreignKey' => 'warehouse_id',
            'joinType' => 'INNER',
        ]);
        $this->hasOne('Adresses', [
            'foreignKey' => 'objectid',
        ]);
        $this->hasMany('Billings', [
            'foreignKey' => 'warehouse_id',
        ]);
        $this->hasMany('Exitslips', [
            'foreignKey' => 'warehouse_id',
        ]);
        $this->hasMany('Moneyboxs', [
            'foreignKey' => 'warehouse_id',
        ]);
        $this->hasMany('Inventories', [
            'foreignKey' => 'warehouse_id',
        ]);
        $this->hasMany('Pofsales', [
            'foreignKey' => 'warehouse_id',
        ]);
        $this->hasMany('Prices', [
            'foreignKey' => 'warehouse_id',
        ]);
        $this->hasMany('Receipts', [
            'foreignKey' => 'warehouse_id',
        ]);
        $this->hasMany('Reports', [
            'foreignKey' => 'warehouse_id',
        ]);
        $this->hasMany('Shippings', [
            'foreignKey' => 'warehouse_id',
        ]);
        $this->hasMany('Slips', [
            'foreignKey' => 'warehouse_id',
        ]);
        $this->hasMany('Supplierorders', [
            'foreignKey' => 'warehouse_id',
        ]);
        $this->hasMany('Warehouses', [
            'foreignKey' => 'warehouse_id',
        ]);
        $this->hasMany('Whproducts', [
            'foreignKey' => 'warehouse_id',
        ]);
        $this->hasMany('Whuserproducts', [
            'foreignKey' => 'warehouse_id',
        ]);
        $this->hasMany('Whusers', [
            'foreignKey' => 'warehouse_id',
        ]);
        $this->hasMany('Zones', [
            'foreignKey' => 'warehouse_id',
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
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->integer('statut')
            ->allowEmptyString('statut');

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
        $rules->add($rules->existsIn(['whnature_id'], 'Whnatures'));
        $rules->add($rules->existsIn(['whtype_id'], 'Whtypes'));
        $rules->add($rules->existsIn(['company_id'], 'Companies'));
        $rules->add($rules->existsIn(['warehouse_id'], 'Warehouses'));

        return $rules;
    }
}
