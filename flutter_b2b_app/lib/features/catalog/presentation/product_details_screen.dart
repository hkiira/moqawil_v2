import 'dart:ui';
import 'package:b2b_app/features/cart/domain/cart_state.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../wishlist/domain/wishlist_state.dart';

class ProductDetailsScreen extends ConsumerStatefulWidget {
  final Map<String, dynamic> product;

  const ProductDetailsScreen({super.key, required this.product});

  @override
  ConsumerState<ProductDetailsScreen> createState() => _ProductDetailsScreenState();
}

class _ProductDetailsScreenState extends ConsumerState<ProductDetailsScreen> {
  int _quantity = 1;

  double _calculateCurrentPrice(double basePrice) {
    double currentPrice = basePrice;
    final tranches = widget.product['tranches'] as List?;
    if (tranches != null && tranches.isNotEmpty) {
      for (final t in tranches) {
        final min = t['min'] as int? ?? 0;
        final max = t['max'] as int?; 
        
        if (_quantity >= min && (max == null || _quantity <= max)) {
          final remise = (t['remise'] as num?)?.toDouble() ?? 0.0;
          final remiseType = t['remisetype_id'];
          
          if (remiseType == 2) {
            currentPrice = basePrice - (basePrice * remise / 100);
          } else if (remiseType == 1) {
            currentPrice = basePrice - remise;
          }
          break;
        }
      }
    }
    return currentPrice > 0 ? currentPrice : 0.0;
  }

  @override
  Widget build(BuildContext context) {
    final product = widget.product;
    final image = product['image'] ?? '';
    final title = product['title'] ?? 'Unknown Product';
    final basePrice = double.tryParse(product['price'].toString()) ?? 0.0;
    final currentPrice = _calculateCurrentPrice(basePrice);
    final category = product['category'] is Map
        ? (product['category']['title'] ?? '')
        : (product['category'] ?? '');
    final description = product['description'] ?? 'No description available for this product.';
    final bonusAmount = (product['bonusAmount'] as num?)?.toDouble()
        ?? (product['bonus_amount'] as num?)?.toDouble()
        ?? 0.0;
    final bonusUnitThreshold = (product['bonusUnitThreshold'] as num?)?.toDouble()
        ?? (product['bonus_unit_threshold'] as num?)?.toDouble()
        ?? 0.0;
    final measurementUnitAbbreviation = product['measurementUnitAbbreviation']?.toString()
        ?? product['measurement_unit_abbreviation']?.toString()
        ?? 'units';

    return Scaffold(
      backgroundColor: const Color(0xFF1E1E2C),
      body: Stack(
        children: [
          Positioned(
            top: -100,
            left: -50,
            child: ImageFiltered(
              imageFilter: ImageFilter.blur(sigmaX: 100, sigmaY: 100),
              child: Container(
                width: 300,
                height: 300,
                decoration: BoxDecoration(
                  color: const Color(0xFF4F46E5).withOpacity(0.3),
                  shape: BoxShape.circle,
                ),
              ),
            ),
          ),
          CustomScrollView(
            slivers: [
              SliverAppBar(
                expandedHeight: 300,
                pinned: true,
                backgroundColor: Colors.transparent,
                flexibleSpace: FlexibleSpaceBar(
                  background: Stack(
                    fit: StackFit.expand,
                    children: [
                      if (image.isNotEmpty)
                        Image.network(
                          image,
                          fit: BoxFit.cover,
                          errorBuilder:
                              (_, __, ___) => const Icon(
                                Icons.image,
                                size: 100,
                                color: Colors.white24,
                              ),
                        )
                      else
                        const Icon(
                          Icons.image,
                          size: 100,
                          color: Colors.white24,
                        ),
                      Container(
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            begin: Alignment.topCenter,
                            end: Alignment.bottomCenter,
                            colors: [
                              Colors.transparent,
                              const Color(0xFF1E1E2C).withOpacity(0.9),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                leading: IconButton(
                  icon: const Icon(Icons.arrow_back, color: Colors.white),
                  onPressed: () => Navigator.pop(context),
                ),
                actions: [
                  Consumer(builder: (context, ref, child) {
                    final int packId = product['id'] ?? 0;
                    final isFav = ref.watch(wishlistProvider.notifier).isFavorite(packId);
                    return IconButton(
                      icon: Icon(
                        isFav ? Icons.favorite : Icons.favorite_border,
                        color: isFav ? Colors.redAccent : Colors.white,
                      ),
                      onPressed: () {
                        final item = WishlistItem(
                          packId: packId,
                          title: title,
                          image: image,
                          price: basePrice,
                        );
                        ref.read(wishlistProvider.notifier).toggleItem(item);
                        ScaffoldMessenger.of(context).showSnackBar(
                          SnackBar(content: Text(isFav ? 'Removed from Wishlist' : 'Added to Wishlist'), backgroundColor: isFav ? Colors.red : Colors.green, duration: const Duration(seconds: 1)),
                        );
                        ref.refresh(wishlistProvider);
                      },
                    );
                  }),
                ],
              ),
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.all(24.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 12,
                          vertical: 6,
                        ),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Text(
                          category,
                          style: const TextStyle(
                            color: Colors.white70,
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),
                      Text(
                        title,
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 28,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Row(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          Text(
                            '${currentPrice.toStringAsFixed(2)} MAD',
                            style: const TextStyle(
                              color: Color(0xFF4F46E5),
                              fontSize: 24,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          if (currentPrice < basePrice) ...[
                            const SizedBox(width: 8),
                            Text(
                              '${basePrice.toStringAsFixed(2)} MAD',
                              style: const TextStyle(
                                color: Colors.white38,
                                fontSize: 16,
                                decoration: TextDecoration.lineThrough,
                              ),
                            ),
                          ],
                        ],
                      ),
                      if (bonusAmount > 0 && bonusUnitThreshold > 0) ...[
                        const SizedBox(height: 12),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                          decoration: BoxDecoration(
                            color: const Color(0xFF4F46E5).withOpacity(0.15),
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(color: const Color(0xFF4F46E5).withOpacity(0.3)),
                          ),
                          child: Row(
                            children: [
                              const Icon(Icons.stars, color: Colors.amber, size: 22),
                              const SizedBox(width: 10),
                              Expanded(
                                child: Text(
                                  'Earn ${bonusAmount % 1 == 0 ? bonusAmount.toInt() : bonusAmount.toStringAsFixed(2)} DH per ${bonusUnitThreshold % 1 == 0 ? bonusUnitThreshold.toInt() : bonusUnitThreshold} $measurementUnitAbbreviation',
                                  style: const TextStyle(
                                    color: Colors.white,
                                    fontSize: 15,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                      const SizedBox(height: 32),
                      const Text(
                        'Description',
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        description,
                        style: const TextStyle(
                          color: Colors.white54,
                          fontSize: 16,
                          height: 1.5,
                        ),
                      ),
                      const SizedBox(height: 32),
                      if (product['tranches'] != null && (product['tranches'] as List).isNotEmpty) ...[
                        const Text(
                          'Pricing Tranches & Offers',
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(height: 16),
                        ...(product['tranches'] as List).map((tranche) {
                          return Container(
                            margin: const EdgeInsets.only(bottom: 12),
                            padding: const EdgeInsets.all(16),
                            decoration: BoxDecoration(
                              color: Colors.white.withValues(alpha: 0.05),
                              borderRadius: BorderRadius.circular(16),
                              border: Border.all(color: Colors.white.withValues(alpha: 0.1)),
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    const Icon(Icons.local_offer, color: Color(0xFF4F46E5), size: 20),
                                    const SizedBox(width: 8),
                                    Expanded(
                                      child: Text(
                                        tranche['title'] ?? 'Offer',
                                        style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16),
                                      ),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 8),
                                Text(
                                  'Buy ${tranche['min']} to ${tranche['max'] ?? 'more'} items',
                                  style: const TextStyle(color: Colors.white70),
                                ),
                                if (tranche['remise'] != null && tranche['remise'] > 0)
                                  Padding(
                                    padding: const EdgeInsets.only(top: 4.0),
                                    child: Text(
                                      'Discount: ${tranche['remise']}${tranche['remisetype_id'] == 2 ? '%' : ' MAD'}',
                                      style: const TextStyle(color: Colors.greenAccent, fontWeight: FontWeight.bold),
                                    ),
                                  ),
                                if (tranche['gift'] != null)
                                  Padding(
                                    padding: const EdgeInsets.only(top: 8.0),
                                    child: Row(
                                      children: [
                                        const Icon(Icons.card_giftcard, color: Colors.orangeAccent, size: 16),
                                        const SizedBox(width: 6),
                                        Expanded(
                                          child: Text(
                                            'Free Gift: ${tranche['gift']['title']}',
                                            style: const TextStyle(color: Colors.orangeAccent),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                              ],
                            ),
                          );
                        }),
                      ],
                      const SizedBox(height: 100),
                    ],
                  ),
                ),
              ),
            ],
          ),
          Positioned(
            bottom: 0,
            left: 0,
            right: 0,
            child: ClipRRect(
              child: BackdropFilter(
                filter: ImageFilter.blur(sigmaX: 15, sigmaY: 15),
                child: Container(
                  padding: const EdgeInsets.all(24),
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.05),
                    border: Border(
                      top: BorderSide(color: Colors.white.withOpacity(0.1)),
                    ),
                  ),
                  child: Row(
                    children: [
                      Container(
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Row(
                          children: [
                            IconButton(
                              icon: const Icon(Icons.remove, color: Colors.white),
                              onPressed: () {
                                if (_quantity > 1) {
                                  setState(() => _quantity--);
                                }
                              },
                            ),
                            Text(
                              '$_quantity',
                              style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold),
                            ),
                            IconButton(
                              icon: const Icon(Icons.add, color: Colors.white),
                              onPressed: () {
                                setState(() => _quantity++);
                              },
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(width: 16),
                      Expanded(
                        child: ElevatedButton(
                          onPressed: () {
                            ref
                                .read(cartProvider.notifier)
                                .addItem(
                                  CartItem(
                                    packId: product['id'],
                                    title: title,
                                    image: image,
                                    price: currentPrice,
                                    quantity: _quantity,
                                  ),
                                );
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(
                                content: Text('$_quantity x $title added to cart!'),
                                backgroundColor: Colors.green,
                              ),
                            );
                          },
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFF4F46E5),
                            padding: const EdgeInsets.symmetric(vertical: 16),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(16),
                            ),
                          ),
                          child: const Text(
                            'Add to Cart',
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                              color: Colors.white,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
