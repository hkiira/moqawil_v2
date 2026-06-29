<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Whproducts Model
 *
 * @property &\Cake\ORM\Association\BelongsTo $Packs
 * @property \App\Model\Table\WarehousesTable&\Cake\ORM\Association\BelongsTo $Warehouses
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 * @property \App\Model\Table\WhuserproductsTable&\Cake\ORM\Association\HasMany $Whuserproducts
 *
 * @method \App\Model\Entity\Whproduct get($primaryKey, $options = [])
 * @method \App\Model\Entity\Whproduct newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Whproduct[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Whproduct|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Whproduct saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Whproduct patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Whproduct[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Whproduct findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class WhproductsTable extends Table
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

        $this->setTable('whproducts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        // Removed: $this->belongsTo('Packs', ...)
        // Polymorphic belongsTo (to Packs or Products based on item_type) is complex.
        // It's often handled by fetching the item manually in the entity or controller,
        // or by using a polymorphic behavior if available/custom-built.
        // For now, Whproduct entities will have item_id and item_type,
        // and you'd load the specific item (Pack or Product) as needed.

        $this->belongsTo('Warehouses', [
            'foreignKey' => 'warehouse_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Packs', [
            'foreignKey' => 'item_id',
            'conditions' => ['Whproducts.item_type' => 'Pack'], // Added condition
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Whuserproducts', [
            'foreignKey' => 'whproduct_id',
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
            ->requirePresence('item_id', 'create')
            ->notEmptyString('item_id');

        $validator
            ->scalar('item_type')
            ->maxLength('item_type', 50) // Adjust length as needed
            ->requirePresence('item_type', 'create')
            ->notEmptyString('item_type')
            ->inList('item_type', ['Pack', 'Product'], 'Invalid item type. Must be Pack or Product.');

        $validator
            ->integer('quantity')
            ->notEmptyString('quantity'); // Or allowEmptyString if 0 is a valid initial state

        $validator
            ->integer('statut')
            ->allowEmptyString('statut'); // Or provide a default, e.g. ->notEmptyString('statut')->inList('statut', [0,1])

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
        // Removed: $rules->add($rules->existsIn(['pack_id'], 'Packs'));
        // Adding a polymorphic rule for item_id requires custom logic:
        // $rules->add(function ($entity, $options) {
        //     if ($entity->item_type === 'Pack') {
        //         $rule = new \Cake\ORM\Rule\ExistsIn(['item_id'], 'Packs');
        //         return $rule($entity, $options);
        //     }
        //     if ($entity->item_type === 'Product') {
        //         $rule = new \Cake\ORM\Rule\ExistsIn(['item_id'], 'Products');
        //         return $rule($entity, $options);
        //     }
        //     return false; // Or true if item_type can be other things not needing this rule
        // }, 'itemExists', [
        //     'errorField' => 'item_id',
        //     'message' => 'The selected item does not exist.'
        // ]);

        $rules->add($rules->existsIn(['warehouse_id'], 'Warehouses'));
        $rules->add($rules->existsIn(['company_id'], 'Companies'));

        return $rules;
    }
}
