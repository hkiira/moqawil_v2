<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Pack Entity
 *
 * @property int $id
 * @property string $code
 * @property string|null $barecode
 * @property string $title
 * @property int|null $pack_id
 * @property int|null $variation_id
 * @property int $gstock
 * @property float $commission
 * @property int|null $statut
 * @property float|null $buyingprice
 * @property int $app
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int $brand_id
 * @property int $packtype_id
 * @property int|null $company_id
 * @property int|null $category_id
 * @property int|null $turnover_id
 * @property int $saletype_id
 * @property int|null $packtax_id
 * @property float|null $loyaltypoints
 *
 * @property \App\Model\Entity\Brand $brand
 * @property \App\Model\Entity\Packtype $packtype
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Category $category
 * @property \App\Model\Entity\Turnover $Turnover
 * @property \App\Model\Entity\Packagingtype $saletypes
 * @property \App\Model\Entity\Packtax $packtax
 * @property \App\Model\Entity\Billingpack[] $billingpacks
 * @property \App\Model\Entity\Invproduct[] $invproducts
 * @property \App\Model\Entity\Orderpack[] $orderpacks
 * @property \App\Model\Entity\Packproduct[] $packproducts
 * @property \App\Model\Entity\Packunite[] $packunites
 * @property \App\Model\Entity\Price[] $prices
 * @property \App\Model\Entity\Slipproduct[] $slipproducts
 * @property \App\Model\Entity\Supporderproduct[] $supporderproducts
 * @property \App\Model\Entity\Tranch[] $tranches
 * @property \App\Model\Entity\Whproduct[] $whproducts
 * @property \App\Model\Entity\Photo $photo
 */
class Pack extends Entity
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
        'barecode' => true,
        'title' => true,
        'pack_id' => true,
        'variation_id' => true,
        'gstock' => true,
        'commission' => true,
        'statut' => true,
        'buyingprice' => true,
        'app' => true,
        'measurement_unit_id' => true,
        'created' => true,
        'modified' => true,
        'brand_id' => true,
        'packtype_id' => true,
        'turnover_id' => true,
        'company_id' => true,
        'category_id' => true,
        'saletype_id' => true,
        'packtax_id' => true,
        'loyaltypoints' => true,
        'brand' => true,
        'packtype' => true,
        'company' => true,
        'category' => true,
        'categoryuserpacks' => true,
        'packagingtype' => true,
        'packtax' => true,
        'billingpacks' => true,
        'invproducts' => true,
        'orderpacks' => true,
        'packproducts' => true,
        'packunites' => true,
        'prices' => true,
        'measurement_quantity' => true,
        'slipproducts' => true,
        'supporderproducts' => true,
        'tranches' => true,
        'whproducts' => true,
        'photo' => true,
        'bonus_amount' => true,
        'bonus_unit_threshold' => true,
    ];
}
