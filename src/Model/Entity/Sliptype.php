<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Sliptype Entity
 *
 * @property int $id
 * @property string $title
 * @property int|null $statut
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Slip[] $slips
 */
class Sliptype extends Entity
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
        'statut' => true,
        'created' => true,
        'modified' => true,
        'slips' => true,
    ];
}
