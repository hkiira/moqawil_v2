<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Whnatures Model
 *
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 * @property \App\Model\Table\InventoriesTable&\Cake\ORM\Association\HasMany $Inventories
 * @property \App\Model\Table\OrderpacksTable&\Cake\ORM\Association\HasMany $Orderpacks
 * @property \App\Model\Table\SlipsTable&\Cake\ORM\Association\HasMany $Slips
 * @property \App\Model\Table\WarehousesTable&\Cake\ORM\Association\HasMany $Warehouses
 *
 * @method \App\Model\Entity\Whnature get($primaryKey, $options = [])
 * @method \App\Model\Entity\Whnature newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Whnature[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Whnature|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Whnature saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Whnature patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Whnature[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Whnature findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class WhnaturesTable extends Table
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

        $this->setTable('whnatures');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Inventories', [
            'foreignKey' => 'whnature_id',
        ]);
        $this->hasMany('Orderpacks', [
            'foreignKey' => 'whnature_id',
        ]);
        $this->hasMany('Slips', [
            'foreignKey' => 'whnature_id',
        ]);
        $this->hasMany('Warehouses', [
            'foreignKey' => 'whnature_id',
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
            ->integer('statut')
            ->notEmptyString('statut');

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
