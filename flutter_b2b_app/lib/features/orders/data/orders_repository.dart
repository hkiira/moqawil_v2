import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:dio/dio.dart';
import '../../../core/network/dio_client.dart';
import '../domain/order_model.dart';

final ordersRepositoryProvider = Provider((ref) {
  return OrdersRepository(ref.watch(dioProvider));
});

class OrdersRepository {
  final Dio _dio;
  OrdersRepository(this._dio);

  Future<List<OrderModel>> fetchMyOrders() async {
    try {
      final response = await _dio.get('/myorders');
      if (response.data['success'] == true) {
        final data = response.data['data'] as List;
        return data.map((e) => OrderModel.fromJson(e)).toList();
      }
      return [];
    } catch (e) {
      throw Exception('Failed to fetch orders: $e');
    }
  }
}
