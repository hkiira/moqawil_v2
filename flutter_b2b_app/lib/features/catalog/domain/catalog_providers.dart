import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/catalog_repository.dart';

class CatalogFilter {
  final String? search;
  final String? categoryId;

  const CatalogFilter({this.search, this.categoryId});

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is CatalogFilter &&
          runtimeType == other.runtimeType &&
          search == other.search &&
          categoryId == other.categoryId;

  @override
  int get hashCode => search.hashCode ^ categoryId.hashCode;
}

final catalogProvider = FutureProvider.family<List<dynamic>, CatalogFilter>((ref, filter) async {
  return ref.watch(catalogRepositoryProvider).getProducts(
    search: filter.search,
    categoryId: filter.categoryId,
  );
});
