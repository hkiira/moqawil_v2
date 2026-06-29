<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Loyaltypoint Entity
 *
 * @property int $id
 * @property string $code
 * @property int $order_id
 * @property int|null $statut
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $company_id
 * @property int $customer_id
 *
 * @property \App\Model\Entity\Order $order
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Loyaltyorderpack[] $loyaltyorderpacks
 */
class Loyaltypointgift extends Entity
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
        'order_id' => true,
        'statut' => true,
        'created' => true,
        'modified' => true,
        'company_id' => true,
        'customer_id' => true,
        'order' => true,
        'company' => true,
        'orderpacks' => true,
        'loyaltyorderpacks' => true,
    ];
}
