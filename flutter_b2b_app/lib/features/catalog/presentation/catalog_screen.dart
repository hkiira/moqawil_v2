import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../domain/catalog_providers.dart';
import '../../cart/domain/cart_state.dart';
import '../../wishlist/domain/wishlist_state.dart';

class CatalogScreen extends ConsumerStatefulWidget {
  final String? searchQuery;
  final String? categoryId;

  const CatalogScreen({super.key, this.searchQuery, this.categoryId});

  @override
  ConsumerState<CatalogScreen> createState() => _CatalogScreenState();
}

class _CatalogScreenState extends ConsumerState<CatalogScreen> {
  @override
  Widget build(BuildContext context) {
    final filter = CatalogFilter(search: widget.searchQuery, categoryId: widget.categoryId);
    final productsAsync = ref.watch(catalogProvider(filter));

    String title = 'All Products';
    if (widget.searchQuery != null && widget.searchQuery!.isNotEmpty) {
      title = 'Search: "${widget.searchQuery}"';
    } else if (widget.categoryId != null) {
      title = 'Category Products';
    }

    return Scaffold(
      backgroundColor: const Color(0xFF1E1E2C),
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.white),
        title: Text(title, style: const TextStyle(color: Colors.white)),
      ),
      body: Stack(
        children: [
          Positioned(
            top: -100, right: -50,
            child: ImageFiltered(
              imageFilter: ImageFilter.blur(sigmaX: 100, sigmaY: 100),
              child: Container(width: 300, height: 300, decoration: BoxDecoration(color: const Color(0xFF4F46E5).withOpacity(0.3), shape: BoxShape.circle)),
            ),
          ),
          
          productsAsync.when(
            data: (products) {
              if (products.isEmpty) {
                return const Center(
                  child: Text('No products found.', style: TextStyle(color: Colors.white70, fontSize: 18)),
                );
              }
              return GridView.builder(
                padding: const EdgeInsets.all(16),
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 2,
                  childAspectRatio: 0.65,
                  crossAxisSpacing: 16,
                  mainAxisSpacing: 16,
                ),
                itemCount: products.length,
                itemBuilder: (context, index) {
                  final product = products[index];
                  return _buildProductCard(context, product, ref);
                },
              );
            },
            loading: () => const Center(child: CircularProgressIndicator()),
            error: (err, stack) => Center(child: Text('Error: $err', style: const TextStyle(color: Colors.red))),
          ),
        ],
      ),
    );
  }

  Widget _buildProductCard(BuildContext context, Map<String, dynamic> product, WidgetRef ref) {
    final int packId = product['id'] ?? 0;
    final String imageUrl = product['image'] != null ? 'http://localhost/moqa' + product['image'] : '';

    return GestureDetector(
      onTap: () => context.push('/product_details', extra: product),
      child: Stack(
        children: [
          ClipRRect(
            borderRadius: BorderRadius.circular(16),
            child: BackdropFilter(
              filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10),
              child: Container(
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.05),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: Colors.white.withOpacity(0.2)),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Expanded(
                      child: Container(
                        width: double.infinity,
                        color: Colors.white.withOpacity(0.1),
                        child: imageUrl.isNotEmpty
                          ? Image.network(imageUrl, fit: BoxFit.cover, errorBuilder: (_,__,___) => const Icon(Icons.image, color: Colors.white54))
                          : const Icon(Icons.image, color: Colors.white54),
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.all(12.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            product['title'] ?? 'Product',
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 14),
                          ),
                          const SizedBox(height: 8),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                '${product['price'] ?? 0} MAD',
                                style: const TextStyle(color: Color(0xFF4F46E5), fontWeight: FontWeight.bold, fontSize: 16),
                              ),
                              if (product['tranches'] != null && (product['tranches'] as List).isNotEmpty)
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                  decoration: BoxDecoration(
                                    color: Colors.orangeAccent.withValues(alpha: 0.2),
                                    borderRadius: BorderRadius.circular(4),
                                    border: Border.all(color: Colors.orangeAccent.withValues(alpha: 0.5)),
                                  ),
                                  child: const Text('Offers', style: TextStyle(color: Colors.orangeAccent, fontSize: 10, fontWeight: FontWeight.bold)),
                                ),
                              GestureDetector(
                                onTap: () {
                                  ref.read(cartProvider.notifier).addItem(
                                    CartItem(
                                      packId: packId,
                                      title: product['title'],
                                      image: imageUrl,
                                      price: (product['price'] as num).toDouble(),
                                    ),
                                  );
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    const SnackBar(content: Text('Added to cart'), backgroundColor: Colors.green),
                                  );
                                },
                                child: Container(
                                  padding: const EdgeInsets.all(6),
                                  decoration: BoxDecoration(color: const Color(0xFF4F46E5), borderRadius: BorderRadius.circular(8)),
                                  child: const Icon(Icons.add_shopping_cart, color: Colors.white, size: 20),
                                ),
                              ),
                            ],
                          )
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
          Positioned(
            top: 8,
            right: 8,
            child: Consumer(builder: (context, ref, child) {
              final isFav = ref.watch(wishlistProvider.notifier).isFavorite(packId);
              return GestureDetector(
                onTap: () {
                  final item = WishlistItem(
                    packId: packId,
                    title: product['title'] ?? '',
                    image: imageUrl,
                    price: double.tryParse(product['price']?.toString() ?? '0') ?? 0,
                  );
                  ref.read(wishlistProvider.notifier).toggleItem(item);
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text(isFav ? 'Removed from Wishlist' : 'Added to Wishlist'), backgroundColor: isFav ? Colors.red : Colors.green, duration: const Duration(seconds: 1)),
                  );
                  ref.refresh(wishlistProvider);
                },
                child: Container(
                  padding: const EdgeInsets.all(6),
                  decoration: const BoxDecoration(color: Colors.black45, shape: BoxShape.circle),
                  child: Icon(
                    isFav ? Icons.favorite : Icons.favorite_border,
                    color: isFav ? Colors.redAccent : Colors.white,
                    size: 20,
                  ),
                ),
              );
            }),
          )
        ],
      ),
    );
  }
}
