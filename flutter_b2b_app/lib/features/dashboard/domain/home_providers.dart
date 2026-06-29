import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/home_repository.dart';

final slidersProvider = FutureProvider<List<dynamic>>((ref) async {
  return ref.watch(homeRepositoryProvider).getSliders();
});

final homeCategoriesProvider = FutureProvider<List<dynamic>>((ref) async {
  return ref.watch(homeRepositoryProvider).getCategories();
});

final brandsProvider = FutureProvider<List<dynamic>>((ref) async {
  return ref.watch(homeRepositoryProvider).getBrands();
});

final newProductsProvider = FutureProvider<List<dynamic>>((ref) async {
  return ref.watch(homeRepositoryProvider).getNewProducts();
});

final recommendedProductsProvider = FutureProvider<List<dynamic>>((ref) async {
  return ref.watch(homeRepositoryProvider).getRecommendedProducts();
});
