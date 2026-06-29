<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Tarifs Model
 *
 * @property \App\Model\Table\TariftypesTable&\Cake\ORM\Association\BelongsTo $Tariftypes
 * @property &\Cake\ORM\Association\BelongsTo $Tarifways
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 * @property \App\Model\Table\OrderpacksTable&\Cake\ORM\Association\HasMany $Orderpacks
 * @property \App\Model\Table\PricesTable&\Cake\ORM\Association\HasMany $Prices
 * @property \App\Model\Table\TarifcategoriesTable&\Cake\ORM\Association\HasMany $Tarifcategories
 *
 * @method \App\Model\Entity\Tarif get($primaryKey, $options = [])
 * @method \App\Model\Entity\Tarif newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Tarif[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Tarif|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tarif saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tarif patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Tarif[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Tarif findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TarifsTable extends Table
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

        $this->setTable('tarifs');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Tariftypes', [
            'foreignKey' => 'tariftype_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Tarifways', [
            'foreignKey' => 'tarifway_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Orderpacks', [
            'foreignKey' => 'tarif_id',
        ]);
        $this->hasMany('Prices', [
            'foreignKey' => 'tarif_id',
        ]);
        $this->hasMany('Tarifcategories', [
            'foreignKey' => 'tarif_id',
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
            ->allowEmptyString('statut');

        $validator
            ->numeric('maxprice')
            ->allowEmptyString('maxprice');

        $validator
            ->numeric('minprice')
            ->requirePresence('minprice', 'create')
            ->notEmptyString('minprice');

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
        $rules->add($rules->existsIn(['tariftype_id'], 'Tariftypes'));
        $rules->add($rules->existsIn(['tarifway_id'], 'Tarifways'));
        $rules->add($rules->existsIn(['company_id'], 'Companies'));

        return $rules;
    }
}
