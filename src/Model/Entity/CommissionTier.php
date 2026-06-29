<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CommissionTier Entity
 *
 * @property int $id
 * @property string $name
 * @property float $min_quantity
 * @property float|null $max_quantity
 * @property string $commission_type
 * @property float $commission_value
 * @property bool $is_active
 * @property int|null $company_id
 * @property int|null $pack_id
 * @property string $apply_type
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Company $company
 * @property \App\Model\Entity\Pack $pack
 * @property \App\Model\Entity\Pack[] $packs
 * @property \App\Model\Entity\Compensation[] $compensations
 */
class CommissionTier extends Entity
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
        'name' => true,
        'min_quantity' => true,
        'max_quantity' => true,
        'commission_type' => true,
        'commission_value' => true,
        'is_active' => true,
        'company_id' => true,
        'pack_id' => true,
        'apply_type' => true,
        'created' => true,
        'modified' => true,
        'company' => true,
        'pack' => true,
        'packs' => true,
        'compensations' => true,
    ];

    /**
     * Virtual field to get formatted quantity range
     *
     * @return string
     */
    protected function _getQuantityRange()
    {
        $min = number_format($this->min_quantity, 0);
        $max = $this->max_quantity ? number_format($this->max_quantity, 0) : '∞';
        return "{$min} - {$max} packs";
    }

    /**
     * Virtual field to get formatted commission
     *
     * @return string
     */
    protected function _getFormattedCommission()
    {
        if ($this->commission_type === 'percentage') {
            return number_format($this->commission_value, 2) . '%';
        }
        return number_format($this->commission_value, 2) . ' DH';
    }

    /**
     * Check if a given quantity falls within this tier's range
     *
     * @param float $quantity Pack quantity
     * @return bool
     */
    public function matchesQuantity($quantity)
    {
        if (!$this->is_active) {
            return false;
        }

        if ($quantity < $this->min_quantity) {
            return false;
        }

        if ($this->max_quantity !== null && $quantity >= $this->max_quantity) {
            return false;
        }

        return true;
    }

    /**
     * Calculate commission for a given quantity and order total
     *
     * @param float $quantity Total pack quantity
     * @param float $orderTotal Total order amount in DH (for percentage calculation)
     * @return float Commission amount in DH
     */
    public function calculateCommission($quantity, $orderTotal = 0)
    {
        if (!$this->matchesQuantity($quantity)) {
            return 0;
        }

        if ($this->commission_type === 'percentage') {
            return ($orderTotal * $this->commission_value) / 100;
        }

        return $this->commission_value;
    }
}
