<?php
namespace App\Service;

use Cake\ORM\TableRegistry;

class OrderPricingService
{
    /**
     * @var \App\Model\Table\PricesTable
     */
    private $Prices;

    public function __construct()
    {
        $this->Prices = TableRegistry::getTableLocator()->get('Prices');
    }

    /**
     * Compute the price of one order line with tranche-based discounts.
     * Supports both quantity-based and amount-based tranches.
     *
     * @param int $packId
     * @param int $quantity Quantity expressed in the base unit used by pricing/tranches.
     * @param int $customerTypeId
     * @param int $warehouseId
     * @param int $companyId
     * @param float|null $totalOrderAmount Optional total order amount for AMOUNT-based tranches (in currency units like DH)
     * @return array{base_price:float,final_unit_price:float,tranche_id:int|null}
     */
    public function priceLine(int $packId, int $quantity, int $customerTypeId, int $warehouseId, int $companyId, ?float $totalOrderAmount = null): array
    {
        $Packs = TableRegistry::getTableLocator()->get('Packs');
        $pack = $Packs->get($packId, [
            'contain' => ['Packunites.Unites.Parentunites']
        ]);

        $price = $this->Prices
            ->find()
            ->contain([
                'Trancheprices' => function ($q) {
                    return $q->contain([
                        'Tranches' => function ($qq) {
                            return $qq->contain(['Remisetypes']);
                        },
                    ]);
                },
            ])
            ->where([
                'Prices.pack_id' => $packId,
                'Prices.customertype_id' => $customerTypeId,
            ])
            ->order([
                // Prefer warehouse-specific price when it exists, otherwise fallback to generic.
                'Prices.warehouse_id' => 'DESC',
                'Prices.id' => 'ASC',
            ])
            ->first();

        if (!$price) {
            throw new \RuntimeException('No base price found for this pack.');
        }

        $selectedTrancheId = null;
        $unitPrice = (float)$price->price;
        $giftPackId = null;
        $giftQuantity = null;

        // Step 1: Check tranches linked specifically to this price via Trancheprices
        $trancheprices = $price->trancheprices ?? [];
        usort($trancheprices, function ($a, $b) {
            $amin = $a->tranch->min ?? 0;
            $bmin = $b->tranch->min ?? 0;
            return $amin <=> $bmin;
        });

        foreach ($trancheprices as $trancheprice) {
            $tranch = $trancheprice->tranch ?? null;
            if (!$tranch) {
                continue;
            }
            if (isset($tranch->statut) && (int)$tranch->statut === 0) {
                continue;
            }

            // Determine the value to compare based on apply_type
            $applyType = $tranch->apply_type ?? 'QUANTITY';
            
            if ($applyType === 'AMOUNT') {
                // For amount-based tranches, use total order amount
                $valueToCompare = $totalOrderAmount ?? 0;
            } else {
                // For quantity-based tranches, normalize based on unit type
                $quantityUnitType = $tranch->quantity_unit_type ?? 'UNITS';
                $valueToCompare = $this->normalizeQuantityForTranche($quantity, $pack, $quantityUnitType);
            }

            $min = (int)$tranch->min;
            $max = $tranch->max === null ? null : (int)$tranch->max;

            if ($valueToCompare < $min) {
                continue;
            }
            if ($max !== null && $max > 0 && $valueToCompare > $max) {
                continue;
            }

            $selectedTrancheId = $tranch->id;
            $code = $tranch->remisetype->code ?? '%';
            $codeLower = strtolower(trim((string)$code));

            if ($codeLower === '%' || $codeLower === 'percent' || $codeLower === 'percentage' || $codeLower === 'pct') {
                $unitPrice = $this->applyPercent($unitPrice, (float)$tranch->remise);
            } elseif ($codeLower === 'red') {
                $unitPrice = $this->applyFixed($unitPrice, (float)$tranch->remise);
            } elseif ($codeLower === 'grt') {
                // Gift: keep price unchanged, but record gift pack and quantity
                $giftPackId = $tranch->pack_id ?? null;
                // Use tranche.remise as quantity if numeric, else default 1
                $giftQuantity = is_numeric($tranch->remise) ? (int)$tranch->remise : 1;
            } else {
                // Fallback: treat as fixed discount
                $unitPrice = $this->applyFixed($unitPrice, (float)$tranch->remise);
            }
            break;
        }

        // Step 2: If no specific tranche matched, check global tranches (those with no Trancheprices)
        if ($selectedTrancheId === null) {
            $Tranches = TableRegistry::getTableLocator()->get('Tranches');
            $globalTranches = $Tranches->find()
                ->contain(['Remisetypes'])
                ->where([
                    'Tranches.statut' => 1,
                ])
                ->order(['Tranches.min' => 'ASC'])
                ->all();
            foreach ($globalTranches as $tranch) {
                // Check if this tranche has any Trancheprices linked
                $hasLinkedPrices = $Tranches->Trancheprices->find()
                    ->where(['Trancheprices.tranche_id' => $tranch->id])
                    ->count() > 0;

                // Only apply global tranches (those with no Trancheprices)
                if ($hasLinkedPrices) {
                    continue;
                }

                // Determine the value to compare based on apply_type
                $applyType = $tranch->apply_type ?? 'QUANTITY';
                
                if ($applyType === 'AMOUNT') {
                    // For amount-based tranches, use total order amount
                    $valueToCompare = $totalOrderAmount ?? 0;
                } else {
                    // For quantity-based tranches, normalize based on unit type
                    $quantityUnitType = $tranch->quantity_unit_type ?? 'UNITS';
                    $valueToCompare = $this->normalizeQuantityForTranche($quantity, $pack, $quantityUnitType);
                }

                $min = (int)$tranch->min;
                $max = $tranch->max === null ? null : (int)$tranch->max;

                if ($valueToCompare < $min) {
                    continue;
                }
                if ($max !== null && $max > 0 && $valueToCompare > $max) {
                    continue;
                }

                $selectedTrancheId = $tranch->id;
                $code = $tranch->remisetype->code ?? '%';
                $codeLower = strtolower(trim((string)$code));

                if ($codeLower === '%' || $codeLower === 'percent' || $codeLower === 'percentage' || $codeLower === 'pct') {
                    $unitPrice = $this->applyPercent($unitPrice, (float)$tranch->remise);
                } elseif ($codeLower === 'red') {
                    $unitPrice = $this->applyFixed($unitPrice, (float)$tranch->remise);
                } elseif ($codeLower === 'grt') {
                    // Gift: keep price unchanged, but record gift pack and quantity
                    $giftPackId = $tranch->pack_id ?? null;
                    // Use tranche.remise as quantity if numeric, else default 1
                    $giftQuantity = is_numeric($tranch->remise) ? (int)$tranch->remise : 1;
                } else {
                    // Fallback: treat as fixed discount
                    $unitPrice = $this->applyFixed($unitPrice, (float)$tranch->remise);
                }
                break;
            }
        }

        return [
            'base_price' => (float)$price->price,
            'final_unit_price' => $unitPrice,
            'tranche_id' => $selectedTrancheId,
            'gift_pack_id' => $giftPackId,
            'gift_quantity' => $giftQuantity,
        ];
    }

    private function applyPercent(float $basePrice, float $pct): float
    {
        $pct = max($pct, 0.0);
        $discount = $basePrice * ($pct / 100.0);
        return max($basePrice - $discount, 0.0);
    }

    private function applyFixed(float $basePrice, float $value): float
    {
        $value = max($value, 0.0);
        return max($basePrice - $value, 0.0);
    }

    /**
     * Normalize quantity for tranche comparison based on quantity_unit_type
     *
     * @param int $quantity Original quantity passed
     * @param object $pack Pack entity with saletype_id and measurement details
     * @param string $quantityUnitType UNITS, PACKAGE, or MEASUREMENT
     * @return float Normalized quantity for comparison
     */
    private function normalizeQuantityForTranche(int $quantity, $pack, string $quantityUnitType): float
    {
        switch ($quantityUnitType) {
            case 'UNITS':
                // Count by individual units (no conversion needed)
                return (float)$quantity;

            case 'PACKAGE':
                // Count by packages/packs
                // If pack has package conversion (e.g., 1 package = 10 units)
                if (isset($pack->package_quantity) && $pack->package_quantity > 0) {
                    return (float)floor($quantity / (float)$pack->package_quantity);
                }
                // If using packunites relationship
                if (isset($pack->packunites) && !empty($pack->packunites)) {
                    foreach ($pack->packunites as $packunite) {
                        if (isset($packunite->quantity) && $packunite->quantity > 0) {
                            return (float)floor($quantity / (float)$packunite->quantity);
                        }
                    }
                }
                // Evaluate to 0 for misconfigured PACKAGE products
                return 0.0;

            case 'MEASUREMENT':
                // Count by weight/volume (KG, liters, etc.)
                // Convert to base measurement unit (grams, ml)
                if (isset($pack->saletype_id) && (int)$pack->saletype_id === 4 && isset($pack->measurement_unit_id)) {
                    // For KG/L type units: convert pieces to KG/L
                    if (in_array((int)$pack->measurement_unit_id, [2, 4], true) && !empty($pack->measurement_quantity)) {
                        return (float)(($quantity * (float)$pack->measurement_quantity) / 1000.0);
                    }
                }
                // If product has direct weight/volume info (but not necessarily KG/L)
                if (isset($pack->measurement_quantity) && $pack->measurement_quantity > 0) {
                    // Quantity represents items, convert to total weight/volume
                    return (float)($quantity * (float)$pack->measurement_quantity);
                }
                // Evaluate to 0 for misconfigured MEASUREMENT products
                return 0.0;

            default:
                // Fallback to units
                return (float)$quantity;
        }
    }
}
