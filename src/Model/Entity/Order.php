<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Order Entity
 *
 * @property int $id
 * @property string $code
 * @property int $customer_id
 * @property int|null $shipping_id
 * @property int|null $payment_id
 * @property int|null $ordertype_id
 * @property int|null $report_id
 * @property int|null $slip_id
 * @property int $pofsale_id
 * @property int|null $commission_id
 * @property int $user_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $statut
 * @property int $company_id
 *
 * @property \App\Model\Entity\Customer $customer
 * @property \App\Model\Entity\Shipping $shipping
 * @property \App\Model\Entity\Payment $payment
 * @property \App\Model\Entity\Ordertype $ordertype
 * @property \App\Model\Entity\Report $report
 * @property \App\Model\Entity\Slip $slip
 * @property \App\Model\Entity\Pofsale $pofsale
 * @property \App\Model\Entity\Commission $commission
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Orderpack[] $orderpacks
 */
class Order extends Entity
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
        'customer_id' => true,
        'shipping_id' => true,
        'ordertype_id' => true,
        'report_id' => true,
        'slip_id' => true,
        'compensation_id' => true,
        'pofsale_id' => true,
        'commission_id' => true,
        'user_id' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'company_id' => true,
        'customer' => true,
        'shipping' => true,
        'ordertype' => true,
        'compensation' => true,
        'loyaltypoints' => true,
        'loyaltypointgifts' => true,
        'report' => true,
        'slip' => true,
        'pofsale' => true,
        'commission' => true,
        'user' => true,
        'company' => true,
        'orderpacks' => true,
        'order_payments' => true,
        'photo' => true,
    ];
}
