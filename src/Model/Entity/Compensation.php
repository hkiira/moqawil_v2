<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Compensation Entity
 *
 * @property int $id
 * @property string $code
 * @property int $user_id
 * @property int|null $commission_tier_id
 * @property float|null $total_quantity
 * @property float|null $commission_amount
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $statut
 * @property \Cake\I18n\FrozenDate|null $datedepart
 * @property \Cake\I18n\FrozenDate|null $datefin
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\CommissionTier $commission_tier
 * @property \App\Model\Entity\Order[] $orders
 */
class Compensation extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'code' => true,
        'user_id' => true,
        'commission_tier_id' => true,
        'total_quantity' => true,
        'commission_amount' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'datedepart' => true,
        'datefin' => true,
        'user' => true,
        'commission_tier' => true,
        'orders' => true,
    ];
}
