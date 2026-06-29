<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Paymentgoal Entity
 *
 * @property int $id
 * @property int $goal_id
 * @property int $payment_id
 * @property float $amount
 * @property int $statut
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Goal $goal
 * @property \App\Model\Entity\Payment $payment
 */
class Salarygoal extends Entity
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
        'goal_id' => true,
        'salary_id' => true,
        'amount' => true,
        'statut' => true,
        'created' => true,
        'modified' => true,
        'goal' => true,
        'salary' => true,
    ];
}
