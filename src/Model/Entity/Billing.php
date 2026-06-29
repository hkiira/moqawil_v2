<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Billing Entity
 *
 * @property int $id
 * @property string $code
 * @property int $user_id
 * @property int $customer_id
 * @property int $billingtype_id
 * @property int $warehouse_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int|null $statut
 * @property int $company_id
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Customer $customer
 * @property \App\Model\Entity\Billingtype $billingtype
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Billingpack[] $billingpacks
 * @property \App\Model\Entity\Shipping[] $shippings
 */
class Billing extends Entity
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
        'customer_id' => true,
        'billingtype_id' => true,
        'warehouse_id' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'company_id' => true,
        'user' => true,
        'customer' => true,
        'billingtype' => true,
        'company' => true,
        'billingpacks' => true,
        'shippings' => true,
    ];
}
