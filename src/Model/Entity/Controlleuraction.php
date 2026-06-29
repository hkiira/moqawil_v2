<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Controlleuraction Entity
 *
 * @property int $id
 * @property int $action_id
 * @property int $controlleur_id
 * @property string|null $description
 * @property int|null $statut
 *
 * @property \App\Model\Entity\Action $action
 * @property \App\Model\Entity\Controlleur $controlleur
 * @property \App\Model\Entity\Access[] $accesses
 */
class Controlleuraction extends Entity
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
        'action_id' => true,
        'controlleur_id' => true,
        'description' => true,
        'statut' => true,
        'action' => true,
        'controlleur' => true,
        'accesses' => true,
    ];
}
