<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Visite Entity
 *
 * @property int $id
 * @property int $customer_id
 * @property string $latittude
 * @property string $longitude
 * @property int $company_id
 * @property int|null $order_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $statut
 *
 * @property \App\Model\Entity\Customer $customer
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Order $order
 */
class Visite extends Entity
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
        'customer_id' => true,
        'latittude' => true,
        'longitude' => true,
        'company_id' => true,
        'user_id' => true,
        'order_id' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'customer' => true,
        'company' => true,
        'user' => true,
        'order' => true,
    ];
}
