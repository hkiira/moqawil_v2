<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Pricetypes Model
 *
 * @property &\Cake\ORM\Association\HasMany $Orderpacks
 * @property &\Cake\ORM\Association\HasMany $Packs
 *
 * @method \App\Model\Entity\Pricetype get($primaryKey, $options = [])
 * @method \App\Model\Entity\Pricetype newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Pricetype[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Pricetype|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Pricetype saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Pricetype patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Pricetype[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Pricetype findOrCreate($search, callable $callback = null, $options = [])
 */
class PricetypesTable extends Table
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

        $this->setTable('pricetypes');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->hasMany('Orderpacks', [
            'foreignKey' => 'pricetype_id',
        ]);
        $this->hasMany('Packs', [
            'foreignKey' => 'pricetype_id',
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

        return $validator;
    }
}
