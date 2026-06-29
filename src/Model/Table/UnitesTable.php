<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Unites Model
 *
 * @property &\Cake\ORM\Association\BelongsTo $Companies
 *
 * @method \App\Model\Entity\Unite get($primaryKey, $options = [])
 * @method \App\Model\Entity\Unite newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Unite[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Unite|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Unite saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Unite patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Unite[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Unite findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UnitesTable extends Table
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

        $this->setTable('unites');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
        ]);
        
        $this->belongsTo('Parentunites', [
            'className' => 'Unites',
            'foreignKey' => 'unite_id',
            'joinType' => 'INNER',
        ]);
        
        $this->hasMany('Subunites', [
            'className' => 'Unites',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
        
        $this->hasMany('Packunites', [
            'foreignKey' => 'unite_id',
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
            ->maxLength('title', 250)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');
        
        $validator
            ->scalar('abrev')
            ->maxLength('abrev', 11)
            ->requirePresence('abrev', 'create')
            ->notEmptyString('abrev');

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
