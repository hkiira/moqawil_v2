<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Exsuser Entity
 *
 * @property int $id
 * @property int $exitslip_id
 * @property int $user_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $company_id
 *
 * @property \App\Model\Entity\Exitslip $exitslip
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Company $company
 */
class Categoryuserpack extends Entity
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
        'categoryuser_id' => true,
        'created' => true,
        'modified' => true,
        'company_id' => true,
        'categoryuser' => true,
        'pack' => true,
        'company' => true,
    ];
}
