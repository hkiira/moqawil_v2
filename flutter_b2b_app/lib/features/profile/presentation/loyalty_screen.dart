import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../domain/loyalty_provider.dart';

class LoyaltyScreen extends ConsumerWidget {
  const LoyaltyScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final loyaltyAsync = ref.watch(loyaltyProvider);

    return Scaffold(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      body: Stack(
        children: [
          Positioned(
            top: -100, right: -50,
            child: ImageFiltered(
              imageFilter: ImageFilter.blur(sigmaX: 100, sigmaY: 100),
              child: Container(width: 300, height: 300, decoration: BoxDecoration(color: Theme.of(context).colorScheme.primary.withOpacity(0.08), shape: BoxShape.circle)),
            ),
          ),
          SafeArea(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    IconButton(icon: Icon(Icons.arrow_back, color: Theme.of(context).colorScheme.onSurface), onPressed: () => Navigator.pop(context)),
                    Text('Loyalty Points', style: TextStyle(color: Theme.of(context).colorScheme.onSurface, fontSize: 24, fontWeight: FontWeight.bold)),
                  ],
                ),
                Expanded(
                  child: RefreshIndicator(
                    onRefresh: () async => ref.refresh(loyaltyProvider),
                    child: loyaltyAsync.when(
                      data: (data) {
                        return ListView(
                          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                          children: [
                            _buildPointsCard(context, data.totalPoints),
                            const SizedBox(height: 32),
                            Text('History', style: TextStyle(color: Theme.of(context).colorScheme.onSurface, fontSize: 18, fontWeight: FontWeight.bold)),
                            const SizedBox(height: 16),
                            if (data.history.isEmpty)
                              Center(child: Text('No points history available', style: TextStyle(color: Theme.of(context).colorScheme.onSurface.withOpacity(0.5), fontSize: 16)))
                            else
                              ...data.history.map((item) => _buildHistoryItem(context, item)),
                          ],
                        );
                      },
                      loading: () => Center(child: CircularProgressIndicator(color: Theme.of(context).colorScheme.primary)),
                      error: (e, st) => Center(child: Text('Error: $e', style: const TextStyle(color: Colors.red))),
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

  Widget _buildPointsCard(BuildContext context, double totalPoints) {
    return Container(
      padding: const EdgeInsets.all(32),
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
      child: Column(
        children: [
          Icon(Icons.star, color: Theme.of(context).colorScheme.secondary, size: 64),
          const SizedBox(height: 16),
          Text('Total Points', style: TextStyle(color: Theme.of(context).colorScheme.onSurface.withOpacity(0.7), fontSize: 18)),
          const SizedBox(height: 8),
          Text(
            '${totalPoints.toStringAsFixed(0)}',
            style: TextStyle(color: Theme.of(context).colorScheme.secondary, fontSize: 48, fontWeight: FontWeight.bold),
          ),
        ],
      ),
    );
  }

  Widget _buildHistoryItem(BuildContext context, item) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.grey.withOpacity(0.15)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 6,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Code: ${item.code}', style: TextStyle(color: Theme.of(context).colorScheme.onSurface, fontWeight: FontWeight.bold, fontSize: 16)),
                const SizedBox(height: 4),
                Text('Value: ${item.valeur} MAD', style: TextStyle(color: Theme.of(context).colorScheme.onSurface.withOpacity(0.6), fontSize: 14)),
              ],
            ),
            Text(
              '+${item.points.toStringAsFixed(0)} pts',
              style: TextStyle(color: Theme.of(context).colorScheme.primary, fontWeight: FontWeight.bold, fontSize: 18),
            )
          ],
        ),
      ),
    );
  }
}
