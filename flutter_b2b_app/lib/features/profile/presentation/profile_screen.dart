import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../domain/profile_provider.dart';
import '../../../core/storage/secure_storage.dart';

class ProfileScreen extends ConsumerWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final profileAsync = ref.watch(profileProvider);

    return Scaffold(
      backgroundColor: Colors.transparent,
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
                const Padding(
                  padding: EdgeInsets.all(24.0),
                  child: Text(
                    'Profile',
                    style: TextStyle(color: Colors.white, fontSize: 32, fontWeight: FontWeight.bold),
                  ),
                ),
                Expanded(
                  child: RefreshIndicator(
                    onRefresh: () async => ref.refresh(profileProvider),
                    child: ListView(
                      padding: const EdgeInsets.symmetric(horizontal: 24),
                      children: [
                        profileAsync.when(
                          data: (profile) {
                            if (profile == null) return const SizedBox.shrink();
                            return _buildProfileCard(profile);
                          },
                          loading: () => const Center(child: CircularProgressIndicator(color: Colors.white)),
                          error: (e, st) => Center(child: Text('Error: $e', style: const TextStyle(color: Colors.red))),
                        ),
                        const SizedBox(height: 32),
                        _buildSettingsMenu(context, ref),
                      ],
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

  Widget _buildProfileCard(profile) {
    return ClipRRect(
      borderRadius: BorderRadius.circular(20),
      child: BackdropFilter(
        filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10),
        child: Container(
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            color: Colors.white.withOpacity(0.08),
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: Colors.white.withOpacity(0.2)),
          ),
          child: Column(
            children: [
              CircleAvatar(
                radius: 40,
                backgroundColor: const Color(0xFF4F46E5).withOpacity(0.2),
                child: Text(
                  profile.name.isNotEmpty ? profile.name.substring(0, 1).toUpperCase() : 'U',
                  style: const TextStyle(color: Colors.white, fontSize: 32, fontWeight: FontWeight.bold),
                ),
              ),
              const SizedBox(height: 16),
              Text(profile.name, style: const TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.bold)),
              const SizedBox(height: 8),
              Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.phone, color: Colors.white54, size: 16),
                  const SizedBox(width: 8),
                  Text(profile.phone, style: const TextStyle(color: Colors.white70, fontSize: 16)),
                ],
              ),
              if (profile.adresse.isNotEmpty) ...[
                const SizedBox(height: 8),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.location_on, color: Colors.white54, size: 16),
                    const SizedBox(width: 8),
                    Flexible(child: Text(profile.adresse, style: const TextStyle(color: Colors.white70, fontSize: 16), textAlign: TextAlign.center)),
                  ],
                ),
              ],
              const SizedBox(height: 16),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.08),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: Colors.white.withOpacity(0.15)),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const Icon(Icons.account_balance_wallet, color: Color(0xFF818CF8), size: 20),
                    const SizedBox(width: 8),
                    Text(
                      'Wallet Balance: ${profile.walletBalance.toStringAsFixed(2)} MAD',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSettingsMenu(BuildContext context, WidgetRef ref) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text('Settings', style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
        const SizedBox(height: 16),
        _buildMenuOption(icon: Icons.person_outline, title: 'Account Information', onTap: () {
          if (context.mounted) context.push('/account_info');
        }),
        _buildMenuOption(icon: Icons.favorite_border, title: 'Wishlist (Favorites)', onTap: () {
          if (context.mounted) context.push('/wishlist');
        }),
        _buildMenuOption(icon: Icons.star_border, title: 'Loyalty Points', onTap: () {
          if (context.mounted) context.push('/loyalty');
        }),
        _buildMenuOption(icon: Icons.notifications_none, title: 'Notifications', onTap: () {
          if (context.mounted) context.push('/notifications');
        }),
        _buildMenuOption(icon: Icons.security, title: 'Privacy Policy', onTap: () {}),
        const SizedBox(height: 32),
        ElevatedButton(
          onPressed: () async {
            await ref.read(secureStorageProvider).deleteToken();
            if (context.mounted) context.go('/login');
          },
          style: ElevatedButton.styleFrom(
            backgroundColor: Colors.red.withOpacity(0.8),
            minimumSize: const Size(double.infinity, 56),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          ),
          child: const Text('Logout', style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
        ),
        const SizedBox(height: 32),
      ],
    );
  }

  Widget _buildMenuOption({required IconData icon, required String title, required VoidCallback onTap}) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(16),
          child: Ink(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.05),
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: Colors.white.withOpacity(0.1)),
            ),
            child: Row(
              children: [
                Icon(icon, color: Colors.white70),
                const SizedBox(width: 16),
                Expanded(child: Text(title, style: const TextStyle(color: Colors.white, fontSize: 16))),
                const Icon(Icons.chevron_right, color: Colors.white38),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
