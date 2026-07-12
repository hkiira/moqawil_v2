import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../domain/orders_providers.dart';
import '../domain/order_model.dart';
import 'package:intl/intl.dart';

class OrdersScreen extends ConsumerWidget {
  const OrdersScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final ordersAsync = ref.watch(ordersProvider);

    return Scaffold(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      body: Stack(
        children: [
          Positioned(
            top: -100, left: -50,
            child: ImageFiltered(
              imageFilter: ImageFilter.blur(sigmaX: 100, sigmaY: 100),
              child: Container(width: 300, height: 300, decoration: BoxDecoration(color: Theme.of(context).colorScheme.primary.withOpacity(0.08), shape: BoxShape.circle)),
            ),
          ),
          SafeArea(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Padding(
                  padding: const EdgeInsets.all(24.0),
                  child: Text(
                    'Order History',
                    style: TextStyle(color: Theme.of(context).colorScheme.onSurface, fontSize: 32, fontWeight: FontWeight.bold),
                  ),
                ),
                Expanded(
                  child: RefreshIndicator(
                    onRefresh: () async => ref.refresh(ordersProvider),
                    child: ordersAsync.when(
                      data: (orders) {
                        if (orders.isEmpty) {
                          return ListView(
                            children: [
                              const SizedBox(height: 100),
                              Center(child: Text('No orders found.', style: TextStyle(color: Theme.of(context).colorScheme.onSurface.withOpacity(0.5), fontSize: 18))),
                            ],
                          );
                        }
                        return ListView.builder(
                          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 8),
                          itemCount: orders.length,
                          itemBuilder: (context, index) {
                            return _buildOrderCard(context, orders[index]);
                          },
                        );
                      },
                      loading: () => Center(child: CircularProgressIndicator(color: Theme.of(context).colorScheme.primary)),
                      error: (err, st) => Center(child: Text('Error: $err', style: const TextStyle(color: Colors.red))),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildOrderCard(BuildContext context, OrderModel order) {
    String formattedDate = order.date;
    try {
      final dt = DateTime.parse(order.date);
      formattedDate = DateFormat('dd MMM yyyy, HH:mm').format(dt);
    } catch (_) {}

    Color statusColor;
    String statusText;
    switch (order.status) {
      case 0: statusColor = Colors.orange; statusText = 'Pending'; break;
      case 1: statusColor = Colors.blue; statusText = 'Processing'; break;
      case 2: statusColor = Colors.green; statusText = 'Completed'; break;
      case 3: statusColor = Colors.red; statusText = 'Cancelled'; break;
      default: statusColor = Colors.grey; statusText = 'Unknown'; break;
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.withOpacity(0.15)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.03),
            blurRadius: 8,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(order.code.isNotEmpty ? order.code : '#ORD-${order.id}', style: TextStyle(color: Theme.of(context).colorScheme.onSurface, fontWeight: FontWeight.bold, fontSize: 16)),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(color: statusColor.withOpacity(0.1), borderRadius: BorderRadius.circular(8)),
                  child: Text(statusText, style: TextStyle(color: statusColor, fontSize: 12, fontWeight: FontWeight.bold)),
                ),
              ],
            ),
            const SizedBox(height: 12),
            Row(
              children: [
                if (order.firstProductImage != null)
                  ClipRRect(
                    borderRadius: BorderRadius.circular(8),
                    child: Image.network(order.firstProductImage!, width: 50, height: 50, fit: BoxFit.cover, errorBuilder: (_,__,___) => Container(width: 50, height: 50, color: Colors.grey.withOpacity(0.1), child: Icon(Icons.image, color: Theme.of(context).colorScheme.primary.withOpacity(0.3)))),
                  )
                else
                  Container(width: 50, height: 50, decoration: BoxDecoration(color: Colors.grey.withOpacity(0.1), borderRadius: BorderRadius.circular(8)), child: Icon(Icons.shopping_bag, color: Theme.of(context).colorScheme.primary.withOpacity(0.3))),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      if (order.firstProductTitle != null)
                        Text(order.firstProductTitle!, style: TextStyle(color: Theme.of(context).colorScheme.onSurface.withOpacity(0.8), fontSize: 14), maxLines: 1, overflow: TextOverflow.ellipsis),
                      Text('${order.itemCount} items', style: TextStyle(color: Theme.of(context).colorScheme.onSurface.withOpacity(0.6), fontSize: 12)),
                      const SizedBox(height: 4),
                      Text(formattedDate, style: TextStyle(color: Theme.of(context).colorScheme.onSurface.withOpacity(0.4), fontSize: 12)),
                    ],
                  ),
                ),
                Text('${order.total.toStringAsFixed(2)} MAD', style: TextStyle(color: Theme.of(context).colorScheme.secondary, fontWeight: FontWeight.bold, fontSize: 16)),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
