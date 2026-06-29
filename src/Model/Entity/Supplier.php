<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Supplier Entity
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $identifiantfiscale
 * @property string|null $patente
 * @property string|null $rc
 * @property string|null $cnss
 * @property string|null $ice
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $statut
 * @property int $company_id
 *
 * @property \App\Model\Entity\Phone $phone
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Receipt[] $receipts
 * @property \App\Model\Entity\Supplierorder[] $supplierorders
 * @property \App\Model\Entity\Supporderproduct[] $supporderproducts
 * @property \App\Model\Entity\Adress $adress
 * @property \App\Model\Entity\Photo $photo
 * @property \App\Model\Entity\Product[] $products
 */
class Supplier extends Entity
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
        'name' => true,
        'phone' => true,
        'identifiantfiscale' => true,
        'patente' => true,
        'rc' => true,
        'cnss' => true,
        'ice' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'company_id' => true,
        'company' => true,
        'receipts' => true,
        'supplierorders' => true,
        'supporderproducts' => true,
        'adress' => true,
        'photo' => true,
        'products' => true,
    ];
}
