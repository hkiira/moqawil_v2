<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Tohaves Model
 *
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\TohavetypesTable&\Cake\ORM\Association\BelongsTo $Tohavetypes
 * @property \App\Model\Table\PofsalesTable&\Cake\ORM\Association\BelongsTo $Pofsales
 * @property \App\Model\Table\ShippingsTable&\Cake\ORM\Association\BelongsTo $Shippings
 * @property \App\Model\Table\CustomersTable&\Cake\ORM\Association\BelongsTo $Customers
 * @property \App\Model\Table\OrderpacksTable&\Cake\ORM\Association\HasMany $Orderpacks
 *
 * @method \App\Model\Entity\Tohave get($primaryKey, $options = [])
 * @method \App\Model\Entity\Tohave newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Tohave[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Tohave|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tohave saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tohave patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Tohave[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Tohave findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TohavesTable extends Table
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

        $this->setTable('tohaves');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Tohavetypes', [
            'foreignKey' => 'tohavetype_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Pofsales', [
            'foreignKey' => 'pofsale_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Shippings', [
            'foreignKey' => 'shipping_id',
        ]);
        $this->belongsTo('Customers', [
            'foreignKey' => 'customer_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Orderpacks', [
            'foreignKey' => 'tohave_id',
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
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['tohavetype_id'], 'Tohavetypes'));
        $rules->add($rules->existsIn(['pofsale_id'], 'Pofsales'));
        $rules->add($rules->existsIn(['shipping_id'], 'Shippings'));
        $rules->add($rules->existsIn(['customer_id'], 'Customers'));

        return $rules;
    }
}
