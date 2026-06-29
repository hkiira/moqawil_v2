<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Tarif Entity
 *
 * @property int $id
 * @property string $code
 * @property string $title
 * @property int $tariftype_id
 * @property int $tarifway_id
 * @property int|null $statut
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int $company_id
 * @property float|null $maxprice
 * @property float $minprice
 *
 * @property \App\Model\Entity\Tariftype $tariftype
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Orderpack[] $orderpacks
 * @property \App\Model\Entity\Price[] $prices
 * @property \App\Model\Entity\Tarifcategory[] $tarifcategories
 */
class Tarif extends Entity
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
        'title' => true,
        'tariftype_id' => true,
        'tarifway_id' => true,
        'statut' => true,
        'created' => true,
        'modified' => true,
        'company_id' => true,
        'maxprice' => true,
        'minprice' => true,
        'tariftype' => true,
        'company' => true,
        'orderpacks' => true,
        'prices' => true,
        'tarifcategories' => true,
    ];
}
