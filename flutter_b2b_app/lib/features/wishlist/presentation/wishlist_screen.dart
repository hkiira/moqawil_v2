import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../domain/wishlist_state.dart';
import '../../cart/domain/cart_state.dart';
import 'package:go_router/go_router.dart';

class WishlistScreen extends ConsumerWidget {
  const WishlistScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final wishlist = ref.watch(wishlistProvider);

    return Scaffold(
      backgroundColor: const Color(0xFF1E1E2C),
      body: Stack(
        children: [
          Positioned(
            top: -100, right: -50,
            child: ImageFiltered(
              imageFilter: ImageFilter.blur(sigmaX: 100, sigmaY: 100),
              child: Container(width: 300, height: 300, decoration: BoxDecoration(color: const Color(0xFF4F46E5).withOpacity(0.3), shape: BoxShape.circle)),
            ),
          ),
          SafeArea(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    IconButton(icon: const Icon(Icons.arrow_back, color: Colors.white), onPressed: () => Navigator.pop(context)),
                    const Text('Wishlist', style: TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.bold)),
                  ],
                ),
                Expanded(
                  child: wishlist.isEmpty
                      ? const Center(child: Text('Your wishlist is empty', style: TextStyle(color: Colors.white54, fontSize: 18)))
                      : ListView.builder(
                          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                          itemCount: wishlist.length,
                          itemBuilder: (context, index) {
                            final item = wishlist[index];
                            return _buildWishlistCard(context, ref, item);
                          },
                        ),
                )
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildWishlistCard(BuildContext context, WidgetRef ref, WishlistItem item) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(16),
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10),
          child: Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.05),
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: Colors.white.withOpacity(0.1)),
            ),
            child: Row(
              children: [
                ClipRRect(
                  borderRadius: BorderRadius.circular(8),
                  child: item.image.isNotEmpty
                      ? Image.network(item.image, width: 80, height: 80, fit: BoxFit.cover, errorBuilder: (_,__,___) => Container(width: 80, height: 80, color: Colors.white10, child: const Icon(Icons.image, color: Colors.white54)))
                      : Container(width: 80, height: 80, color: Colors.white10, child: const Icon(Icons.image, color: Colors.white54)),
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(item.title, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16), maxLines: 2, overflow: TextOverflow.ellipsis),
                      const SizedBox(height: 8),
                      Text('${item.price} MAD', style: const TextStyle(color: Color(0xFF4F46E5), fontWeight: FontWeight.bold, fontSize: 16)),
                    ],
                  ),
                ),
                Column(
                  children: [
                    IconButton(
                      icon: const Icon(Icons.delete_outline, color: Colors.redAccent),
                      onPressed: () {
                        ref.read(wishlistProvider.notifier).removeItem(item.packId);
                      },
                    ),
                    IconButton(
                      icon: const Icon(Icons.add_shopping_cart, color: Colors.greenAccent),
                      onPressed: () {
                        ref.read(cartProvider.notifier).addItem(
                          CartItem(packId: item.packId, title: item.title, image: item.image, price: item.price)
                        );
                        ScaffoldMessenger.of(context).showSnackBar(
                          SnackBar(content: Text('${item.title} added to cart'), backgroundColor: Colors.green),
                        );
                      },
                    ),
                  ],
                )
              ],
            ),
          ),
        ),
      ),
    );
  }
}
