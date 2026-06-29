<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Adress Entity
 *
 * @property int $id
 * @property string $title
 * @property int $city_id
 * @property string|null $controleur
 * @property int|null $objectid
 * @property int|null $statut
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\City $city
 */
class Adress extends Entity
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
        'city_id' => true,
        'controleur' => true,
        'objectid' => true,
        'statut' => true,
        'created' => true,
        'modified' => true,
        'city' => true,
    ];
}
