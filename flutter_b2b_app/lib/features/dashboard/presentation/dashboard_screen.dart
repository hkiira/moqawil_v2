import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:carousel_slider/carousel_slider.dart';
import 'package:go_router/go_router.dart';
import '../domain/home_providers.dart';
import '../../../core/storage/secure_storage.dart';

class DashboardScreen extends ConsumerStatefulWidget {
  const DashboardScreen({super.key});

  @override
  ConsumerState<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends ConsumerState<DashboardScreen> {
  final ScrollController _scrollController = ScrollController();
  bool _isScrolled = false;

  @override
  void initState() {
    super.initState();
    _scrollController.addListener(() {
      if (_scrollController.offset > 50 && !_isScrolled) {
        setState(() => _isScrolled = true);
      } else if (_scrollController.offset <= 50 && _isScrolled) {
        setState(() => _isScrolled = false);
      }
    });
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      body: Stack(
        children: [
          // Global Glassmorphism Backgrounds
          Positioned(
            top: -100, right: -50,
            child: ImageFiltered(
              imageFilter: ImageFilter.blur(sigmaX: 100, sigmaY: 100),
              child: Container(width: 300, height: 300, decoration: BoxDecoration(color: Theme.of(context).colorScheme.primary.withOpacity(0.08), shape: BoxShape.circle)),
            ),
          ),

          CustomScrollView(
            controller: _scrollController,
            slivers: [
              SliverAppBar(
                expandedHeight: 0,
                floating: true,
                pinned: true,
                elevation: _isScrolled ? 2 : 0,
                backgroundColor: _isScrolled ? Theme.of(context).scaffoldBackgroundColor.withOpacity(0.95) : Colors.transparent,
                title: Container(
                  height: 40,
                  decoration: BoxDecoration(
                    color: Colors.black.withOpacity(0.05),
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(color: Colors.grey.withOpacity(0.2)),
                  ),
                  child: TextField(
                    readOnly: true,
                    onTap: () => context.push('/search'),
                    style: TextStyle(color: Theme.of(context).colorScheme.onSurface),
                    decoration: InputDecoration(
                      hintText: 'Search products...',
                      hintStyle: TextStyle(color: Theme.of(context).colorScheme.onSurface.withOpacity(0.5)),
                      prefixIcon: Icon(Icons.search, color: Theme.of(context).colorScheme.onSurface.withOpacity(0.5)),
                      border: InputBorder.none,
                      contentPadding: const EdgeInsets.symmetric(vertical: 10),
                    ),
                  ),
                ),
                actions: [
                  IconButton(
                    icon: Icon(Icons.notifications_none, color: Theme.of(context).colorScheme.onSurface),
                    onPressed: () {},
                  ),
                  IconButton(
                    icon: Icon(Icons.shopping_cart_outlined, color: Theme.of(context).colorScheme.onSurface),
                    onPressed: () {},
                  ),
                  IconButton(
                    icon: Icon(Icons.logout, color: Theme.of(context).colorScheme.onSurface),
                    onPressed: () async {
                      await ref.read(secureStorageProvider).deleteToken();
                      if (context.mounted) context.go('/login');
                    },
                  ),
                ],
              ),
              
              SliverList(
                delegate: SliverChildListDelegate([
                  const SizedBox(height: 16),
                  _buildSliderSection(),
                  const SizedBox(height: 32),
                  _buildCategorySection(),
                  const SizedBox(height: 32),
                  _buildNewProductsSection(),
                  const SizedBox(height: 32),
                  _buildBrandsSection(),
                  const SizedBox(height: 48),
                ]),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildSliderSection() {
    final slidersAsync = ref.watch(slidersProvider);
    return slidersAsync.when(
      loading: () => const SizedBox(height: 180, child: Center(child: CircularProgressIndicator())),
      error: (err, stack) => SizedBox(height: 180, child: Center(child: Text('Error: $err', style: const TextStyle(color: Colors.red)))),
      data: (sliders) {
        if (sliders.isEmpty) return const SizedBox.shrink();
        return CarouselSlider(
          options: CarouselOptions(
            height: 180.0,
            autoPlay: true,
            enlargeCenterPage: true,
            aspectRatio: 16/9,
            autoPlayCurve: Curves.fastOutSlowIn,
            enableInfiniteScroll: true,
            autoPlayAnimationDuration: const Duration(milliseconds: 800),
            viewportFraction: 0.85,
          ),
          items: sliders.map((slider) {
            return Builder(
              builder: (BuildContext context) {
                return ClipRRect(
                  borderRadius: BorderRadius.circular(16),
                  child: Image.network(
                    slider['image'] ?? '',
                    fit: BoxFit.cover,
                    width: double.infinity,
                    errorBuilder: (context, error, stackTrace) => Container(
                      color: Colors.grey.withOpacity(0.1),
                      child: Center(child: Icon(Icons.image, color: Theme.of(context).colorScheme.primary.withOpacity(0.4), size: 50)),
                    ),
                  ),
                );
              },
            );
          }).toList(),
        );
      },
    );
  }

  Widget _buildCategorySection() {
    final categoriesAsync = ref.watch(homeCategoriesProvider);
    return categoriesAsync.when(
      loading: () => const Center(child: CircularProgressIndicator()),
      error: (err, stack) => const SizedBox.shrink(),
      data: (categories) {
        if (categories.isEmpty) return const SizedBox.shrink();
        return Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('Categories', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Theme.of(context).colorScheme.onSurface)),
              const SizedBox(height: 16),
              GridView.builder(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 4,
                  crossAxisSpacing: 16,
                  mainAxisSpacing: 16,
                  childAspectRatio: 0.8,
                ),
                itemCount: categories.length,
                itemBuilder: (context, index) {
                  final cat = categories[index];
                  return GestureDetector(
                    onTap: () => context.push('/catalog', extra: {'categoryId': cat['id'].toString()}),
                    child: Column(
                      children: [
                        Expanded(
                          child: ClipRRect(
                            borderRadius: BorderRadius.circular(12),
                            child: Container(
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(color: Colors.grey.withOpacity(0.15)),
                                boxShadow: [
                                  BoxShadow(
                                    color: Colors.black.withOpacity(0.03),
                                    blurRadius: 6,
                                    offset: const Offset(0, 2),
                                  ),
                                ],
                              ),
                              child: cat['image'] != null && cat['image'].toString().isNotEmpty
                                ? Image.network(cat['image'], fit: BoxFit.cover, errorBuilder: (_,__,___) => Center(child: Icon(Icons.category, color: Theme.of(context).colorScheme.primary.withOpacity(0.4))))
                                : Center(child: Icon(Icons.category, color: Theme.of(context).colorScheme.primary.withOpacity(0.4))),
                            ),
                          ),
                        ),
                        const SizedBox(height: 8),
                        Text(cat['title'] ?? 'Category', maxLines: 1, overflow: TextOverflow.ellipsis, style: TextStyle(color: Theme.of(context).colorScheme.onSurface.withOpacity(0.8), fontSize: 12)),
                      ],
                    ),
                  );
                },
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildNewProductsSection() {
    final productsAsync = ref.watch(newProductsProvider);
    return productsAsync.when(
      loading: () => const Center(child: CircularProgressIndicator()),
      error: (err, stack) => const SizedBox.shrink(),
      data: (products) {
        if (products.isEmpty) return const SizedBox.shrink();
        return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Text('New Arrivals', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Theme.of(context).colorScheme.onSurface)),
            ),
            const SizedBox(height: 16),
            SizedBox(
              height: 260,
              child: ListView.builder(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                scrollDirection: Axis.horizontal,
                itemCount: products.length,
                itemBuilder: (context, index) {
                  final product = products[index];
                  return Container(
                    width: 160,
                    margin: const EdgeInsets.only(right: 16),
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
                        ClipRRect(
                          borderRadius: const BorderRadius.vertical(top: Radius.circular(16)),
                          child: Container(
                            height: 140,
                            color: Colors.grey.withOpacity(0.05),
                            child: product['image'] != null && product['image'].toString().isNotEmpty
                                ? Image.network(product['image'], fit: BoxFit.cover, errorBuilder: (_,__,___) => Center(child: Icon(Icons.shopping_bag, size: 50, color: Theme.of(context).colorScheme.primary.withOpacity(0.3))))
                                : Center(child: Icon(Icons.shopping_bag, size: 50, color: Theme.of(context).colorScheme.primary.withOpacity(0.3))),
                          ),
                        ),
                        Padding(
                          padding: const EdgeInsets.all(12),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(product['title'] ?? 'Product', maxLines: 2, overflow: TextOverflow.ellipsis, style: TextStyle(color: Theme.of(context).colorScheme.onSurface, fontWeight: FontWeight.bold)),
                              const SizedBox(height: 8),
                              Text('\$${product['price'] ?? '0.00'}', style: TextStyle(color: Theme.of(context).colorScheme.secondary, fontWeight: FontWeight.bold, fontSize: 16)),
                            ],
                          ),
                        )
                      ],
                    ),
                  );
                },
              ),
            ),
          ],
        );
      },
    );
  }

  Widget _buildBrandsSection() {
    final brandsAsync = ref.watch(brandsProvider);
    return brandsAsync.when(
      loading: () => const Center(child: CircularProgressIndicator()),
      error: (err, stack) => const SizedBox.shrink(),
      data: (brands) {
        if (brands.isEmpty) return const SizedBox.shrink();
        return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Text('Featured Brands', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Theme.of(context).colorScheme.onSurface)),
            ),
            const SizedBox(height: 16),
            SizedBox(
              height: 100,
              child: ListView.builder(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                scrollDirection: Axis.horizontal,
                itemCount: brands.length,
                itemBuilder: (context, index) {
                  final brand = brands[index];
                  return ClipRRect(
                    borderRadius: BorderRadius.circular(12),
                    child: Container(
                      width: 100,
                      margin: const EdgeInsets.only(right: 16),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: Colors.grey.withOpacity(0.15)),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.02),
                            blurRadius: 6,
                            offset: const Offset(0, 2),
                          ),
                        ],
                      ),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          brand['image'] != null && brand['image'].toString().isNotEmpty
                              ? Image.network(brand['image'], width: 40, height: 40, errorBuilder: (_,__,___) => Icon(Icons.branding_watermark, color: Theme.of(context).colorScheme.primary.withOpacity(0.4), size: 40))
                              : Icon(Icons.branding_watermark, color: Theme.of(context).colorScheme.primary.withOpacity(0.4), size: 40),
                          const SizedBox(height: 8),
                          Text(brand['title'] ?? 'Brand', style: TextStyle(color: Theme.of(context).colorScheme.onSurface.withOpacity(0.8))),
                        ],
                      ),
                    ),
                  );
                },
              ),
            ),
          ],
        );
      },
    );
  }
}
