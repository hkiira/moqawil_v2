<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Saletype Entity
 *
 * @property int $id
 * @property string $code
 * @property string $title
 * @property int $company_id
 *
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Pack[] $packs
 */
class Saletype extends Entity
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
        'title' => true,
        'company_id' => true,
        'company' => true,
        'packs' => true,
    ];
}
