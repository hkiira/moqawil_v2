<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Accesrole Entity
 *
 * @property int $id
 * @property int $access_id
 * @property int $role_id
 * @property int $company_id
 * @property int|null $authorised
 * @property int|null $hisown
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $statut
 *
 * @property \App\Model\Entity\Access $access
 * @property \App\Model\Entity\Role $role
 * @property \App\Model\Entity\Company $company
 */
class Accesrole extends Entity
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
        'access_id' => true,
        'role_id' => true,
        'company_id' => true,
        'authorised' => true,
        'hisown' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'access' => true,
        'role' => true,
        'company' => true,
    ];
}
