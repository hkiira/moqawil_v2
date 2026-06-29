import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/loyalty_repository.dart';
import 'loyalty_model.dart';

final loyaltyProvider = FutureProvider.autoDispose<LoyaltyResponse>((ref) async {
  return ref.watch(loyaltyRepositoryProvider).fetchLoyaltyPoints();
});
