<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Shipping Entity
 *
 * @property int $id
 * @property string $code
 * @property int $customer_id
 * @property int $user_id
 * @property int|null $billing_id
 * @property int|null $slip_id
 * @property int|null $exitslip_id
 * @property int|null $report_id
 * @property int $warehouse_id
 * @property string|null $comment
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int|null $statut
 * @property int $company_id
 *
 * @property \App\Model\Entity\Customer $customer
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Billing $billing
 * @property \App\Model\Entity\Slip $slip
 * @property \App\Model\Entity\Exitslip $exitslip
 * @property \App\Model\Entity\Report $report
 * @property \App\Model\Entity\Warehouse $warehouse
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Order[] $orders
 */
class Shipping extends Entity
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
        'user_id' => true,
        'billing_id' => true,
        'slip_id' => true,
        'exitslip_id' => true,
        'report_id' => true,
        'warehouse_id' => true,
        'comment' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'company_id' => true,
        'customer' => true,
        'user' => true,
        'billing' => true,
        'slip' => true,
        'exitslip' => true,
        'report' => true,
        'warehouse' => true,
        'company' => true,
        'orders' => true,
    ];
}
