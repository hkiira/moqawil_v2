import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/network/dio_client.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:dio/dio.dart';

final authRepositoryProvider = Provider((ref) {
  final dio = ref.watch(dioProvider);
  return AuthRepository(dio, const FlutterSecureStorage());
});

class AuthRepository {
  final Dio _dio;
  final FlutterSecureStorage _storage;

  AuthRepository(this._dio, this._storage);

  Future<void> login(String phone, String password) async {
    final response = await _dio.post('/login', data: {
      'phone': phone,
      'password': password,
    });

    if (response.data['success']) {
      final token = response.data['data']['token'];
      await _storage.write(key: 'jwt_token', value: token);
    } else {
      throw Exception(response.data['message']);
    }
  }

  Future<void> signup(Map<String, dynamic> data) async {
    final response = await _dio.post('/customerSignup', data: data);
    if (!response.data['success']) {
      throw Exception(response.data['message']);
    }
  }

  Future<void> logout() async {
    await _storage.delete(key: 'jwt_token');
  }
}
