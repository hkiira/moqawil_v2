<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Company Entity
 *
 * @property int $id
 * @property string $name
 * @property string|null $adresse
 * @property int $tva
 * @property string|null $city
 * @property string|null $identifiantfiscale
 * @property string|null $patente
 * @property string|null $rc
 * @property string|null $cnss
 * @property string|null $ice
 * @property string|null $phone
 * @property string|null $mail
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $statut
 * @property string $code
 *
 * @property \App\Model\Entity\Accesrole[] $accesroles
 * @property \App\Model\Entity\Access[] $accesses
 * @property \App\Model\Entity\Accesuser[] $accesusers
 * @property \App\Model\Entity\Category[] $categories
 * @property \App\Model\Entity\Customertype[] $customertypes
 * @property \App\Model\Entity\Packproduct[] $packproducts
 * @property \App\Model\Entity\Pack[] $packs
 * @property \App\Model\Entity\Packunite[] $packunites
 * @property \App\Model\Entity\Photo[] $photos
 * @property \App\Model\Entity\Price[] $prices
 * @property \App\Model\Entity\Product[] $products
 * @property \App\Model\Entity\Tranch[] $tranches
 * @property \App\Model\Entity\Unite[] $unites
 * @property \App\Model\Entity\User[] $users
 * @property \App\Model\Entity\Warehouse[] $warehouses
 * @property \App\Model\Entity\Whnature[] $whnatures
 * @property \App\Model\Entity\Whproduct[] $whproducts
 * @property \App\Model\Entity\Whtype[] $whtypes
 * @property \App\Model\Entity\Whuserproduct[] $whuserproducts
 * @property \App\Model\Entity\Companycode[] $companycodes
 */
class Company extends Entity
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
        'name' => true,
        'adresse' => true,
        'tva' => true,
        'city' => true,
        'identifiantfiscale' => true,
        'patente' => true,
        'rc' => true,
        'cnss' => true,
        'ice' => true,
        'phone' => true,
        'mail' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'code' => true,
        'accesroles' => true,
        'accesses' => true,
        'accesusers' => true,
        'categories' => true,
        'customertypes' => true,
        'packproducts' => true,
        'packs' => true,
        'packunites' => true,
        'photos' => true,
        'prices' => true,
        'products' => true,
        'tranches' => true,
        'unites' => true,
        'users' => true,
        'warehouses' => true,
        'whnatures' => true,
        'whproducts' => true,
        'whtypes' => true,
        'whuserproducts' => true,
        'companycodes' => true,
    ];
}
