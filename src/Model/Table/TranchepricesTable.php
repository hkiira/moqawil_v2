<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Trancheprices Model
 *
 * @property \App\Model\Table\PricesTable&\Cake\ORM\Association\BelongsTo $Prices
 * @property \App\Model\Table\TranchesTable&\Cake\ORM\Association\BelongsTo $Tranches
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 *
 * @method \App\Model\Entity\Trancheprice get($primaryKey, $options = [])
 * @method \App\Model\Entity\Trancheprice newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Trancheprice[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Trancheprice|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Trancheprice saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Trancheprice patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Trancheprice[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Trancheprice findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TranchepricesTable extends Table
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

        $this->setTable('trancheprices');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Prices', [
            'foreignKey' => 'price_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Tranches', [
            'foreignKey' => 'tranche_id',
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
        $rules->add($rules->existsIn(['price_id'], 'Prices'));
        $rules->add($rules->existsIn(['tranche_id'], 'Tranches'));
        $rules->add($rules->existsIn(['company_id'], 'Companies'));

        return $rules;
    }
}
