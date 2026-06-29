<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Tarifcategory Entity
 *
 * @property int $id
 * @property int $tarif_id
 * @property int $category_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $statut
 * @property int|null $company_id
 *
 * @property \App\Model\Entity\Tarif $tarif
 * @property \App\Model\Entity\Category $category
 * @property \App\Model\Entity\Company $company
 */
class Tarifcategory extends Entity
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
        'tarif_id' => true,
        'category_id' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'company_id' => true,
        'tarif' => true,
        'category' => true,
        'company' => true,
    ];
}
