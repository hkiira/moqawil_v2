<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Price Entity
 *
 * @property int $id
 * @property float $price
 * @property float|null $minp
 * @property float|null $maxp
 * @property int $editted
 * @property int $pack_id
 * @property int|null $tarif_id
 * @property int $customertype_id
 * @property int|null $warehouse_id
 * @property int $company_id
 * @property int|null $statut
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Pack $pack
 * @property \App\Model\Entity\Tarif $tarif
 * @property \App\Model\Entity\Customertype $customertype
 * @property \App\Model\Entity\Warehouse $warehouse
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Trancheprice[] $trancheprices
 */
class Price extends Entity
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
        'price' => true,
        'minp' => true,
        'maxp' => true,
        'editted' => true,
        'pack_id' => true,
        'tarif_id' => true,
        'customertype_id' => true,
        'warehouse_id' => true,
        'company_id' => true,
        'statut' => true,
        'created' => true,
        'modified' => true,
        'pack' => true,
        'tarif' => true,
        'customertype' => true,
        'warehouse' => true,
        'company' => true,
        'trancheprices' => true,
    ];
}
