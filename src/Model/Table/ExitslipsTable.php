<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Exitslips Model
 *
 * @property \App\Model\Table\ExitsliptypesTable&\Cake\ORM\Association\BelongsTo $Exitsliptypes
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\WarehousesTable&\Cake\ORM\Association\BelongsTo $Warehouses
 * @property \App\Model\Table\ExsusersTable&\Cake\ORM\Association\HasMany $Exsusers
 * @property &\Cake\ORM\Association\HasMany $Inventories
 * @property \App\Model\Table\ShippingsTable&\Cake\ORM\Association\HasMany $Shippings
 * @property \App\Model\Table\SlipsTable&\Cake\ORM\Association\HasMany $Slips
 *
 * @method \App\Model\Entity\Exitslip get($primaryKey, $options = [])
 * @method \App\Model\Entity\Exitslip newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Exitslip[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Exitslip|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Exitslip saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Exitslip patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Exitslip[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Exitslip findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ExitslipsTable extends Table
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

        $this->setTable('exitslips');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Exitsliptypes', [
            'foreignKey' => 'exitsliptype_id',
        ]);
        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
        ]);
        $this->belongsTo('Warehouses', [
            'foreignKey' => 'warehouse_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Exsusers', [
            'foreignKey' => 'exitslip_id',
        ]);
        $this->hasMany('Inventories', [
            'foreignKey' => 'exitslip_id',
        ]);
        $this->hasMany('Shippings', [
            'foreignKey' => 'exitslip_id',
        ]);
        $this->hasMany('Slips', [
            'foreignKey' => 'exitslip_id',
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
            ->maxLength('code', 20)
            ->allowEmptyString('code');

        $validator
            ->scalar('livreur')
            ->maxLength('livreur', 255)
            ->allowEmptyString('livreur');

        $validator
            ->integer('validate')
            ->allowEmptyString('validate');

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
        $rules->add($rules->existsIn(['exitsliptype_id'], 'Exitsliptypes'));
        $rules->add($rules->existsIn(['company_id'], 'Companies'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['warehouse_id'], 'Warehouses'));

        return $rules;
    }
}
