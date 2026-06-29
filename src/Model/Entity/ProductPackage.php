<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ProductPackage Entity
 *
 * @property int $id
 * @property float $weight
 * @property string $unit
 * @property bool $is_default
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $statut
 * @property int $company_id
 *
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Product[] $products
 */
class ProductPackage extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'weight' => true,
        'unit' => true,
        'is_default' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'company_id' => true,
        'company' => true,
        'products' => true,
        '_joinData' => true
    ];
} 