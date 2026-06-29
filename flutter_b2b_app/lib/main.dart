import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'core/routing/app_router.dart';

void main() {
  runApp(
    const ProviderScope(
      child: B2BCustomerApp(),
    ),
  );
}

class B2BCustomerApp extends StatelessWidget {
  const B2BCustomerApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp.router(
      title: 'B2B Customer App',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: const Color(0xFF667eea)), // Matching web primary color
        useMaterial3: true,
      ),
      routerConfig: goRouter,
    );
  }
}
