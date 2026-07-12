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
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: IconThemeData(color: Theme.of(context).colorScheme.onSurface),
        title: Text(title, style: TextStyle(color: Theme.of(context).colorScheme.onSurface)),
      ),
      body: Stack(
        children: [
          Positioned(
            top: -100, right: -50,
            child: ImageFiltered(
              imageFilter: ImageFilter.blur(sigmaX: 100, sigmaY: 100),
              child: Container(width: 300, height: 300, decoration: BoxDecoration(color: Theme.of(context).colorScheme.primary.withOpacity(0.08), shape: BoxShape.circle)),
            ),
          ),
          
          RefreshIndicator(
            onRefresh: () async => ref.refresh(catalogProvider(filter)),
            child: productsAsync.when(
              data: (products) {
                if (products.isEmpty) {
                  return ListView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    children: [
                      SizedBox(
                        height: MediaQuery.of(context).size.height * 0.7,
                        child: Center(
                          child: Text('No products found.', style: TextStyle(color: Theme.of(context).colorScheme.onSurface.withOpacity(0.7), fontSize: 18)),
                        ),
                      ),
                    ],
                  );
                }
                return GridView.builder(
                  physics: const AlwaysScrollableScrollPhysics(),
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
              error: (err, stack) => ListView(
                physics: const AlwaysScrollableScrollPhysics(),
                children: [
                  SizedBox(
                    height: MediaQuery.of(context).size.height * 0.7,
                    child: Center(child: Text('Error: $err', style: const TextStyle(color: Colors.red))),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildProductCard(BuildContext context, Map<String, dynamic> product, WidgetRef ref) {
    final int packId = product['id'] ?? 0;
    final String rawImage = product['image']?.toString() ?? '';
    final String imageUrl = rawImage.startsWith('http') ? rawImage : (rawImage.isNotEmpty ? 'http://localhost/moqa' + rawImage : '');

    return GestureDetector(
      onTap: () => context.push('/product_details', extra: product),
      child: Stack(
        children: [
          Container(
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
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Expanded(
                  child: Container(
                    width: double.infinity,
                    color: Colors.grey.withOpacity(0.05),
                    child: imageUrl.isNotEmpty
                      ? Image.network(imageUrl, fit: BoxFit.cover, errorBuilder: (_,__,___) => Icon(Icons.image, color: Theme.of(context).colorScheme.primary.withOpacity(0.3)))
                      : Icon(Icons.image, color: Theme.of(context).colorScheme.primary.withOpacity(0.3)),
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
                        style: TextStyle(color: Theme.of(context).colorScheme.onSurface, fontWeight: FontWeight.bold, fontSize: 14),
                      ),
                      const SizedBox(height: 8),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(
                            '${product['price'] ?? 0} MAD',
                            style: TextStyle(color: Theme.of(context).colorScheme.primary, fontWeight: FontWeight.bold, fontSize: 16),
                          ),
                          if (product['tranches'] != null && (product['tranches'] as List).isNotEmpty)
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                              decoration: BoxDecoration(
                                color: Colors.orangeAccent.withValues(alpha: 0.1),
                                borderRadius: BorderRadius.circular(4),
                                border: Border.all(color: Colors.orangeAccent.withValues(alpha: 0.4)),
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
                              decoration: BoxDecoration(color: Theme.of(context).colorScheme.primary, borderRadius: BorderRadius.circular(8)),
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
                  decoration: BoxDecoration(
                    color: Colors.white,
                    shape: BoxShape.circle,
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.1),
                        blurRadius: 4,
                        offset: const Offset(0, 1),
                      ),
                    ],
                  ),
                  child: Icon(
                    isFav ? Icons.favorite : Icons.favorite_border,
                    color: isFav ? Colors.redAccent : Colors.black54,
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
