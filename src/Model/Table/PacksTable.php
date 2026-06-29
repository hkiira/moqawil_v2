<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableSchemaInterface;
use Cake\ORM\RelationsCollection;
use Cake\Database\Schema\TableSchema;

/**
 * Packs Model
 *
 * @property &\Cake\ORM\Association\BelongsTo $Packs
 * @property &\Cake\ORM\Association\BelongsTo $Variations
 * @property \App\Model\Table\BrandsTable&\Cake\ORM\Association\BelongsTo $Brands
 * @property \App\Model\Table\PacktypesTable&\Cake\ORM\Association\BelongsTo $Packtypes
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 * @property \App\Model\Table\CategoriesTable&\Cake\ORM\Association\BelongsTo $Categories
 * @property \App\Model\Table\TurnoversTable&\Cake\ORM\Association\BelongsTo $Turnovers
 * @property \App\Model\Table\PackagingtypesTable&\Cake\ORM\Association\BelongsTo $Packagingtypes
 * @property \App\Model\Table\PacktaxesTable&\Cake\ORM\Association\BelongsTo $Packtaxes
 * @property \App\Model\Table\BillingpacksTable&\Cake\ORM\Association\HasMany $Billingpacks
 * @property \App\Model\Table\InvproductsTable&\Cake\ORM\Association\HasMany $Invproducts
 * @property \App\Model\Table\OrderpacksTable&\Cake\ORM\Association\HasMany $Orderpacks
 * @property \App\Model\Table\PackproductsTable&\Cake\ORM\Association\HasMany $Packproducts
 * @property &\Cake\ORM\Association\HasMany $Packs
 * @property \App\Model\Table\PackunitesTable&\Cake\ORM\Association\HasMany $Packunites
 * @property \App\Model\Table\PricesTable&\Cake\ORM\Association\HasMany $Prices
 * @property \App\Model\Table\SlipproductsTable&\Cake\ORM\Association\HasMany $Slipproducts
 * @property \App\Model\Table\SupporderproductsTable&\Cake\ORM\Association\HasMany $Supporderproducts
 * @property \App\Model\Table\TranchesTable&\Cake\ORM\Association\HasMany $Tranches
 * @property \App\Model\Table\WhproductsTable&\Cake\ORM\Association\HasMany $Whproducts
 *
 * @method \App\Model\Entity\Pack get($primaryKey, $options = [])
 * @method \App\Model\Entity\Pack newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Pack[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Pack|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Pack saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Pack patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Pack[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Pack findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PacksTable extends Table
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

        $this->setTable('packs');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Packs', [
            'foreignKey' => 'pack_id',
        ]);
        $this->hasMany('Categoryuserpacks', [
            'foreignKey' => 'pack_id',
        ]);
        $this->belongsTo('Variations', [
            'foreignKey' => 'variation_id',
        ]);
        $this->belongsTo('Brands', [
            'foreignKey' => 'brand_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Turnovers', [
            'foreignKey' => 'turnover_id',
        ]);
        $this->belongsTo('Packtypes', [
            'foreignKey' => 'packtype_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Subpacks', [
            'className' => 'Packs'
        ]);
        $this->belongsTo('Parentpacks', [
            'className' => 'Packs',
            'foreignKey' => 'pack_id',
        ]);
        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
        ]);
        $this->belongsTo('Categories', [
            'foreignKey' => 'category_id',
        ]);
        $this->belongsTo('Saletypes', [
            'foreignKey' => 'saletype_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Packtaxes', [
            'foreignKey' => 'packtax_id',
        ]);
        $this->belongsTo('MeasurementUnits', [
            'foreignKey' => 'measurement_unit_id',
            'joinType' => 'LEFT'
        ]);
        $this->hasMany('Billingpacks', [
            'foreignKey' => 'pack_id',
        ]);
        $this->hasMany('Invproducts', [
            'foreignKey' => 'pack_id',
        ]);
        $this->hasMany('Orderpacks', [
            'foreignKey' => 'pack_id',
        ]);
        $this->hasMany('Packproducts', [
            'foreignKey' => 'pack_id',
        ]);
        $this->hasMany('Packs', [
            'foreignKey' => 'pack_id',
        ]);
        $this->hasMany('Packunites', [
            'foreignKey' => 'pack_id',
        ]);
        $this->hasMany('Prices', [
            'foreignKey' => 'pack_id',
        ]);
        $this->hasMany('Slipproducts', [
            'foreignKey' => 'item_id', // Changed from pack_id
            'conditions' => ['Slipproducts.item_type' => 'Pack'], // Added condition
            'propertyName' => 'slipproducts' // Explicit property name
        ]);
        $this->hasMany('Supporderproducts', [
            'foreignKey' => 'pack_id',
        ]);
        $this->hasMany('Tranches', [
            'foreignKey' => 'pack_id',
        ]);
        $this->hasMany('Whproducts', [
            'foreignKey' => 'item_id', // Changed from pack_id
            'conditions' => ['Whproducts.item_type' => 'Pack'], // Added condition
            'propertyName' => 'whproducts' // Explicit property name
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
            ->scalar('code')
            ->maxLength('code', 255)
            ->requirePresence('code', 'create')
            ->notEmptyString('code');

        $validator
            ->scalar('barecode')
            ->maxLength('barecode', 50)
            ->allowEmptyString('barecode');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->integer('gstock')
            ->notEmptyString('gstock');

        $validator
            ->numeric('commission')
            ->notEmptyString('commission');

        $validator
            ->integer('statut')
            ->allowEmptyString('statut');

        $validator
            ->numeric('buyingprice')
            ->allowEmptyString('buyingprice');

        $validator
            ->integer('app')
            ->notEmptyString('app');

        $validator
            ->numeric('loyaltypoints')
            ->allowEmptyString('loyaltypoints');

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
        $rules->add($rules->existsIn(['pack_id'], 'Packs'));
        $rules->add($rules->existsIn(['variation_id'], 'Variations'));
        $rules->add($rules->existsIn(['brand_id'], 'Brands'));
        $rules->add($rules->existsIn(['turnover_id'], 'Turnovers'));
        $rules->add($rules->existsIn(['packtype_id'], 'Packtypes'));
        $rules->add($rules->existsIn(['company_id'], 'Companies'));
        $rules->add($rules->existsIn(['category_id'], 'Categories'));
        $rules->add($rules->existsIn(['saletype_id'], 'Saletypes'));
        $rules->add($rules->existsIn(['packtax_id'], 'Packtaxes'));

        return $rules;
    }

}
