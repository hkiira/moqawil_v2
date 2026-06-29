<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Packtaxes Model
 *
 * @property &\Cake\ORM\Association\HasMany $Packs
 *
 * @method \App\Model\Entity\Packtax get($primaryKey, $options = [])
 * @method \App\Model\Entity\Packtax newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Packtax[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Packtax|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Packtax saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Packtax patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Packtax[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Packtax findOrCreate($search, callable $callback = null, $options = [])
 */
class PacktaxesTable extends Table
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

        $this->setTable('packtaxes');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->hasMany('Packs', [
            'foreignKey' => 'packtax_id',
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
            ->numeric('valeur')
            ->notEmptyString('valeur');

        $validator
            ->integer('statut')
            ->allowEmptyString('statut');

        return $validator;
    }
}
