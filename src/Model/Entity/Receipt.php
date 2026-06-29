<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Receipt Entity
 *
 * @property int $id
 * @property string $code
 * @property int $supplier_id
 * @property int $user_id
 * @property int|null $supplierorder_id
 * @property int $company_id
 * @property int $warehouse_id
 * @property int|null $statut
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Supplier $supplier
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Supplierorder $supplierorder
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Supporderproduct[] $supporderproducts
 */
class Receipt extends Entity
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
        'supplier_id' => true,
        'user_id' => true,
        'supplierorder_id' => true,
        'company_id' => true,
        'warehouse_id' => true,
        'statut' => true,
        'created' => true,
        'modified' => true,
        'supplier' => true,
        'user' => true,
        'supplierorder' => true,
        'company' => true,
        'supporderproducts' => true,
    ];
}
