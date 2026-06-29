<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Loyaltyorderpacks Model
 *
 * @property \App\Model\Table\LoyaltypointsTable&\Cake\ORM\Association\BelongsTo $Loyaltypoints
 * @property \App\Model\Table\OrderpacksTable&\Cake\ORM\Association\BelongsTo $Orderpacks
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 *
 * @method \App\Model\Entity\Loyaltyorderpack get($primaryKey, $options = [])
 * @method \App\Model\Entity\Loyaltyorderpack newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Loyaltyorderpack[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Loyaltyorderpack|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Loyaltyorderpack saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Loyaltyorderpack patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Loyaltyorderpack[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Loyaltyorderpack findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LoyaltyorderpacksTable extends Table
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

        $this->setTable('loyaltyorderpacks');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Loyaltypoints', [
            'foreignKey' => 'loyaltypoint_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Orderpacks', [
            'foreignKey' => 'orderpack_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
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
            ->numeric('points')
            ->allowEmptyString('points');

        $validator
            ->numeric('valeur')
            ->allowEmptyString('valeur');

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
        $rules->add($rules->existsIn(['loyaltypoint_id'], 'Loyaltypoints'));
        $rules->add($rules->existsIn(['orderpack_id'], 'Orderpacks'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['company_id'], 'Companies'));

        return $rules;
    }
}
