<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PaymentSplit Entity
 *
 * @property int $id
 * @property int $payment_id
 * @property int $payment_method_id
 * @property float $amount
 * @property \Cake\I18n\FrozenDate|null $cheque_date
 * @property int $statut
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Payment $payment
 * @property \App\Model\Entity\PaymentMethod $payment_method
 */
class PaymentSplit extends Entity
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
        'payment_id' => true,
        'payment_method_id' => true,
        'amount' => true,
        'cheque_date' => true,
        'statut' => true,
        'created' => true,
        'modified' => true,
        'payment' => true,
        'payment_method' => true,
    ];
} 