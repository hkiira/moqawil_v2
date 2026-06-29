import 'dart:io' show Platform;
import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'auth_interceptor.dart';

final dioProvider = Provider<Dio>((ref) {
  // Use 10.0.2.2 for Android Emulator, localhost for iOS Simulator/Web
  String baseUrl = 'http://localhost/moqa/api/b2b';
  try {
    if (Platform.isAndroid) {
      baseUrl = 'http://10.0.2.2/moqa/api/b2b';
    }
  } catch (e) {
    // Platform.isAndroid throws on Web
  }

  final dio = Dio(BaseOptions(
    baseUrl: baseUrl, // Update for production
    connectTimeout: const Duration(seconds: 10),
    receiveTimeout: const Duration(seconds: 10),
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
  ));

  // Add the Auth Interceptor to automatically inject the JWT
  dio.interceptors.add(AuthInterceptor(ref));

  return dio;
});
