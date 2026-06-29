<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Pofsuser Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $pofsale_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $statut
 * @property int $company_id
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Pofsale $pofsale
 * @property \App\Model\Entity\Company $company
 */
class Pofsuser extends Entity
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
        'user_id' => true,
        'pofsale_id' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'company_id' => true,
        'user' => true,
        'pofsale' => true,
        'company' => true,
    ];
}
