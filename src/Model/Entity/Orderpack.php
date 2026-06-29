<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Orderpack Entity
 *
 * @property int $id
 * @property int|null $order_id
 * @property int $pack_id
 * @property int|null $whnature_id
 * @property string|null $justification
 * @property int $quantity
 * @property float $price
 * @property int|null $tranche_id
 * @property int|null $tranche_id
 * @property int|null $turnover_id
 * @property int|null $commission_id
 * @property float|null $commissionpack
 * @property int|null $statut
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int $company_id
 * @property int $user_id
 * @property float|null $loyaltypoints
 * @property int|null $loyalityvalidation
 *
 * @property \App\Model\Entity\Order $order
 * @property \App\Model\Entity\Pack $pack
 * @property \App\Model\Entity\Whnature $whnature
 * @property \App\Model\Entity\Tranch $tranch
 * @property \App\Model\Entity\Tarif $tarif
 * @property \App\Model\Entity\Commission $commission
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Loyaltyorderpack[] $loyaltyorderpacks
 * @property \App\Model\Entity\Orderpackproduct[] $orderpackproducts
 */
class Orderpack extends Entity
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
        'order_id' => true,
        'pack_id' => true,
        'whnature_id' => true,
        'justification' => true,
        'quantity' => true,
        'price' => true,
        'tranche_id' => true,
        'turnover_id' => true,
        'tarif_id' => true,
        'loyaltypointgift_id' => true,
        'commission_id' => true,
        'commissionpack' => true,
        'statut' => true,
        'created' => true,
        'modified' => true,
        'company_id' => true,
        'user_id' => true,
        'loyaltypoints' => true,
        'loyalityvalidation' => true,
        'order' => true,
        'pack' => true,
        'whnature' => true,
        'tranch' => true,
        'tarif' => true,
        'commission' => true,
        'loyaltypointgift' => true,
        'company' => true,
        'user' => true,
        'loyaltyorderpacks' => true,
        'orderpackproducts' => true,
    ];
}
