<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Goals Model
 *
 * @property \App\Model\Table\GoaltypesTable&\Cake\ORM\Association\BelongsTo $Goaltypes
 * @property &\Cake\ORM\Association\HasMany $Paymentgoals
 *
 * @method \App\Model\Entity\Goal get($primaryKey, $options = [])
 * @method \App\Model\Entity\Goal newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Goal[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Goal|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Goal saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Goal patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Goal[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Goal findOrCreate($search, callable $callback = null, $options = [])
 */
class GoalsTable extends Table
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

        $this->setTable('goals');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('Goaltypes', [
            'foreignKey' => 'goaltype_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Paymentgoals', [
            'foreignKey' => 'goal_id',
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
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->numeric('min')
            ->allowEmptyString('min');

        $validator
            ->numeric('max')
            ->allowEmptyString('max');

        $validator
            ->numeric('montant')
            ->notEmptyString('montant');

        $validator
            ->integer('perdays')
            ->requirePresence('perdays', 'create')
            ->notEmptyString('perdays');

        $validator
            ->integer('permounts')
            ->requirePresence('permounts', 'create')
            ->notEmptyString('permounts');

        $validator
            ->integer('statut')
            ->requirePresence('statut', 'create')
            ->notEmptyString('statut');

        $validator
            ->numeric('goal')
            ->notEmptyString('goal');

        $validator
            ->numeric('reward')
            ->notEmptyString('reward');

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
        $rules->add($rules->existsIn(['goaltype_id'], 'Goaltypes'));

        return $rules;
    }
}
