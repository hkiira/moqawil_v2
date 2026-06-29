import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/network/dio_client.dart';
import 'package:dio/dio.dart';

final homeRepositoryProvider = Provider((ref) {
  return HomeRepository(ref.watch(dioProvider));
});

class HomeRepository {
  final Dio _dio;
  HomeRepository(this._dio);

  Future<List<dynamic>> getSliders() async {
    final response = await _dio.get('/homeSliders');
    return response.data['data'];
  }

  Future<List<dynamic>> getCategories() async {
    final response = await _dio.get('/homecategories');
    return response.data['data'];
  }

  Future<List<dynamic>> getBrands() async {
    final response = await _dio.get('/homebrands');
    return response.data['data'];
  }

  Future<List<dynamic>> getNewProducts() async {
    final response = await _dio.get('/newhomeproducts');
    return response.data['data'];
  }

  Future<List<dynamic>> getRecommendedProducts() async {
    final response = await _dio.get('/recommendedhomeproducts');
    return response.data['data'];
  }
}
