<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Supporderproduct Entity
 *
 * @property int $id
 * @property int $supplierorder_id
 * @property int $product_id
 * @property int $quantity
 * @property float $price
 * @property int|null $statut
 * @property int|null $receipt_id
 * @property int $user_id
 * @property int $supplier_id
 * @property int $company_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Supplierorder $supplierorder
 * @property \App\Model\Entity\Product $product
 * @property \App\Model\Entity\Receipt $receipt
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Supplier $supplier
 * @property \App\Model\Entity\Company $company
 */
class Supporderproduct extends Entity
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
        'supplierorder_id' => true,
        'product_id' => true,
        'quantity' => true,
        'price' => true,
        'statut' => true,
        'receipt_id' => true,
        'productunite_id' => true,
        'user_id' => true,
        'supplier_id' => true,
        'company_id' => true,
        'created' => true,
        'modified' => true,
        'supplierorder' => true,
        'productunite' => true,
        'product' => true,
        'receipt' => true,
        'user' => true,
        'supplier' => true,
        'company' => true,
    ];
}
