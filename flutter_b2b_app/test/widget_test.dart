import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:dio/dio.dart';
import 'package:b2b_app/core/network/dio_client.dart';
import 'package:b2b_app/core/storage/secure_storage.dart';
import 'package:b2b_app/main.dart';

class FakeDio extends Fake implements Dio {
  @override
  Future<Response<T>> get<T>(
    String path, {
    Object? data,
    Map<String, dynamic>? queryParameters,
    Options? options,
    CancelToken? cancelToken,
    ProgressCallback? onReceiveProgress,
  }) async {
    if (path == '/init') {
      return Response<T>(
        requestOptions: RequestOptions(path: path),
        data: {
          'success': true,
          'data': {
            'maintenance_mode': 'false',
          }
        } as T,
        statusCode: 200,
      );
    }
    throw UnimplementedError('No mock for $path');
  }
}

class FakeSecureStorage extends Fake implements SecureStorage {
  @override
  Future<String?> getToken() async => null;
}

void main() {
  testWidgets('App starts with CircularProgressIndicator on Splash Screen', (WidgetTester tester) async {
    // Build our app and trigger a frame.
    await tester.pumpWidget(
      ProviderScope(
        overrides: [
          dioProvider.overrideWithValue(FakeDio()),
          secureStorageProvider.overrideWithValue(FakeSecureStorage()),
        ],
        child: const B2BCustomerApp(),
      ),
    );

    // Verify that CircularProgressIndicator is displayed initially.
    expect(find.byType(CircularProgressIndicator), findsOneWidget);

    // Let the async initialization complete to avoid pending timers/microtasks
    await tester.pumpAndSettle();
  });
}


