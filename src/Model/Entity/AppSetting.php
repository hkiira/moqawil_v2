<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AppSetting Entity
 *
 * @property int $id
 * @property string $key_name
 * @property string $key_value
 * @property string $description
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class AppSetting extends Entity
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
        'key_name' => true,
        'key_value' => true,
        'description' => true,
        'created' => true,
        'modified' => true,
    ];
}
