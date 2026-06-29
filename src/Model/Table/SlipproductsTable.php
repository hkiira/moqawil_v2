<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Slipproducts Model
 *
 * @property \App\Model\Table\PacksTable&\Cake\ORM\Association\BelongsTo $Packs
 * @property \App\Model\Table\SlipsTable&\Cake\ORM\Association\BelongsTo $Slips
 * @property &\Cake\ORM\Association\BelongsTo $Whnatures
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 *
 * @method \App\Model\Entity\Slipproduct get($primaryKey, $options = [])
 * @method \App\Model\Entity\Slipproduct newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Slipproduct[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Slipproduct|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Slipproduct saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Slipproduct patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Slipproduct[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Slipproduct findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SlipproductsTable extends Table
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

        $this->setTable('slipproducts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Products', [
            'foreignKey' => 'item_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Packs', [
            'foreignKey' => 'item_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Productunites', [
            'foreignKey' => 'unity_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Packunites', [
            'foreignKey' => 'unity_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Slips', [
            'foreignKey' => 'slip_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Whnatures', [
            'foreignKey' => 'whnature_id',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
        ]);
        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
            'joinType' => 'INNER',
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
            ->integer('quantity')
            ->notEmptyString('quantity');

        $validator
            ->numeric('price')
            ->notEmptyString('price');

        $validator
            ->integer('uservalidate')
            ->allowEmptyString('uservalidate');

        $validator
            ->integer('statut')
            ->allowEmptyString('statut');
        
        $validator
            ->scalar('item_type')
            ->maxLength('item_type', 50) // Adjust length as needed
            ->requirePresence('item_type', 'create')
            ->notEmptyString('item_type')
            ->inList('item_type', ['Pack', 'Product'], 'Invalid item type. Must be Pack or Product.');

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
        $rules->add($rules->existsIn(['slip_id'], 'Slips'));
        $rules->add($rules->existsIn(['whnature_id'], 'Whnatures'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['company_id'], 'Companies'));

        return $rules;
    }
}
