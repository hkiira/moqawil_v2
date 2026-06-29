<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Accesuser Entity
 *
 * @property int $id
 * @property int $access_id
 * @property int $user_id
 * @property int $company_id
 * @property int|null $authorised
 * @property int $hisown
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $statut
 *
 * @property \App\Model\Entity\Access $access
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Company $company
 */
class Accesuser extends Entity
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
        'user_id' => true,
        'company_id' => true,
        'authorised' => true,
        'hisown' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'access' => true,
        'user' => true,
        'company' => true,
    ];
}
