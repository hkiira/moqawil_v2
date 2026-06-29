import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/network/dio_client.dart';
import '../domain/cart_state.dart';
import 'package:dio/dio.dart';

final cartRepositoryProvider = Provider((ref) {
  return CartRepository(ref.watch(dioProvider));
});

class CartRepository {
  final Dio _dio;
  CartRepository(this._dio);

  Future<bool> submitOrder(List<CartItem> items) async {
    try {
      final payload = {
        'cartItems': items.map((item) => {
          'pack_id': item.packId,
          'quantity': item.quantity,
          'price': item.price,
        }).toList(),
      };
      
      final response = await _dio.post('/addOrder', data: payload);
      if (response.data['success'] == true) {
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }
}
