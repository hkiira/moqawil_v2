<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * StockMovements Model
 *
 * @property \App\Model\Table\ItemsTable&\Cake\ORM\Association\BelongsTo $Items
 * @property \App\Model\Table\WarehousesTable&\Cake\ORM\Association\BelongsTo $Warehouses
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 * @property \App\Model\Table\RelatedDocumentsTable&\Cake\ORM\Association\BelongsTo $RelatedDocuments
 * @property \App\Model\Table\ValidatedByUsersTable&\Cake\ORM\Association\BelongsTo $ValidatedByUsers
 *
 * @method \App\Model\Entity\StockMovement get($primaryKey, $options = [])
 * @method \App\Model\Entity\StockMovement newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\StockMovement[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\StockMovement|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StockMovement saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StockMovement patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\StockMovement[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\StockMovement findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class StockMovementsTable extends Table
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

        $this->setTable('stock_movements');
        $this->setDisplayField('id'); // Or perhaps a combination like item_type and quantity_change
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        // item_id is polymorphic, so no direct belongsTo('Items')
        // You would typically fetch the related Pack or Product manually in your code
        // based on item_type and item_id.

        $this->belongsTo('Warehouses', [
            'foreignKey' => 'warehouse_id',
            'joinType' => 'INNER', // Or 'LEFT' if warehouse_id can be nullable
        ]);
        $this->belongsTo('Users', [ // User who performed the action
            'foreignKey' => 'user_id',
            // joinType 'LEFT' if user_id can be null (e.g. system actions)
        ]);
        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
            // joinType 'LEFT' if company_id can be null
        ]);
        
        // For validated_by_user_id, if it points to the Users table:
        $this->belongsTo('ValidatedByUsers', [
            'className' => 'Users',
            'foreignKey' => 'validated_by_user_id',
        ]);
        // related_document_id is also polymorphic, so no direct belongsTo here.
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
            ->integer('item_id')
            ->requirePresence('item_id', 'create')
            ->notEmptyString('item_id');

        $validator
            ->scalar('item_type')
            ->maxLength('item_type', 50)
            ->requirePresence('item_type', 'create')
            ->notEmptyString('item_type')
            ->inList('item_type', ['Product', 'Pack'], 'Invalid item type. Must be Product or Pack.');

        $validator
            ->decimal('quantity_change')
            ->requirePresence('quantity_change', 'create')
            ->notEmptyString('quantity_change');
            // ->add('quantity_change', 'notZero', [
            //     'rule' => function ($value, $context) {
            //         return $value != 0;
            //     },
            //     'message' => 'Quantity change cannot be zero.'
            // ]);


        $validator
            ->decimal('balance_after_movement')
            ->requirePresence('balance_after_movement', 'create')
            ->notEmptyString('balance_after_movement');

        $validator
            ->scalar('movement_type')
            ->maxLength('movement_type', 100)
            ->requirePresence('movement_type', 'create')
            ->notEmptyString('movement_type');
        
        $validator
            ->integer('user_id')
            ->allowEmptyString('user_id'); // Allow null if system can make movements

        $validator
            ->integer('company_id')
            ->allowEmptyString('company_id');

        $validator
            ->integer('related_document_id')
            ->allowEmptyString('related_document_id');

        $validator
            ->scalar('related_document_type')
            ->maxLength('related_document_type', 50)
            ->allowEmptyString('related_document_type');

        $validator
            ->scalar('notes')
            ->allowEmptyString('notes');
        
        $validator
            ->integer('validated_by_user_id')
            ->allowEmptyString('validated_by_user_id');

        $validator
            ->dateTime('validation_timestamp')
            ->allowEmptyDateTime('validation_timestamp');

        $validator
            ->scalar('validation_status')
            ->maxLength('validation_status', 20)
            ->allowEmptyString('validation_status')
            ->inList('validation_status', ['pending', 'approved', 'rejected', ''], 'Invalid validation status.', true);


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
        // $rules->add($rules->existsIn(['item_id'], 'Items')); // Cannot do this for polymorphic key easily
        $rules->add($rules->existsIn(['warehouse_id'], 'Warehouses'));
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['allowMissing' => true]); // Allow if user_id is nullable
        $rules->add($rules->existsIn(['company_id'], 'Companies'), ['allowMissing' => true]); // Allow if company_id is nullable
        // $rules->add($rules->existsIn(['related_document_id'], 'RelatedDocuments')); // Polymorphic
        $rules->add($rules->existsIn(['validated_by_user_id'], 'ValidatedByUsers', ['allowMissing' => true])); // Allow if nullable

        // Custom rule to check item_id based on item_type could be added here if needed
        // $rules->add(function ($entity, $options) {
        //     $tableLocator = \Cake\ORM\TableRegistry::getTableLocator();
        //     if ($entity->item_type === 'Product') {
        //         return $tableLocator->get('Products')->exists(['id' => $entity->item_id]);
        //     }
        //     if ($entity->item_type === 'Pack') {
        //         return $tableLocator->get('Packs')->exists(['id' => $entity->item_id]);
        //     }
        //     return false; // Or true if other item_types are allowed without this check
        // }, 'itemExistsInCorrectTable', [
        //     'errorField' => 'item_id',
        //     'message' => 'The specified item does not exist in the corresponding table.'
        // ]);

        return $rules;
    }
}
