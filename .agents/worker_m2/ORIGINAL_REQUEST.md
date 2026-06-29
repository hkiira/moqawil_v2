## 2026-06-22T21:51:35Z
You are an API Developer. Your working directory is: d:\wamp64\www\moqa\.agents\worker_m2
Your task is to implement Milestone 2: Backend API & Checkout logic.

Implementation details:
1. File to modify: `src/Controller/B2bCustomerApiController.php`
2. Update `login()` method (approx line 132-143):
   Add `'wallet_balance' => (float)$customer->wallet_balance` under the `customer` data array.
3. Update `profile()` method (approx line 166-175):
   Add `'wallet_balance' => (float)$customer->wallet_balance` under the returned `data` array.
4. Update `products()` method (approx line 422-438) and `newhomeproducts()` method (approx line 903-919):
   Add the following properties to each product item in the `$data[]` array:
   - `'bonus_amount' => (float)$pack->bonus_amount,`
   - `'bonus_unit_threshold' => (float)$pack->bonus_unit_threshold,`
   - `'measurement_unit_abbreviation' => $pack->measurement_unit ? $pack->measurement_unit->abbreviation : '',`
5. Update `addOrder()` method (approx line 554):
   Add the bonus calculation and wallet balance incrementing logic immediately after the order is successfully saved:
   ```php
        if ($this->Orders->save($order)) {
            // Calculate total earned bonus and update customer's wallet balance
            $totalBonus = 0.0;
            $savedOrder = $this->Orders->get($order->id, [
                'contain' => ['Orderpacks.Packs']
            ]);
            if (!empty($savedOrder->orderpacks)) {
                foreach ($savedOrder->orderpacks as $orderpack) {
                    $pack = $orderpack->pack;
                    if ($pack && $pack->bonus_amount > 0 && $pack->bonus_unit_threshold > 0) {
                        $itemQty = (float)$orderpack->quantity;
                        $measQty = (float)$pack->measurement_quantity;
                        if ($measQty <= 0) {
                            $measQty = 1.0;
                        }
                        $totalUnits = $itemQty * $measQty;
                        $itemBonus = ($totalUnits / (float)$pack->bonus_unit_threshold) * (float)$pack->bonus_amount;
                        $totalBonus += $itemBonus;
                    }
                }
            }
            if ($totalBonus > 0) {
                $customer = $this->Orders->Customers->get($customer_id);
                $customer->wallet_balance = (float)$customer->wallet_balance + $totalBonus;
                $this->Orders->Customers->save($customer);
            }
            // ... (keep the original response code)
   ```

Verify your code styles using phpcs to ensure it adheres to CakePHP coding standards.

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A Forensic Auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Please write a handoff report at `d:\wamp64\www\moqa\.agents\worker_m2\handoff.md` and notify me when completed.
