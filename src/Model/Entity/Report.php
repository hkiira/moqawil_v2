<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Report Entity
 *
 * @property int $id
 * @property string|null $code
 * @property int|null $company_id
 * @property int|null $user_id
 * @property int|null $sellerid
 * @property int $warehouse_id
 * @property int|null $historypayement_id
 * @property int|null $validate
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $statut
 *
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Warehouse $warehouse
 * @property \App\Model\Entity\Charge[] $charges
 * @property \App\Model\Entity\Order[] $orders
 * @property \App\Model\Entity\Shipping[] $shippings
 * @property \App\Model\Entity\Slip[] $slips
 */
class Report extends Entity
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
        'sellerid' => true,
        'warehouse_id' => true,
        'historypayement_id' => true,
        'validate' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'company' => true,
        'user' => true,
        'warehouse' => true,
        'charges' => true,
        'orders' => true,
        'shippings' => true,
        'slips' => true,
    ];
}
