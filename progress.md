# Progress Tracker - Wallet & Purchase Bonus System

## Current Status
Last visited: 2026-06-22T22:50:00Z

## Iteration Status
Current iteration: 1 / 32

## Checklist
- [ ] Milestone 1: DB Schema & Models
  - [ ] Create Phinx migration for wallet and bonus fields
  - [ ] Update Customer and Pack entity accessible properties
  - [ ] Run migration and verify schema
- [ ] Milestone 2: Backend API & Checkout
  - [ ] Update profile endpoint to return `wallet_balance`
  - [ ] Update products endpoints to return `bonus_amount`, `bonus_unit_threshold`, `measurement_unit_abbreviation`
  - [ ] Implement checkout bonus calculation and wallet credit in `addOrder`
- [ ] Milestone 3: API Integration Verification
  - [ ] Write integration test cases in `B2bCustomerApiControllerTest`
  - [ ] Run PHPUnit tests and verify correctness
- [ ] Milestone 4: Flutter App Integration
  - [ ] Add `walletBalance` to `ProfileModel` and update parsing
  - [ ] Display wallet balance in `ProfileScreen` UI
  - [ ] Display bonus rule in `ProductDetailsScreen` UI
- [ ] Milestone 5: E2E & Integrity Verification
  - [ ] Run full test suite (Backend + Flutter)
  - [ ] Run Forensic Auditor check for zero-cheating compliance
  - [ ] Compile handoff report and finalize completion
