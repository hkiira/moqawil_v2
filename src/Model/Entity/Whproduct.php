<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Whproduct Entity
 *
 * @property int $id
 * @property int $item_id // ID of the Pack or Product
 * @property string $item_type // 'Pack' or 'Product'
 * @property int $warehouse_id
 * @property int $quantity
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $statut
 * @property int $company_id
 *
 * @property \App\Model\Entity\Warehouse $warehouse
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Whuserproduct[] $whuserproducts
 * 
 * @property \App\Model\Entity\Pack $pack (Virtual, if item_type is 'Pack')
 * @property \App\Model\Entity\Product $product (Virtual, if item_type is 'Product')
 */
class Whproduct extends Entity
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
        'pack_id' => true,
        'product_id' => true,
        'warehouse_id' => true,
        'quantity' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'company_id' => true,
        'warehouse' => true,
        'company' => true,
        'whuserproducts' => true,
    ];

    protected function _getPackId()
    {
        if ($this->item_type === 'Pack') {
            return $this->item_id;
        }
        return isset($this->_properties['pack_id']) ? $this->_properties['pack_id'] : null;
    }

    protected function _setPackId($value)
    {
        if ($value !== null) {
            $this->set('item_id', $value);
            $this->set('item_type', 'Pack');
        }
        return $value;
    }

    protected function _getProductId()
    {
        if ($this->item_type === 'Product') {
            return $this->item_id;
        }
        return isset($this->_properties['product_id']) ? $this->_properties['product_id'] : null;
    }

    protected function _setProductId($value)
    {
        if ($value !== null) {
            $this->set('item_id', $value);
            $this->set('item_type', 'Product');
        }
        return $value;
    }
}
