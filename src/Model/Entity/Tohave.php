<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Tohave Entity
 *
 * @property int $id
 * @property string $code
 * @property int $company_id
 * @property int $user_id
 * @property int $tohavetype_id
 * @property int $pofsale_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int $statut
 * @property int|null $shipping_id
 * @property int $customer_id
 *
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Tohavetype $tohavetype
 * @property \App\Model\Entity\Pofsale $pofsale
 * @property \App\Model\Entity\Shipping $shipping
 * @property \App\Model\Entity\Customer $customer
 * @property \App\Model\Entity\Orderpack[] $orderpacks
 */
class Tohave extends Entity
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
        'company_id' => true,
        'user_id' => true,
        'tohavetype_id' => true,
        'pofsale_id' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'shipping_id' => true,
        'customer_id' => true,
        'company' => true,
        'user' => true,
        'tohavetype' => true,
        'pofsale' => true,
        'shipping' => true,
        'customer' => true,
        'orderpacks' => true,
    ];
}
