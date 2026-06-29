<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CommissionTiers Model
 *
 * @property \App\Model\Table\CompaniesTable&\Cake\ORM\Association\BelongsTo $Companies
 * @property \App\Model\Table\PacksTable&\Cake\ORM\Association\BelongsTo $Packs
 * @property \App\Model\Table\PacksTable&\Cake\ORM\Association\BelongsToMany $Packs
 * @property \App\Model\Table\CompensationsTable&\Cake\ORM\Association\HasMany $Compensations
 *
 * @method \App\Model\Entity\CommissionTier get($primaryKey, $options = [])
 * @method \App\Model\Entity\CommissionTier newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CommissionTier[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CommissionTier|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CommissionTier saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CommissionTier patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CommissionTier[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CommissionTier findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CommissionTiersTable extends Table
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

        $this->setTable('commission_tiers');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Companies', [
            'foreignKey' => 'company_id',
        ]);
        $this->belongsTo('Packs', [
            'foreignKey' => 'pack_id',
        ]);
        $this->belongsToMany('Packs', [
            'foreignKey' => 'commission_tier_id',
            'targetForeignKey' => 'pack_id',
            'joinTable' => 'commission_tiers_packs',
        ]);
        
        $this->hasMany('Compensations', [
            'foreignKey' => 'commission_tier_id',
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
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->decimal('min_quantity')
            ->requirePresence('min_quantity', 'create')
            ->notEmptyString('min_quantity')
            ->greaterThanOrEqual('min_quantity', 0, 'Minimum quantity must be greater than or equal to 0');

        $validator
            ->decimal('max_quantity')
            ->allowEmptyString('max_quantity')
            ->add('max_quantity', 'greaterThanMin', [
                'rule' => function($value, $context) {
                    if ($value === null || $value === '') {
                        return true;
                    }
                    return $value > $context['data']['min_quantity'];
                },
                'message' => 'Maximum quantity must be greater than minimum quantity'
            ]);

        $validator
            ->scalar('commission_type')
            ->requirePresence('commission_type', 'create')
            ->notEmptyString('commission_type')
            ->inList('commission_type', ['fixed', 'percentage'], 'Commission type must be either fixed or percentage');

        $validator
            ->decimal('commission_value')
            ->requirePresence('commission_value', 'create')
            ->notEmptyString('commission_value')
            ->greaterThanOrEqual('commission_value', 0, 'Commission value must be greater than or equal to 0');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

        $validator
            ->integer('company_id')
            ->allowEmptyString('company_id');

        $validator
            ->integer('pack_id')
            ->allowEmptyString('pack_id');

        $validator
            ->scalar('apply_type')
            ->requirePresence('apply_type', 'create')
            ->notEmptyString('apply_type')
            ->inList('apply_type', ['all', 'single', 'combined'], 'Apply type must be all, single, or combined');

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

    /**
     * Before save hook to ensure default company assignment
     *
     * @param \Cake\Event\EventInterface $event Event object
     * @param \Cake\Datasource\EntityInterface $entity Entity being saved
     * @param \ArrayObject $options Options
     * @return void
     */
    public function beforeSave($event, $entity, $options)
    {
        if (empty($entity->company_id)) {
            $entity->company_id = 1;
        }
    }

    /**
     * Find active commission tiers
     *
     * @param \Cake\ORM\Query $query Query object
     * @return \Cake\ORM\Query
     */
    public function findActive(Query $query)
    {
        return $query->where(['CommissionTiers.is_active' => true])
            ->order(['CommissionTiers.min_quantity' => 'ASC']);
    }

    /**
     * Find applicable commission tier for given quantity and packs
     *
     * @param float $quantity Pack quantity
     * @param array $packIds Array of pack IDs being processed
     * @param int|null $companyId Company ID (optional)
     * @return array Array of matching tiers
     */
    public function findByQuantityAndPacks($quantity, $packIds = [], $companyId = null)
    {
        $query = $this->find('active')
            ->contain(['Packs']);

        if ($companyId) {
            $query->where([
                'OR' => [
                    'CommissionTiers.company_id IS' => null,
                    'CommissionTiers.company_id' => $companyId
                ]
            ]);
        } else {
            $query->where(['CommissionTiers.company_id IS' => null]);
        }

        $query->where(['CommissionTiers.min_quantity <=' => $quantity])
            ->where([
                'OR' => [
                    'CommissionTiers.max_quantity IS' => null,
                    'CommissionTiers.max_quantity >' => $quantity
                ]
            ]);

        $tiers = $query->all()->toArray();
        
        $matchingTiers = [];
        foreach ($tiers as $tier) {
            if ($tier->apply_type === 'all') {
                // Apply to all packs - no pack restriction
                if (empty($tier->packs)) {
                    $matchingTiers[] = $tier;
                }
            } elseif ($tier->apply_type === 'single' && !empty($tier->packs)) {
                // Single mode: each pack evaluated individually
                $matchingTiers[] = $tier;
            } elseif ($tier->apply_type === 'combined' && !empty($tier->packs)) {
                // Combined mode: all selected packs combined
                $tierPackIds = array_map(function($p) { return $p->id; }, $tier->packs);
                // Check if any of the order packs match the tier packs
                $intersection = array_intersect($tierPackIds, $packIds);
                if (!empty($intersection)) {
                    $matchingTiers[] = $tier;
                }
            }
        }
        
        return $matchingTiers;
    }

    /**
     * Get all active tiers for a company or global tiers
     *
     * @param int|null $companyId Company ID (optional)
     * @return \Cake\ORM\Query
     */
    public function findByCompany($companyId = null)
    {
        $query = $this->find('active');

        if ($companyId) {
            $query->where([
                'OR' => [
                    'CommissionTiers.company_id IS' => null,
                    'CommissionTiers.company_id' => $companyId
                ]
            ]);
        } else {
            $query->where(['CommissionTiers.company_id IS' => null]);
        }

        return $query;
    }

    /**
     * Calculate commission based on quantity and order total
     *
     * @param float $quantity Total pack quantity
     * @param float $orderTotal Total order amount in DH
     * @param int|null $companyId Company ID (optional)
     * @return array ['tier' => CommissionTier, 'amount' => float]
     */
    public function calculateCommission($quantity, $orderTotal = 0, $companyId = null)
    {
        $tier = $this->findByQuantity($quantity, $companyId);

        if (!$tier) {
            return [
                'tier' => null,
                'amount' => 0,
                'type' => null,
                'value' => 0
            ];
        }

        $amount = $tier->calculateCommission($quantity, $orderTotal);

        return [
            'tier' => $tier,
            'amount' => $amount,
            'type' => $tier->commission_type,
            'value' => $tier->commission_value
        ];
    }
}
