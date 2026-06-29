<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Inventory Entity
 *
 * @property int $id
 * @property string $code
 * @property int $user_id
 * @property int $warehouse_id
 * @property int $whnature_id
 * @property int|null $exitslip_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $company_id
 * @property int|null $statut
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Warehouse $warehouse
 * @property \App\Model\Entity\Whnature $whnature
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Invproduct[] $invproducts
 */
class Inventory extends Entity
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
        'warehouse_id' => true,
        'whnature_id' => true,
        'exitslip_id' => true,
        'created' => true,
        'modified' => true,
        'company_id' => true,
        'statut' => true,
        'user' => true,
        'warehouse' => true,
        'whnature' => true,
        'company' => true,
        'invproducts' => true,
    ];
}
