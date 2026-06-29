<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Historypayement Entity
 *
 * @property int $id
 * @property string $code
 * @property int $user_id
 * @property int|null $company_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int $validate
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Moneybox[] $moneyboxs
 * @property \App\Model\Entity\Report[] $reports
 */
class Historypayement extends Entity
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
        'company_id' => true,
        'created' => true,
        'modified' => true,
        'validate' => true,
        'user' => true,
        'company' => true,
        'moneyboxs' => true,
        'reports' => true,
    ];
}
