<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Photo Entity
 *
 * @property int $id
 * @property string $title
 * @property string|null $photo
 * @property string|null $dir
 * @property string|null $controleur
 * @property int|null $objectid
 * @property int|null $statut
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $company_id
 *
 * @property \App\Model\Entity\Company $company
 */
class Photo extends Entity
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
        'photo' => true,
        'dir' => true,
        'controleur' => true,
        'objectid' => true,
        'statut' => true,
        'created' => true,
        'modified' => true,
        'company_id' => true,
        'company' => true,
    ];
}
