<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Products Model
 *
 * @property \App\Model\Table\CategoriesTable&\Cake\ORM\Association\BelongsTo $Categories
 * @property \App\Model\Table\UnitesTable&\Cake\ORM\Association\BelongsTo $Unites
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 * @property \App\Model\Table\PackproductsTable&\Cake\ORM\Association\HasMany $Packproducts
 * @property &\Cake\ORM\Association\HasMany $Supporderproducts
 * @property \App\Model\Table\WhproductsTable&\Cake\ORM\Association\HasMany $Whproducts
 *
 * @method \App\Model\Entity\Product get($primaryKey, $options = [])
 * @method \App\Model\Entity\Product newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Product[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Product|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Product saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Product patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Product[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Product findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ProductsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void // Added :void
    {
        parent::initialize($config);

        $this->setTable('products');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Categories', [
            'foreignKey' => 'category_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Suppliers', [
            'foreignKey' => 'supplier_id',
        ]);
        $this->hasMany('Packproducts', [
            'foreignKey' => 'product_id',
        ]);
        $this->hasMany('orderpackproducts', [
            'foreignKey' => 'product_id',
        ]);
        $this->hasMany('Supporderproducts', [
            'foreignKey' => 'product_id',
        ]);
        $this->hasMany('Slipproducts', [
            'foreignKey' => 'item_id', // Changed from pack_id
            'conditions' => ['Slipproducts.item_type' => 'Product'], // Added condition
            'propertyName' => 'slipproducts' // Explicit property name
        ]);
        $this->hasMany('Productunites', [
            'foreignKey' => 'product_id',
        ]);
        $this->hasMany('Whproducts', [
            'foreignKey' => 'item_id', // Changed from product_id
            'conditions' => ['Whproducts.item_type' => 'Product'], // Added condition
            'dependent' => true, // Cascade deletes to stock records
            'propertyName' => 'whproducts' // Explicit property name
        ]);
        $this->belongsTo('MeasurementUnits', [
            'foreignKey' => 'measurement_unit_id',
            'joinType' => 'LEFT'
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
    public function validationDefault(Validator $validator): Validator // Added :Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('reference')
            ->maxLength('reference', 255)
            ->requirePresence('reference', 'create')
            ->notEmptyString('reference');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->numeric('buyingprice')
            ->requirePresence('buyingprice', 'create')
            ->notEmptyString('buyingprice');

        $validator
            ->numeric('sellingprice')
            ->requirePresence('sellingprice', 'create')
            ->notEmptyString('sellingprice');

        $validator
            ->numeric('commission')
            ->allowEmptyString('commission');

        $validator
            ->integer('editted')
            ->allowEmptyString('editted');

        $validator
            ->integer('statut')
            ->allowEmptyString('statut');

        $validator
            ->numeric('measurement_quantity')
            ->notEmptyString('measurement_quantity')
            ->greaterThanOrEqual('measurement_quantity', 0.01, 'La quantité doit être supérieure à 0');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker // Added :RulesChecker
    {
        $rules->add($rules->existsIn(['measurement_unit_id'], 'MeasurementUnits'));
        $rules->add($rules->existsIn(['category_id'], 'Categories'));
        $rules->add($rules->existsIn(['supplier_id'], 'Suppliers'));
        $rules->add($rules->existsIn(['company_id'], 'Companies'));

        return $rules;
    }
}
