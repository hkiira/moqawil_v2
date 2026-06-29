<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Controlleurs Model
 *
 * @property \App\Model\Table\ControlleuractionsTable&\Cake\ORM\Association\HasMany $Controlleuractions
 *
 * @method \App\Model\Entity\Controlleur get($primaryKey, $options = [])
 * @method \App\Model\Entity\Controlleur newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Controlleur[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Controlleur|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Controlleur saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Controlleur patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Controlleur[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Controlleur findOrCreate($search, callable $callback = null, $options = [])
 */
class ControlleursTable extends Table
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

        $this->setTable('controlleurs');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Controlleuractions', [
            'foreignKey' => 'controlleur_id',
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
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->integer('display')
            ->requirePresence('display', 'create')
            ->notEmptyString('display');

        $validator
            ->integer('statut')
            ->allowEmptyString('statut');

        return $validator;
    }
}
