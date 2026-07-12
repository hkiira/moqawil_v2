import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../domain/wishlist_state.dart';
import '../../cart/domain/cart_state.dart';

class WishlistScreen extends ConsumerWidget {
  const WishlistScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final wishlist = ref.watch(wishlistProvider);

    return Scaffold(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      body: Stack(
        children: [
          Positioned(
            top: -100, right: -50,
            child: ImageFiltered(
              imageFilter: ImageFilter.blur(sigmaX: 100, sigmaY: 100),
              child: Container(width: 300, height: 300, decoration: BoxDecoration(color: Theme.of(context).colorScheme.primary.withOpacity(0.08), shape: BoxShape.circle)),
            ),
          ),
          SafeArea(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    IconButton(icon: Icon(Icons.arrow_back, color: Theme.of(context).colorScheme.onSurface), onPressed: () => Navigator.pop(context)),
                    Text('Wishlist', style: TextStyle(color: Theme.of(context).colorScheme.onSurface, fontSize: 24, fontWeight: FontWeight.bold)),
                  ],
                ),
                Expanded(
                  child: wishlist.isEmpty
                      ? Center(child: Text('Your wishlist is empty', style: TextStyle(color: Theme.of(context).colorScheme.onSurface.withOpacity(0.5), fontSize: 18)))
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
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.grey.withOpacity(0.15)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.03),
            blurRadius: 8,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Row(
          children: [
            ClipRRect(
              borderRadius: BorderRadius.circular(8),
              child: item.image.isNotEmpty
                  ? Image.network(item.image, width: 80, height: 80, fit: BoxFit.cover, errorBuilder: (_,__,___) => Container(width: 80, height: 80, color: Colors.grey.withOpacity(0.1), child: Icon(Icons.image, color: Theme.of(context).colorScheme.primary.withOpacity(0.3))))
                  : Container(width: 80, height: 80, color: Colors.grey.withOpacity(0.1), child: Icon(Icons.image, color: Theme.of(context).colorScheme.primary.withOpacity(0.3))),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(item.title, style: TextStyle(color: Theme.of(context).colorScheme.onSurface, fontWeight: FontWeight.bold, fontSize: 16), maxLines: 2, overflow: TextOverflow.ellipsis),
                  const SizedBox(height: 8),
                  Text('${item.price} MAD', style: TextStyle(color: Theme.of(context).colorScheme.primary, fontWeight: FontWeight.bold, fontSize: 16)),
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
                  icon: Icon(Icons.add_shopping_cart, color: Theme.of(context).colorScheme.primary),
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
    );
  }
}
