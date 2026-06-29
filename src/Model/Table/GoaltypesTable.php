<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Goaltypes Model
 *
 * @property \App\Model\Table\GoalsTable&\Cake\ORM\Association\HasMany $Goals
 *
 * @method \App\Model\Entity\Goaltype get($primaryKey, $options = [])
 * @method \App\Model\Entity\Goaltype newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Goaltype[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Goaltype|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Goaltype saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Goaltype patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Goaltype[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Goaltype findOrCreate($search, callable $callback = null, $options = [])
 */
class GoaltypesTable extends Table
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

        $this->setTable('goaltypes');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->hasMany('Goals', [
            'foreignKey' => 'goaltype_id',
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
            ->integer('statut')
            ->allowEmptyString('statut');

        return $validator;
    }
}
