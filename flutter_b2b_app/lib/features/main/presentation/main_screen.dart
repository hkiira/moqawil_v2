import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../domain/main_providers.dart';
import '../../dashboard/presentation/dashboard_screen.dart';
import '../../catalog/presentation/catalog_screen.dart';
import '../../cart/presentation/cart_screen.dart';
import '../../orders/presentation/orders_screen.dart';
import '../../profile/presentation/profile_screen.dart';

class MainScreen extends ConsumerWidget {
  const MainScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final currentIndex = ref.watch(bottomNavProvider);

    final pages = [
      const DashboardScreen(),
      const CatalogScreen(),
      const CartScreen(),
      const OrdersScreen(),
      const ProfileScreen(),
    ];

    return Scaffold(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      body: pages[currentIndex],
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          color: Theme.of(context).colorScheme.surface,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.05),
              blurRadius: 10,
              offset: const Offset(0, -2),
            ),
          ],
          border: Border(top: BorderSide(color: Colors.grey.withOpacity(0.2))),
        ),
        child: BottomNavigationBar(
          backgroundColor: Theme.of(context).colorScheme.surface,
          elevation: 0,
          type: BottomNavigationBarType.fixed,
          selectedItemColor: Theme.of(context).colorScheme.primary,
          unselectedItemColor: Theme.of(context).bottomNavigationBarTheme.unselectedItemColor ?? Colors.grey,
          currentIndex: currentIndex,
          onTap: (index) => ref.read(bottomNavProvider.notifier).setIndex(index),
          items: const [
            BottomNavigationBarItem(icon: Icon(Icons.home_outlined), activeIcon: Icon(Icons.home), label: 'Home'),
            BottomNavigationBarItem(icon: Icon(Icons.grid_view), activeIcon: Icon(Icons.grid_view_rounded), label: 'Catalog'),
            BottomNavigationBarItem(icon: Icon(Icons.shopping_cart_outlined), activeIcon: Icon(Icons.shopping_cart), label: 'Cart'),
            BottomNavigationBarItem(icon: Icon(Icons.receipt_long_outlined), activeIcon: Icon(Icons.receipt_long), label: 'Orders'),
            BottomNavigationBarItem(icon: Icon(Icons.person_outline), activeIcon: Icon(Icons.person), label: 'Profile'),
          ],
        ),
      ),
    );
  }
}
