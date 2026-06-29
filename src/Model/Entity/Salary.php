<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Payment Entity
 *
 * @property int $id
 * @property string $code
 * @property int $user_id
 * @property int $payment_method_id
 * @property float $amount
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $statut
 * @property \Cake\I18n\FrozenDate|null $datedepart
 * @property \Cake\I18n\FrozenDate|null $datefin
 * @property \Cake\I18n\FrozenDate|null $cheque_date
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\PaymentMethod $payment_method
 * @property \App\Model\Entity\Paymentgoal[] $paymentgoals
 * @property \App\Model\Entity\Shipping[] $shippings
 */
class Salary extends Entity
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
        'user_id' => true,
        'payment_method_id' => true,
        'amount' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'datedepart' => true,
        'datefin' => true,
        'cheque_date' => true,
        'user' => true,
        'payment_method' => true,
        'paymentgoals' => true,
        'shippings' => true,
    ];
}
