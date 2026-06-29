<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Commission Entity
 *
 * @property int $id
 * @property string|null $code
 * @property int|null $company_id
 * @property float|null $salary
 * @property float $taux
 * @property int|null $user_id
 * @property int|null $warehouse_id
 * @property int|null $validate
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $statut
 *
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Orderpack[] $orderpacks
 * @property \App\Model\Entity\Order[] $orders
 * @property \App\Model\Entity\Slip[] $slips
 */
class Commission extends Entity
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
        'salary' => true,
        'taux' => true,
        'user_id' => true,
        'warehouse_id' => true,
        'validate' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'company' => true,
        'user' => true,
        'orderpacks' => true,
        'orders' => true,
        'slips' => true,
    ];
}
