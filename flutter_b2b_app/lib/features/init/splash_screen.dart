import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/network/dio_client.dart';
import '../../../core/storage/secure_storage.dart';

class SplashScreen extends ConsumerStatefulWidget {
  const SplashScreen({super.key});

  @override
  ConsumerState<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends ConsumerState<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _initializeApp();
  }

  Future<void> _initializeApp() async {
    try {
      // 1. Call /init to get App Settings
      final dio = ref.read(dioProvider);
      final response = await dio.get('/init');
      
      if (response.data['success']) {
        final settings = response.data['data'];
        if (settings['maintenance_mode'] == 'true') {
          // Show maintenance screen logic here
          // return;
        }
      }

      // 2. Check for existing JWT
      final storage = ref.read(secureStorageProvider);
      final token = await storage.getToken();

      if (token != null) {
        // Token exists, auto-login
        if (mounted) context.go('/home');
      } else {
        // No token, go to onboarding
        if (mounted) context.go('/onboarding');
      }
    } catch (e) {
      // Handle error (e.g., no internet)
      if (mounted) context.go('/onboarding');
    }
  }

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      body: Center(
        child: CircularProgressIndicator(),
      ),
    );
  }
}
