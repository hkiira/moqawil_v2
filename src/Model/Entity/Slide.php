<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Slide Entity
 *
 * @property int $id
 * @property int $slider_id
 * @property string $title
 * @property string $photo
 * @property string $dir
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $statut
 * @property int|null $company_id
 *
 * @property \App\Model\Entity\Slider $slider
 * @property \App\Model\Entity\Company $company
 */
class Slide extends Entity
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
        'slider_id' => true,
        'title' => true,
        'photo' => true,
        'dir' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'company_id' => true,
        'slider' => true,
        'company' => true,
    ];
}
