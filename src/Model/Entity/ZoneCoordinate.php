<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ZoneCoordinate Entity
 *
 * @property int $id
 * @property int $zone_id
 * @property float $latitude
 * @property float $longitude
 * @property int $sequence_order
 *
 * @property \App\Model\Entity\Zone $zone
 */
class ZoneCoordinate extends Entity
{
    protected $_accessible = [
        'zone_id' => true,
        'latitude' => true,
        'longitude' => true,
        'sequence_order' => true,
        'zone' => true,
    ];
}
