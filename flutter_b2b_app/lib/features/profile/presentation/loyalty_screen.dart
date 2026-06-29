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
      backgroundColor: const Color(0xFF1E1E2C),
      body: Stack(
        children: [
          Positioned(
            top: -100, right: -50,
            child: ImageFiltered(
              imageFilter: ImageFilter.blur(sigmaX: 100, sigmaY: 100),
              child: Container(width: 300, height: 300, decoration: BoxDecoration(color: const Color(0xFF4F46E5).withOpacity(0.3), shape: BoxShape.circle)),
            ),
          ),
          SafeArea(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    IconButton(icon: const Icon(Icons.arrow_back, color: Colors.white), onPressed: () => Navigator.pop(context)),
                    const Text('Loyalty Points', style: TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.bold)),
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
                            _buildPointsCard(data.totalPoints),
                            const SizedBox(height: 32),
                            const Text('History', style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
                            const SizedBox(height: 16),
                            if (data.history.isEmpty)
                              const Center(child: Text('No points history available', style: TextStyle(color: Colors.white54, fontSize: 16)))
                            else
                              ...data.history.map((item) => _buildHistoryItem(item)),
                          ],
                        );
                      },
                      loading: () => const Center(child: CircularProgressIndicator(color: Colors.white)),
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

  Widget _buildPointsCard(double totalPoints) {
    return ClipRRect(
      borderRadius: BorderRadius.circular(20),
      child: BackdropFilter(
        filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10),
        child: Container(
          padding: const EdgeInsets.all(32),
          decoration: BoxDecoration(
            color: Colors.white.withOpacity(0.08),
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: Colors.white.withOpacity(0.2)),
          ),
          child: Column(
            children: [
              const Icon(Icons.star, color: Colors.amber, size: 64),
              const SizedBox(height: 16),
              const Text('Total Points', style: TextStyle(color: Colors.white70, fontSize: 18)),
              const SizedBox(height: 8),
              Text(
                '${totalPoints.toStringAsFixed(0)}',
                style: const TextStyle(color: Colors.white, fontSize: 48, fontWeight: FontWeight.bold),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildHistoryItem(item) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(16),
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10),
          child: Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.05),
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: Colors.white.withOpacity(0.1)),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Code: ${item.code}', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                    const SizedBox(height: 4),
                    Text('Value: ${item.valeur} MAD', style: const TextStyle(color: Colors.white54, fontSize: 14)),
                  ],
                ),
                Text(
                  '+${item.points.toStringAsFixed(0)} pts',
                  style: const TextStyle(color: Colors.greenAccent, fontWeight: FontWeight.bold, fontSize: 18),
                )
              ],
            ),
          ),
        ),
      ),
    );
  }
}
