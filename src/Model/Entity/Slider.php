<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Slider Entity
 *
 * @property int $id
 * @property string $title
 * @property int|null $category_id
 * @property int|null $brand_id
 * @property int|null $statut
 * @property int|null $company_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Category $category
 * @property \App\Model\Entity\Brand $brand
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Slide[] $slides
 */
class Slider extends Entity
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
        'title' => true,
        'category_id' => true,
        'brand_id' => true,
        'statut' => true,
        'company_id' => true,
        'created' => true,
        'modified' => true,
        'category' => true,
        'brand' => true,
        'company' => true,
        'slides' => true,
    ];
}
