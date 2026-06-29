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
        'warehouse_id' => true,
        'quantity' => true,
        'created' => true,
        'modified' => true,
        'statut' => true,
        'company_id' => true,
        // 'product' and 'pack' are not direct fields to be mass-assigned here.
        // They are loaded via associations or virtual properties.
        'warehouse' => true, // Assuming this is a loaded association
        'company' => true,   // Assuming this is a loaded association
        'whuserproducts' => true, // Assuming this is a loaded association
    ];

    // Optional: Add virtual properties or methods to get the specific item (Pack or Product)
    // protected function _getPack()
    // {
    //     if ($this->item_type === 'Pack' && $this->item_id) {
    //         $packsTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Packs');
    //         return $packsTable->findById($this->item_id)->first();
    //     }
    //     return null;
    // }

    // protected function _getProduct()
    // {
    //     if ($this->item_type === 'Product' && $this->item_id) {
    //         $productsTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Products');
    //         return $productsTable->findById($this->item_id)->first();
    //     }
    //     return null;
    // }
}
