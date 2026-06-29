<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Slipproduct Entity
 *
 * @property int $id
 * @property int $pack_id
 * @property int $quantity
 * @property float $price
 * @property int $slip_id
 * @property int|null $whnature_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $user_id
 * @property int|null $uservalidate
 * @property int|null $statut
 * @property int $company_id
 *
 * @property \App\Model\Entity\Pack $pack
 * @property \App\Model\Entity\Slip $slip
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Company $company
 */
class Slipproduct extends Entity
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
        'item_id' => true,
        'item_type' => true,
        'quantity' => true,
        'price' => true,
        'slip_id' => true,
        'unity_id' => true,
        'whnature_id' => true,
        'created' => true,
        'modified' => true,
        'user_id' => true,
        'uservalidate' => true,
        'statut' => true,
        'company_id' => true,
        'product' => true,
        'pack' => true,
        'slip' => true,
        'user' => true,
        'company' => true,
    ];
}
