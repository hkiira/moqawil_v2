<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Orderpacks Model
 *
 * @property \App\Model\Table\OrdersTable&\Cake\ORM\Association\BelongsTo $Orders
 * @property \App\Model\Table\PacksTable&\Cake\ORM\Association\BelongsTo $Packs
 * @property \App\Model\Table\WhnaturesTable&\Cake\ORM\Association\BelongsTo $Whnatures
 * @property \App\Model\Table\TranchesTable&\Cake\ORM\Association\BelongsTo $Tranches
 * @property \App\Model\Table\TurnoversTable&\Cake\ORM\Association\BelongsTo $turnovers
 * @property \App\Model\Table\TarifsTable&\Cake\ORM\Association\BelongsTo $Tarifs
 * @property \App\Model\Table\CommissionsTable&\Cake\ORM\Association\BelongsTo $Commissions
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\LoyaltyorderpacksTable&\Cake\ORM\Association\HasMany $Loyaltyorderpacks
 * @property \App\Model\Table\OrderpackproductsTable&\Cake\ORM\Association\HasMany $Orderpackproducts
 *
 * @method \App\Model\Entity\Orderpack get($primaryKey, $options = [])
 * @method \App\Model\Entity\Orderpack newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Orderpack[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Orderpack|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Orderpack saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Orderpack patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Orderpack[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Orderpack findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class OrderpacksTable extends Table
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

        $this->setTable('orderpacks');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Orders', [
            'foreignKey' => 'order_id',
        ]);
        $this->belongsTo('Packs', [
            'foreignKey' => 'pack_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Whnatures', [
            'foreignKey' => 'whnature_id',
        ]);
        $this->belongsTo('Turnovers', [
            'foreignKey' => 'turnover_id',
        ]);
        $this->belongsTo('Tranches', [
            'foreignKey' => 'tranche_id',
        ]);
        $this->belongsTo('Tarifs', [
            'foreignKey' => 'tarif_id',
        ]);
        $this->belongsTo('Loyaltypointgifts', [
            'foreignKey' => 'loyaltypointgift_id',
        ]);
        $this->belongsTo('Commissions', [
            'foreignKey' => 'commission_id',
        ]);
        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Loyaltyorderpacks', [
            'foreignKey' => 'orderpack_id',
        ]);
        $this->hasMany('Orderpackproducts', [
            'foreignKey' => 'orderpack_id',
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
            ->scalar('justification')
            ->maxLength('justification', 225)
            ->allowEmptyString('justification');

        $validator
            ->integer('quantity')
            ->requirePresence('quantity', 'create')
            ->notEmptyString('quantity');

        $validator
            ->numeric('price')
            ->requirePresence('price', 'create')
            ->notEmptyString('price');

        $validator
            ->numeric('commissionpack')
            ->allowEmptyString('commissionpack');

        $validator
            ->integer('statut')
            ->allowEmptyString('statut');

        $validator
            ->numeric('loyaltypoints')
            ->allowEmptyString('loyaltypoints');

        $validator
            ->integer('loyalityvalidation')
            ->allowEmptyString('loyalityvalidation');

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
        $rules->add($rules->existsIn(['order_id'], 'Orders'));
        $rules->add($rules->existsIn(['pack_id'], 'Packs'));
        $rules->add($rules->existsIn(['whnature_id'], 'Whnatures'));
        $rules->add($rules->existsIn(['tranche_id'], 'Tranches'));
        $rules->add($rules->existsIn(['tarif_id'], 'Tarifs'));
        $rules->add($rules->existsIn(['loyaltypointgift_id'], 'Loyaltypointgifts'));
        $rules->add($rules->existsIn(['commission_id'], 'Commissions'));
        $rules->add($rules->existsIn(['company_id'], 'Companies'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }
}
