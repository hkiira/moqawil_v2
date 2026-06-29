<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Billingpack Entity
 *
 * @property int $id
 * @property int|null $billing_id
 * @property int $pack_id
 * @property int $quantity
 * @property float $price
 * @property float|null $commission
 * @property int|null $statut
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int $company_id
 * @property int $user_id
 *
 * @property \App\Model\Entity\Billing $billing
 * @property \App\Model\Entity\Pack $pack
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\User $user
 */
class Billingpack extends Entity
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
        'billing_id' => true,
        'pack_id' => true,
        'quantity' => true,
        'price' => true,
        'commission' => true,
        'statut' => true,
        'created' => true,
        'modified' => true,
        'company_id' => true,
        'user_id' => true,
        'billing' => true,
        'pack' => true,
        'company' => true,
        'user' => true,
    ];
}
