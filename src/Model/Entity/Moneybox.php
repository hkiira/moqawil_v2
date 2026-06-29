<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Moneybox Entity
 *
 * @property int $id
 * @property string $code
 * @property int $warehouse_id
 * @property int|null $historypayement_id
 * @property float $received
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $company_id
 * @property int $user_id
 * @property int $validate
 * @property int $statut
 *
 * @property \App\Model\Entity\Warehouse $warehouse
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\User $user
 */
class Moneybox extends Entity
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
        'code' => true,
        'warehouse_id' => true,
        'historypayement_id' => true,
        'received' => true,
        'created' => true,
        'modified' => true,
        'company_id' => true,
        'user_id' => true,
        'validate' => true,
        'statut' => true,
        'warehouse' => true,
        'company' => true,
        'user' => true,
    ];
}
