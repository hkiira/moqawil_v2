<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Supplierorder Entity
 *
 * @property int $id
 * @property string $code
 * @property int $supplier_id
 * @property int $warehouse_id
 * @property int $user_id
 * @property int|null $statut
 * @property int $company_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Supplier $supplier
 * @property \App\Model\Entity\Warehouse $warehouse
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Receipt[] $receipts
 * @property \App\Model\Entity\Supporderproduct[] $supporderproducts
 */
class Supplierorder extends Entity
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
        'warehouse_id' => true,
        'user_id' => true,
        'statut' => true,
        'company_id' => true,
        'created' => true,
        'modified' => true,
        'supplier' => true,
        'warehouse' => true,
        'user' => true,
        'company' => true,
        'receipts' => true,
        'supporderproducts' => true,
    ];
}
