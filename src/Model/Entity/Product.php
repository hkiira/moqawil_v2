<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Product Entity
 *
 * @property int $id
 * @property string $reference
 * @property string $title
 * @property float $buyingprice
 * @property float $sellingprice
 * @property float|null $commission
 * @property int $category_id
 * @property int $unite_id
 * @property int|null $editted
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $statut
 * @property int $company_id
 *
 * @property \App\Model\Entity\Category $category
 * @property \App\Model\Entity\Unite $unite
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Packproduct[] $packproducts
 * @property \App\Model\Entity\Whproduct[] $whproducts
 * @property \App\Model\Entity\ProductPackage[] $product_packages
 */
class Product extends Entity
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
        'reference' => true,
        'title' => true,
        'buyingprice' => true,
        'sellingprice' => true,
        'commission' => true,
        'category_id' => true,
        'measurement_unit_id' => true,
        'editted' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'supplier_id' => true,
        'company_id' => true,
        'category' => true,
        'company' => true,
        'measurement_unit' => true,
        'supplier' => true,
        'packproducts' => true,
        'whproducts' => true,
        'productunites' => true,
    ];
}
