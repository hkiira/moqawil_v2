<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Tranch Entity
 *
 * @property int $id
 * @property string $code
 * @property string $title
 * @property int $min
 * @property int $max
 * @property int $remise
 * @property int $remisetype_id
 * @property int $company_id
 * @property int|null $pack_id
 * @property int|null $statut
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Remisetype $remisetype
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Pack $pack
 */
class Tranch extends Entity
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
        'min' => true,
        'max' => true,
        'remise' => true,
        'remisetype_id' => true,
        'company_id' => true,
        'pack_id' => true,
        'statut' => true,
        'created' => true,
        'modified' => true,
        'remisetype' => true,
        'company' => true,
        'pack' => true,
    ];
}
