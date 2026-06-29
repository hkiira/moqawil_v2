<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Controlleuractions Model
 *
 * @property \App\Model\Table\ActionsTable&\Cake\ORM\Association\BelongsTo $Actions
 * @property \App\Model\Table\ControlleursTable&\Cake\ORM\Association\BelongsTo $Controlleurs
 * @property \App\Model\Table\AccessesTable&\Cake\ORM\Association\HasMany $Accesses
 *
 * @method \App\Model\Entity\Controlleuraction get($primaryKey, $options = [])
 * @method \App\Model\Entity\Controlleuraction newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Controlleuraction[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Controlleuraction|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Controlleuraction saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Controlleuraction patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Controlleuraction[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Controlleuraction findOrCreate($search, callable $callback = null, $options = [])
 */
class ControlleuractionsTable extends Table
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

        $this->setTable('controlleuractions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Actions', [
            'foreignKey' => 'action_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Controlleurs', [
            'foreignKey' => 'controlleur_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Accesses', [
            'foreignKey' => 'controlleuraction_id',
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
            ->scalar('description')
            ->maxLength('description', 255)
            ->allowEmptyString('description');

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
        $rules->add($rules->existsIn(['action_id'], 'Actions'));
        $rules->add($rules->existsIn(['controlleur_id'], 'Controlleurs'));

        return $rules;
    }
}
