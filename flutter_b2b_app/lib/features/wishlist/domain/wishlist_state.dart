import 'package:flutter_riverpod/flutter_riverpod.dart';

class WishlistItem {
  final int packId;
  final String title;
  final String image;
  final double price;

  WishlistItem({
    required this.packId,
    required this.title,
    required this.image,
    required this.price,
  });

  Map<String, dynamic> toMap() {
    return {
      'id': packId,
      'title': title,
      'image': image,
      'price': price,
    };
  }
}

class WishlistNotifier extends Notifier<List<WishlistItem>> {
  @override
  List<WishlistItem> build() => [];

  void toggleItem(WishlistItem item) {
    final exists = state.any((e) => e.packId == item.packId);
    if (exists) {
      state = state.where((e) => e.packId != item.packId).toList();
    } else {
      state = [...state, item];
    }
  }

  bool isFavorite(int packId) {
    return state.any((e) => e.packId == packId);
  }

  void removeItem(int packId) {
    state = state.where((e) => e.packId != packId).toList();
  }
}

final wishlistProvider = NotifierProvider<WishlistNotifier, List<WishlistItem>>(WishlistNotifier.new);
