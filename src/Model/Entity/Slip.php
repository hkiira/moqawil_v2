<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Slip Entity
 *
 * @property int $id
 * @property string|null $code
 * @property string|null $raison
 * @property int|null $statut
 * @property int|null $commission_id
 * @property int $warehouse_id
 * @property int|null $warehoused
 * @property int|null $whnature_id
 * @property int|null $report_id
 * @property int|null $whnatured
 * @property int $user_id
 * @property int|null $uservalidate
 * @property int $sliptype_id
 * @property int $company_id
 * @property int|null $exitslip_id
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \Cake\I18n\FrozenTime|null $created
 *
 * @property \App\Model\Entity\Warehouse $warehouse
 * @property \App\Model\Entity\Whnature $whnature
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Sliptype $sliptype
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Exitslip $exitslip
 * @property \App\Model\Entity\Report $report
 * @property \App\Model\Entity\Order[] $orders
 * @property \App\Model\Entity\Shipping[] $shippings
 * @property \App\Model\Entity\Slipproduct[] $slipproducts
 */
class Slip extends Entity
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
        'raison' => true,
        'statut' => true,
        'commission_id' => true,
        'warehouse_id' => true,
        'warehoused' => true,
        'whnature_id' => true,
        'report_id' => true,
        'whnatured' => true,
        'user_id' => true,
        'uservalidate' => true,
        'sliptype_id' => true,
        'company_id' => true,
        'exitslip_id' => true,
        'modified' => true,
        'created' => true,
        'warehouse' => true,
        'whnature' => true,
        'user' => true,
        'sliptype' => true,
        'company' => true,
        'exitslip' => true,
        'report' => true,
        'orders' => true,
        'shippings' => true,
        'slipproducts' => true,
    ];
}
