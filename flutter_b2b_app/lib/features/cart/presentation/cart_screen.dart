import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../domain/cart_state.dart';
import '../data/cart_repository.dart';

class CartScreen extends ConsumerStatefulWidget {
  const CartScreen({super.key});

  @override
  ConsumerState<CartScreen> createState() => _CartScreenState();
}

class _CartScreenState extends ConsumerState<CartScreen> {
  bool _isCheckingOut = false;
  final double _deliveryFee = 15.0; // Static delivery fee

  Future<void> _checkout() async {
    final items = ref.read(cartProvider);
    if (items.isEmpty) return;

    setState(() => _isCheckingOut = true);
    
    final success = await ref.read(cartRepositoryProvider).submitOrder(items);

    if (mounted) {
      setState(() => _isCheckingOut = false);
      if (success) {
        ref.read(cartProvider.notifier).clearCart();
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Order placed successfully!'), backgroundColor: Colors.green),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Failed to place order.'), backgroundColor: Colors.red),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final cartItems = ref.watch(cartProvider);
    final subTotal = ref.read(cartProvider.notifier).totalPrice;
    final total = cartItems.isEmpty ? 0.0 : subTotal + _deliveryFee;

    return Scaffold(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      body: Stack(
        children: [
          // Global Glassmorphism Backgrounds
          Positioned(
            top: -100, right: -50,
            child: ImageFiltered(
              imageFilter: ImageFilter.blur(sigmaX: 100, sigmaY: 100),
              child: Container(width: 300, height: 300, decoration: BoxDecoration(color: Theme.of(context).colorScheme.primary.withOpacity(0.08), shape: BoxShape.circle)),
            ),
          ),
          
          SafeArea(
            child: cartItems.isEmpty
                ? Center(
                    child: Text('Your cart is empty', style: TextStyle(color: Theme.of(context).colorScheme.onSurface.withOpacity(0.7), fontSize: 18)),
                  )
                : Column(
                    children: [
                      Padding(
                        padding: const EdgeInsets.all(24.0),
                        child: Align(
                          alignment: Alignment.centerLeft,
                          child: Text(
                            'Your Cart',
                            style: TextStyle(color: Theme.of(context).colorScheme.onSurface, fontSize: 32, fontWeight: FontWeight.bold),
                          ),
                        ),
                      ),
                      Expanded(
                        child: ListView.builder(
                          itemCount: cartItems.length,
                          itemBuilder: (context, index) {
                            final item = cartItems[index];
                            return _buildCartItem(item, ref);
                          },
                        ),
                      ),
                      _buildCheckoutBar(subTotal, _deliveryFee, total),
                    ],
                  ),
          ),
        ],
      ),
    );
  }

  Widget _buildCartItem(CartItem item, WidgetRef ref) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 24, vertical: 8),
      padding: const EdgeInsets.all(12),
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
      child: Row(
        children: [
          ClipRRect(
            borderRadius: BorderRadius.circular(12),
            child: Image.network(
              item.image,
              width: 70,
              height: 70,
              fit: BoxFit.cover,
              errorBuilder: (c, e, s) => Container(
                width: 70, height: 70, color: Colors.grey.withOpacity(0.1),
                child: Icon(Icons.image, color: Theme.of(context).colorScheme.primary.withOpacity(0.3)),
              )
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(item.title, style: TextStyle(color: Theme.of(context).colorScheme.onSurface, fontWeight: FontWeight.bold, fontSize: 16), maxLines: 2, overflow: TextOverflow.ellipsis),
                const SizedBox(height: 8),
                Text('${item.price} MAD', style: TextStyle(color: Theme.of(context).colorScheme.primary, fontWeight: FontWeight.bold, fontSize: 14)),
              ],
            ),
          ),
          Row(
            children: [
              IconButton(
                icon: Icon(Icons.remove_circle_outline, color: Theme.of(context).colorScheme.onSurface.withOpacity(0.6)),
                onPressed: () => ref.read(cartProvider.notifier).updateQuantity(item.packId, item.quantity - 1),
              ),
              Text('${item.quantity}', style: TextStyle(color: Theme.of(context).colorScheme.onSurface, fontSize: 18, fontWeight: FontWeight.bold)),
              IconButton(
                icon: Icon(Icons.add_circle_outline, color: Theme.of(context).colorScheme.onSurface.withOpacity(0.6)),
                onPressed: () => ref.read(cartProvider.notifier).updateQuantity(item.packId, item.quantity + 1),
              ),
            ],
          )
        ],
      ),
    );
  }

  Widget _buildCheckoutBar(double subTotal, double deliveryFee, double total) {
    return Container(
      padding: const EdgeInsets.all(24),
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
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Subtotal:', style: TextStyle(color: Theme.of(context).colorScheme.onSurface.withOpacity(0.7), fontSize: 16)),
              Text('${subTotal.toStringAsFixed(2)} MAD', style: TextStyle(color: Theme.of(context).colorScheme.onSurface, fontSize: 16)),
            ],
          ),
          const SizedBox(height: 8),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Delivery Fee:', style: TextStyle(color: Theme.of(context).colorScheme.onSurface.withOpacity(0.7), fontSize: 16)),
              Text('${deliveryFee.toStringAsFixed(2)} MAD', style: TextStyle(color: Theme.of(context).colorScheme.onSurface, fontSize: 16)),
            ],
          ),
          Divider(color: Colors.grey.withOpacity(0.2), height: 24),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Total:', style: TextStyle(color: Theme.of(context).colorScheme.onSurface, fontSize: 20, fontWeight: FontWeight.bold)),
              Text('${total.toStringAsFixed(2)} MAD', style: TextStyle(color: Theme.of(context).colorScheme.secondary, fontSize: 24, fontWeight: FontWeight.bold)),
            ],
          ),
          const SizedBox(height: 16),
          ElevatedButton(
            onPressed: _isCheckingOut ? null : _checkout,
            style: ElevatedButton.styleFrom(
              backgroundColor: Theme.of(context).colorScheme.primary,
              padding: const EdgeInsets.symmetric(vertical: 16),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
            ),
            child: _isCheckingOut
                ? const SizedBox(width: 24, height: 24, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                : const Text('Confirm Order', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.white)),
          )
        ],
      ),
    );
  }
}
