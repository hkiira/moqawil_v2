<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Turnover Entity
 *
 * @property int $id
 * @property string|null $title
 * @property float|null $commission
 * @property int|null $statut
 *
 * @property \App\Model\Entity\Orderpack[] $orderpacks
 * @property \App\Model\Entity\Pack[] $packs
 */
class Turnover extends Entity
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
        'title' => true,
        'commission' => true,
        'statut' => true,
        'orderpacks' => true,
        'packs' => true,
    ];
}
