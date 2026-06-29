<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Packunites Model
 *
 * @property \App\Model\Table\PacksTable&\Cake\ORM\Association\BelongsTo $Packs
 * @property \App\Model\Table\UnitesTable&\Cake\ORM\Association\BelongsTo $Unites
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 *
 * @method \App\Model\Entity\Packunite get($primaryKey, $options = [])
 * @method \App\Model\Entity\Packunite newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Packunite[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Packunite|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Packunite saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Packunite patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Packunite[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Packunite findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PackunitesTable extends Table
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

        $this->setTable('packunites');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Packs', [
            'foreignKey' => 'pack_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Unites', [
            'foreignKey' => 'unite_id',
            'joinType' => 'INNER',
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
            ->requirePresence('quantity', 'create')
            ->notEmptyString('quantity');

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
        $rules->add($rules->existsIn(['pack_id'], 'Packs'));
        $rules->add($rules->existsIn(['unite_id'], 'Unites'));
        $rules->add($rules->existsIn(['company_id'], 'Companies'));

        return $rules;
    }
}
