<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Accesses Model
 *
 * @property \App\Model\Table\ControlleuractionsTable&\Cake\ORM\Association\BelongsTo $Controlleuractions
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 * @property \App\Model\Table\AccesrolesTable&\Cake\ORM\Association\HasMany $Accesroles
 * @property \App\Model\Table\AccesusersTable&\Cake\ORM\Association\HasMany $Accesusers
 *
 * @method \App\Model\Entity\Access get($primaryKey, $options = [])
 * @method \App\Model\Entity\Access newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Access[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Access|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Access saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Access patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Access[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Access findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AccessesTable extends Table
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

        $this->setTable('accesses');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Controlleuractions', [
            'foreignKey' => 'controlleuraction_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Accesroles', [
            'foreignKey' => 'access_id',
        ]);
        $this->hasMany('Accesusers', [
            'foreignKey' => 'access_id',
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
        $rules->add($rules->existsIn(['controlleuraction_id'], 'Controlleuractions'));
        $rules->add($rules->existsIn(['company_id'], 'Companies'));

        return $rules;
    }
}
