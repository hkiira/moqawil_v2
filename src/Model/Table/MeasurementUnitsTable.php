<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MeasurementUnits Model
 *
 * @property &\Cake\ORM\Association\BelongsTo $Companies
 *
 * @method \App\Model\Entity\MeasurementUnit get($primaryKey, $options = [])
 * @method \App\Model\Entity\MeasurementUnit newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MeasurementUnit[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MeasurementUnit|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MeasurementUnit saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MeasurementUnit patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MeasurementUnit[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MeasurementUnit findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MeasurementUnitsTable extends Table
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

        $this->setTable('measurement_units');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
        ]);

        $this->hasMany('Products', [
            'foreignKey' => 'measurement_unit_id',
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
            ->scalar('abbreviation')
            ->maxLength('abbreviation', 11)
            ->requirePresence('abbreviation', 'create')
            ->notEmptyString('abbreviation');

        $validator
            ->scalar('type')
            ->maxLength('type', 50)
            ->requirePresence('type', 'create')
            ->notEmptyString('type')
            ->inList('type', ['volume', 'weight', 'length', 'area', 'other']);

        $validator
            ->numeric('conversion_factor')
            ->allowEmptyString('conversion_factor')
            ->greaterThanOrEqual('conversion_factor', 0);

        $validator
            ->scalar('base_unit')
            ->maxLength('base_unit', 50)
            ->allowEmptyString('base_unit');

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