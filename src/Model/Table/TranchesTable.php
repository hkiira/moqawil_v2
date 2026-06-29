<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Tranches Model
 *
 * @property \App\Model\Table\RemisetypesTable&\Cake\ORM\Association\BelongsTo $Remisetypes
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 * @property \App\Model\Table\PacksTable&\Cake\ORM\Association\BelongsTo $Packs
 *
 * @method \App\Model\Entity\Tranch get($primaryKey, $options = [])
 * @method \App\Model\Entity\Tranch newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Tranch[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Tranch|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tranch saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tranch patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Tranch[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Tranch findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TranchesTable extends Table
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

        $this->setTable('tranches');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Remisetypes', [
            'foreignKey' => 'remisetype_id',
        ]);
        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Packs', [
            'foreignKey' => 'pack_id',
        ]);
        $this->hasMany('Trancheprices', [
            'foreignKey' => 'tranche_id',
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
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->integer('min')
            ->requirePresence('min', 'create')
            ->notEmptyString('min');

        $validator
            ->integer('max')
            ->requirePresence('max', 'create')
            ->notEmptyString('max');

        $validator
            ->integer('remise')
            ->notEmptyString('remise');

        $validator
            ->integer('statut')
            ->allowEmptyString('statut');

        $validator
            ->scalar('apply_type')
            ->maxLength('apply_type', 50)
            ->inList('apply_type', ['QUANTITY', 'AMOUNT'])
            ->allowEmptyString('apply_type');

        $validator
            ->scalar('quantity_unit_type')
            ->maxLength('quantity_unit_type', 50)
            ->inList('quantity_unit_type', ['UNITS', 'PACKAGE', 'MEASUREMENT'])
            ->allowEmptyString('quantity_unit_type');

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
        $rules->add($rules->existsIn(['remisetype_id'], 'Remisetypes'));
        $rules->add($rules->existsIn(['company_id'], 'Companies'));
        $rules->add($rules->existsIn(['pack_id'], 'Packs'));

        return $rules;
    }
}
