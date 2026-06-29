<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Access Entity
 *
 * @property int $id
 * @property int $controlleuraction_id
 * @property int $company_id
 * @property int|null $statut
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Controlleuraction $controlleuraction
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Accesrole[] $accesroles
 * @property \App\Model\Entity\Accesuser[] $accesusers
 */
class Access extends Entity
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
        'controlleuraction_id' => true,
        'company_id' => true,
        'statut' => true,
        'created' => true,
        'modified' => true,
        'controlleuraction' => true,
        'company' => true,
        'accesroles' => true,
        'accesusers' => true,
    ];
}
