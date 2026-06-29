<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Controlleur Entity
 *
 * @property int $id
 * @property string $title
 * @property string $name
 * @property int $display
 * @property int|null $statut
 *
 * @property \App\Model\Entity\Access[] $accesses
 * @property \App\Model\Entity\Controlleuraction[] $controlleuractions
 */
class Controlleur extends Entity
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
        'name' => true,
        'display' => true,
        'statut' => true,
        'accesses' => true,
        'controlleuractions' => true,
    ];
}
