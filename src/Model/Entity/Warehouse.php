<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Warehouse Entity
 *
 * @property int $id
 * @property string $code
 * @property string $title
 * @property int $whnature_id
 * @property int $whtype_id
 * @property int $company_id
 * @property int|null $warehouse_id
 * @property int|null $statut
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Whnature $whnature
 * @property \App\Model\Entity\Whtype $whtype
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Pofsale[] $pofsales
 * @property \App\Model\Entity\Warehouse[] $subwarehouses
 * @property \App\Model\Entity\Warehouse $parentwarehouse
 * @property \App\Model\Entity\Whproduct[] $whproducts
 * @property \App\Model\Entity\Whuser[] $whusers
 * @property \App\Model\Entity\Adress $adress
 * @property \App\Model\Entity\Whuserproduct[] $whuserproducts
 * @property \App\Model\Entity\Slip[] $slips
 */
class Warehouse extends Entity
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
        'whnature_id' => true,
        'whtype_id' => true,
        'company_id' => true,
        'warehouse_id' => true,
        'statut' => true,
        'created' => true,
        'modified' => true,
        'whnature' => true,
        'whtype' => true,
        'company' => true,
        'pofsales' => true,
        'subwarehouses' => true,
        'parentwarehouse' => true,
        'whproducts' => true,
        'whusers' => true,
        'adress' => true,
        'whuserproducts' => true,
        'slips' => true,
        'inventories' => true,
    ];
}
