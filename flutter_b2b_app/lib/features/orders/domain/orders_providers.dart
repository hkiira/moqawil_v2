import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/orders_repository.dart';
import 'order_model.dart';

final ordersProvider = FutureProvider.autoDispose<List<OrderModel>>((ref) async {
  return ref.watch(ordersRepositoryProvider).fetchMyOrders();
});
