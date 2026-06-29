import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:dio/dio.dart';
import '../../../core/network/dio_client.dart';
import '../domain/profile_model.dart';

final profileRepositoryProvider = Provider((ref) {
  return ProfileRepository(ref.watch(dioProvider));
});

class ProfileRepository {
  final Dio _dio;
  ProfileRepository(this._dio);

  Future<ProfileModel?> fetchProfile() async {
    try {
      final response = await _dio.get('/profile');
      if (response.data['success'] == true) {
        return ProfileModel.fromJson(response.data['data']);
      }
      return null;
    } catch (e) {
      throw Exception('Failed to fetch profile: $e');
    }
  }
}
