<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Suppliers Model
 *
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 * @property \App\Model\Table\ProductsTable&\Cake\ORM\Association\HasMany $Products
 * @property \App\Model\Table\ReceiptsTable&\Cake\ORM\Association\HasMany $Receipts
 * @property \App\Model\Table\SupplierordersTable&\Cake\ORM\Association\HasMany $Supplierorders
 * @property \App\Model\Table\SupporderproductsTable&\Cake\ORM\Association\HasMany $Supporderproducts
 *
 * @method \App\Model\Entity\Supplier get($primaryKey, $options = [])
 * @method \App\Model\Entity\Supplier newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Supplier[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Supplier|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Supplier saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Supplier patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Supplier[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Supplier findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SuppliersTable extends Table
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

        $this->setTable('suppliers');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Products', [
            'foreignKey' => 'supplier_id',
        ]);
        $this->hasMany('Receipts', [
            'foreignKey' => 'supplier_id',
        ]);
        $this->hasMany('Supplierorders', [
            'foreignKey' => 'supplier_id',
        ]);
        
        $this->hasOne('Adresses', [
            'foreignKey' => 'objectid',
        ]);
        
        $this->hasMany('Supporderproducts', [
            'foreignKey' => 'supplier_id',
        ]);
        
        $this->hasOne('Photos', [
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
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 15)
            ->allowEmptyString('phone');

        $validator
            ->scalar('identifiantfiscale')
            ->maxLength('identifiantfiscale', 255)
            ->allowEmptyString('identifiantfiscale');

        $validator
            ->scalar('patente')
            ->maxLength('patente', 255)
            ->allowEmptyString('patente');

        $validator
            ->scalar('rc')
            ->maxLength('rc', 255)
            ->allowEmptyString('rc');

        $validator
            ->scalar('cnss')
            ->maxLength('cnss', 255)
            ->allowEmptyString('cnss');

        $validator
            ->scalar('ice')
            ->maxLength('ice', 255)
            ->allowEmptyString('ice');

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
        $rules->add($rules->existsIn(['company_id'], 'Companies'));

        return $rules;
    }
}
