import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:dio/dio.dart';
import '../../../core/network/dio_client.dart';
import '../domain/loyalty_model.dart';

final loyaltyRepositoryProvider = Provider((ref) {
  return LoyaltyRepository(ref.watch(dioProvider));
});

class LoyaltyRepository {
  final Dio _dio;
  LoyaltyRepository(this._dio);

  Future<LoyaltyResponse> fetchLoyaltyPoints() async {
    try {
      final response = await _dio.get('/my-loyalty-points');
      if (response.data['success'] == true) {
        return LoyaltyResponse.fromJson(response.data['data']);
      }
      return LoyaltyResponse(totalPoints: 0, history: []);
    } catch (e) {
      throw Exception('Failed to fetch loyalty points: $e');
    }
  }
}
