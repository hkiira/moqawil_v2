<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Goal Entity
 *
 * @property int $id
 * @property string $title
 * @property int $goaltype_id
 * @property float|null $min
 * @property float|null $max
 * @property float $montant
 * @property int $perdays
 * @property int $permounts
 * @property int $statut
 * @property float $goal
 * @property float $reward
 *
 * @property \App\Model\Entity\Goaltype $goaltype
 */
class Goal extends Entity
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
        'goaltype_id' => true,
        'min' => true,
        'max' => true,
        'montant' => true,
        'perdays' => true,
        'permounts' => true,
        'statut' => true,
        'goal' => true,
        'reward' => true,
        'goaltype' => true,
    ];
}
