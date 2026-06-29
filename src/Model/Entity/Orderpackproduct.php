<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Orderpackproduct Entity
 *
 * @property int $id
 * @property int $orderpack_id
 * @property int $product_id
 * @property int $quantity
 * @property float $buyingprice
 * @property int|null $statut
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int $company_id
 * @property int $user_id
 *
 * @property \App\Model\Entity\Orderpack $orderpack
 * @property \App\Model\Entity\Product $product
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\User $user
 */
class Orderpackproduct extends Entity
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
        'orderpack_id' => true,
        'product_id' => true,
        'quantity' => true,
        'buyingprice' => true,
        'statut' => true,
        'created' => true,
        'modified' => true,
        'company_id' => true,
        'user_id' => true,
        'orderpack' => true,
        'product' => true,
        'company' => true,
        'user' => true,
    ];
}
