<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Compensations Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\CommissionTiersTable&\Cake\ORM\Association\BelongsTo $CommissionTiers
 * @property \App\Model\Table\OrdersTable&\Cake\ORM\Association\HasMany $Orders
 *
 * @method \App\Model\Entity\Compensation get($primaryKey, $options = [])
 * @method \App\Model\Entity\Compensation newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Compensation[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Compensation|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Compensation saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Compensation patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Compensation[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Compensation findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CompensationsTable extends Table
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

        $this->setTable('compensations');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CommissionTiers', [
            'foreignKey' => 'commission_tier_id',
        ]);
        $this->hasMany('Orders', [
            'foreignKey' => 'compensation_id',
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
            ->requirePresence('statut', 'create')
            ->notEmptyString('statut');

        $validator
            ->date('datedepart')
            ->allowEmptyDate('datedepart');

        $validator
            ->date('datefin')
            ->allowEmptyDate('datefin');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    /**
     * Find compensations by date range
     *
     * @param \Cake\ORM\Query $query Query object
     * @param array $options Options containing dateStart and dateEnd
     * @return \Cake\ORM\Query
     */
    public function findByDateRange(Query $query, array $options)
    {
        if (isset($options['dateStart'])) {
            $query->where(['Compensations.datedepart >=' => $options['dateStart']]);
        }

        if (isset($options['dateEnd'])) {
            $query->where(['Compensations.datefin <=' => $options['dateEnd']]);
        }

        return $query;
    }

    /**
     * Find compensations by user and status
     *
     * @param \Cake\ORM\Query $query Query object
     * @param array $options Options containing userId and/or statut
     * @return \Cake\ORM\Query
     */
    public function findByUserAndStatus(Query $query, array $options)
    {
        if (isset($options['userId'])) {
            $query->where(['Compensations.user_id' => $options['userId']]);
        }

        if (isset($options['statut'])) {
            $query->where(['Compensations.statut' => $options['statut']]);
        }

        return $query;
    }

    /**
     * Find pending compensations
     *
     * @param \Cake\ORM\Query $query Query object
     * @return \Cake\ORM\Query
     */
    public function findPending(Query $query)
    {
        return $query->where(['Compensations.statut' => 0]);
    }

    /**
     * Find paid compensations
     *
     * @param \Cake\ORM\Query $query Query object
     * @return \Cake\ORM\Query
     */
    public function findPaid(Query $query)
    {
        return $query->where(['Compensations.statut' => 2]);
    }

    /**
     * Calculate total quantity from orders
     *
     * @param array $orders Array of orders with orderpacks
     * @param int|null $packId Pack ID to filter by (optional)
     * @return float Total pack quantity
     */
    public function calculateTotalQuantity($orders, $packId = null)
    {
        $totalQuantity = 0;

        foreach ($orders as $order) {
            if (!empty($order->orderpacks)) {
                foreach ($order->orderpacks as $orderpack) {
                    // If packId is specified, only count that specific pack
                    if ($packId && $orderpack->pack_id != $packId) {
                        continue;
                    }
                    $quantity = $orderpack->qte ?? 0;
                    $totalQuantity += $quantity;
                }
            }
        }

        return round($totalQuantity, 2);
    }

    /**
     * Calculate quantity grouped by pack
     *
     * @param array $orders Array of orders with orderpacks
     * @return array Pack quantities grouped by pack_id
     */
    public function calculateQuantityByPack($orders)
    {
        $packQuantities = [];

        foreach ($orders as $order) {
            if (!empty($order->orderpacks)) {
                foreach ($order->orderpacks as $orderpack) {
                    $packId = $orderpack->pack_id;
                    $quantity = $orderpack->qte ?? 0;
                    
                    if (!isset($packQuantities[$packId])) {
                        $packQuantities[$packId] = 0;
                    }
                    $packQuantities[$packId] += $quantity;
                }
            }
        }

        return $packQuantities;
    }

    /**
     * Calculate total order amount
     *
     * @param array $orders Array of orders with orderpacks
     * @return float Total order amount in DH
     */
    public function calculateTotalOrderAmount($orders)
    {
        $total = 0;

        foreach ($orders as $order) {
            if (!empty($order->orderpacks)) {
                foreach ($order->orderpacks as $orderpack) {
                    $price = $orderpack->prix ?? 0;
                    $quantity = $orderpack->qte ?? 0;
                    $total += ($price * $quantity);
                }
            }
        }

        return round($total, 2);
    }

    /**
     * Calculate and save commission for a compensation
     *
     * @param \App\Model\Entity\Compensation $compensation Compensation entity with loaded orders
     * @return \App\Model\Entity\Compensation Updated compensation entity
     */
    public function calculateAndSaveCommission($compensation)
    {
        if (empty($compensation->orders)) {
            return $compensation;
        }

        $this->loadModel('CommissionTiers');
        $companyId = null;
        if (!empty($compensation->orders[0]->company_id)) {
            $companyId = $compensation->orders[0]->company_id;
        }

        // Get pack quantities grouped by pack
        $packQuantities = $this->calculateQuantityByPack($compensation->orders);
        $packIds = array_keys($packQuantities);
        
        // Calculate total order amount
        $orderTotal = $this->calculateTotalOrderAmount($compensation->orders);
        
        $totalCommission = 0;
        $appliedTier = null;
        $totalQuantity = array_sum($packQuantities);
        
        // Find all matching tiers
        $matchingTiers = $this->CommissionTiers->findByQuantityAndPacks($totalQuantity, $packIds, $companyId);
        
        foreach ($matchingTiers as $tier) {
            if ($tier->apply_type === 'all' && empty($tier->packs)) {
                // Global tier - apply once for total quantity
                $commission = $tier->calculateCommission($totalQuantity, $orderTotal);
                $totalCommission += $commission;
                $appliedTier = $tier;
                
            } elseif ($tier->apply_type === 'single' && !empty($tier->packs)) {
                // Single mode - calculate for each pack individually
                $tierPackIds = array_map(function($p) { return $p->id; }, $tier->packs);
                foreach ($tierPackIds as $tierPackId) {
                    if (isset($packQuantities[$tierPackId])) {
                        $packQty = $packQuantities[$tierPackId];
                        if ($packQty >= $tier->min_quantity && ($tier->max_quantity === null || $packQty < $tier->max_quantity)) {
                            $commission = $tier->calculateCommission($packQty, $orderTotal);
                            $totalCommission += $commission;
                            $appliedTier = $tier;
                        }
                    }
                }
                
            } elseif ($tier->apply_type === 'combined' && !empty($tier->packs)) {
                // Combined mode - sum quantities of all matching packs
                $tierPackIds = array_map(function($p) { return $p->id; }, $tier->packs);
                $combinedQty = 0;
                foreach ($tierPackIds as $tierPackId) {
                    if (isset($packQuantities[$tierPackId])) {
                        $combinedQty += $packQuantities[$tierPackId];
                    }
                }
                if ($combinedQty >= $tier->min_quantity && ($tier->max_quantity === null || $combinedQty < $tier->max_quantity)) {
                    $commission = $tier->calculateCommission($combinedQty, $orderTotal);
                    $totalCommission += $commission;
                    $appliedTier = $tier;
                }
            }
        }
        
        $compensation->total_quantity = $totalQuantity;
        $compensation->commission_amount = $totalCommission;
        $compensation->commission_tier_id = $appliedTier ? $appliedTier->id : null;

        return $compensation;
    }
}
