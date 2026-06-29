<?php
namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * Customer Entity
 *
 * @property int $id
 * @property string|null $code
 * @property string $name
 * @property string|null $phone
 * @property string|null $adresse
 * @property int $zone_id
 * @property int $customertype_id
 * @property string|null $ice
 * @property string|null $latitude
 * @property string|null $longitude
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $statut
 * @property int $company_id
 *
 * @property \App\Model\Entity\Zone $zone
 * @property \App\Model\Entity\Customertype $customertype
 * @property \App\Model\Entity\Company $company
 */
class Customer extends Entity
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
        'name' => true,
        'phone' => true,
        'adresse' => true,
        'zone_id' => true,
        'customertype_id' => true,
        'ice' => true,
        'password' => true,
        'latitude' => true,
        'longitude' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'referral' => true,
        'referred' => true,
        'company_id' => true,
        'zone' => true,
        'customertype' => true,
        'company' => true,
        'photo' => true,
        'wallet_balance' => true,
    ];

    protected $_hidden = [
        'password',
    ];
}
