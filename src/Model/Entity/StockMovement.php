<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * StockMovement Entity
 *
 * @property int $id
 * @property int $item_id
 * @property string $item_type
 * @property int $warehouse_id
 * @property float $quantity_change
 * @property float $balance_after_movement
 * @property string $movement_type
 * @property int|null $user_id
 * @property int|null $company_id
 * @property int|null $related_document_id
 * @property string|null $related_document_type
 * @property string|null $notes
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $validated_by_user_id
 * @property \Cake\I18n\FrozenTime|null $validation_timestamp
 * @property string|null $validation_status
 *
 * @property \App\Model\Entity\Warehouse $warehouse
 * @property \App\Model\Entity\User $user 
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\User $validated_by_user (Points to Users table, aliased as ValidatedByUsers in Table class)
 *
 * // Virtual properties for the polymorphic item (Pack or Product) can be added here if needed:
 * @property \App\Model\Entity\Product|\App\Model\Entity\Pack|null $associated_item
 */
class StockMovement extends Entity
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
        'quantity_change' => true,
        'balance_after_movement' => true,
        'movement_type' => true,
        'user_id' => true,
        'company_id' => true,
        'related_document_id' => true,
        'related_document_type' => true,
        'notes' => true,
        'created' => true,
        'modified' => true,
        'validated_by_user_id' => true,
        'validation_timestamp' => true,
        'validation_status' => true,
        // 'item' is not a direct field/association to be mass-assigned.
        'warehouse' => true, // For loaded association
        'user' => true,      // For loaded association (user_id)
        'company' => true,   // For loaded association
        // 'related_document' is not a direct field/association.
        'validated_by_user' => true, // For loaded association (validated_by_user_id)
    ];

    // Example of a virtual property to get the associated item (Product or Pack)
    // protected function _getAssociatedItem()
    // {
    //     if (!empty($this->item_type) && !empty($this->item_id)) {
    //         $tableAlias = ($this->item_type === 'Product') ? 'Products' : (($this->item_type === 'Pack') ? 'Packs' : null);
    //         if ($tableAlias) {
    //             $table = \Cake\ORM\TableRegistry::getTableLocator()->get($tableAlias);
    //             return $table->findById($this->item_id)->first();
    //         }
    //     }
    //     return null;
    // }
}
