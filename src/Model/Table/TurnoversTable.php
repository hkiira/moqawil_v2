<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Turnovers Model
 *
 * @property \App\Model\Table\OrderpacksTable&\Cake\ORM\Association\HasMany $Orderpacks
 * @property \App\Model\Table\PacksTable&\Cake\ORM\Association\HasMany $Packs
 *
 * @method \App\Model\Entity\Turnover get($primaryKey, $options = [])
 * @method \App\Model\Entity\Turnover newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Turnover[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Turnover|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Turnover saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Turnover patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Turnover[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Turnover findOrCreate($search, callable $callback = null, $options = [])
 */
class TurnoversTable extends Table
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

        $this->setTable('turnovers');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->hasMany('Orderpacks', [
            'foreignKey' => 'turnover_id',
        ]);
        $this->hasMany('Packs', [
            'foreignKey' => 'turnover_id',
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
            ->allowEmptyString('title');

        $validator
            ->numeric('commission')
            ->allowEmptyString('commission');

        $validator
            ->integer('statut')
            ->allowEmptyString('statut');

        return $validator;
    }
}
