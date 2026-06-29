<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Zone Entity
 *
 * @property int $id
 * @property string $code
 * @property string $title
 * @property int|null $zone_id
 * @property int $city_id
 * @property int $warehouse_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $statut
 * @property int $company_id
 *
 * @property \App\Model\Entity\Zone[] $zones
 * @property \App\Model\Entity\City $city
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Customer[] $customers
 * @property \App\Model\Entity\Zoneuser[] $zoneusers
 * @property \App\Model\Entity\Zone[] $subzones
 * @property \App\Model\Entity\Zone $parentzone
 */
class Zone extends Entity
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
        'zone_id' => true,
        'city_id' => true,
        'warehouse_id' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'company_id' => true,
        'zones' => true,
        'city' => true,
        'company' => true,
        'customers' => true,
        'zoneusers' => true,
        'subzones' => true,
        'parentzone' => true,
    ];
}
