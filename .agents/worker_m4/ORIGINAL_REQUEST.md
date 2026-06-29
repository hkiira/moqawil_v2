## 2026-06-22T21:56:23Z
You are a Flutter Developer. Your working directory is: d:\wamp64\www\moqa\.agents\worker_m4
Your task is to implement Milestone 4: Flutter App Integration.

Modifications:
1. File: `flutter_b2b_app/lib/features/profile/domain/profile_model.dart`
   - Add field `final double walletBalance;` to `ProfileModel`.
   - Update the constructor to include `required this.walletBalance`.
   - Update `fromJson` to parse `walletBalance: (json['wallet_balance'] as num?)?.toDouble() ?? 0.0,`.
2. File: `flutter_b2b_app/lib/features/profile/presentation/profile_screen.dart`
   - In `_buildProfileCard(profile)` (approx line 65-114), display the user's `walletBalance` inside the card.
   - Use a clean wallet container/widget matching the card's design. E.g., showing `Wallet Balance: ${profile.walletBalance.toStringAsFixed(2)} MAD` with an icon (e.g. `Icons.account_balance_wallet`).
3. File: `flutter_b2b_app/lib/features/catalog/presentation/product_details_screen.dart`
   - Parse `bonusAmount`, `bonusUnitThreshold`, and `measurementUnitAbbreviation` from `product`.
   - Under the product price display (approx lines 181-204), display a banner/message with the bonus rule if configured (i.e. `bonusAmount > 0` and `bonusUnitThreshold > 0`).
   - Visual message format: e.g. "Earn 3 DH per 10 Kg" or generic "Earn X DH per Y units". Use a nice layout matching the product details screen styling.
4. Verify compiling and testing:
   - Run `flutter test` inside `d:\wamp64\www\moqa\flutter_b2b_app` to verify all test suites compile and pass successfully.

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A Forensic Auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Write a handoff report at `d:\wamp64\www\moqa\.agents\worker_m4\handoff.md` and notify me when completed.
