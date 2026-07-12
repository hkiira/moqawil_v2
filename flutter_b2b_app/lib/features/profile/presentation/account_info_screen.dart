import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../domain/profile_provider.dart';

class AccountInfoScreen extends ConsumerWidget {
  const AccountInfoScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final profileAsync = ref.watch(profileProvider);

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
                    Text('Account Information', style: TextStyle(color: Theme.of(context).colorScheme.onSurface, fontSize: 24, fontWeight: FontWeight.bold)),
                  ],
                ),
                Expanded(
                  child: profileAsync.when(
                    data: (profile) {
                      if (profile == null) return Center(child: Text('No Data', style: TextStyle(color: Theme.of(context).colorScheme.onSurface)));
                      return ListView(
                        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                        children: [
                          _buildInfoCard(context, 'Name', profile.name, Icons.person),
                          _buildInfoCard(context, 'Phone Number', profile.phone, Icons.phone),
                          _buildInfoCard(context, 'Shipping Address', profile.adresse.isNotEmpty ? profile.adresse : 'No address provided', Icons.location_on),
                        ],
                      );
                    },
                    loading: () => Center(child: CircularProgressIndicator(color: Theme.of(context).colorScheme.primary)),
                    error: (e, st) => Center(child: Text('Error: $e', style: const TextStyle(color: Colors.red))),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoCard(BuildContext context, String title, String value, IconData icon) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
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
        padding: const EdgeInsets.all(20),
        child: Row(
          children: [
            Icon(icon, color: Theme.of(context).colorScheme.primary, size: 32),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title, style: TextStyle(color: Theme.of(context).colorScheme.onSurface.withOpacity(0.5), fontSize: 14)),
                  const SizedBox(height: 4),
                  Text(value, style: TextStyle(color: Theme.of(context).colorScheme.onSurface, fontSize: 18, fontWeight: FontWeight.bold)),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
