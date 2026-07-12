import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'core/routing/app_router.dart';

import 'core/theme/app_theme.dart';

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
      theme: AppTheme.lightTheme,
      routerConfig: goRouter,
    );
  }
}
