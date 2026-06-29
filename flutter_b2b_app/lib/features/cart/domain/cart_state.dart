import 'package:flutter_riverpod/legacy.dart';

class CartItem {
  final int packId;
  final String title;
  final String image;
  final double price;
  int quantity;

  CartItem({
    required this.packId,
    required this.title,
    required this.image,
    required this.price,
    this.quantity = 1,
  });
}

class CartNotifier extends StateNotifier<List<CartItem>> {
  CartNotifier() : super([]);

  void addItem(CartItem item) {
    final existingIndex = state.indexWhere((i) => i.packId == item.packId);
    if (existingIndex >= 0) {
      final updated = List<CartItem>.from(state);
      updated[existingIndex].quantity += item.quantity;
      state = updated;
    } else {
      state = [...state, item];
    }
  }

  void updateQuantity(int packId, int newQuantity) {
    if (newQuantity <= 0) {
      state = state.where((item) => item.packId != packId).toList();
    } else {
      state = [
        for (final item in state)
          if (item.packId == packId)
            CartItem(
              packId: item.packId,
              title: item.title,
              image: item.image,
              price: item.price,
              quantity: newQuantity,
            )
          else
            item,
      ];
    }
  }

  void clearCart() {
    state = [];
  }

  double get totalPrice {
    return state.fold(
      0.0,
      (total, item) => total + (item.price * item.quantity),
    );
  }
}

final cartProvider = StateNotifierProvider<CartNotifier, List<CartItem>>((ref) {
  return CartNotifier();
});
