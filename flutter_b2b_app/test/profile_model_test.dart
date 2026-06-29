import 'package:flutter_test/flutter_test.dart';
import 'package:b2b_app/features/profile/domain/profile_model.dart';

void main() {
  group('ProfileModel Tests', () {
    test('should parse profile json with wallet_balance as double', () {
      final json = {
        'id': 1,
        'name': 'John Doe',
        'phone': '123456789',
        'adresse': '123 Main St',
        'wallet_balance': 150.75,
      };

      final profile = ProfileModel.fromJson(json);

      expect(profile.id, 1);
      expect(profile.name, 'John Doe');
      expect(profile.phone, '123456789');
      expect(profile.adresse, '123 Main St');
      expect(profile.walletBalance, 150.75);
    });

    test('should parse profile json with wallet_balance as int', () {
      final json = {
        'id': 2,
        'name': 'Jane Doe',
        'phone': '987654321',
        'adresse': '456 Oak Ave',
        'wallet_balance': 200,
      };

      final profile = ProfileModel.fromJson(json);

      expect(profile.walletBalance, 200.0);
    });

    test('should default wallet_balance to 0.0 when missing or null', () {
      final json = {
        'id': 3,
        'name': 'Bob Smith',
        'phone': '5551234',
        'adresse': '789 Pine Rd',
      };

      final profile = ProfileModel.fromJson(json);

      expect(profile.walletBalance, 0.0);
    });
  });
}
