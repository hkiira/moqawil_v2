<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Pofsbrand Entity
 *
 * @property int $id
 * @property string $code
 * @property string $title
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $statut
 * @property int $company_id
 *
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Pofsmodele[] $pofsmodeles
 */
class Pofsbrand extends Entity
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
        'created' => true,
        'modified' => true,
        'statut' => true,
        'company_id' => true,
        'company' => true,
        'pofsmodeles' => true,
    ];
}
