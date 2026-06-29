<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Packunite Entity
 *
 * @property int $id
 * @property int $pack_id
 * @property int $unite_id
 * @property int $quantity
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $statut
 * @property int $company_id
 *
 * @property \App\Model\Entity\Pack $pack
 * @property \App\Model\Entity\Unite $unite
 * @property \App\Model\Entity\Company $company
 */
class Packunite extends Entity
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
        'pack_id' => true,
        'unite_id' => true,
        'quantity' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'company_id' => true,
        'pack' => true,
        'unite' => true,
        'company' => true,
    ];
}
