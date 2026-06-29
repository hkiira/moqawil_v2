<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \App\Model\Table\RolesTable&\Cake\ORM\Association\BelongsTo $Roles
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 * @property \App\Model\Table\AccesusersTable&\Cake\ORM\Association\HasMany $Accesusers
 * @property &\Cake\ORM\Association\HasMany $Orderpackproducts
 * @property &\Cake\ORM\Association\HasMany $Orderpacks
 * @property &\Cake\ORM\Association\HasMany $Orders
 * @property &\Cake\ORM\Association\HasMany $Receipts
 * @property &\Cake\ORM\Association\HasMany $Shippings
 * @property &\Cake\ORM\Association\HasMany $Slipproducts
 * @property &\Cake\ORM\Association\HasMany $Slips
 * @property &\Cake\ORM\Association\HasMany $Supplierorders
 * @property &\Cake\ORM\Association\HasMany $Supporderproducts
 * @property &\Cake\ORM\Association\HasMany $Whuserproducts
 * @property &\Cake\ORM\Association\HasMany $Whusers
 * @property &\Cake\ORM\Association\HasMany $Zoneusers
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
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

        $this->setTable('users');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
        ]);
        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Categoryusers', [
            'foreignKey' => 'categoryuser_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Accesusers', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Orderpackproducts', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Orderpacks', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Orders', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Exitslips', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Receipts', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Pofsusers', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Shippings', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Inventories', [
            'foreignKey' => 'warehouse_id',
        ]);
        $this->hasMany('Slipproducts', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Slips', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Supplierorders', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Supporderproducts', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Whuserproducts', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Whusers', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Zoneusers', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Reports', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Moneyboxs', [
            'foreignKey' => 'user_id',
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
            ->scalar('firstname')
            ->maxLength('firstname', 255)
            ->requirePresence('firstname', 'create')
            ->notEmptyString('firstname');
        
        $validator
            ->scalar('code')
            ->maxLength('code', 255)
            ->requirePresence('code', 'create')
            ->notEmptyString('code');

        $validator
            ->scalar('lastname')
            ->maxLength('lastname', 255)
            ->requirePresence('lastname', 'create')
            ->notEmptyString('lastname');

        $validator
            ->scalar('grpassword')
            ->maxLength('grpassword', 255)
            ->notEmptyString('grpassword');

        $validator
            ->scalar('cin')
            ->maxLength('cin', 255)
            ->allowEmptyString('cin');

        $validator
            ->scalar('referral')
            ->maxLength('referral', 255)
            ->allowEmptyString('referral');

        $validator
            ->date('birthday')
            ->allowEmptyDate('birthday');

        $validator
            ->scalar('username')
            ->maxLength('username', 255)
            ->requirePresence('username', 'create')
            ->notEmptyString('username')
            ->add('username', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->email('email')
            ->allowEmptyString('email');

        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->requirePresence('password', 'create')
            ->notEmptyString('password');

        $validator
            ->integer('statut')
            ->allowEmptyString('statut');
        
        $validator
            ->integer('app')
            ->allowEmptyString('app');

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
        $rules->add($rules->isUnique(['username']));
        $rules->add($rules->existsIn(['role_id'], 'Roles'));
        $rules->add($rules->existsIn(['company_id'], 'Companies'));
        $rules->add($rules->existsIn(['categoryuser_id'], 'Categoryusers'));

        return $rules;
    }
}
