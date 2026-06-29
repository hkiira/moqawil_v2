<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Loyaltyorderpack Entity
 *
 * @property int $id
 * @property int $loyaltypoint_id
 * @property int $orderpack_id
 * @property float|null $points
 * @property float|null $valeur
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int $user_id
 * @property int|null $company_id
 *
 * @property \App\Model\Entity\Loyaltypoint $loyaltypoint
 * @property \App\Model\Entity\Orderpack $orderpack
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Company $company
 */
class Loyaltyorderpack extends Entity
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
        'loyaltypoint_id' => true,
        'orderpack_id' => true,
        'points' => true,
        'valeur' => true,
        'created' => true,
        'modified' => true,
        'user_id' => true,
        'company_id' => true,
        'loyaltypoint' => true,
        'orderpack' => true,
        'user' => true,
        'company' => true,
    ];
}
