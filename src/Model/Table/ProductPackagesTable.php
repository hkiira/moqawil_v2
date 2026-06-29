<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ProductPackages Model
 *
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 * @property \App\Model\Table\ProductsTable&\Cake\ORM\Association\BelongsToMany $Products
 *
 * @method \App\Model\Entity\ProductPackage get($primaryKey, $options = [])
 * @method \App\Model\Entity\ProductPackage newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ProductPackage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ProductPackage|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ProductPackage saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ProductPackage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ProductPackage[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ProductPackage findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ProductPackagesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('product_packages');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsToMany('Products', [
            'foreignKey' => 'product_package_id',
            'targetForeignKey' => 'product_id',
            'joinTable' => 'products_product_packages',
            'through' => 'ProductsProductPackages',
            'dependent' => true
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->decimal('weight')
            ->notEmptyString('weight')
            ->greaterThan('weight', 0, 'Weight must be greater than 0');

        $validator
            ->scalar('unit')
            ->maxLength('unit', 10)
            ->notEmptyString('unit')
            ->inList('unit', ['kg', 'g', 'l', 'ml', 'pcs'], 'Invalid unit');

        $validator
            ->boolean('is_default')
            ->notEmptyString('is_default');

        $validator
            ->integer('statut')
            ->allowEmptyString('statut');

        $validator
            ->integer('company_id')
            ->notEmptyString('company_id');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['company_id'], 'Companies'));

        return $rules;
    }
} 