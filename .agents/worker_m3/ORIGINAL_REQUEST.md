## 2026-06-22T21:53:57Z
You are an Integration Tester. Your working directory is: d:\wamp64\www\moqa\.agents\worker_m3
Your task is to implement Milestone 3: API Integration Verification.

Steps:
1. Create a standalone PHP test script: `tests/test_wallet_checkout.php`
   - It should boot CakePHP using `config/bootstrap.php`.
   - Setup a test customer (reset wallet_balance to 0.00).
   - Setup a test pack (set bonus_amount = 3.00, bonus_unit_threshold = 10.00, measurement_quantity = 10.00).
   - Generate a valid B2B JWT token using the secret `'super_secret_b2b_app_key_2026_very_long_secure_string'`.
   - Build a `Cake\Http\ServerRequest` with the JWT token header and parsing `cartItems` payload containing 1 quantity of the test pack.
   - Instantiate `App\Controller\B2bCustomerApiController` with the request, run the `addOrder()` action.
   - Fetch the customer again and assert that their `wallet_balance` is now exactly `3.00`.
   - Print success message and return exit code 0 on success, or 1 on failure.
2. Run this test script:
   `php tests/test_wallet_checkout.php`
3. Confirm the output shows success.

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A Forensic Auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Please report back with a handoff report showing the script execution output.
