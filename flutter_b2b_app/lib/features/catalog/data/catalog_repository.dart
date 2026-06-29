import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/network/dio_client.dart';
import 'package:dio/dio.dart';

final catalogRepositoryProvider = Provider((ref) {
  return CatalogRepository(ref.watch(dioProvider));
});

class CatalogRepository {
  final Dio _dio;
  CatalogRepository(this._dio);

  Future<List<dynamic>> getProducts({String? search, String? categoryId}) async {
    final queryParams = <String, dynamic>{};
    if (search != null && search.isNotEmpty) queryParams['search'] = search;
    if (categoryId != null && categoryId.isNotEmpty) queryParams['category_id'] = categoryId;

    final response = await _dio.get('/products', queryParameters: queryParams);
    return response.data['data'];
  }
}
